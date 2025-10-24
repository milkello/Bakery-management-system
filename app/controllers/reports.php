<?php
require_once __DIR__ . '/../../config/config.php';

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// === Sales Report ===
$stmtSales = $conn->prepare("
    SELECT s.*, p.name AS product_name 
    FROM sales s 
    JOIN products p ON s.product_id = p.id
    WHERE DATE(s.created_at) BETWEEN ? AND ?
    ORDER BY s.created_at DESC
");
$stmtSales->execute([$start_date, $end_date]);
$sales_data = $stmtSales->fetchAll();

// === Production Report ===
$stmtProd = $conn->prepare("
    SELECT pr.*, p.name AS product_name
    FROM production pr
    JOIN products p ON pr.product_id = p.id
    WHERE DATE(pr.created_at) BETWEEN ? AND ?
    ORDER BY pr.created_at DESC
");
$stmtProd->execute([$start_date, $end_date]);
$production_data = $stmtProd->fetchAll();

// === Totals ===
$total_sales = array_sum(array_column($sales_data, 'total_price'));
$total_production = count($production_data);
