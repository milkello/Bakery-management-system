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
            
            $stmt = $this->conn->prepare("
                SELECT t.*, e.first_name, e.last_name 
                FROM time_off_requests t
                JOIN employees e ON t.employee_id = e.id
                WHERE t.start_date <= ? AND t.end_date >= ?
                AND t.status IN ('pending', 'approved')
                ORDER BY t.start_date ASC
                LIMIT 5
            ");
            
            $stmt->execute([$week_end, $week_start]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
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