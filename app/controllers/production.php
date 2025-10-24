<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

// PRODUCTION LOGIC USING RECIPE TABLES
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['produce'])) {
    try {
        $conn->beginTransaction();
        
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $created_by = $_SESSION['user_id'];
        
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
        
        // 2. Get recipe for this product
        $recipe_stmt = $conn->prepare("
            SELECT r.id as recipe_id 
            FROM recipes r 
            WHERE r.product_id = ?
            LIMIT 1
        ");
        $recipe_stmt->execute([$product_id]);
        $recipe = $recipe_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$recipe) {
            throw new Exception("🚫 IMPOSSIBLE PRODUCTION: No recipe found for {$product_name}!");
        }
        
        $recipe_id = $recipe['recipe_id'];
        
        // 3. Get recipe ingredients
        $ingredients_stmt = $conn->prepare("
            SELECT ri.material_id, ri.quantity as required_qty, ri.unit,
                   rm.name as material_name, rm.stock_quantity as available_qty
            FROM recipe_ingredients ri
            JOIN raw_materials rm ON ri.material_id = rm.id
            WHERE ri.recipe_id = ?
        ");
        $ingredients_stmt->execute([$recipe_id]);
        $ingredients = $ingredients_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($ingredients)) {
            throw new Exception("🚫 IMPOSSIBLE PRODUCTION: Recipe found but no ingredients defined for {$product_name}!");
        }
        
        // 4. Check if production is possible (enough materials)
        $impossible_reasons = [];
        $materials_used = "";
        
        foreach ($ingredients as $ingredient) {
            $required_total = $ingredient['required_qty'] * $quantity;
            $available = $ingredient['available_qty'];
            
            // Check if completely insufficient
            if ($required_total > $available) {
                $impossible_reasons[] = [
                    'material' => $ingredient['material_name'],
                    'required' => $required_total,
                    'available' => $available,
                    'unit' => $ingredient['unit']
                ];
            }
        }
        
        // 5. If impossible, show IMPOSSIBLE PRODUCTION alert and stop
        if (!empty($impossible_reasons)) {
            $conn->rollBack();
            $error = "🚫 IMPOSSIBLE PRODUCTION<br><br>";
            $error .= "<strong>Cannot produce {$quantity} units of {$product_name}</strong><br>";
            $error .= "Insufficient materials:<br>";
            
            foreach ($impossible_reasons as $reason) {
                $error .= "• <strong>{$reason['material']}</strong>: ";
                $error .= "Required {$reason['required']}{$reason['unit']}, ";
                $error .= "Available {$reason['available']}{$reason['unit']}<br>";
            }
            
            $error .= "<br><em>Please restock materials or reduce production quantity.</em>";
            
        } else {
            // 6. Production is possible - deduct materials and record production
            foreach ($ingredients as $ingredient) {
                $used_qty = $ingredient['required_qty'] * $quantity;
                
                // Deduct from raw materials
                $update_stmt = $conn->prepare("
                    UPDATE raw_materials 
                    SET stock_quantity = stock_quantity - ? 
                    WHERE id = ?
                ");
                $update_stmt->execute([$used_qty, $ingredient['material_id']]);
                
                $materials_used .= "{$ingredient['material_name']}: {$used_qty}{$ingredient['unit']}, ";
            }
            
            $materials_used = rtrim($materials_used, ", ");
            
            // 7. Record production
            $production_stmt = $conn->prepare("
                INSERT INTO production (product_id, quantity_produced, raw_materials_used, created_by, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $production_stmt->execute([$product_id, $quantity, $materials_used, $created_by]);
            
            // 8. Update product stock
            $update_product_stmt = $conn->prepare("
                UPDATE products 
                SET stock = COALESCE(stock, 0) + ? 
                WHERE id = ?
            ");
            $update_product_stmt->execute([$quantity, $product_id]);
            
            $conn->commit();
            $message = "✅ Successfully produced {$quantity} units of {$product_name}!";
        }
        
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

// Pre-fetch all recipes for the view (no AJAX needed)
$all_recipes = [];
foreach ($products as $product) {
    $recipe_stmt = $conn->prepare("
        SELECT r.id as recipe_id 
        FROM recipes r 
        WHERE r.product_id = ?
        LIMIT 1
    ");
    $recipe_stmt->execute([$product['id']]);
    $recipe = $recipe_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($recipe) {
        $ingredients_stmt = $conn->prepare("
            SELECT ri.material_id, ri.quantity, ri.unit,
                   rm.name as material_name, rm.stock_quantity as available_qty
            FROM recipe_ingredients ri
            JOIN raw_materials rm ON ri.material_id = rm.id
            WHERE ri.recipe_id = ?
        ");
        $ingredients_stmt->execute([$recipe['recipe_id']]);
        $all_recipes[$product['id']] = $ingredients_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Calculate stats
$total_production = $conn->query("SELECT COUNT(*) FROM production")->fetchColumn();
$today_production = $conn->query("SELECT COUNT(*) FROM production WHERE DATE(created_at) = CURDATE()")->fetchColumn();

include __DIR__ . '/../views/production.php';
?>