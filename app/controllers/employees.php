<?php
require_once __DIR__ . '/../../config/config.php';

// Fetch all employees
$stmt = $conn->query("SELECT * FROM employees ORDER BY id DESC");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compute statistics
$total_employees = count($employees);
$current_working = $conn->query("SELECT COUNT(*) FROM employees WHERE status = 'Active'")->fetchColumn();
$avg_rating = $conn->query("SELECT ROUND(AVG(rating), 1) FROM employees")->fetchColumn();



// Include the view
include __DIR__ . '/../views/employees.php';
?>
