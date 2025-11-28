<?php
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$userId = $_SESSION['user_id'];

$message = '';
$error = '';

// Handle planning ingredients for a product (creates or updates a material_order and links it to product)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf($_POST['csrf'] ?? '')) {
    if (isset($_POST['action']) && $_POST['action'] === 'plan_ingredients') {
        if (!$isAdmin) { $error = 'Only admin can plan ingredients.'; }
        else {
            try {
                $conn->beginTransaction();
                $product_id = intval($_POST['product_id']);
                $plan_date = $_POST['plan_date'] ?: date('Y-m-d');
                $note = $_POST['note'] ?? null;

                $material_ids = $_POST['material_id'] ?? [];
                $qtys = $_POST['qty'] ?? [];
                $unit_prices = $_POST['unit_price'] ?? [];

                $total_value = 0;
                for ($i = 0; $i < count($material_ids); $i++) {
                    $q = floatval($qtys[$i] ?? 0);
                    $p = floatval($unit_prices[$i] ?? 0);
                    $total_value += ($q * $p);
                }

                // Check for existing plan for product and date
                $existingPlanStmt = $conn->prepare('SELECT pmp.id as pmp_id, pmp.order_id FROM product_material_plans pmp WHERE pmp.product_id = ? AND pmp.plan_date = ? LIMIT 1');
                $existingPlanStmt->execute([$product_id, $plan_date]);
                $existing = $existingPlanStmt->fetch(PDO::FETCH_ASSOC);

                $itemStmt = $conn->prepare('INSERT INTO material_order_items (order_id, material_id, qty, unit_price, total_value) VALUES (?, ?, ?, ?, ?)');
                $updateMat = $conn->prepare('UPDATE raw_materials SET stock_quantity = stock_quantity - ? WHERE id = ?');
                $restoreMat = $conn->prepare('UPDATE raw_materials SET stock_quantity = stock_quantity + ? WHERE id = ?');

                $possible = true;
                $stmt = $conn->prepare('SELECT stock_quantity FROM raw_materials WHERE id = ?');
                for ($i = 0; $i < count($material_ids); $i++) {
                    $m = intval($material_ids[$i]);
                    $stmt->execute([$m]);
                    $stock = $stmt->fetchColumn();
                    if ($stock < $qtys[$i]) {
                        $possible = false;
                        break;
                    }
                }

                if (!$possible) {
                    $error = 'Impossible action: not enough stock for one of the ingredients.';
                } else {
                    if ($existing) {
                        // Update existing order: restore previous items then replace
                        $order_id = $existing['order_id'];
                        $oldItems = $conn->prepare('SELECT material_id, qty FROM material_order_items WHERE order_id = ?');
                        $oldItems->execute([$order_id]);
                        foreach ($oldItems->fetchAll(PDO::FETCH_ASSOC) as $oi) {
                            $restoreMat->execute([$oi['qty'], $oi['material_id']]);
                        }
                        $conn->prepare('DELETE FROM material_order_items WHERE order_id = ?')->execute([$order_id]);

                        $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
                        $stmtProduct = $conn->prepare("SELECT name, sku FROM products WHERE id=?");
                        $stmtProduct->execute([$product_id]);
                        $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
                        $name = $product['name'] ?? '';
                        $sku = $product['sku'] ?? '';
                        $stmtNotif->execute(['plan_update', "Updated plan for $name ($sku) on $plan_date"]);

                        for ($i = 0; $i < count($material_ids); $i++) {
                            $m = intval($material_ids[$i]);
                            $q = floatval($qtys[$i]);
                            $p = floatval($unit_prices[$i]);
                            if ($q <= 0) continue;
                            $tv = round($q * $p, 2);
                            $itemStmt->execute([$order_id, $m, $q, $p, $tv]);
                            $updateMat->execute([$q, $m]);
                        }
                        $conn->prepare('UPDATE material_orders SET total_value = ?, note = ? WHERE id = ?')->execute([$total_value, $note, $order_id]);
                        $message = 'Plan updated.';
                    } else {
                        $stmt = $conn->prepare('INSERT INTO material_orders (order_date, total_value, note, created_by, created_at) VALUES (?, ?, ?, ?, NOW())');
                        $stmt->execute([$plan_date, $total_value, $note, $userId]);
                        $order_id = $conn->lastInsertId();

                        $stmtProduct = $conn->prepare("SELECT name, sku FROM products WHERE id=?");
                        $stmtProduct->execute([$product_id]);
                        $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
                        $name = $product['name'] ?? '';
                        $sku = $product['sku'] ?? '';
                        $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
                        $plan_date_formatted = date('d/m/Y', strtotime($plan_date));
                        $stmtNotif->execute(['plan_update', "Saved plan for $name ($sku) on $plan_date_formatted"]);

                        for ($i = 0; $i < count($material_ids); $i++) {
                            $m = intval($material_ids[$i]);
                            $q = floatval($qtys[$i]);
                            $p = floatval($unit_prices[$i]);
                            if ($q <= 0) continue;
                            $tv = round($q * $p, 2);
                            $itemStmt->execute([$order_id, $m, $q, $p, $tv]);
                            $updateMat->execute([$q, $m]);
                        }

                        $link = $conn->prepare('INSERT INTO product_material_plans (product_id, order_id, plan_date, created_at) VALUES (?, ?, ?, NOW())');
                        $link->execute([$product_id, $order_id, $plan_date]);
                        $message = 'Plan saved.';

                    }
                    $conn->commit();
                    header('Location: ?page=product_boards'); exit;
                }
            } catch (Exception $e) {
                $conn->rollBack();
                $error = 'Error saving plan: ' . $e->getMessage();
            }
        }
    }

    // Production/Sales are now handled separately:
    // - Production is recorded via the production page using production table
    // - Sales are recorded via sales page using sales table
    // - This page only shows aggregated data from those tables
}

// Fetch products and today's plans/stats
$products = $conn->query('SELECT id, name, sku, unit, COALESCE(stock,0) as stock, COALESCE(price,0) as price FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Daily totals for stats cards
$daily_total_value_used = 0;
$stmt = $conn->prepare('
    SELECT product_id, SUM(total_value) as total_value
    FROM product_material_plans
    JOIN material_orders ON material_orders.id = product_material_plans.order_id
    WHERE product_material_plans.plan_date = CURDATE()
    GROUP BY product_id
');
$stmt->execute();
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($plans as $p) {
    $pid = $p['product_id'];
    $daily_total_value_used += (float)($p['total_value'] ?? 0);
}

// For quick display, load today's plans and stats into maps
$today = date('Y-m-d');
$plansStmt = $conn->prepare('SELECT pmp.*, mo.total_value FROM product_material_plans pmp JOIN material_orders mo ON mo.id = pmp.order_id WHERE pmp.plan_date = CURDATE()');
$plansStmt->execute();
$plans = $plansStmt->fetchAll(PDO::FETCH_ASSOC);
$plansByProduct = [];
foreach ($plans as $p) { $plansByProduct[$p['product_id']][] = $p; }

// Fetch today's production data from production table
$productionStmt = $conn->prepare('
    SELECT product_id, SUM(quantity_produced) as produced, (SELECT COALESCE(price,0) FROM products WHERE id = product_id) as price
    FROM production
    WHERE DATE(created_at) = CURDATE()
    GROUP BY product_id
');
$productionStmt->execute();
$productionData = $productionStmt->fetchAll(PDO::FETCH_ASSOC);
$productionByProduct = [];
$daily_total_produced = 0;
$daily_total_revenue = 0;
foreach ($productionData as $pd) {
    $productionByProduct[$pd['product_id']] = $pd;
    $daily_total_produced += (int)($pd['produced'] ?? 0);
    $daily_total_revenue += (float)($pd['produced'] ?? 0) * ($pd['price'] ?? 0);
}

// Fetch today's sales data from sales table
$salesStmt = $conn->prepare('
    SELECT product_id, SUM(total_price) as revenue
    FROM sales
    WHERE DATE(created_at) = CURDATE()
    GROUP BY product_id
');
$salesStmt->execute();
$salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);
$salesByProduct = [];
$daily_total_revenue_used = 0;
foreach ($salesData as $sd) {
    $salesByProduct[$sd['product_id']] = $sd;
    $daily_total_revenue_used += (float)($sd['revenue'] ?? 0);
}

// Combine production and sales data into stats format for display
$statsByProduct = [];
foreach ($products as $product) {
    $pid = $product['id'];

    $produced = $productionByProduct[$pid]['produced'] ?? 0;

    $plan_value = 0;
    if (isset($plansByProduct[$pid])) {
        foreach ($plansByProduct[$pid] as $planRow) {
            $plan_value += (float)($planRow['total_value'] ?? 0);
        }
    }

    $product_value = ((float)($product['price'] ?? 0)) * ((float)$produced);

    $statsByProduct[$pid] = [
        'product_id' => $pid,
        'stat_date' => $today,
        'produced' => $produced,
        'sold' => $salesByProduct[$pid]['sold'] ?? 0,
        'revenue' => $salesByProduct[$pid]['revenue'] ?? 0,
        'plan_value' => $plan_value,
        'product_value' => $product_value,
    ];
}

include __DIR__ . '/../views/product_boards.php';

?>
