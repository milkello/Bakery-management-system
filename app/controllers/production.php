<?php
if (!isset($_SESSION['user_id'])) { 
    header('Location: ?page=login'); 
    exit; 
}
require_once __DIR__ . '/../../config/config.php';

$message = "";
$error = "";
$user_role = $_SESSION['role'] ?? 'employee';

// PRODUCTION LOGIC - SIMPLIFIED: Only check for material order plan, don't deduct materials
// Materials are already deducted when plan is created in product_boards
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['produce'])) {
    try {
        $conn->beginTransaction();
        
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $created_by = $_SESSION['user_id'];
        $today = date('Y-m-d');
        $is_update = isset($_POST['is_update']) && $_POST['is_update'] == '1';
        
        // Validate quantity
        if ($quantity <= 0) {
            throw new Exception("Quantity must be greater than 0!");
        }
        
        // 1. Get product info
        $product_stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
        $product_stmt->execute([$product_id]);
        $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            throw new Exception("Product not found!");
        }
        $product_name = $product['name'];
        
        // 2. Check if production already exists for this product today
        $existingProdCheck = $conn->prepare("
            SELECT id, quantity_produced
            FROM production
            WHERE product_id = ? AND DATE(created_at) = ?
            LIMIT 1
        ");
        $existingProdCheck->execute([$product_id, $today]);
        $existingProd = $existingProdCheck->fetch(PDO::FETCH_ASSOC);
        
        // If production exists and this is NOT an update request, throw error
        if ($existingProd && !$is_update) {
            throw new Exception("⚠️ Production for {$product_name} has already been recorded today ({$existingProd['quantity_produced']} units)!<br><br>Use the UPDATE button to correct any mistakes.");
        }
        
        // If this is an update but no existing production, throw error
        if ($is_update && !$existingProd) {
            throw new Exception("⚠️ No existing production found to update!");
        }
        
        // 3. Check if a material order plan exists for this product today
        $planCheck = $conn->prepare("
            SELECT pmp.id, pmp.order_id, mo.total_value
            FROM product_material_plans pmp
            JOIN material_orders mo ON mo.id = pmp.order_id
            WHERE pmp.product_id = ? AND pmp.plan_date = ?
            LIMIT 1
        ");
        $planCheck->execute([$product_id, $today]);
        $plan = $planCheck->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            throw new Exception("⚠️ No material plan found for {$product_name} on {$today}!<br><br>Please create an ingredient plan in Product Boards before recording production.");
        }
        
        // 4. Get the materials from the plan (for display purposes only)
        $planItemsStmt = $conn->prepare("
            SELECT moi.*, rm.name as material_name, rm.unit
            FROM material_order_items moi
            LEFT JOIN raw_materials rm ON rm.id = moi.material_id
            WHERE moi.order_id = ?
        ");
        $planItemsStmt->execute([$plan['order_id']]);
        $planItems = $planItemsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $materials_used = "";
        foreach ($planItems as $item) {
            $materials_used .= "{$item['material_name']}: {$item['qty']}{$item['unit']}, ";
        }
        $materials_used = rtrim($materials_used, ", ");
        
        if ($is_update && $existingProd) {
            // 5a. UPDATE existing production
            $old_quantity = $existingProd['quantity_produced'];
            $quantity_diff = $quantity - $old_quantity;
            
            $update_production_stmt = $conn->prepare("
                UPDATE production 
                SET quantity_produced = ?, raw_materials_used = ?
                WHERE id = ?
            ");
            $update_production_stmt->execute([$quantity, $materials_used, $existingProd['id']]);
            
            // 5b. Adjust product stock by the difference
            $update_product_stmt = $conn->prepare("
                UPDATE products 
                SET stock = COALESCE(stock, 0) + ? 
                WHERE id = ?
            ");
            $update_product_stmt->execute([$quantity_diff, $product_id]);
        } else {
            // 5a. INSERT new production (no material deduction - already done in plan)
            $production_stmt = $conn->prepare("
                INSERT INTO production (product_id, quantity_produced, raw_materials_used, created_by, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $production_stmt->execute([$product_id, $quantity, $materials_used, $created_by]);
            
            // 5b. Update product stock
            $update_product_stmt = $conn->prepare("
                UPDATE products 
                SET stock = COALESCE(stock, 0) + ? 
                WHERE id = ?
            ");
            $update_product_stmt->execute([$quantity, $product_id]);
        }
        
        // 6. Create or update notification
        $checkNotif = $conn->prepare("
            SELECT id, message FROM notifications 
            WHERE type = 'production' 
            AND DATE(created_at) = ?
            AND JSON_EXTRACT(data, '$.product_id') = ?
            LIMIT 1
        ");
        $checkNotif->execute([$today, $product_id]);
        $existingNotif = $checkNotif->fetch(PDO::FETCH_ASSOC);
        
        if ($existingNotif) {
            // Update existing notification with new total
            $getTotalProduced = $conn->prepare("
                SELECT SUM(quantity_produced) as total 
                FROM production 
                WHERE product_id = ? AND DATE(created_at) = ?
            ");
            $getTotalProduced->execute([$product_id, $today]);
            $totalProduced = $getTotalProduced->fetch(PDO::FETCH_ASSOC)['total'];
            
            $updateNotif = $conn->prepare("
                UPDATE notifications 
                SET message = ?, data = ?, created_at = NOW()
                WHERE id = ?
            ");
            $notifMessage = "Produced {$totalProduced} units of {$product_name}";
            $notifData = json_encode(['product_id' => $product_id, 'quantity' => $totalProduced, 'product_name' => $product_name]);
            $updateNotif->execute([$notifMessage, $notifData, $existingNotif['id']]);
        } else {
            // Create new notification
            $createNotif = $conn->prepare("
                INSERT INTO notifications (type, message, data, is_read, created_by, created_at) 
                VALUES ('production', ?, ?, 0, ?, NOW())
            ");
            $notifMessage = "Produced {$quantity} units of {$product_name}";
            $notifData = json_encode(['product_id' => $product_id, 'quantity' => $quantity, 'product_name' => $product_name]);
            $createNotif->execute([$notifMessage, $notifData, $created_by]);
        }
        
        $conn->commit();
        $message = $is_update ? "✅ Successfully updated production to {$quantity} units of {$product_name}!" : "✅ Successfully produced {$quantity} units of {$product_name}!";
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "❌ Error: " . $e->getMessage();
    }
}

// Fetch data for display
$products = $conn->query("SELECT id, name, sku, stock FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$raw_materials = $conn->query("SELECT id, name, stock_quantity, unit FROM raw_materials ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$recent_production = $conn->query("
    SELECT p.*, pr.name as product_name, u.username 
    FROM production p 
    LEFT JOIN products pr ON p.product_id = pr.id 
    LEFT JOIN users u ON p.created_by = u.id 
    ORDER BY p.created_at DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Pre-fetch today's material plans for each product
$today = date('Y-m-d');
$today_plans = [];
$planStmt = $conn->prepare("
    SELECT pmp.product_id, pmp.order_id, mo.total_value
    FROM product_material_plans pmp
    JOIN material_orders mo ON mo.id = pmp.order_id
    WHERE pmp.plan_date = ?
");
$planStmt->execute([$today]);
$plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($plans as $plan) {
    $today_plans[$plan['product_id']] = $plan;
}

// Pre-fetch today's production for each product (to show update option)
$today_production_data = [];
$todayProdStmt = $conn->prepare("
    SELECT product_id, id, quantity_produced, created_at
    FROM production
    WHERE DATE(created_at) = ?
");
$todayProdStmt->execute([$today]);
$todayProds = $todayProdStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($todayProds as $prod) {
    $today_production_data[$prod['product_id']] = $prod;
}

// Calculate stats
$total_production = $conn->query("SELECT COUNT(*) FROM production")->fetchColumn();
$today_production = $conn->query("SELECT COUNT(*) FROM production WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$toal_stock = $conn->query("SELECT SUM(stock) FROM products")->fetchColumn() ?? 0;



include __DIR__ . '/../views/production.php';
?>