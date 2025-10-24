<?php
require_once __DIR__ . '/../../config/config.php';

echo "<h2>Testing Recipes for Products</h2>";

$products = $conn->query("SELECT id, name FROM products")->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $product) {
    echo "<h3>Product: {$product['name']} (ID: {$product['id']})</h3>";
    
    $recipes = $conn->prepare("
        SELECT r.id, ri.material_id, ri.quantity, ri.unit, rm.name as material_name
        FROM recipes r 
        LEFT JOIN recipe_ingredients ri ON r.id = ri.recipe_id 
        LEFT JOIN raw_materials rm ON ri.material_id = rm.id 
        WHERE r.product_id = ?
    ");
    $recipes->execute([$product['id']]);
    $ingredients = $recipes->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($ingredients)) {
        echo "<p style='color: red;'>❌ No recipe found for this product</p>";
    } else {
        echo "<ul>";
        foreach ($ingredients as $ing) {
            echo "<li>{$ing['material_name']}: {$ing['quantity']} {$ing['unit']}</li>";
        }
        echo "</ul>";
    }
}

echo "<h2>Production Table Records</h2>";
$production = $conn->query("SELECT * FROM production ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
if (empty($production)) {
    echo "<p style='color: red;'>❌ No records in production table</p>";
} else {
    echo "<pre>" . print_r($production, true) . "</pre>";
}
?>