<?php
require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');

try {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'Invalid id']);
        exit;
    }

    $stmt = $conn->prepare("SELECT r.id AS recipe_id, r.product_id, p.name AS product_name FROM recipes r JOIN products p ON r.product_id = p.id WHERE r.id = ?");
    $stmt->execute([$id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        echo json_encode(['success' => false, 'error' => 'Recipe not found']);
        exit;
    }

    $stmt = $conn->prepare("SELECT ri.id AS id, ri.quantity, ri.unit, m.id AS material_id, m.name AS material_name FROM recipe_ingredients ri JOIN raw_materials m ON ri.material_id = m.id WHERE ri.recipe_id = ?");
    $stmt->execute([$id]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $recipe['ingredients'] = $ingredients;

    echo json_encode(['success' => true, 'data' => $recipe]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
