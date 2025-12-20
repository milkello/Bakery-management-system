<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

// AJAX detection is now handled in the main router (public/index.php)
$isAjax = (
    (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
);

if (!class_exists('Dompdf\\Dompdf')) {
    @require_once __DIR__ . '/../../vendor/autoload.php';
}

// Base data for suppliers page
$suppliers = $conn->query("SELECT id, name, phone, email, address, created_at FROM suppliers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Products and customers are needed for recording sales per supplier
$products = $conn->query("SELECT * FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$customers = $conn->query("SELECT id, name, customer_type, phone FROM customers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Load today's supplier trip items with sold/remaining quantities for allocation
$supplierTripItems = [];
$supplierTripsBySupplier = [];
try {
    $stmtTrips = $conn->query("
        SELECT 
            t.id AS trip_id,
            t.supplier_id,
            t.trip_date,
            t.status,
            i.id AS item_id,
            i.product_id,
            i.qty_dispatched,
            i.qty_returned,
            p.name AS product_name,
            p.sku,
            COALESCE(SUM(s.qty), 0) AS allocated_qty
        FROM supplier_trips t
        JOIN supplier_trip_items i ON i.trip_id = t.id
        JOIN products p ON p.id = i.product_id
        LEFT JOIN sales s ON s.supplier_trip_item_id = i.id
        WHERE DATE(t.trip_date) = CURDATE()
        GROUP BY t.id, t.supplier_id, t.trip_date, t.status,
                 i.id, i.product_id, i.qty_dispatched, i.qty_returned,
                 p.name, p.sku
        ORDER BY t.trip_date DESC, t.id DESC
    ");

    while ($row = $stmtTrips->fetch(PDO::FETCH_ASSOC)) {
        $sold = (int)$row['qty_dispatched'] - (int)$row['qty_returned'];
        $allocated = (int)$row['allocated_qty'];
        $remaining = $sold - $allocated;
        if ($remaining < 0) { $remaining = 0; }

        $row['sold_qty'] = $sold;
        $row['remaining_qty'] = $remaining;
        $supplierTripItems[$row['supplier_id']][] = $row;

        $sid = (int)$row['supplier_id'];
        $tid = (int)$row['trip_id'];
        if ($sid && $tid && empty($supplierTripsBySupplier[$sid][$tid])) {
            $supplierTripsBySupplier[$sid][$tid] = [
                'id' => $tid,
                'trip_date' => $row['trip_date'],
                'status' => $row['status'],
            ];
        }
    }
} catch (Exception $e) {
    // If supplier_trips tables don't exist, ignore and keep supplierTripItems empty
}

// Handle creating a new supplier record from suppliers page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'create_supplier') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, phone, email, address, created_at) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$name, $phone, $email, $address]);
    }

    header('Location: ?page=suppliers');
    exit;
}

// Handle updating an existing supplier record from suppliers page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'update_supplier') {
    $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($supplier_id > 0 && $name !== '') {
        $stmt = $conn->prepare("UPDATE suppliers SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $address, $supplier_id]);
    }

    header('Location: ?page=suppliers');
    exit;
}

// Handle deleting a supplier from suppliers page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'delete_supplier') {
    $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;

    if ($supplier_id > 0) {
        try {
            $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
            $stmt->execute([$supplier_id]);
            // If there are FK constraints, this will throw and simply fall through
        } catch (PDOException $e) {
            // You can later surface a friendly message if needed
        }
    }

    header('Location: ?page=suppliers');
    exit;
}

// Handle updating an existing supplier sale from the History tab
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'update_supplier_sale') {
    $sale_id = isset($_POST['sale_id']) ? (int)$_POST['sale_id'] : 0;
    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unit_price = isset($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.0;
    $payment_method = $_POST['payment_method'] ?? null;

    $ok = false;
    $errorMessage = 'Failed to update sale.';

    if ($sale_id > 0 && $qty > 0 && $unit_price >= 0) {
        // Load sale including its supplier and optional trip item link
        $stmtCheck = $conn->prepare("SELECT supplied_by, supplier_trip_item_id FROM sales WHERE id = ?");
        $stmtCheck->execute([$sale_id]);
        $saleRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($saleRow && !empty($saleRow['supplied_by'])) {
            $tripItemId = !empty($saleRow['supplier_trip_item_id']) ? (int)$saleRow['supplier_trip_item_id'] : 0;

            if ($tripItemId > 0) {
                // This sale is tied to a specific trip item; enforce remaining quantity on that trip
                $stmtTrip = $conn->prepare("
                    SELECT 
                        i.qty_dispatched,
                        i.qty_returned,
                        s_main.qty AS current_qty,
                        COALESCE(SUM(CASE WHEN s.id <> s_main.id THEN s.qty ELSE 0 END), 0) AS other_allocated
                    FROM supplier_trip_items i
                    JOIN sales s_main ON s_main.supplier_trip_item_id = i.id
                    LEFT JOIN sales s ON s.supplier_trip_item_id = i.id
                    WHERE s_main.id = ?
                    GROUP BY i.qty_dispatched, i.qty_returned, s_main.qty
                ");
                $stmtTrip->execute([$sale_id]);
                $tripRow = $stmtTrip->fetch(PDO::FETCH_ASSOC);

                if ($tripRow) {
                    $qty_dispatched = (int)$tripRow['qty_dispatched'];
                    $qty_returned = (int)$tripRow['qty_returned'];
                    $other_allocated = (int)$tripRow['other_allocated'];

                    // Sold units available across all sales for this item
                    $sold = $qty_dispatched - $qty_returned;
                    if ($sold < 0) { $sold = 0; }

                    // Max we can assign to this edited sale so that total allocations do not exceed sold
                    $max_for_this_sale = $sold - $other_allocated;
                    if ($max_for_this_sale < 0) { $max_for_this_sale = 0; }

                    if ($qty > $max_for_this_sale) {
                        $errorMessage = 'Quantity exceeds remaining items available for this trip. Maximum allowed for this sale is ' . $max_for_this_sale . '.';
                    } else {
                        $total_price = $qty * $unit_price;
                        $stmtUpd = $conn->prepare("UPDATE sales SET qty = ?, unit_price = ?, total_price = ?, payment_method = ? WHERE id = ?");
                        $stmtUpd->execute([$qty, $unit_price, $total_price, $payment_method, $sale_id]);
                        $ok = true;
                        $errorMessage = 'Sale updated successfully.';
                    }
                }
            } else {
                // Supplier-based sale not tied to a specific trip item; keep previous behaviour
                $total_price = $qty * $unit_price;
                $stmtUpd = $conn->prepare("UPDATE sales SET qty = ?, unit_price = ?, total_price = ?, payment_method = ? WHERE id = ?");
                $stmtUpd->execute([$qty, $unit_price, $total_price, $payment_method, $sale_id]);
                $ok = true;
                $errorMessage = 'Sale updated successfully.';
            }
        }
    }

    if ($isAjax) {
        // Get updated data for all tabs
        $updatedData = getUpdatedSupplierData($conn, $saleRow['supplied_by'] ?? 0);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $ok,
            'message' => $errorMessage,
            'sale' => $ok ? [
                'id' => $sale_id,
                'qty' => $qty,
                'unit_price' => $unit_price,
                'total_price' => isset($total_price) ? $total_price : null,
                'payment_method' => $payment_method,
            ] : null,
            'updatedData' => $updatedData,
        ]);
        exit;
    }

    header('Location: ?page=suppliers');
    exit;
}

// Handle creating a new supplier trip from suppliers page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'create_supplier_trip') {
    $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $trip_date   = !empty($_POST['trip_date']) ? $_POST['trip_date'] : date('Y-m-d');
    $note        = $_POST['note'] ?? '';
    $rows_product_id = $_POST['product_id'] ?? [];
    $rows_qty_dispatched = $_POST['qty_dispatched'] ?? [];

    $tripCreated = false;
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
        $tripCreated = $trip_id > 0;
    }

    if ($isAjax) {
        // Get updated data for all tabs
        $updatedData = getUpdatedSupplierData($conn, $supplier_id);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $tripCreated,
            'message' => $tripCreated ? 'Trip created successfully.' : 'Failed to create trip.',
            'trip_id' => $tripCreated ? $trip_id : null,
            'updatedData' => $updatedData,
        ]);
        exit;
    }

    header('Location: ?page=suppliers');
    exit;
}

// Handle updating returns from suppliers page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'update_supplier_trip_returns') {
    $trip_id = isset($_POST['trip_id']) ? (int)$_POST['trip_id'] : 0;
    if ($trip_id) {
        // For each item in the trip, get dispatched, current returns, and already allocated (sold) qty
        $stmtItems = $conn->prepare("SELECT i.id, i.product_id, i.qty_dispatched, i.qty_returned, COALESCE(SUM(s.qty),0) AS allocated_qty FROM supplier_trip_items i LEFT JOIN sales s ON s.supplier_trip_item_id = i.id WHERE i.trip_id = ? GROUP BY i.id, i.product_id, i.qty_dispatched, i.qty_returned");
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

        $hadLimitViolation = false;

        foreach ($rows_item_id as $idx => $iid) {
            $iid = (int)$iid;
            if (!isset($map[$iid])) continue;

            $row = $map[$iid];
            $requested_return = isset($rows_qty_returned[$idx]) ? (int)$rows_qty_returned[$idx] : 0;
            if ($requested_return < 0) $requested_return = 0;

            $qty_dispatched = (int)$row['qty_dispatched'];
            $allocated_qty = (int)$row['allocated_qty'];
            // Max returns = dispatched minus already sold
            $max_return_allowed = max($qty_dispatched - $allocated_qty, 0);

            if ($requested_return > $max_return_allowed) {
                $requested_return = $max_return_allowed;
                $hadLimitViolation = true;
            }

            $old_return = (int)$row['qty_returned'];
            $delta = $requested_return - $old_return;
            if ($delta !== 0) {
                $stmtUpdateItem->execute([$requested_return, $iid]);
                $stmtUpdateStock->execute([$delta, (int)$row['product_id']]);
            }
        }

        if ($isAjax) {
            // Get supplier_id from trip
            $stmtSupplier = $conn->prepare("SELECT supplier_id FROM supplier_trips WHERE id = ?");
            $stmtSupplier->execute([$trip_id]);
            $supplierRow = $stmtSupplier->fetch(PDO::FETCH_ASSOC);
            $supplier_id = $supplierRow ? (int)$supplierRow['supplier_id'] : 0;

            // Get updated data for all tabs
            $updatedData = getUpdatedSupplierData($conn, $supplier_id);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => !$hadLimitViolation,
                'message' => $hadLimitViolation
                    ? 'Some returns were limited because that quantity has already been sold.'
                    : 'Returns updated successfully.',
                'updatedData' => $updatedData,
            ]);
            exit;
        }
    }

    header('Location: ?page=suppliers');
    exit;
}

// Handle creating a new sale from suppliers page (always supplier-based)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'create_supplier_sale') {
    $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity_sold = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unit_price = isset($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.0;
    $customer_id = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
    $customer_type = $_POST['customer_type'] ?? 'Regular';
    $payment_method = $_POST['payment_method'] ?? 'Cash';

    $saleCreated = false;
    if ($supplier_id && $product_id && $quantity_sold > 0 && $unit_price >= 0) {
        // Supplier-based sale: stock is managed via supplier trips/returns, so we do not touch stock here.
        $total_price = $quantity_sold * $unit_price;

        $stmtSale = $conn->prepare("
            INSERT INTO sales (product_id, qty, unit_price, total_price, customer_type, payment_method, customer_id, supplied_by, sold_by, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");
        $stmtSale->execute([
            $product_id,
            $quantity_sold,
            $unit_price,
            $total_price,
            $customer_type,
            $payment_method,
            $customer_id,
            $supplier_id,
            $_SESSION['user_id'] ?? 1,
            $_SESSION['user_id'] ?? 1
        ]);

        // Simple sale notification
        $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
        $stmtProduct = $conn->prepare("SELECT name, sku FROM products WHERE id=?");
        $stmtProduct->execute([$product_id]);
        $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
        $name = $product['name'] ?? '';
        $sku = $product['sku'] ?? '';
        $saleCreated = true;
    }

    if ($isAjax) {
        // Get updated data for all tabs
        $updatedData = getUpdatedSupplierData($conn, $supplier_id);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $saleCreated,
            'message' => $saleCreated ? 'Sale recorded successfully.' : 'Failed to record sale.',
            'updatedData' => $updatedData,
        ]);
        exit;
    }

    header('Location: ?page=suppliers');
    exit;
}

// Handle allocating a sale from a specific supplier trip item to a customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'allocate_trip_sale') {
    $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $item_id = isset($_POST['supplier_trip_item_id']) ? (int)$_POST['supplier_trip_item_id'] : 0;
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity_sold = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unit_price = isset($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.0;
    $customer_id = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
    $customer_type = $_POST['customer_type'] ?? 'Regular';
    $payment_method = $_POST['payment_method'] ?? 'Cash';

    $allocated = false;
    if ($supplier_id && $item_id && $product_id && $quantity_sold > 0 && $unit_price >= 0) {
        // Recompute remaining for this item on the server to avoid over-allocation
        $stmtCheck = $conn->prepare("
            SELECT 
                i.qty_dispatched,
                i.qty_returned,
                COALESCE(SUM(s.qty), 0) AS allocated_qty
            FROM supplier_trip_items i
            JOIN supplier_trips t ON t.id = i.trip_id
            LEFT JOIN sales s ON s.supplier_trip_item_id = i.id
            WHERE i.id = ? AND t.supplier_id = ?
            GROUP BY i.qty_dispatched, i.qty_returned
        ");
        $stmtCheck->execute([$item_id, $supplier_id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $sold = (int)$row['qty_dispatched'] - (int)$row['qty_returned'];
            $allocated = (int)$row['allocated_qty'];
            $remaining = $sold - $allocated;
            if ($remaining < 0) { $remaining = 0; }

            if ($quantity_sold <= $remaining) {
                $total_price = $quantity_sold * $unit_price;

                $stmtSale = $conn->prepare("
                    INSERT INTO sales (product_id, qty, unit_price, total_price, customer_type, payment_method, customer_id, supplied_by, supplier_trip_item_id, sold_by, created_by)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)
                ");
                $stmtSale->execute([
                    $product_id,
                    $quantity_sold,
                    $unit_price,
                    $total_price,
                    $customer_type,
                    $payment_method,
                    $customer_id,
                    $supplier_id,
                    $item_id,
                    $_SESSION['user_id'] ?? 1,
                    $_SESSION['user_id'] ?? 1
                ]);

                // Notification
                $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
                $stmtProduct = $conn->prepare("SELECT name, sku FROM products WHERE id=?");
                $stmtProduct->execute([$product_id]);
                $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
                $name = $product['name'] ?? '';
                $sku = $product['sku'] ?? '';
                $stmtNotif->execute(['sale', "Trip allocation: supplier #$supplier_id sold $quantity_sold units of $name ($sku) via trip item #$item_id."]);  

                $allocated = true;
            }
        }
    }

    if ($isAjax) {
        // Get updated data for all tabs
        $updatedData = getUpdatedSupplierData($conn, $supplier_id);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $allocated,
            'message' => $allocated ? 'Trip allocation recorded successfully.' : 'Failed to record allocation. Check remaining quantity.',
            'updatedData' => $updatedData,
        ]);
        exit;
    }

    header('Location: ?page=suppliers');
    exit;
}

$supplierTotalsStmt = $conn->query("SELECT supplied_by AS supplier_id, SUM(total_price) AS total_value FROM sales WHERE supplied_by IS NOT NULL GROUP BY supplied_by");
$supplierTotalsRaw = $supplierTotalsStmt->fetchAll(PDO::FETCH_ASSOC);
$supplierTotals = [];
foreach ($supplierTotalsRaw as $row) {
    $supplierTotals[$row['supplier_id']] = $row['total_value'];
}

$topWeekly = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE YEARWEEK(s.created_at) = YEARWEEK(CURDATE()) GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$topMonthly = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE DATE_FORMAT(s.created_at, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$topYearly = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE DATE_FORMAT(s.created_at, '%Y') = DATE_FORMAT(CURDATE(), '%Y') GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Preload recent sales history per supplier (used in the modal History tab)
$supplierHistories = [];
try {
    $histStmt = $conn->query("
        SELECT 
            s.*, 
            p.name AS product_name,
            p.sku,
            c.name AS customer_name
        FROM sales s
        JOIN products p ON p.id = s.product_id
        LEFT JOIN customers c ON c.id = s.customer_id
        WHERE s.supplied_by IS NOT NULL
        ORDER BY s.created_at DESC
        LIMIT 500
    ");

    while ($row = $histStmt->fetch(PDO::FETCH_ASSOC)) {
        $sid = (int)($row['supplied_by'] ?? 0);
        if ($sid > 0) {
            $supplierHistories[$sid][] = $row;
        }
    }
} catch (Exception $e) {
    // If sales table or columns are missing, keep histories empty
}

if (isset($_GET['action']) && $_GET['action'] === 'export_pdf') {
    $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
    $start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
    $end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

    if (!$supplier_id || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        header('HTTP/1.1 400 Bad Request');
        echo 'Invalid input.';
        exit;
    }

    $stmtSupp = $conn->prepare("SELECT * FROM suppliers WHERE id = ? LIMIT 1");
    $stmtSupp->execute([$supplier_id]);
    $supplier = $stmtSupp->fetch(PDO::FETCH_ASSOC);
    if (!$supplier) {
        header('HTTP/1.1 404 Not Found');
        echo 'Supplier not found.';
        exit;
    }

    $stmt = $conn->prepare("SELECT s.*, p.name AS product_name, p.sku, c.name AS customer_name FROM sales s JOIN products p ON s.product_id = p.id LEFT JOIN customers c ON c.id = s.customer_id WHERE s.supplied_by = ? AND DATE(s.created_at) BETWEEN ? AND ? ORDER BY s.created_at ASC");
    $stmt->execute([$supplier_id, $start_date, $end_date]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!class_exists('Dompdf\\Dompdf')) {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'PDF generator not available.';
        exit;
    }

    $dompdf = new \Dompdf\Dompdf();

    $html = '<!doctype html><html><head><meta charset="utf-8"><title>Supplier History</title>';
    $html .= '<style>body{font-family:Arial,Helvetica,sans-serif;font-size:12px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:6px;text-align:left;}th{background:#333;color:#fff;}</style>';
    $html .= '</head><body>';
    $html .= '<h2>Supplier History</h2>';
    $html .= '<p><strong>Supplier:</strong> '.htmlspecialchars($supplier['name']).'</p>';
    $html .= '<p>Period: '.htmlspecialchars($start_date).' to '.htmlspecialchars($end_date).'</p>';
    $html .= '<table><thead><tr><th>#</th><th>Date</th><th>Product</th><th>Customer</th><th>Qty</th><th>Unit</th><th>Total</th><th>Payment</th></tr></thead><tbody>';

    $i = 1;
    $total_value = 0.0;
    foreach ($rows as $r) {
        $total_value += (float)($r['total_price'] ?? 0);
        $html .= '<tr>';
        $html .= '<td>'.$i++.'</td>';
        $html .= '<td>'.htmlspecialchars($r['created_at'] ?? '').'</td>';
        $html .= '<td>'.htmlspecialchars($r['product_name'] ?? '').'</td>';
        $html .= '<td>'.htmlspecialchars($r['customer_name'] ?? '').'</td>';
        $html .= '<td>'.htmlspecialchars($r['qty'] ?? 0).'</td>';
        $html .= '<td>'.number_format($r['unit_price'] ?? 0, 0).'</td>';
        $html .= '<td>'.number_format($r['total_price'] ?? 0, 0).'</td>';
        $html .= '<td>'.htmlspecialchars($r['payment_method'] ?? '').'</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $html .= '<p><strong>Total value:</strong> '.number_format($total_value, 0).' Rwf</p>';
    $html .= '</body></html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    if (ob_get_length()) { ob_end_clean(); }
    $filename = 'supplier_history_'.$supplier_id.'_'.$start_date.'_to_'.$end_date.'.pdf';
    $dompdf->stream($filename, ['Attachment' => 1]);
    exit;
}

function getUpdatedSupplierData($conn, $supplierId) {
    $data = [];

    // Get updated supplier totals
    $supplierTotalsStmt = $conn->query("SELECT supplied_by AS supplier_id, SUM(total_price) AS total_value FROM sales WHERE supplied_by IS NOT NULL GROUP BY supplied_by");
    $supplierTotalsRaw = $supplierTotalsStmt->fetchAll(PDO::FETCH_ASSOC);
    $supplierTotals = [];
    foreach ($supplierTotalsRaw as $row) {
        $supplierTotals[$row['supplier_id']] = $row['total_value'];
    }
    $data['supplierTotals'] = $supplierTotals;

    // Get updated top suppliers
    $data['topWeekly'] = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE YEARWEEK(s.created_at, 1) = YEARWEEK(CURDATE(), 1) GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $data['topMonthly'] = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE DATE_FORMAT(s.created_at, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $data['topYearly'] = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE DATE_FORMAT(s.created_at, '%Y') = DATE_FORMAT(CURDATE(), '%Y') GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    // Get updated supplier trip items for this supplier
    $supplierTripItems = [];
    $supplierTripsBySupplier = [];
    try {
        $stmtTrips = $conn->prepare("
            SELECT
                t.id AS trip_id,
                t.supplier_id,
                t.trip_date,
                t.status,
                i.id AS item_id,
                i.product_id,
                i.qty_dispatched,
                i.qty_returned,
                p.name AS product_name,
                p.sku,
                p.price AS product_price,
                COALESCE(SUM(s.qty), 0) AS allocated_qty
            FROM supplier_trips t
            JOIN supplier_trip_items i ON i.trip_id = t.id
            JOIN products p ON p.id = i.product_id
            LEFT JOIN sales s ON s.supplier_trip_item_id = i.id
            WHERE t.supplier_id = ? AND DATE(t.trip_date) = CURDATE()
            GROUP BY t.id, t.supplier_id, t.trip_date, t.status,
                     i.id, i.product_id, i.qty_dispatched, i.qty_returned,
                     p.name, p.sku, p.price
            ORDER BY t.trip_date DESC, t.id DESC
        ");
        $stmtTrips->execute([$supplierId]);

        while ($row = $stmtTrips->fetch(PDO::FETCH_ASSOC)) {
            $sold = (int)$row['qty_dispatched'] - (int)$row['qty_returned'];
            $allocated = (int)$row['allocated_qty'];
            $remaining = $sold - $allocated;
            if ($remaining < 0) { $remaining = 0; }

            $row['sold_qty'] = $sold;
            $row['remaining_qty'] = $remaining;
            $supplierTripItems[] = $row;

            $tid = (int)$row['trip_id'];
            if ($tid && empty($supplierTripsBySupplier[$tid])) {
                $supplierTripsBySupplier[$tid] = [
                    'id' => $tid,
                    'trip_date' => $row['trip_date'],
                    'status' => $row['status'],
                ];
            }
        }
    } catch (Exception $e) {
        // If supplier_trips tables don't exist, keep empty
    }
    $data['supplierTripItems'] = $supplierTripItems;
    $data['supplierTripsBySupplier'] = $supplierTripsBySupplier;

    // Get updated supplier histories for this supplier
    $supplierHistories = [];
    try {
        $histStmt = $conn->prepare("
            SELECT
                s.*,
                p.name AS product_name,
                p.sku,
                c.name AS customer_name
            FROM sales s
            JOIN products p ON p.id = s.product_id
            LEFT JOIN customers c ON c.id = s.customer_id
            WHERE s.supplied_by = ?
            ORDER BY s.created_at DESC
            LIMIT 500
        ");
        $histStmt->execute([$supplierId]);

        while ($row = $histStmt->fetch(PDO::FETCH_ASSOC)) {
            $supplierHistories[] = $row;
        }
    } catch (Exception $e) {
        // If sales table or columns are missing, keep empty
    }
    $data['supplierHistories'] = $supplierHistories;

    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'get_supplier_data') {
    $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    if ($supplier_id > 0) {
        $updatedData = getUpdatedSupplierData($conn, $supplier_id);
        header('Content-Type: application/json');
        echo json_encode($updatedData);
        exit;
    }
}

// Handle AJAX GET requests for supplier history
if (isset($_GET['action']) && $_GET['action'] === 'get_supplier_history') {
    $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
    if ($supplier_id > 0) {
        $history = [];
        try {
            $histStmt = $conn->prepare("
                SELECT
                    s.*,
                    p.name AS product_name,
                    p.sku,
                    c.name AS customer_name
                FROM sales s
                JOIN products p ON p.id = s.product_id
                LEFT JOIN customers c ON c.id = s.customer_id
                WHERE s.supplied_by = ?
                ORDER BY s.created_at DESC
                LIMIT 500
            ");
            $histStmt->execute([$supplier_id]);

            while ($row = $histStmt->fetch(PDO::FETCH_ASSOC)) {
                $history[] = $row;
            }
        } catch (Exception $e) {
            // If sales table or columns are missing, keep empty
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'history' => $history
        ]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid supplier ID'
        ]);
        exit;
    }
}

// Handle AJAX GET requests for supplier trips
if (isset($_GET['action']) && $_GET['action'] === 'get_supplier_trips') {
    $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
    if ($supplier_id > 0) {
        $trips = [];
        try {
            $stmtTrips = $conn->prepare("
                SELECT
                    t.id AS trip_id,
                    t.supplier_id,
                    t.trip_date,
                    t.status,
                    i.id AS item_id,
                    i.product_id,
                    i.qty_dispatched,
                    i.qty_returned,
                    p.name AS product_name,
                    p.sku,
                    COALESCE(SUM(s.qty), 0) AS allocated_qty
                FROM supplier_trips t
                JOIN supplier_trip_items i ON i.trip_id = t.id
                JOIN products p ON p.id = i.product_id
                LEFT JOIN sales s ON s.supplier_trip_item_id = i.id
                WHERE t.supplier_id = ? AND DATE(t.trip_date) = CURDATE()
                GROUP BY t.id, t.supplier_id, t.trip_date, t.status,
                         i.id, i.product_id, i.qty_dispatched, i.qty_returned,
                         p.name, p.sku
                ORDER BY t.trip_date DESC, t.id DESC
            ");
            $stmtTrips->execute([$supplier_id]);

            while ($row = $stmtTrips->fetch(PDO::FETCH_ASSOC)) {
                $sold = (int)$row['qty_dispatched'] - (int)$row['qty_returned'];
                $allocated = (int)$row['allocated_qty'];
                $remaining = $sold - $allocated;
                if ($remaining < 0) { $remaining = 0; }

                $row['sold_qty'] = $sold;
                $row['remaining_qty'] = $remaining;

                $tid = (int)$row['trip_id'];
                if (!isset($trips[$tid])) {
                    $trips[$tid] = [
                        'id' => $tid,
                        'trip_date' => $row['trip_date'],
                        'status' => $row['status'],
                        'items' => []
                    ];
                }
                $trips[$tid]['items'][] = $row;
            }

            // Convert to array
            $trips = array_values($trips);
        } catch (Exception $e) {
            // If supplier_trips tables don't exist, keep empty
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'trips' => $trips
        ]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid supplier ID'
        ]);
        exit;
    }
}

include __DIR__ . '/../views/suppliers.php';
