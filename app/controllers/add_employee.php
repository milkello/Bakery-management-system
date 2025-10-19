<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $position = $_POST['position'] ?? '';
        $status = $_POST['status'] ?? 'Active';
        $salary_type = $_POST['salary_type'] ?? 'monthly';
        $salary_amount = $_POST['salary_amount'] ?? '0';
        
        // Validate required fields
        if (empty($first_name) || empty($last_name) || empty($email) || empty($position)) {
            throw new Exception('All required fields must be filled');
        }
        
        $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, email, phone, position, status, salary_type, salary_amount, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $success = $stmt->execute([
            $first_name,
            $last_name,
            $email,
            $phone,
            $position,
            $status,
            $salary_type,
            $salary_amount
        ]);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Employee added successfully']);
        } else {
            throw new Exception('Failed to add employee');
        }
        
    } catch (Exception $e) {
        error_log("Add employee error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>