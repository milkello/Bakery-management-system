<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

require_once __DIR__ . '/../../config/config.php';

$message = "";
$error = "";

// For optional per-customer history display (non-AJAX fallback if needed)
$customer_history = [];
$history_customer = null;

// AJAX: Customer history for modal
if (isset($_GET['action']) && $_GET['action'] === 'history' && isset($_GET['id']) && isset($_GET['ajax'])) {
    try {
        $cid = (int)$_GET['id'];

        $custStmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
        $custStmt->execute([$cid]);
        $history_customer = $custStmt->fetch(PDO::FETCH_ASSOC);

        if ($history_customer) {
            $histStmt = $conn->prepare("
                SELECT s.id, s.product_id, s.qty, s.total_price, s.payment_method, s.created_at,
                       p.name AS product_name
                FROM sales s
                LEFT JOIN products p ON s.product_id = p.id
                WHERE s.customer_id = ?
                ORDER BY s.created_at DESC
                LIMIT 100
            ");
            $histStmt->execute([$cid]);
            $customer_history = $histStmt->fetchAll(PDO::FETCH_ASSOC);

            // Render minimal HTML snippet for the modal
            ob_start();
            ?>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-lime-400">Purchase History: <?= htmlspecialchars($history_customer['name']) ?></h3>
                <a href="?page=exports_pdf&type=customer_history&customer_id=<?= $history_customer['id'] ?>&from=<?= date('Y-m-d', strtotime('-30 days')) ?>&to=<?= date('Y-m-d') ?>" target="_blank" class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-3 py-1 rounded text-xs flex items-center space-x-1">
                    <i data-feather="download" class="w-4 h-4"></i>
                    <span>Export PDF</span>
                </a>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-2 px-3 text-lime-400">Date</th>
                            <th class="text-left py-2 px-3 text-lime-400">Product</th>
                            <th class="text-left py-2 px-3 text-lime-400">Qty</th>
                            <th class="text-left py-2 px-3 text-lime-400">Total</th>
                            <th class="text-left py-2 px-3 text-lime-400">Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customer_history as $h): ?>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 px-3 text-gray-300">
                                <?= date('M j, Y H:i', strtotime($h['created_at'])) ?>
                            </td>
                            <td class="py-2 px-3 text-gray-300">
                                <?= htmlspecialchars($h['product_name'] ?? 'N/A') ?>
                            </td>
                            <td class="py-2 px-3 text-gray-300">
                                <?= intval($h['qty'] ?? 0) ?>
                            </td>
                            <td class="py-2 px-3 text-gray-300">
                                <?= number_format($h['total_price'] ?? 0, 0) ?> Rwf
                            </td>
                            <td class="py-2 px-3 text-gray-300">
                                <?= htmlspecialchars($h['payment_method'] ?? '-') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
            $html = ob_get_clean();
            while (ob_get_level()) { ob_end_clean(); }
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
        } else {
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Customer not found.';
        }
    } catch (Exception $e) {
        while (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Failed to load history.';
    }
    exit;
}

// Handle Add/Edit Customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_customer'])) {
    try {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $customer_type = $_POST['customer_type'] ?? 'Regular';
        $notes = trim($_POST['notes'] ?? '');
        
        if (empty($name)) {
            throw new Exception("Customer name is required!");
        }
        
        if (isset($_POST['customer_id']) && !empty($_POST['customer_id'])) {
            // Update existing customer
            $stmt = $conn->prepare("
                UPDATE customers 
                SET name = ?, phone = ?, email = ?, address = ?, customer_type = ?, notes = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $phone, $email, $address, $customer_type, $notes, $_POST['customer_id']]);
            $message = "Customer updated successfully!";
        } else {
            // Add new customer
            $stmt = $conn->prepare("
                INSERT INTO customers (name, phone, email, address, customer_type, notes, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $phone, $email, $address, $customer_type, $notes, $_SESSION['user_id']]);
            $message = "Customer added successfully!";
        }
        
        header("Location: ?page=customers&success=1");
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle Delete Customer
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        header("Location: ?page=customers&deleted=1");
        exit;
    } catch (Exception $e) {
        $error = "Failed to delete customer: " . $e->getMessage();
    }
}

// Get success message from URL
if (isset($_GET['success'])) {
    $message = "Operation completed successfully!";
}
if (isset($_GET['deleted'])) {
    $message = "Customer deleted successfully!";
}

// Fetch all customers
$customers = $conn->query("
    SELECT c.*, u.username as created_by_name
    FROM customers c
    LEFT JOIN users u ON c.created_by = u.id
    ORDER BY c.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$total_customers = count($customers);
$regular_count = count(array_filter($customers, fn($c) => $c['customer_type'] === 'Regular'));
$wholesale_count = count(array_filter($customers, fn($c) => $c['customer_type'] === 'Wholesale'));
$vip_count = count(array_filter($customers, fn($c) => $c['customer_type'] === 'VIP'));

// Get top buying customers (last 30 days)
$top_buyers = $conn->query("
    SELECT c.id, c.name, c.customer_type, c.phone,
           COUNT(s.id) as total_purchases,
           SUM(s.total_price) as total_spent,
           MAX(s.created_at) as last_purchase
    FROM customers c
    LEFT JOIN sales s ON c.id = s.customer_id
    WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY c.id
    HAVING total_purchases > 0
    ORDER BY total_spent DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../views/customers.php';
?>
