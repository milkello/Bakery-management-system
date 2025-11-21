<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}
    
require_once __DIR__ . '/../../config/config.php';

$message = "";
$error = "";
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Handle PDF generation for employee's own data
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
        
        // Redirect to PDF generator with parameters (employee specific)
        header("Location: ?page=exports_pdf&type=employee_$report_type&from=$date_from&to=$date_to&user_id=$user_id");
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get employee's statistics
$my_production_count = $conn->prepare("SELECT COUNT(*) FROM production WHERE created_by = ?");
$my_production_count->execute([$user_id]);
$total_productions = $my_production_count->fetchColumn();

$my_production_qty = $conn->prepare("SELECT SUM(quantity_produced) FROM production WHERE created_by = ?");
$my_production_qty->execute([$user_id]);
$total_units_produced = $my_production_qty->fetchColumn() ?? 0;

$my_sales_count = $conn->prepare("SELECT COUNT(*) FROM sales WHERE sold_by = ? OR created_by = ?");
$my_sales_count->execute([$user_id, $user_id]);
$total_sales = $my_sales_count->fetchColumn();

$my_sales_revenue = $conn->prepare("SELECT SUM(total_price) FROM sales WHERE sold_by = ? OR created_by = ?");
$my_sales_revenue->execute([$user_id, $user_id]);
// $total_revenue = $my_sales_revenue->fetchColumn() ?? 0;
$rev_stmt = $conn->prepare("SELECT SUM(total_price) FROM sales WHERE sold_by = ? OR created_by = ?");
$rev_stmt->execute([$user_id, $user_id]);
$total_revenue = (int) ($rev_stmt->fetchColumn() ?: 0);
$today_revenue = $conn->query("SELECT SUM(total_price) FROM sales WHERE DATE(created_at) = CURDATE() OR created_by = '$user_id'")->fetchColumn();

// Recent activity - use sold_by for sales since that's what's populated
$recent_activity = $conn->prepare("
    SELECT 'production' as type, product_id, quantity_produced as quantity, created_at 
    FROM production 
    WHERE created_by = ?
    UNION ALL
    SELECT 'sale' as type, product_id, qty as quantity, created_at
    FROM sales
    WHERE sold_by = ? OR created_by = ?
    ORDER BY created_at DESC
    LIMIT 10
");
$recent_activity->execute([$user_id, $user_id, $user_id]);
$activities = $recent_activity->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../views/employee_reports.php';
?>
