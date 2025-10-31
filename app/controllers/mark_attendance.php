<?php
// Use output buffering so any stray warnings/notices don't break JSON output
ob_start();
session_start();
require_once __DIR__ . '/../../config/config.php';

// Always return JSON
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Clear any buffered output and return clean JSON
        if (ob_get_length()) ob_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }

    $employee_id = intval($_POST['employee_id'] ?? 0);
    $status = strtolower($_POST['status'] ?? '');

    if (!$employee_id || !in_array($status, ['present', 'absent'])) {
        if (ob_get_length()) ob_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    // Check if record for today exists
    $stmt = $conn->prepare("SELECT id FROM attendance WHERE employee_id = ? AND date = CURDATE()");
    $stmt->execute([$employee_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    $now = date('Y-m-d H:i:s');
    $meta = json_encode(['updated_at' => $now]);

    if ($existing) {
        // Update existing attendance
        $update = $conn->prepare("UPDATE attendance SET status = ?, meta = ? WHERE id = ?");
        $update->execute([$status, $meta, $existing['id']]);
        $message = "Attendance updated to '$status'.";
    } else {
        // Insert new attendance
        $meta = json_encode(['created_at' => $now]);
        $insert = $conn->prepare("INSERT INTO attendance (employee_id, date, status, meta) VALUES (?, CURDATE(), ?, ?)");
        $insert->execute([$employee_id, $status, $meta]);
        $message = "Marked as '$status'.";
    }

    // Clear any buffered output (warnings/notices) before sending JSON
    if (ob_get_length()) ob_clean();
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => $message]);
} catch (Exception $e) {
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    // Log the error server-side for diagnostics
    error_log('mark_attendance error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>