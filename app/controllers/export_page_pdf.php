<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;

$type = $_GET['type'] ?? 'sales';

// Helper functions
function formatCurrency($amount) {
    return number_format($amount, 0) . ' Frw';
}

function getPDFPageStyles() {
    return "
    <style>
        @page { margin: 20px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #84cc16;
        }
        .header h1 {
            color: #84cc16;
            font-size: 22px;
            margin: 0 0 5px 0;
        }
        .header .subtitle {
            color: #666;
            font-size: 11px;
        }
        .meta-info {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #84cc16;
        }
        .meta-info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #84cc16;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .total-row {
            background: #f0f9ff !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #f0f9ff;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #84cc16;
        }
        .stat-label {
            color: #666;
            font-size: 10px;
        }
        .stat-value {
            color: #84cc16;
            font-size: 16px;
            font-weight: bold;
        }
    </style>";
}

// Generate PDFs based on type
$html = '';
$filename = '';

switch ($type) {
    case 'sales':
        // Get all sales data
        $sales = $conn->query("
            SELECT s.*, p.name AS product_name, p.product_code, u.username AS sold_by_name
            FROM sales s
            JOIN products p ON s.product_id = p.id
            LEFT JOIN users u ON s.sold_by = u.id
            ORDER BY s.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $total_revenue = $conn->query("SELECT SUM(total_price) FROM sales")->fetchColumn() ?? 0;
        $total_count = count($sales);
        
        $html = getPDFPageStyles();
        $html .= "
        <div class='header'>
            <h1>Sales Records Export</h1>
            <div class='subtitle'>Bakery Management System</div>
        </div>
        
        <div class='meta-info'>
            <p><strong>Export Date:</strong> " . date('F j, Y g:i A') . "</p>
            <p><strong>Total Records:</strong> $total_count sales</p>
            <p><strong>Total Revenue:</strong> " . formatCurrency($total_revenue) . "</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Code</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Sold By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($sales as $sale) {
            $html .= "
                <tr>
                    <td>#{$sale['id']}</td>
                    <td>{$sale['product_name']}</td>
                    <td>" . ($sale['product_code'] ?? 'N/A') . "</td>
                    <td>" . number_format($sale['qty']) . "</td>
                    <td>" . formatCurrency($sale['unit_price']) . "</td>
                    <td><strong>" . formatCurrency($sale['total_price']) . "</strong></td>
                    <td>{$sale['customer_type']}</td>
                    <td>{$sale['payment_method']}</td>
                    <td>" . ($sale['sold_by_name'] ?? 'N/A') . "</td>
                    <td>" . date('M j, Y', strtotime($sale['created_at'])) . "</td>
                </tr>";
        }
        
        $html .= "
                <tr class='total-row'>
                    <td colspan='5' style='text-align: right;'><strong>TOTAL REVENUE:</strong></td>
                    <td colspan='5'><strong>" . formatCurrency($total_revenue) . "</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class='footer'>
            <p>Bakery Management System - Sales Export</p>
            <p>This is a computer-generated document. No signature is required.</p>
        </div>";
        
        $filename = "Sales_Export_" . date('Y-m-d') . ".pdf";
        break;
        
    case 'products':
        // Get all products
        $products = $conn->query("
            SELECT * FROM products ORDER BY name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $total_value = $conn->query("SELECT SUM(stock * price) FROM products")->fetchColumn() ?? 0;
        
        $html = getPDFPageStyles();
        $html .= "
        <div class='header'>
            <h1>Products Inventory Export</h1>
            <div class='subtitle'>Bakery Management System</div>
        </div>
        
        <div class='meta-info'>
            <p><strong>Export Date:</strong> " . date('F j, Y g:i A') . "</p>
            <p><strong>Total Products:</strong> " . count($products) . "</p>
            <p><strong>Total Inventory Value:</strong> " . formatCurrency($total_value) . "</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Value</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($products as $product) {
            $value = ($product['stock'] ?? 0) * ($product['price'] ?? 0);
            $html .= "
                <tr>
                    <td>#{$product['id']}</td>
                    <td>" . ($product['sku'] ?? 'N/A') . "</td>
                    <td>{$product['name']}</td>
                    <td>" . formatCurrency($product['price']) . "</td>
                    <td>" . number_format($product['stock'] ?? 0) . "</td>
                    <td><strong>" . formatCurrency($value) . "</strong></td>
                    <td>" . ($product['category'] ?? 'N/A') . "</td>
                </tr>";
        }
        
        $html .= "
                <tr class='total-row'>
                    <td colspan='5' style='text-align: right;'><strong>TOTAL VALUE:</strong></td>
                    <td colspan='2'><strong>" . formatCurrency($total_value) . "</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class='footer'>
            <p>Bakery Management System - Products Export</p>
        </div>";
        
        $filename = "Products_Export_" . date('Y-m-d') . ".pdf";
        break;
        
    case 'production':
        // Get all production records
        $production = $conn->query("
            SELECT pr.*, p.name AS product_name, u.username AS created_by_name
            FROM production pr
            JOIN products p ON pr.product_id = p.id
            LEFT JOIN users u ON pr.created_by = u.id
            ORDER BY pr.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $total_qty = $conn->query("SELECT SUM(quantity_produced) FROM production")->fetchColumn() ?? 0;
        
        $html = getPDFPageStyles();
        $html .= "
        <div class='header'>
            <h1>Production Records Export</h1>
            <div class='subtitle'>Bakery Management System</div>
        </div>
        
        <div class='meta-info'>
            <p><strong>Export Date:</strong> " . date('F j, Y g:i A') . "</p>
            <p><strong>Total Records:</strong> " . count($production) . "</p>
            <p><strong>Total Products Produced:</strong> " . number_format($total_qty) . "</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Producer</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($production as $prod) {
            $html .= "
                <tr>
                    <td>#{$prod['id']}</td>
                    <td>{$prod['product_name']}</td>
                    <td><strong>" . number_format($prod['quantity_produced']) . "</strong></td>
                    <td>" . ($prod['created_by_name'] ?? 'N/A') . "</td>
                    <td>" . date('M j, Y', strtotime($prod['created_at'])) . "</td>
                </tr>";
        }
        
        $html .= "
                <tr class='total-row'>
                    <td colspan='2' style='text-align: right;'><strong>TOTAL PRODUCED:</strong></td>
                    <td colspan='3'><strong>" . number_format($total_qty) . " </strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class='footer'>
            <p>Bakery Management System - Production Export</p>
        </div>";
        
        $filename = "Production_Export_" . date('Y-m-d') . ".pdf";
        break;
        
    case 'raw_materials':
        // Get all raw materials
        $materials = $conn->query("
            SELECT * FROM raw_materials ORDER BY name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $total_value = $conn->query("SELECT SUM(stock_quantity * unit_cost) FROM raw_materials")->fetchColumn() ?? 0;
        
        $html = getPDFPageStyles();
        $html .= "
        <div class='header'>
            <h1>Raw Materials Inventory Export</h1>
            <div class='subtitle'>Bakery Management System</div>
        </div>
        
        <div class='meta-info'>
            <p><strong>Export Date:</strong> " . date('F j, Y g:i A') . "</p>
            <p><strong>Total Materials:</strong> " . count($materials) . "</p>
            <p><strong>Total Inventory Value:</strong> " . formatCurrency($total_value) . "</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Material Name</th>
                    <th>Stock</th>
                    <th>Unit</th>
                    <th>Unit Cost</th>
                    <th>Total Value</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($materials as $material) {
            $value = ($material['stock_quantity'] ?? 0) * ($material['unit_cost'] ?? 0);
            $status = ($material['stock_quantity'] ?? 0) <= ($material['reorder_level'] ?? 0) ? '⚠️ LOW' : '✓ OK';
            
            $html .= "
                <tr>
                    <td>#{$material['id']}</td>
                    <td>{$material['name']}</td>
                    <td>" . number_format($material['stock_quantity'], 0) . "</td>
                    <td>{$material['unit']}</td>
                    <td>" . formatCurrency($material['unit_cost']) . "</td>
                    <td><strong>" . formatCurrency($value) . "</strong></td>
                    <td>" . number_format($material['reorder_level'], 0) . "</td>
                    <td>$status</td>
                </tr>";
        }
        
        $html .= "
                <tr class='total-row'>
                    <td colspan='5' style='text-align: right;'><strong>TOTAL VALUE:</strong></td>
                    <td colspan='3'><strong>" . formatCurrency($total_value) . "</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class='footer'>
            <p>Bakery Management System - Raw Materials Export</p>
        </div>";
        
        $filename = "RawMaterials_Export_" . date('Y-m-d') . ".pdf";
        break;
        
    case 'employees':
        // Get all employees
        $employees = $conn->query("
            SELECT * FROM employees ORDER BY first_name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        $html = getPDFPageStyles();
        $html .= "
        <div class='header'>
            <h1>Employees Directory Export</h1>
            <div class='subtitle'>Bakery Management System</div>
        </div>
        
        <div class='meta-info'>
            <p><strong>Export Date:</strong> " . date('F j, Y g:i A') . "</p>
            <p><strong>Total Employees:</strong> " . count($employees) . "</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Hire Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($employees as $emp) {
            $html .= "
                <tr>
                    <td>#{$emp['id']}</td>
                    <td>{$emp['first_name']} {$emp['last_name']}</td>
                    <td>" . ($emp['email'] ?? 'N/A') . "</td>
                    <td>" . ($emp['phone'] ?? 'N/A') . "</td>
                    <td>" . ($emp['position'] ?? 'N/A') . "</td>
                    <td>" . ($emp['hire_date'] ? date('M j, Y', strtotime($emp['hire_date'])) : 'N/A') . "</td>
                    <td>" . ($emp['status'] ?? 'active') . "</td>
                </tr>";
        }
        
        $html .= "
            </tbody>
        </table>
        
        <div class='footer'>
            <p>Bakery Management System - Employees Export</p>
        </div>";
        
        $filename = "Employees_Export_" . date('Y-m-d') . ".pdf";
        break;
        
    default:
        die('Invalid export type');
}

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($filename, ['Attachment' => true]);
exit;
