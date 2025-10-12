<?php
require_once __DIR__ . '/../../config/config.php';

// Ensure database connection exists
if (!isset($conn) || !$conn) {
    die("Database connection not established.");
}

// Fetch products and materials for dropdowns
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$materials = $conn->query("SELECT * FROM raw_materials")->fetchAll(PDO::FETCH_ASSOC);

// Add recipe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $material_ids = $_POST['materials'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $units = $_POST['units'] ?? [];

    try {
        // Insert main recipe
        $stmt = $conn->prepare("INSERT INTO recipes (product_id) VALUES (?)");
        $stmt->execute([$product_id]);
        $recipe_id = $conn->lastInsertId();

        // Insert ingredients
        $stmt2 = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, material_id, quantity, unit) VALUES (?,?,?,?)");
        foreach ($material_ids as $index => $material_id) {
            if (!empty($material_id) && !empty($quantities[$index]) && !empty($units[$index])) {
                $stmt2->execute([$recipe_id, $material_id, $quantities[$index], $units[$index]]);
            }
        }

        header("Location: index.php?page=recipes");
        exit;

    } catch (PDOException $e) {
        die("Error adding recipe: " . $e->getMessage());
    }
}

// Fetch all recipes with ingredients
$recipes = $conn->query("
    SELECT r.id AS recipe_id, p.name AS product_name, ri.quantity, ri.unit, m.name AS material_name
    FROM recipes r
    JOIN products p ON r.product_id = p.id
    JOIN recipe_ingredients ri ON ri.recipe_id = r.id
    JOIN raw_materials m ON ri.material_id = m.id
    ORDER BY r.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Pass data to view
include __DIR__ . '/../views/recipes.php';
?>
