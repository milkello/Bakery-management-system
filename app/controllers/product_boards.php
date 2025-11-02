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

                if ($existing) {
                    // Update existing order: restore previous items then replace
                    $order_id = $existing['order_id'];
                    $oldItems = $conn->prepare('SELECT material_id, qty FROM material_order_items WHERE order_id = ?');
                    $oldItems->execute([$order_id]);
                    foreach ($oldItems->fetchAll(PDO::FETCH_ASSOC) as $oi) {
                        $restoreMat->execute([$oi['qty'], $oi['material_id']]);
                    }
                    $conn->prepare('DELETE FROM material_order_items WHERE order_id = ?')->execute([$order_id]);

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
            } catch (Exception $e) {
                $conn->rollBack();
                $error = 'Error saving plan: ' . $e->getMessage();
            }
        }
    }

    // Handle recording production/sales into product_daily_stats and sales table
    if (isset($_POST['action']) && $_POST['action'] === 'record_stats') {
        try {
            $product_id = intval($_POST['product_id']);
            $stat_date = $_POST['stat_date'] ?: date('Y-m-d');
            $produced = intval($_POST['produced'] ?? 0);
            $sold = intval($_POST['sold'] ?? 0);
            // Compute revenue server-side from product price for security and consistency
            $priceStmt = $conn->prepare('SELECT COALESCE(price,0) as price FROM products WHERE id = ? LIMIT 1');
            $priceStmt->execute([$product_id]);
            $p = $priceStmt->fetch(PDO::FETCH_ASSOC);
            $price = $p ? floatval($p['price']) : 0.0;
            $revenue = round($sold * $price, 2);

            // Upsert stats
            $upsert = $conn->prepare(
                "INSERT INTO product_daily_stats (product_id, stat_date, produced, sold, revenue, created_by, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE produced = VALUES(produced), sold = VALUES(sold), revenue = VALUES(revenue), created_by = VALUES(created_by), created_at = NOW()"
            );
            $upsert->execute([$product_id, $stat_date, $produced, $sold, $revenue, $userId]);

            // update product stock
            $delta = $produced - $sold;
            if ($delta != 0) {
                $conn->prepare('UPDATE products SET stock = COALESCE(stock,0) + ? WHERE id = ?')->execute([$delta, $product_id]);
            }

            // For sales we update daily stats and produce notifications/logs, but
            // we avoid inserting a new 'sales' row here to prevent duplicate records.
            // Dashboard will compute totals from `product_daily_stats`.
            if ($sold > 0) {
                $total_price = $revenue;
                // notification (informational)
                $notif = $conn->prepare('INSERT INTO notifications (type, message, data, is_read, created_by, created_at) VALUES (?, ?, ?, 0, ?, NOW())');
                $messageText = "Sold {$sold} units of product_id {$product_id} for {$total_price}";
                $notif->execute(['sale', $messageText, json_encode(['product_id'=>$product_id,'qty'=>$sold,'total'=>$total_price]), $userId]);

                // log
                $log = $conn->prepare('INSERT INTO logs (user_id, action, meta, created_at) VALUES (?, ?, ?, NOW())');
                $log->execute([$userId, 'Product sold', json_encode(['product_id'=>$product_id,'qty'=>$sold,'total'=>$total_price])]);
            }

            $message = 'Stats recorded.';
            header('Location: ?page=product_boards'); exit;
        } catch (Exception $e) {
            $error = 'Error recording stats: ' . $e->getMessage();
        }
    }
}

// Fetch products and today's plans/stats
$products = $conn->query('SELECT id, name, sku, unit, COALESCE(stock,0) as stock, COALESCE(price,0) as price FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

// For quick display, load today's plans and stats into maps
$today = date('Y-m-d');
$plansStmt = $conn->prepare('SELECT pmp.*, mo.total_value FROM product_material_plans pmp JOIN material_orders mo ON mo.id = pmp.order_id WHERE pmp.plan_date = ?');
$plansStmt->execute([$today]);
$plans = $plansStmt->fetchAll(PDO::FETCH_ASSOC);
$plansByProduct = [];
foreach ($plans as $p) { $plansByProduct[$p['product_id']][] = $p; }

$statsStmt = $conn->prepare('SELECT * FROM product_daily_stats WHERE stat_date = ?');
$statsStmt->execute([$today]);
$stats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);
$statsByProduct = [];
foreach ($stats as $s) { $statsByProduct[$s['product_id']] = $s; }

include __DIR__ . '/../views/product_boards.php';

?>
