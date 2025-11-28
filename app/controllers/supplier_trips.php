<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

// Load suppliers and products
$suppliers = $conn->query("SELECT id, name FROM suppliers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$products  = $conn->query("SELECT id, name, sku, COALESCE(stock,0) AS stock FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle trip creation and returns update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? '';

    if ($mode === 'create_trip') {
        $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
        $trip_date   = !empty($_POST['trip_date']) ? $_POST['trip_date'] : date('Y-m-d');
        $note        = $_POST['note'] ?? '';
        $rows_product_id = $_POST['product_id'] ?? [];
        $rows_qty_dispatched = $_POST['qty_dispatched'] ?? [];

        if ($supplier_id && !empty($rows_product_id)) {
            $stmtTrip = $conn->prepare("INSERT INTO supplier_trips (supplier_id, trip_date, status, note, created_by) VALUES (?,?,?,?,?)");
            $stmtTrip->execute([$supplier_id, $trip_date, 'open', $note, $_SESSION['user_id'] ?? null]);
            $trip_id = (int)$conn->lastInsertId();

            $stmtItem = $conn->prepare("INSERT INTO supplier_trip_items (trip_id, product_id, qty_dispatched, qty_sold, qty_returned) VALUES (?,?,?,?,0)");
            $stmtStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

            foreach ($rows_product_id as $idx => $pid) {
                $pid = (int)$pid;
                $qty = isset($rows_qty_dispatched[$idx]) ? (int)$rows_qty_dispatched[$idx] : 0;
                if ($pid && $qty > 0) {
                    $stmtItem->execute([$trip_id, $pid, $qty, 0]);
                    $stmtStock->execute([$qty, $pid, $qty]);
                }
            }
        }

        header('Location: ?page=supplier_trips');
        exit;
    }

    if ($mode === 'update_returns') {
        $trip_id = isset($_POST['trip_id']) ? (int)$_POST['trip_id'] : 0;
        if ($trip_id) {
            $stmtItems = $conn->prepare("SELECT id, product_id, qty_returned FROM supplier_trip_items WHERE trip_id = ?");
            $stmtItems->execute([$trip_id]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $map = [];
            foreach ($items as $it) {
                $map[$it['id']] = $it;
            }

            $rows_item_id = $_POST['item_id'] ?? [];
            $rows_qty_returned = $_POST['qty_returned'] ?? [];

            $stmtUpdateItem = $conn->prepare("UPDATE supplier_trip_items SET qty_returned = ? WHERE id = ?");
            $stmtUpdateStock = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");

            foreach ($rows_item_id as $idx => $iid) {
                $iid = (int)$iid;
                if (!isset($map[$iid])) continue;
                $new_return = isset($rows_qty_returned[$idx]) ? (int)$rows_qty_returned[$idx] : 0;
                if ($new_return < 0) $new_return = 0;

                $old_return = (int)$map[$iid]['qty_returned'];
                $delta = $new_return - $old_return;
                if ($delta !== 0) {
                    $stmtUpdateItem->execute([$new_return, $iid]);
                    $stmtUpdateStock->execute([$delta, (int)$map[$iid]['product_id']]);
                }
            }
        }

        header('Location: ?page=supplier_trips');
        exit;
    }
}

// Fetch recent trips with supplier name
$trips = $conn->query("SELECT t.*, s.name AS supplier_name FROM supplier_trips t JOIN suppliers s ON s.id = t.supplier_id ORDER BY t.id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);

// For editing a specific trip (returns)
$edit_trip = null;
$edit_items = [];
if (isset($_GET['trip_id'])) {
    $trip_id = (int)$_GET['trip_id'];
    $stmtT = $conn->prepare("SELECT t.*, s.name AS supplier_name FROM supplier_trips t JOIN suppliers s ON s.id = t.supplier_id WHERE t.id = ? LIMIT 1");
    $stmtT->execute([$trip_id]);
    $edit_trip = $stmtT->fetch(PDO::FETCH_ASSOC);

    if ($edit_trip) {
        $stmtI = $conn->prepare("SELECT i.*, p.name AS product_name, p.sku FROM supplier_trip_items i JOIN products p ON p.id = i.product_id WHERE i.trip_id = ?");
        $stmtI->execute([$trip_id]);
        $edit_items = $stmtI->fetchAll(PDO::FETCH_ASSOC);
    }
}

include __DIR__ . '/../views/supplier_trips.php';
