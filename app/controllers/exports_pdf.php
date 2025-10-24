<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Fetch Sales
$stmt = $conn->prepare("SELECT s.*, p.name AS product_name FROM sales s JOIN products p ON s.product_id=p.id WHERE DATE(s.created_at) BETWEEN ? AND ?");
$stmt->execute([$start_date, $end_date]);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = "<h2>Bakery Sales Report ($start_date to $end_date)</h2>
<table border='1' cellspacing='0' cellpadding='5'>
<tr><th>ID</th><th>Product</th><th>Qty</th><th>Total</th><th>Customer</th><th>Payment</th><th>Date</th></tr>";
foreach($sales as $s){
  $html .= "<tr>
  <td>{$s['id']}</td>
  <td>{$s['product_name']}</td>
  <td>{$s['quantity_sold']}</td>
  <td>{$s['total_price']}</td>
  <td>{$s['customer_type']}</td>
  <td>{$s['payment_method']}</td>
  <td>{$s['created_at']}</td>
  </tr>";
}
$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("sales_report_$start_date-$end_date.pdf");
exit;
