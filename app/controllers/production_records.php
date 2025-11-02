<?php
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }

$message = '';
$error = '';

// Allow staff and admin to record production
$canRecord = in_array($_SESSION['role'] ?? '', ['admin','staff','manager']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf($_POST['csrf'] ?? '')) {
    if (!$canRecord) {
        $error = 'You do not have permission to record production.';
    } else {
        try {
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);
            $note = $_POST['note'] ?? null;
            $user = $_SESSION['user_id'];

            if ($quantity <= 0) throw new Exception('Quantity must be > 0');

            $stmt = $conn->prepare('INSERT INTO production_records (product_id, quantity, created_by, created_at, note) VALUES (?, ?, ?, NOW(), ?)');
            $stmt->execute([$product_id, $quantity, $user, $note]);

            // update product stock
            $conn->prepare('UPDATE products SET stock = COALESCE(stock,0) + ? WHERE id = ?')->execute([$quantity, $product_id]);

            $message = 'Production recorded.';
            header('Location: ?page=production_records'); exit;
        } catch (Exception $e) {
            $error = 'Error recording production: ' . $e->getMessage();
        }
    }
}

// Fetch products and recent records
$products = $conn->query('SELECT id, name, stock FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$recent = $conn->query('SELECT pr.*, p.name as product_name, u.username FROM production_records pr LEFT JOIN products p ON p.id = pr.product_id LEFT JOIN users u ON u.id = pr.created_by ORDER BY pr.created_at DESC LIMIT 50')->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../views/production_records.php';

?>
