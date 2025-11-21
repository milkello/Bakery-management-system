<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

// Optional: vendor autoload for PDF generation
if (!class_exists('Dompdf\\Dompdf')) {
    @require_once __DIR__ . '/../../vendor/autoload.php';
}

// Fetch all products for dropdown
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all customers for dropdown
$customers = $conn->query("SELECT id, name, customer_type, phone FROM customers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle sale submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'], $_POST['unit_price'])) {
    $product_id = intval($_POST['product_id']);
    $quantity_sold = intval($_POST['quantity']);
    $unit_price = floatval($_POST['unit_price']);
    $customer_id = !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
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
            INSERT INTO sales (product_id, qty, unit_price, total_price, customer_type, payment_method, customer_id, sold_by, created_by)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $stmtSale->execute([
            $product_id,
            $quantity_sold,
            $unit_price,
            $total_price,
            $customer_type,
            $payment_method,
            $customer_id,
            $_SESSION['user_id'] ?? 1,
            $_SESSION['user_id'] ?? 1
        ]);

        // Notify successful sale
        $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
        $stmtProduct = $conn->prepare("SELECT name, sku FROM products WHERE id=?");
        $stmtProduct->execute([$product_id]);
        $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
        $name = $product['name'] ?? '';
        $sku = $product['sku'] ?? '';
        $stmtNotif->execute(['sale', "Sold $quantity_sold units of $name ($sku) successfully."]);

        // If the sale was paid via MoMo, record payment reference as a notification for tracing
        if (!empty($_POST['payment_reference'])) {
            $pref = substr($_POST['payment_reference'], 0, 64);
            $phone = $_POST['payer_phone'] ?? '';
            $msg = "MoMo payment reference: {$pref}" . ($phone ? " (payer: {$phone})" : '');
            $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
            $stmtNotif->execute(['momo_payment', $msg]);
        }
        // Notify successful sale
        // $stmtNotif = $conn->prepare("INSERT INTO notifications (type, message) VALUES (?, ?)");
        // $stmtProduct = $conn->prepare("SELECT name, sku FROM products WHERE id=?");
        // $stmtProduct->execute([$product_id]);
        // $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
        // $name = $product['name'] ?? '';
        // $sku = $product['sku'] ?? '';
        // $stmtNotif->execute(['sale', "Sold $quantity_sold units of $name ($sku) successfully."]);
    }

    header("Location: ?page=sales");
    exit;
}

// Sales report generation (GET: ?page=sales&action=report&start_date=YYYY-MM-DD&end_date=YYYY-MM-DD&format=pdf|csv)
if (isset($_GET['action']) && $_GET['action'] === 'report') {
    // parse dates
    $start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-6 days'));
    $end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
    // validate simple format (YYYY-MM-DD)
    $start_ok = preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date);
    $end_ok = preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date);
    if (!$start_ok || !$end_ok) {
        header('HTTP/1.1 400 Bad Request');
        echo 'Invalid date format. Use YYYY-MM-DD.';
        exit;
    }

    $format = strtolower($_GET['format'] ?? 'pdf');

    $stmt = $conn->prepare(
        "SELECT s.*, p.name AS product_name, p.product_code, u.username AS sold_by
         FROM sales s
         LEFT JOIN products p ON s.product_id = p.id
         LEFT JOIN users u ON s.sold_by = u.id
         WHERE DATE(s.created_at) BETWEEN ? AND ?
         ORDER BY s.created_at ASC"
    );
    $stmt->execute([$start_date, $end_date]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // totals
    $total_count = count($rows);
    $total_revenue = 0.0;
    foreach ($rows as $r) { $total_revenue += floatval($r['total_price'] ?? 0); }

    if ($format === 'csv') {
        $filename = "sales_report_{$start_date}_to_{$end_date}.csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Product', 'Product Code', 'Quantity', 'Unit Price', 'Total Price', 'Customer Type', 'Payment Method', 'Sold By', 'Date']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['product_name'] ?? '',
                $r['product_code'] ?? '',
                $r['qty'] ?? 0,
                number_format($r['unit_price'] ?? 0, 0),
                number_format($r['total_price'] ?? 0, 0),
                $r['customer_type'] ?? '',
                $r['payment_method'] ?? '',
                $r['sold_by'] ?? '',
                $r['created_at'] ?? '',
            ]);
        }
        fputcsv($out, []);
        fputcsv($out, ['Totals', '', $total_count, '', number_format($total_revenue, 0)]);
        fclose($out);
        exit;
    }

    // default -> PDF via Dompdf
    if (!class_exists('Dompdf\\Dompdf')) {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'PDF generator not available.';
        exit;
    }

    $dompdf = new \Dompdf\Dompdf();

    $html = '<!doctype html><html><head><meta charset="utf-8"><title>Sales Report</title>';
    $html .= '<style>body{font-family:Arial,Helvetica,sans-serif;font-size:12px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:6px;text-align:left;}th{background:#333;color:#fff;}</style>';
    $html .= '</head><body>';
    $html .= '<h2>Sales Report</h2>';
    $html .= '<p>Period: ' . htmlspecialchars($start_date) . ' to ' . htmlspecialchars($end_date) . '</p>';
    $html .= '<table><thead><tr><th>#</th><th>Product</th><th>Code</th><th>Qty</th><th>Unit</th><th>Total</th><th>Customer</th><th>Payment</th><th>Sold By</th><th>Date</th></tr></thead><tbody>';
    $i = 1;
    foreach ($rows as $r) {
        $html .= '<tr>';
        $html .= '<td>' . $i++ . '</td>';
        $html .= '<td>' . htmlspecialchars($r['product_name'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($r['product_code'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($r['qty'] ?? 0) . '</td>';
        $html .= '<td>$' . number_format($r['unit_price'] ?? 0, 0) . '</td>';
        $html .= '<td>$' . number_format($r['total_price'] ?? 0, 0) . '</td>';
        $html .= '<td>' . htmlspecialchars($r['customer_type'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($r['payment_method'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($r['sold_by'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($r['created_at'] ?? '') . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    $html .= '<p><strong>Total records:</strong> ' . $total_count . ' &nbsp;&nbsp; <strong>Total revenue:</strong> $' . number_format($total_revenue, 0) . '</p>';
    $html .= '</body></html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // clean buffers then stream
    if (ob_get_length()) { ob_end_clean(); }
    $filename = "sales_report_{$start_date}_to_{$end_date}.pdf";
    $dompdf->stream($filename, ['Attachment' => 1]);
    exit;
}

// Fetch all sales logs
$sales_logs = $conn->query("
    SELECT s.*, p.name AS product_name, p.sku, u.username AS sold_by
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