<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $employeeId = intval($_GET['id']);
    
    try {
        $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$employeeId]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($employee) {
            echo json_encode($employee);
        } else {
            echo json_encode(['error' => 'Employee not found']);
        }
    } catch (Exception $e) {
        error_log("Get employee error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Employee ID required']);
}
?>