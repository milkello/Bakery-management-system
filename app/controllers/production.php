<?php
require_once __DIR__ . '/../../config/config.php';

// Fetch all products for dropdown
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Handle production submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity_produced'])) {
    $product_id = $_POST['product_id'];
    $quantity_produced = $_POST['quantity_produced'];

    // Insert production record
    $stmt = $conn->prepare("INSERT INTO production (product_id, quantity_produced, created_by) VALUES (?,?,?)");
    $stmt->execute([$product_id, $quantity_produced, $_SESSION['user_id']]);

    // Update stock
    $stmtUpdate = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id=?");
    $stmtUpdate->execute([$quantity_produced, $product_id]);

    // Add notification
    $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
    $stmtNotif->execute(['production', "Produced $quantity_produced units of product ID $product_id"]);

    header("Location: index.php?page=production");
    exit;
}

// Fetch all production records
$productions = $conn->query("
    SELECT p.*, pr.name AS product_name, u.username AS produced_by
    FROM production p
    JOIN products pr ON p.product_id = pr.id
    JOIN users u ON p.created_by = u.id
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../views/production.php';
?>
