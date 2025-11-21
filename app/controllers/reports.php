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

// === Production Report (with grouping and ingredients) ===
$stmtProd = $conn->prepare("
    SELECT 
        pr.id AS production_id,
        pr.product_id,
        pr.quantity_produced,
        pr.created_at,
        p.name AS product_name,
        DATE(pr.created_at) AS prod_date,
        pmp.id AS plan_id,
        pmp.plan_date,
        mo.id AS order_id
    FROM production pr
    JOIN products p ON pr.product_id = p.id
    LEFT JOIN product_material_plans pmp 
        ON pmp.product_id = pr.product_id 
       AND pmp.plan_date = DATE(pr.created_at)
    LEFT JOIN material_orders mo ON mo.id = pmp.order_id
    WHERE DATE(pr.created_at) BETWEEN ? AND ?
    ORDER BY prod_date DESC, p.name ASC, pr.created_at DESC
");
$stmtProd->execute([$start_date, $end_date]);
$production_data = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

// Preload ingredients for all related material orders in this range
$production_grouped = [];
$orderIds = [];
foreach ($production_data as $row) {
    if (!empty($row['order_id'])) {
        $orderIds[(int)$row['order_id']] = true;
    }
}

$ingredientsByOrder = [];
if (!empty($orderIds)) {
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $ids = array_keys($orderIds);
    $ingStmt = $conn->prepare("
        SELECT moi.order_id, moi.qty, moi.total_value, rm.name AS material_name, rm.unit
        FROM material_order_items moi
        LEFT JOIN raw_materials rm ON rm.id = moi.material_id
        WHERE moi.order_id IN ($placeholders)
    ");
    $ingStmt->execute($ids);
    $rows = $ingStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $ing) {
        $oid = (int)$ing['order_id'];
        if (!isset($ingredientsByOrder[$oid])) {
            $ingredientsByOrder[$oid] = [];
        }
        $ingredientsByOrder[$oid][] = $ing;
    }
}

// Build grouped structure: $production_grouped[date][product_id]
foreach ($production_data as $row) {
    $date = $row['prod_date'];
    $pid = (int)$row['product_id'];
    if (!isset($production_grouped[$date])) {
        $production_grouped[$date] = [];
    }
    if (!isset($production_grouped[$date][$pid])) {
        $production_grouped[$date][$pid] = [
            'product_id' => $pid,
            'product_name' => $row['product_name'],
            'total_quantity' => 0,
            'entries' => [],
            'ingredients' => [],
        ];
    }

    $production_grouped[$date][$pid]['total_quantity'] += (int)$row['quantity_produced'];
    $production_grouped[$date][$pid]['entries'][] = $row;

    if (!empty($row['order_id'])) {
        $oid = (int)$row['order_id'];
        if (isset($ingredientsByOrder[$oid])) {
            $production_grouped[$date][$pid]['ingredients'] = $ingredientsByOrder[$oid];
        }
    }
}

// === Totals ===
$total_sales = array_sum(array_column($sales_data, 'total_price'));
$total_production = count($production_data);
