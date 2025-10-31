<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;

// Basic auth/session check
// Start output buffering to avoid stray output corrupting PDF binary
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    if (ob_get_length()) ob_clean();
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Accept start_date and end_date via GET
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

try {
    $stmt = $conn->prepare("SELECT s.*, e.first_name, e.last_name FROM schedules s LEFT JOIN employees e ON s.assigned_to = e.id WHERE DATE(s.start_time) BETWEEN ? AND ? ORDER BY s.start_time ASC");
    $stmt->execute([$start_date, $end_date]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = "<html><head><meta charset='utf-8'><style>
    body { font-family: DejaVu Sans, Arial, sans-serif; color:#222 }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #ddd; padding:8px; font-size:12px }
    th { background:#111827; color:#fff }
    h2 { text-align:center }
    .small { font-size:11px; color:#666 }
    </style></head><body>";

    $html .= "<h2>Schedule Export: " . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date) . "</h2>";
    $html .= "<p class='small'>Generated: " . date('Y-m-d H:i:s') . "</p>";

    if (empty($schedules)) {
        $html .= "<p>No schedules found for the selected period.</p>";
    } else {
        $html .= "<table><thead><tr><th>Date</th><th>Time</th><th>Title</th><th>Type</th><th>Assigned To</th><th>Status</th><th>Description</th></tr></thead><tbody>";
        $last_date = null;
        $group_index = 0; // alternate group background per date
        foreach ($schedules as $s) {
            $date = date('M j, Y', strtotime($s['start_time']));
            $time = date('g:i A', strtotime($s['start_time'])) . ' - ' . date('g:i A', strtotime($s['end_time']));
            $assigned = $s['first_name'] ? htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) : 'Unassigned';
            $title = htmlspecialchars($s['title']);
            $type = htmlspecialchars($s['schedule_type']);
            $status = htmlspecialchars($s['status']);
            $desc = htmlspecialchars($s['description'] ?? '');

            // When date changes increment group_index so background alternates per day
            if ($last_date !== $date) {
                $group_index++;
                $last_date = $date;
            }

            // choose background color per group (light gray / white)
            $bg = ($group_index % 2 === 0) ? '#ffffff' : '#f3f4f6';

            $html .= "<tr style='background: $bg;'>";
            $html .= "<td>$date</td><td>$time</td><td>$title</td><td>$type</td><td>$assigned</td><td>$status</td><td>$desc</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";
    }

    $html .= "</body></html>";

    $dompdf = new Dompdf();
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->loadHtml($html);
    $dompdf->render();

    $filename = "schedules_{$start_date}_to_{$end_date}.pdf";
    // Clear any buffered output (warnings/notices) before sending PDF
    if (ob_get_length()) ob_clean();
    // If download=1 is set, force download (Attachment=1). Otherwise default to inline display (open in browser) Attachment=0.
    $attachment = (isset($_GET['download']) && $_GET['download'] === '1') ? 1 : 0;
    $dompdf->stream($filename, ['Attachment' => $attachment]);
    exit;

} catch (Exception $e) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json', true, 500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

?>
