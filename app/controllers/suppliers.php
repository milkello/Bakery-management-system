<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

if (!class_exists('Dompdf\\Dompdf')) {
    @require_once __DIR__ . '/../../vendor/autoload.php';
}

// Base data for suppliers page
$suppliers = $conn->query("SELECT id, name, phone, email, address, created_at FROM suppliers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Products and customers are needed for recording sales per supplier
$products = $conn->query("SELECT * FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$customers = $conn->query("SELECT id, name, customer_type, phone FROM customers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle creating a new sale from suppliers page (always supplier-based)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode']) && $_POST['mode'] === 'create_supplier_sale') {
    $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity_sold = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unit_price = isset($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.0;
    $customer_id = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
    $customer_type = $_POST['customer_type'] ?? 'Regular';
    $payment_method = $_POST['payment_method'] ?? 'Cash';

    if ($supplier_id && $product_id && $quantity_sold > 0 && $unit_price >= 0) {
        // Supplier-based sale: stock is managed via supplier trips/returns, so we do not touch stock here.
        $total_price = $quantity_sold * $unit_price;

        $stmtSale = $conn->prepare("
            INSERT INTO sales (product_id, qty, unit_price, total_price, customer_type, payment_method, customer_id, supplied_by, sold_by, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");
        $stmtSale->execute([
            $product_id,
            $quantity_sold,
            $unit_price,
            $total_price,
            $customer_type,
            $payment_method,
            $customer_id,
            $supplier_id,
            $_SESSION['user_id'] ?? 1,
            $_SESSION['user_id'] ?? 1
        ]);

        // Simple sale notification
        $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
        $stmtProduct = $conn->prepare("SELECT name, sku FROM products WHERE id=?");
        $stmtProduct->execute([$product_id]);
        $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
        $name = $product['name'] ?? '';
        $sku = $product['sku'] ?? '';
        $stmtNotif->execute(['sale', "Supplier #$supplier_id sold $quantity_sold units of $name ($sku) successfully."]);        
    }

    header('Location: ?page=suppliers');
    exit;
}

$supplierTotalsStmt = $conn->query("SELECT supplied_by AS supplier_id, SUM(total_price) AS total_value FROM sales WHERE supplied_by IS NOT NULL GROUP BY supplied_by");
$supplierTotalsRaw = $supplierTotalsStmt->fetchAll(PDO::FETCH_ASSOC);
$supplierTotals = [];
foreach ($supplierTotalsRaw as $row) {
    $supplierTotals[$row['supplier_id']] = $row['total_value'];
}

$topWeekly = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE YEARWEEK(s.created_at, 1) = YEARWEEK(CURDATE(), 1) GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$topMonthly = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE DATE_FORMAT(s.created_at, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$topYearly = $conn->query("SELECT sp.id, sp.name, SUM(s.total_price) AS total_value FROM sales s JOIN suppliers sp ON sp.id = s.supplied_by WHERE DATE_FORMAT(s.created_at, '%Y') = DATE_FORMAT(CURDATE(), '%Y') GROUP BY sp.id, sp.name ORDER BY total_value DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['action']) && $_GET['action'] === 'export_pdf') {
    $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
    $start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
    $end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

    if (!$supplier_id || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        header('HTTP/1.1 400 Bad Request');
        echo 'Invalid input.';
        exit;
    }

    $stmtSupp = $conn->prepare("SELECT * FROM suppliers WHERE id = ? LIMIT 1");
    $stmtSupp->execute([$supplier_id]);
    $supplier = $stmtSupp->fetch(PDO::FETCH_ASSOC);
    if (!$supplier) {
        header('HTTP/1.1 404 Not Found');
        echo 'Supplier not found.';
        exit;
    }

    $stmt = $conn->prepare("SELECT s.*, p.name AS product_name, p.sku, c.name AS customer_name FROM sales s JOIN products p ON s.product_id = p.id LEFT JOIN customers c ON c.id = s.customer_id WHERE s.supplied_by = ? AND DATE(s.created_at) BETWEEN ? AND ? ORDER BY s.created_at ASC");
    $stmt->execute([$supplier_id, $start_date, $end_date]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!class_exists('Dompdf\\Dompdf')) {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'PDF generator not available.';
        exit;
    }

    $dompdf = new \Dompdf\Dompdf();

    $html = '<!doctype html><html><head><meta charset="utf-8"><title>Supplier History</title>';
    $html .= '<style>body{font-family:Arial,Helvetica,sans-serif;font-size:12px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:6px;text-align:left;}th{background:#333;color:#fff;}</style>';
    $html .= '</head><body>';
    $html .= '<h2>Supplier History</h2>';
    $html .= '<p><strong>Supplier:</strong> '.htmlspecialchars($supplier['name']).'</p>';
    $html .= '<p>Period: '.htmlspecialchars($start_date).' to '.htmlspecialchars($end_date).'</p>';
    $html .= '<table><thead><tr><th>#</th><th>Date</th><th>Product</th><th>Customer</th><th>Qty</th><th>Unit</th><th>Total</th><th>Payment</th></tr></thead><tbody>';

    $i = 1;
    $total_value = 0.0;
    foreach ($rows as $r) {
        $total_value += (float)($r['total_price'] ?? 0);
        $html .= '<tr>';
        $html .= '<td>'.$i++.'</td>';
        $html .= '<td>'.htmlspecialchars($r['created_at'] ?? '').'</td>';
        $html .= '<td>'.htmlspecialchars($r['product_name'] ?? '').'</td>';
        $html .= '<td>'.htmlspecialchars($r['customer_name'] ?? '').'</td>';
        $html .= '<td>'.htmlspecialchars($r['qty'] ?? 0).'</td>';
        $html .= '<td>'.number_format($r['unit_price'] ?? 0, 0).'</td>';
        $html .= '<td>'.number_format($r['total_price'] ?? 0, 0).'</td>';
        $html .= '<td>'.htmlspecialchars($r['payment_method'] ?? '').'</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $html .= '<p><strong>Total value:</strong> '.number_format($total_value, 0).' Rwf</p>';
    $html .= '</body></html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    if (ob_get_length()) { ob_end_clean(); }
    $filename = 'supplier_history_'.$supplier_id.'_'.$start_date.'_to_'.$end_date.'.pdf';
    $dompdf->stream($filename, ['Attachment' => 1]);
    exit;
}

include __DIR__ . '/../views/suppliers.php';
