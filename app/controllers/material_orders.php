<?php
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }

// Only admins can create/delete orders (lists of materials to be removed)
// Other roles may view
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$message = '';
$error = '';

// Create new material order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf($_POST['csrf'] ?? '')) {
    if (!$isAdmin) {
        $error = 'Only admin can create material orders.';
    } else {
        try {
            $conn->beginTransaction();

            $order_date = $_POST['order_date'] ?: date('Y-m-d');
            $note = $_POST['note'] ?? null;
            $created_by = $_SESSION['user_id'];

            // items arrays
            $material_ids = $_POST['material_id'] ?? [];
            $qtys = $_POST['qty'] ?? [];
            $unit_prices = $_POST['unit_price'] ?? [];

            $total_value = 0;
            // calculate total first
            for ($i = 0; $i < count($material_ids); $i++) {
                $m = intval($material_ids[$i]);
                $q = floatval($qtys[$i]);
                $p = floatval($unit_prices[$i]);
                $total_value += ($q * $p);
            }

            $stmt = $conn->prepare("INSERT INTO material_orders (order_date, total_value, note, created_by, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$order_date, $total_value, $note, $created_by]);
            $order_id = $conn->lastInsertId();

            // insert items and deduct stock
            $itemStmt = $conn->prepare("INSERT INTO material_order_items (order_id, material_id, qty, unit_price, total_value) VALUES (?, ?, ?, ?, ?)");
            $updateMat = $conn->prepare("UPDATE raw_materials SET stock_quantity = stock_quantity - ? WHERE id = ?");

            for ($i = 0; $i < count($material_ids); $i++) {
                $m = intval($material_ids[$i]);
                $q = floatval($qtys[$i]);
                $p = floatval($unit_prices[$i]);
                $tv = round($q * $p, 2);

                if ($q <= 0) continue;

                $itemStmt->execute([$order_id, $m, $q, $p, $tv]);
                $updateMat->execute([$q, $m]);
            }

            $conn->commit();
            $message = 'Material order saved.';
            header('Location: ?page=material_orders'); exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $error = 'Error saving material order: ' . $e->getMessage();
        }
    }
}

// Delete single order (restore stock)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && $isAdmin) {
    $id = intval($_GET['id']);
    try {
        $conn->beginTransaction();
        $items = $conn->prepare('SELECT material_id, qty FROM material_order_items WHERE order_id = ?');
        $items->execute([$id]);
        $rows = $items->fetchAll(PDO::FETCH_ASSOC);
        $restore = $conn->prepare('UPDATE raw_materials SET stock_quantity = stock_quantity + ? WHERE id = ?');
        foreach ($rows as $r) {
            $restore->execute([$r['qty'], $r['material_id']]);
        }
        $conn->prepare('DELETE FROM material_order_items WHERE order_id = ?')->execute([$id]);
        $conn->prepare('DELETE FROM material_orders WHERE id = ?')->execute([$id]);
        $conn->commit();
        header('Location: ?page=material_orders'); exit;
    } catch (Exception $e) {
        $conn->rollBack();
        $error = 'Error deleting order: ' . $e->getMessage();
    }
}

// Delete by date range (POST form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_range']) && check_csrf($_POST['csrf'] ?? '') && $isAdmin) {
    $start = $_POST['start_date'] ?: null;
    $end = $_POST['end_date'] ?: null;
    if ($start && $end) {
        try {
            $conn->beginTransaction();
            $orders = $conn->prepare('SELECT id FROM material_orders WHERE order_date BETWEEN ? AND ?');
            $orders->execute([$start, $end]);
            $ordersList = $orders->fetchAll(PDO::FETCH_COLUMN);
            $restore = $conn->prepare('UPDATE raw_materials SET stock_quantity = stock_quantity + ? WHERE id = ?');
            foreach ($ordersList as $oid) {
                $items = $conn->prepare('SELECT material_id, qty FROM material_order_items WHERE order_id = ?');
                $items->execute([$oid]);
                foreach ($items->fetchAll(PDO::FETCH_ASSOC) as $r) {
                    $restore->execute([$r['qty'], $r['material_id']]);
                }
                $conn->prepare('DELETE FROM material_order_items WHERE order_id = ?')->execute([$oid]);
                $conn->prepare('DELETE FROM material_orders WHERE id = ?')->execute([$oid]);
            }
            $conn->commit();
            $message = 'Orders deleted for selected range.';
            header('Location: ?page=material_orders'); exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $error = 'Error deleting orders by range: ' . $e->getMessage();
        }
    } else {
        $error = 'Please select a valid start and end date.';
    }
}

// Fetch materials and orders for display
$materials = $conn->query('SELECT id, name, unit, stock_quantity, unit_cost FROM raw_materials ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$orders = $conn->query('SELECT * FROM material_orders ORDER BY created_at DESC LIMIT 200')->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../views/material_orders.php';

?>
