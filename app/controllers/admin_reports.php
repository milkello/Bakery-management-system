<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=login');
    exit;
}

require_once __DIR__ . '/../../config/config.php';

$message = "";
$error = "";

// Handle PDF generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_pdf'])) {
    try {
        $report_type = $_POST['report_type'] ?? '';
        $date_from = $_POST['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
        $date_to = $_POST['date_to'] ?? date('Y-m-d');
        
        // Validate dates
        if (empty($date_from) || empty($date_to)) {
            throw new Exception("Please select both start and end dates!");
        }
        
        if (strtotime($date_from) > strtotime($date_to)) {
            throw new Exception("Start date cannot be after end date!");
        }
        
        // Redirect to PDF generator with parameters
        header("Location: ?page=exports_pdf&type=$report_type&from=$date_from&to=$date_to");
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get summary statistics for the form
$total_employees = $conn->query("SELECT COUNT(*) FROM employees WHERE position not in ('admin')")->fetchColumn();
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_materials = $conn->query("SELECT COUNT(*) FROM raw_materials")->fetchColumn();
$total_sales = $conn->query("SELECT SUM(total_price) FROM sales")->fetchColumn() ?? 0;
$total_customers = $conn->query("SELECT COUNT(*) FROM customers")->fetchColumn() ?? 0;

// Recent reports generated
$recent_reports = [];

include __DIR__ . '/../views/admin_reports.php';
?>
