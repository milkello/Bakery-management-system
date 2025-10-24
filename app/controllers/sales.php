<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

// Fetch all products for dropdown
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Handle sale submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'], $_POST['unit_price'])) {
    $product_id = intval($_POST['product_id']);
    $quantity_sold = intval($_POST['quantity']);
    $unit_price = floatval($_POST['unit_price']);
    $customer_type = $_POST['customer_type'] ?? 'Regular';
    $payment_method = $_POST['payment_method'] ?? 'Cash';

    // Check if stock column exists
    $checkStock = $conn->prepare("SHOW COLUMNS FROM products LIKE 'stock'");
    $checkStock->execute();
    if ($checkStock->rowCount() === 0) {
        // Add stock column if missing
        $conn->exec("ALTER TABLE products ADD COLUMN stock INT DEFAULT 0");
    }

    // Get current stock
    $stmtStock = $conn->prepare("SELECT stock FROM products WHERE id=?");
    $stmtStock->execute([$product_id]);
    $stock = $stmtStock->fetchColumn();

    // Prevent selling more than available
    if ($quantity_sold > $stock) {
        $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
        $stmtNotif->execute(['over_usage', "Attempted sale exceeds available stock for Product ID $product_id"]);
    } else {
        // Deduct stock
        $stmtUpdate = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id=?");
        $stmtUpdate->execute([$quantity_sold, $product_id]);

        // Calculate total price
        $total_price = $quantity_sold * $unit_price;

        // Insert sale
        $stmtSale = $conn->prepare("
            INSERT INTO sales (product_id, qty, unit_price, total_price, customer_type, payment_method, sold_by)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmtSale->execute([
            $product_id,
            $quantity_sold,
            $unit_price,
            $total_price,
            $customer_type,
            $payment_method,
            $_SESSION['user']['id'] ?? 1 // Fallback user if not logged in
        ]);

        // Notify successful sale
        $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
        $stmtNotif->execute(['sale', "Sold $quantity_sold units of Product ID $product_id successfully."]);
    }

    header("Location: ?page=sales");
    exit;
}

// Fetch all sales logs
$sales_logs = $conn->query("
    SELECT s.*, p.name AS product_name, p.product_code, u.username AS sold_by
    FROM sales s
    JOIN products p ON s.product_id = p.id
    LEFT JOIN users u ON s.sold_by = u.id
    ORDER BY s.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$total_sales = $conn->query("SELECT COUNT(*) FROM sales")->fetchColumn();
$total_revenue = $conn->query("SELECT SUM(total_price) FROM sales")->fetchColumn();
$today_revenue = $conn->query("SELECT SUM(total_price) FROM sales WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$avg_sale_value = $conn->query("SELECT AVG(total_price) FROM sales")->fetchColumn();

// Pass data to view
include __DIR__ . '/../views/sales.php';
?>