<?php
require_once __DIR__ . '/../../config/config.php';

class ScheduleManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // CREATE A NEW SCHEDULE
    public function createSchedule($data) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO schedules (title, description, schedule_type, start_time, end_time, assigned_to, status, meta, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $meta = isset($data['meta']) ? json_encode($data['meta']) : null;
            
            $success = $stmt->execute([
                $data['title'],
                $data['description'] ?? null,
                $data['schedule_type'],
                $data['start_time'],
                $data['end_time'],
                $data['assigned_to'] ?? null,
                $data['status'] ?? 'scheduled',
                $meta,
                $data['created_by'] ?? $_SESSION['user_id']
            ]);
            
            if ($success) {
                $schedule_id = $this->conn->lastInsertId();
                $this->createScheduleNotification($schedule_id, $data);
                return $schedule_id;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Schedule creation error: " . $e->getMessage());
            return false;
        }
    }
    
    // CREATE SHIFT SCHEDULE
    public function createShiftSchedule($employee_id, $shift_data) {
        $meta = [
            'employee_id' => $employee_id,
            'shift_type' => $shift_data['shift_type'],
            'break_times' => $shift_data['break_times'] ?? [],
            'responsibilities' => $shift_data['responsibilities'] ?? []
        ];
        
        $data = [
            'title' => "Shift: " . ucfirst($shift_data['shift_type']),
            'description' => $shift_data['description'] ?? "Employee work shift",
            'schedule_type' => 'shift',
            'start_time' => $shift_data['start_time'],
            'end_time' => $shift_data['end_time'],
            'assigned_to' => $employee_id,
            'meta' => $meta,
            'created_by' => $_SESSION['user_id']
        ];
        
        return $this->createSchedule($data);
    }
    
    // GET SCHEDULES FOR CALENDAR VIEW
    public function getSchedulesForCalendar($start_date, $end_date, $employee_id = null) {
        try {
            $where = ["start_time BETWEEN ? AND ?"];
            $params = [$start_date, $end_date];
            
            if ($employee_id) {
                $where[] = "assigned_to = ?";
                $params[] = $employee_id;
            }
            
            $whereClause = "WHERE " . implode(" AND ", $where);
            
            $stmt = $this->conn->prepare("
                SELECT s.*, 
                       e.first_name, e.last_name, e.position, e.profile_pic,
                       u.username as created_by_name
                FROM schedules s
                LEFT JOIN employees e ON s.assigned_to = e.id
                LEFT JOIN users u ON s.created_by = u.id
                $whereClause
                ORDER BY s.start_time ASC
            ");
            
            $stmt->execute($params);
            $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse meta data and format for calendar
            foreach ($schedules as &$schedule) {
                if ($schedule['meta']) {
                    $schedule['meta_data'] = json_decode($schedule['meta'], true);
                }
                
                // Add calendar-specific fields
                $schedule['date'] = date('Y-m-d', strtotime($schedule['start_time']));
                $schedule['time_range'] = date('g:i A', strtotime($schedule['start_time'])) . ' - ' . 
                                         date('g:i A', strtotime($schedule['end_time']));
            }
            
            return $schedules;
            
        } catch (PDOException $e) {
            error_log("Get schedules for calendar error: " . $e->getMessage());
            return [];
        }
    }
    
    // GET SHIFT COVERAGE STATISTICS
    public function getShiftCoverage($week_start, $week_end) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) as total_shifts,
                    SUM(CASE WHEN schedule_type = 'shift' AND JSON_EXTRACT(meta, '$.shift_type') = 'morning' THEN 1 ELSE 0 END) as morning_shifts,
                    SUM(CASE WHEN schedule_type = 'shift' AND JSON_EXTRACT(meta, '$.shift_type') = 'afternoon' THEN 1 ELSE 0 END) as afternoon_shifts,
                    SUM(CASE WHEN schedule_type = 'shift' AND DAYOFWEEK(start_time) IN (1,7) THEN 1 ELSE 0 END) as weekend_shifts
                FROM schedules 
                WHERE start_time BETWEEN ? AND ? 
                AND schedule_type = 'shift'
            ");
            
            $stmt->execute([$week_start, $week_end]);
            $coverage = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total_employees = $this->conn->query("SELECT COUNT(*) FROM employees WHERE status = 'Active'")->fetchColumn();
            
            return [
                'morning' => [
                    'assigned' => $coverage['morning_shifts'] ?? 0,
                    'total' => $total_employees
                ],
                'afternoon' => [
                    'assigned' => $coverage['afternoon_shifts'] ?? 0,
                    'total' => $total_employees
                ],
                'weekend' => [
                    'assigned' => $coverage['weekend_shifts'] ?? 0,
                    'total' => $total_employees
                ]
            ];
            
        } catch (PDOException $e) {
            error_log("Get shift coverage error: " . $e->getMessage());
            return [
                'morning' => ['assigned' => 0, 'total' => 0],
                'afternoon' => ['assigned' => 0, 'total' => 0],
                'weekend' => ['assigned' => 0, 'total' => 0]
            ];
        }
    }
    
    // GET TIME OFF REQUESTS
    public function getTimeOffRequests($week_start, $week_end) {
        try {
            // Check if time_off_requests table exists
            $tableExists = $this->conn->query("SHOW TABLES LIKE 'time_off_requests'")->rowCount() > 0;
            
            if (!$tableExists) {
                return [];
            }
            
            // Use LEFT JOIN so requests with missing employee rows still surface for debugging
            // and remove the hard LIMIT so all matching requests are returned for the period.
            $stmt = $this->conn->prepare("
                SELECT t.*, e.first_name, e.last_name 
                FROM time_off_requests t
                LEFT JOIN employees e ON t.employee_id = e.id
                --WHERE t.start_date <= ? AND t.end_date >= ?
                AND t.status IN ('pending', 'approved')
                ORDER BY t.start_date ASC
            ");

            $stmt->execute([$week_end, $week_start]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // If employee name is missing, fill with placeholder for display
            foreach ($rows as &$r) {
                if (empty($r['first_name']) && empty($r['last_name'])) {
                    $r['first_name'] = 'Unknown';
                    $r['last_name'] = '';
                }
            }

            return $rows;
            
        } catch (PDOException $e) {
            error_log("Get time off requests error: " . $e->getMessage());
            return [];
        }
    }
    
    // UPDATE SCHEDULE STATUS
    public function updateScheduleStatus($schedule_id, $status) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE schedules 
                SET status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            
            return $stmt->execute([$status, $schedule_id]);
            
        } catch (PDOException $e) {
            error_log("Update schedule status error: " . $e->getMessage());
            return false;
        }
    }
    
    // DELETE SCHEDULE
    public function deleteSchedule($schedule_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM schedules WHERE id = ?");
            return $stmt->execute([$schedule_id]);
            
        } catch (PDOException $e) {
            error_log("Delete schedule error: " . $e->getMessage());
            return false;
        }
    }
    
    // GET ALL SCHEDULES (for list views)
    public function getAllSchedules($filters = []) {
        try {
            $where = [];
            $params = [];
            
            if (isset($filters['schedule_type'])) {
                $where[] = "schedule_type = ?";
                $params[] = $filters['schedule_type'];
            }
            
            if (isset($filters['status'])) {
                $where[] = "status = ?";
                $params[] = $filters['status'];
            }
            
            if (isset($filters['start_date'])) {
                $where[] = "DATE(start_time) >= ?";
                $params[] = $filters['start_date'];
            }
            
            if (isset($filters['end_date'])) {
                $where[] = "DATE(end_time) <= ?";
                $params[] = $filters['end_date'];
            }
            
            $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";
            
            $stmt = $this->conn->prepare("
                SELECT s.*, 
                       e.first_name, e.last_name, e.position, e.profile_pic,
                       u.username as created_by_name
                FROM schedules s
                LEFT JOIN employees e ON s.assigned_to = e.id
                LEFT JOIN users u ON s.created_by = u.id
                $whereClause
                ORDER BY s.start_time ASC
            ");
            
            $stmt->execute($params);
            $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse meta data
            foreach ($schedules as &$schedule) {
                if ($schedule['meta']) {
                    $schedule['meta_data'] = json_decode($schedule['meta'], true);
                }
            }
            
            return $schedules;
            
        } catch (PDOException $e) {
            error_log("Get all schedules error: " . $e->getMessage());
            return [];
        }
    }
    
    // CREATE SCHEDULE NOTIFICATION
    private function createScheduleNotification($schedule_id, $schedule_data) {
        try {
            $message = "New schedule created: " . $schedule_data['title'];
            
            $meta = [
                'schedule_id' => $schedule_id,
                'schedule_type' => $schedule_data['schedule_type'],
                'start_time' => $schedule_data['start_time'],
                'end_time' => $schedule_data['end_time']
            ];
            
            $stmt = $this->conn->prepare("
                INSERT INTO notifications (type, message, meta) 
                VALUES (?, ?, ?)
            ");
            
            return $stmt->execute([
                'schedule_created',
                $message,
                json_encode($meta)
            ]);
            
        } catch (PDOException $e) {
            error_log("Schedule notification error: " . $e->getMessage());
            return false;
        }
    }
}

// MAIN CONTROLLER LOGIC
$scheduleManager = new ScheduleManager($conn);

// Handle view type and date
$view_type = $_GET['view'] ?? 'week';
$current_date = $_GET['date'] ?? date('Y-m-d');

// Calculate date ranges based on view type
if ($view_type === 'week') {
    $week_start = date('Y-m-d', strtotime('monday this week', strtotime($current_date)));
    $week_end = date('Y-m-d', strtotime('sunday this week', strtotime($current_date)));
    $display_text = date('M j', strtotime($week_start)) . ' - ' . date('M j, Y', strtotime($week_end));
    $prev_link = "?page=schedules&view=week&date=" . date('Y-m-d', strtotime('-1 week', strtotime($current_date)));
    $next_link = "?page=schedules&view=week&date=" . date('Y-m-d', strtotime('+1 week', strtotime($current_date)));
    
    // For week view, we need schedules for the entire week
    $start_date = $week_start;
    $end_date = $week_end;
} elseif ($view_type === 'month') {
    $month_start = date('Y-m-01', strtotime($current_date));
    $month_end = date('Y-m-t', strtotime($current_date));
    $display_text = date('F Y', strtotime($current_date));
    $prev_link = "?page=schedules&view=month&date=" . date('Y-m-d', strtotime('-1 month', strtotime($current_date)));
    $next_link = "?page=schedules&view=month&date=" . date('Y-m-d', strtotime('+1 month', strtotime($current_date)));
    
    // For month view, get schedules for the entire month
    $start_date = $month_start;
    $end_date = $month_end;
} else { // day view
    $display_text = date('F j, Y', strtotime($current_date));
    $prev_link = "?page=schedules&view=day&date=" . date('Y-m-d', strtotime('-1 day', strtotime($current_date)));
    $next_link = "?page=schedules&view=day&date=" . date('Y-m-d', strtotime('+1 day', strtotime($current_date)));
    
    // For day view, get schedules only for the specific day
    $start_date = $current_date;
    $end_date = $current_date;
}

// AJAX: return all time off requests (no date filter) for debugging/listing
if (isset($_GET['ajax']) && $_GET['ajax'] === 'time_off_all') {
    try {
        // Clear output buffers
        while (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: application/json');

        $stmt = $conn->prepare("SELECT t.*, e.first_name, e.last_name FROM time_off_requests t LEFT JOIN employees e ON t.employee_id = e.id ORDER BY t.start_date DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
    } catch (PDOException $e) {
        while (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Generate week days for week view
$week_days = [];
if ($view_type === 'week') {
    for ($i = 0; $i < 7; $i++) {
        $day_date = date('Y-m-d', strtotime($week_start . " +$i days"));
        $week_days[] = [
            'date' => $day_date,
            'day_name' => date('D', strtotime($day_date)),
            'day_number' => date('j', strtotime($day_date))
        ];
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_shift'])) {
        $shift_data = [
            'shift_type' => $_POST['shift_type'],
            'start_time' => $_POST['shift_date'] . ' ' . ($_POST['shift_type'] === 'custom' ? $_POST['start_time'] : '06:00:00'),
            'end_time' => $_POST['shift_date'] . ' ' . ($_POST['shift_type'] === 'custom' ? $_POST['end_time'] : '14:00:00'),
            'description' => $_POST['description'] ?? ($_POST['shift_type'] === 'morning' ? 'Morning Shift' : 'Afternoon Shift')
        ];
        
        if ($_POST['shift_type'] === 'afternoon') {
            $shift_data['start_time'] = $_POST['shift_date'] . ' 14:00:00';
            $shift_data['end_time'] = $_POST['shift_date'] . ' 22:00:00';
        }
        
        $shift_id = $scheduleManager->createShiftSchedule($_POST['employee_id'], $shift_data);
        if ($shift_id) {
            $_SESSION['message'] = "Shift created successfully!";
        } else {
            $_SESSION['error'] = "Failed to create shift.";
        }
        
        header("Location: ?page=schedules&view=$view_type&date=$current_date");
        exit;
    }

    // Handle creating a time off request
    if (isset($_POST['action']) && $_POST['action'] === 'create_time_off_request') {
        $employee_id = $_POST['employee_id'] ?? null;
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;
        $reason = $_POST['reason'] ?? null;

        // Basic validation
        if (!$employee_id || !$start_date || !$end_date) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
                exit;
            }
            $_SESSION['error'] = 'Please fill all required fields for time off request.';
            header("Location: ?page=schedules&view=$view_type&date=$current_date");
            exit;
        }

        // Ensure start <= end
        if (strtotime($start_date) > strtotime($end_date)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Start date must be before or equal to end date.']);
                exit;
            }
            $_SESSION['error'] = 'Start date must be before or equal to end date.';
            header("Location: ?page=schedules&view=$view_type&date=$current_date");
            exit;
        }

        try {
            // Check employee exists
            $empStmt = $conn->prepare("SELECT id, first_name, last_name FROM employees WHERE id = ?");
            $empStmt->execute([$employee_id]);
            $emp = $empStmt->fetch(PDO::FETCH_ASSOC);
            if (!$emp) {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Selected employee not found.']);
                    exit;
                }
                $_SESSION['error'] = 'Selected employee not found.';
                header("Location: ?page=schedules&view=$view_type&date=$current_date");
                exit;
            }

            $insert = $conn->prepare("INSERT INTO time_off_requests (employee_id, start_date, end_date, reason, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
            $ok = $insert->execute([$employee_id, $start_date, $end_date, $reason]);
            $newId = $ok ? $conn->lastInsertId() : null;

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                // Clear any output buffers to avoid corrupting JSON
                while (ob_get_level()) { ob_end_clean(); }
                header('Content-Type: application/json');
                if ($ok) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Time off request submitted.',
                        'request' => [
                            'id' => $newId,
                            'employee_id' => $employee_id,
                            'first_name' => $emp['first_name'],
                            'last_name' => $emp['last_name'],
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'status' => 'pending',
                            'reason' => $reason
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to submit time off request.']);
                }
                exit;
            }

            if ($ok) {
                $_SESSION['message'] = 'Time off request submitted.';
            } else {
                $_SESSION['error'] = 'Failed to submit time off request.';
            }
        } catch (PDOException $e) {
            error_log('Time off insert error: ' . $e->getMessage());
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                while (ob_get_level()) { ob_end_clean(); }
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to submit time off request.']);
                exit;
            }
            $_SESSION['error'] = 'Failed to submit time off request.';
        }

        header("Location: ?page=schedules&view=$view_type&date=$current_date");
        exit;
    }

    // Handle updating time off request status (approve/reject)
    if (isset($_POST['action']) && $_POST['action'] === 'update_time_off_status') {
        $req_id = $_POST['request_id'] ?? null;
        $new_status = $_POST['status'] ?? null; // expected 'approved' or 'rejected'

        if (!$req_id || !in_array($new_status, ['approved', 'rejected'])) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid request.']);
                exit;
            }
            $_SESSION['error'] = 'Invalid request.';
            header("Location: ?page=schedules&view=$view_type&date=$current_date");
            exit;
        }

        try {
            $upd = $conn->prepare("UPDATE time_off_requests SET status = ? WHERE id = ?");
            $ok = $upd->execute([$new_status, $req_id]);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                while (ob_get_level()) { ob_end_clean(); }
                header('Content-Type: application/json');
                echo json_encode(['success' => $ok]);
                exit;
            }

            if ($ok) {
                $_SESSION['message'] = 'Time off request updated.';
            } else {
                $_SESSION['error'] = 'Failed to update request.';
            }
        } catch (PDOException $e) {
            error_log('Update time off status error: ' . $e->getMessage());
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                while (ob_get_level()) { ob_end_clean(); }
                header('Content-Type: application/json');
                echo json_encode(['success' => false]);
                exit;
            }
            $_SESSION['error'] = 'Failed to update request.';
        }

        header("Location: ?page=schedules&view=$view_type&date=$current_date");
        exit;
    }

    // Seed a sample time off request (convenience for testing)
    if (isset($_POST['action']) && $_POST['action'] === 'seed_time_off') {
        try {
            // pick first active employee
            $emp = $conn->query("SELECT id FROM employees WHERE status = 'Active' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!$emp) {
                $_SESSION['error'] = 'No active employees to seed.';
                header("Location: ?page=schedules&view=$view_type&date=$current_date");
                exit;
            }
            // pick mid-week dates
            $seed_start = $week_start;
            $seed_end = $week_start;
            $insert = $conn->prepare("INSERT INTO time_off_requests (employee_id, start_date, end_date, reason, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
            $ok = $insert->execute([$emp['id'], $seed_start, $seed_end, 'Seed request']);
            if ($ok) {
                $_SESSION['message'] = 'Sample time off request added.';
            } else {
                $_SESSION['error'] = 'Failed to add sample request.';
            }
        } catch (PDOException $e) {
            error_log('Seed time off error: ' . $e->getMessage());
            $_SESSION['error'] = 'Failed to add sample request.';
        }

        header("Location: ?page=schedules&view=$view_type&date=$current_date");
        exit;
    }
    
    // Handle status updates
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        if (isset($_POST['schedule_id'], $_POST['status'])) {
            $success = $scheduleManager->updateScheduleStatus($_POST['schedule_id'], $_POST['status']);
            if ($success) {
                $_SESSION['message'] = "Schedule status updated!";
            } else {
                $_SESSION['error'] = "Failed to update schedule status.";
            }
        }
        header("Location: ?page=schedules&view=$view_type&date=$current_date");
        exit;
    }
    
    // Handle schedule deletion
    if (isset($_POST['action']) && $_POST['action'] === 'delete_schedule') {
        if (isset($_POST['schedule_id'])) {
            $success = $scheduleManager->deleteSchedule($_POST['schedule_id']);
            if ($success) {
                $_SESSION['message'] = "Schedule deleted successfully!";
            } else {
                $_SESSION['error'] = "Failed to delete schedule.";
            }
        }
        header("Location: ?page=schedules&view=$view_type&date=$current_date");
        exit;
    }

    // Copy last week schedules into current week
    if (isset($_POST['action']) && $_POST['action'] === 'copy_last_week') {
        try {
            // Determine week ranges relative to the provided date (or current_date)
            $target_date = $_POST['date'] ?? $current_date;
            $curr_week_start = date('Y-m-d', strtotime('monday this week', strtotime($target_date)));
            $curr_week_end = date('Y-m-d', strtotime('sunday this week', strtotime($target_date)));
            $prev_week_start = date('Y-m-d', strtotime('monday last week', strtotime($target_date)));
            $prev_week_end = date('Y-m-d', strtotime('sunday last week', strtotime($target_date)));

            // Fetch previous week's schedules
            $stmt = $conn->prepare("SELECT * FROM schedules WHERE DATE(start_time) BETWEEN ? AND ? ORDER BY start_time ASC");
            $stmt->execute([$prev_week_start, $prev_week_end]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $copied = 0;

            foreach ($rows as $row) {
                // Compute day offset from prev week start
                $orig_date = date('Y-m-d', strtotime($row['start_time']));
                $offset_days = (int)( (strtotime($orig_date) - strtotime($prev_week_start)) / 86400 );
                $new_date = date('Y-m-d', strtotime($curr_week_start . " +{$offset_days} days"));

                $time_start = date('H:i:s', strtotime($row['start_time']));
                $time_end = date('H:i:s', strtotime($row['end_time']));

                $new_start = $new_date . ' ' . $time_start;
                $new_end = $new_date . ' ' . $time_end;

                // Skip if an identical schedule already exists (same assigned_to & start_time)
                $checkStmt = $conn->prepare("SELECT COUNT(*) FROM schedules WHERE assigned_to = ? AND start_time = ?");
                $checkStmt->execute([$row['assigned_to'], $new_start]);
                if ($checkStmt->fetchColumn() > 0) {
                    continue;
                }

                // Prepare data for creation
                $data = [
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'schedule_type' => $row['schedule_type'],
                    'start_time' => $new_start,
                    'end_time' => $new_end,
                    'assigned_to' => $row['assigned_to'] ?: null,
                    'status' => 'scheduled', // reset status for new week
                    'meta' => $row['meta'] ? json_decode($row['meta'], true) : null,
                    'created_by' => $_SESSION['user_id'] ?? null
                ];

                $newId = $scheduleManager->createSchedule($data);
                if ($newId) {
                    $copied++;
                }
            }

            $_SESSION['message'] = "Copied {$copied} schedules from last week.";
        } catch (Exception $e) {
            error_log('Copy last week error: ' . $e->getMessage());
            $_SESSION['error'] = 'Failed to copy last week schedules.';
        }

        header("Location: ?page=schedules&view=$view_type&date=$current_date");
        exit;
    }
}

// Fetch data for the view
$employees = $conn->query("SELECT id, first_name, last_name, position, profile_pic FROM employees WHERE status = 'Active'")->fetchAll(PDO::FETCH_ASSOC);

// Get schedules for the current view period
$schedules = $scheduleManager->getSchedulesForCalendar($start_date . ' 00:00:00', $end_date . ' 23:59:59');

// Organize schedule data for week view
$schedule_data = [];
foreach ($schedules as $schedule) {
    if ($schedule['assigned_to']) {
        $employee_id = $schedule['assigned_to'];
        $date = date('Y-m-d', strtotime($schedule['start_time']));
        $shift_type = $schedule['meta_data']['shift_type'] ?? 'morning';
        
        if (!isset($schedule_data[$employee_id])) {
            $schedule_data[$employee_id] = [];
        }
        
        $schedule_data[$employee_id][$date] = [
            'shift_type' => $shift_type,
            'start_time' => date('g:iA', strtotime($schedule['start_time'])),
            'end_time' => date('g:iA', strtotime($schedule['end_time'])),
            'status' => $schedule['status']
        ];
    }
}

// Get statistics (use the same period for all views)
$shift_coverage = $scheduleManager->getShiftCoverage($start_date, $end_date);
$time_off_requests = $scheduleManager->getTimeOffRequests($start_date, $end_date);

// Calculate stats for cards (global, not view-specific)
$total_schedules = $conn->query("SELECT COUNT(*) FROM schedules")->fetchColumn();
$upcoming_schedules = $conn->query("SELECT COUNT(*) FROM schedules WHERE start_time > NOW() AND status = 'scheduled'")->fetchColumn();
$in_progress_schedules = $conn->query("SELECT COUNT(*) FROM schedules WHERE status = 'in_progress'")->fetchColumn();
$completed_schedules = $conn->query("SELECT COUNT(*) FROM schedules WHERE status = 'completed'")->fetchColumn();

// Get session messages
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

// Include the view
include __DIR__ . '/../views/schedules.php';
?>