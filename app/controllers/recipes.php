<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

// Ensure database connection exists
if (!isset($conn) || !$conn) {
    die("Database connection not established.");
}

// Fetch products and materials for dropdowns
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$materials = $conn->query("SELECT * FROM raw_materials")->fetchAll(PDO::FETCH_ASSOC);

// Handle Add Recipe
// Handle Add or Update Recipe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $material_ids = $_POST['materials'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $units = $_POST['units'] ?? [];
    $recipe_id = !empty($_POST['recipe_id']) ? intval($_POST['recipe_id']) : null;

    try {
        if ($recipe_id) {
            // Update existing recipe: update product and reset ingredients
            $stmt = $conn->prepare("UPDATE recipes SET product_id = ? WHERE id = ?");
            $stmt->execute([$product_id, $recipe_id]);

            // Delete old ingredients
            $stmt = $conn->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?");
            $stmt->execute([$recipe_id]);
        } else {
            // Insert main recipe
            $stmt = $conn->prepare("INSERT INTO recipes (product_id) VALUES (?)");
            $stmt->execute([$product_id]);
            $recipe_id = $conn->lastInsertId();
        }

        // Insert ingredients
        $stmt2 = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, material_id, quantity, unit) VALUES (?,?,?,?)");
        foreach ($material_ids as $index => $material_id) {
            if (!empty($material_id) && isset($quantities[$index]) && $quantities[$index] !== '' && !empty($units[$index])) {
                $stmt2->execute([$recipe_id, $material_id, $quantities[$index], $units[$index]]);
            }
        }

        header("Location: ?page=recipes");
        exit;

    } catch (PDOException $e) {
        die(($recipe_id ? "Error updating recipe: " : "Error adding recipe: ") . $e->getMessage());
    }
}

// Handle Delete Recipe
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        // Delete ingredients first
        $stmt = $conn->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?");
        $stmt->execute([$_GET['id']]);
        
        // Then delete recipe
        $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        header("Location: ?page=recipes");
        exit;
    } catch (PDOException $e) {
        die("Error deleting recipe: " . $e->getMessage());
    }
}

// Fetch all recipes with ingredients grouped by recipe
$recipes = $conn->query("
    SELECT r.id AS recipe_id, p.name AS product_name, p.id AS product_id
    FROM recipes r
    JOIN products p ON r.product_id = p.id
    ORDER BY r.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get ingredients for each recipe
// Use indexed loop (no reference) to avoid PHP "foreach by reference" side-effects
foreach ($recipes as $i => $recipe) {
    $stmt = $conn->prepare(
        "SELECT ri.quantity, ri.unit, m.name AS material_name, m.id AS material_id
         FROM recipe_ingredients ri
         JOIN raw_materials m ON ri.material_id = m.id
         WHERE ri.recipe_id = ?"
    );
    $stmt->execute([$recipe['recipe_id']]);
    $recipes[$i]['ingredients'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Calculate statistics
$total_recipes = count($recipes);
$total_products_with_recipes = $conn->query("SELECT COUNT(DISTINCT product_id) FROM recipes")->fetchColumn();
$avg_ingredients_per_recipe = $conn->query("
    SELECT AVG(ingredient_count) FROM (
        SELECT COUNT(*) as ingredient_count FROM recipe_ingredients GROUP BY recipe_id
    ) as counts
")->fetchColumn();

// Pass data to view
include __DIR__ . '/../views/recipes.php';
?>