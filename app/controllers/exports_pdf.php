<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;

// Get parameters
$report_type = $_GET['type'] ?? 'sales';
$date_from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['to'] ?? date('Y-m-d');
$user_id = $_GET['user_id'] ?? null;

// ==================== HELPER FUNCTIONS ====================

function getPDFStyles() {
    return "
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: left;
            margin-bottom: 30px;
            border-bottom: 3px solid #84cc16;
            padding-bottom: 15px;
        }
        .header1 {
            text-align: right;
            margin-bottom: 30px;
            border-bottom: 3px solid #84cc16;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #84cc16;
            font-size: 24px;
            margin: 0;
            font-weight: bold;
        }
        .header .subtitle {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }
        .meta-info {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .meta-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            border-bottom:1px solid black;
            color: black;
            padding: 7px 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .summary-box {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .summary-box h3 {
            margin: 0 0 10px 0;
            color: #2e7d32;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #999;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        .day-block {
            background:#f0fdf4;
            border:1px solid #86efac;
            margin:10px 0;
            padding:10px;
            border-radius:6px;

            /* Page-break only before IF needed */
            page-break-inside: avoid;
        }

        /* Product block inside each day */
        .product-block {
            background:#ecfdf5;
            border:1px solid #6ee7b7;
            padding:8px;
            margin-bottom:8px;
            border-radius:6px;
            page-break-inside: avoid;
        }
    </style>
    ";
}

function formatCurrency($amount) {
    return number_format($amount, 0) . ' Rwf';
}

function formatDate($date) {
    return date('M j, Y g:i A', strtotime($date));
}

// ======================= ADMIN PANEL ====================
// ==================== REPORT GENERATORS ====================

function generateSalesReport($conn, $date_from, $date_to) {
    // Fetch sales data
    $stmt = $conn->prepare("
        SELECT s.*, p.name AS product_name, u.username as sold_by, c.name as customer_name
        FROM sales s 
        JOIN products p ON s.product_id = p.id 
        LEFT JOIN users u ON s.created_by = u.id
        LEFT JOIN customers c ON s.customer_id = c.id
        WHERE DATE(s.created_at) BETWEEN ? AND ?
        ORDER BY s.created_at DESC
    ");
    $stmt->execute([$date_from, $date_to]);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate summary
    $total_sales = count($sales);
    $total_revenue = array_sum(array_column($sales, 'total_price'));
    $total_qty = array_sum(array_column($sales, 'qty'));
    
    $html = getPDFStyles();
    $stmte = $conn->prepare('SELECT business_name FROM system_settings LIMIT 1');
    $stmte->execute();
    $business_name = $stmte->fetchColumn();

    $logoBase64 = '';
    $logoPath = __DIR__ . '/../../logo.png';
    if (file_exists($logoPath)) {
        $logoContent = @file_get_contents($logoPath);
        if ($logoContent !== false) {
            $logoBase64 = base64_encode($logoContent);
        }
    }

    $logoImgTag = $logoBase64
        ? "<div style='float:right;'><img style='width: 100px;' src='data:image/png;base64," . $logoBase64 . "'/>
        <div class='subtitle'>" .$business_name ."</div></div>"
        : '';

    $html .= "
    <div class='header'>
        <div style='float:left;'>
        <br><br>
            <h1>Sales Report</h1><br>
            <p>Generated at:</strong> " . date('j / m / Y , g:i A') . "</p>
        </div>
        $logoImgTag
        <div style='clear:both;'></div>
    </div>
    
    <div class='summary-box'>
        <h3>Statistics</h3>
        <p><strong>Total Sales Value:</strong> " . formatCurrency($total_revenue) . "</p>
        <p><strong>Average Of Transactions:</strong> " . formatCurrency($total_sales > 0 ? $total_revenue / $total_sales : 0) . "</p>
        <p><strong>Period:</strong> " . date('j / m / Y', strtotime($date_from)) . " <strong>TO</strong> " . date('j / m / Y', strtotime($date_to)) . "</p>
    </div><br><br>
    ";
    
    if (!empty($sales)) {
        $html .= "
        <table>
            <thead>
                <tr>
                    <th style='height: 2px;'>ID</th>
                    <th style='height: 2px;'>Product</th>
                    <th style='height: 2px;'>Customer Name</th>
                    <th style='height: 2px;'>Type</th>
                    <th style='height: 2px;'>Payment</th>
                    <th style='height: 2px;'>Sold By</th>
                    <th style='height: 2px;'>Supplied By</th>
                    <th style='height: 2px;'>Date</th>
                    <th style='height: 2px;'>Qty</th>
                    <th style='height: 2px;'>Price</th>
                    <th style='height: 2px;'>Total</th>
                </tr>
            </thead>
            <tbody>";
        $i = 1 ;
        $totalsales = 0;
        foreach($sales as $sale) {
            $customer_display = !empty($sale['customer_name']) ? $sale['customer_name'] : 'Walk-in';
            $totalsales += $sale['total_price'];
            $html .= "
                <tr>
                    <td style='background:rgba(137, 81, 210, 0.7);'>{$i}</td>
                    <td style='background:rgba(137, 81, 210, 0.5);'>{$sale['product_name']}</td>
                    <td style='background:rgba(160, 135, 250, 0.5);'>{$customer_display}</td>
                    <td style='background:rgba(160, 135, 250, 0.5);'>{$sale['customer_type']}</td>
                    <td style='background:rgba(160, 135, 250, 0.5);'>{$sale['payment_method']}</td>
                    <td style='background:rgba(251, 255, 0, 0.5);'>" . ($sale['sold_by'] ?? 'N/A') . "</td>
                    <td style='background:rgba(251, 255, 0, 0.5);'>" . (
                        $sale['supplied_by'] ? 
                        $conn->query("SELECT name FROM suppliers WHERE id = {$sale['supplied_by']} LIMIT 1")->fetchColumn() :
                        'N/A'
                    ) . "</td>
                    <td style='background:rgba(251, 255, 0, 0.5);'>" . date('M j, Y', strtotime($sale['created_at'])) . "</td>
                    <td style='background:rgba(135, 206, 250, 0.5);'>{$sale['qty']}</td>
                    <td style='background:rgba(135, 206, 250, 0.5);'>" . formatCurrency($sale['unit_price']) . "</td>
                    <td style='background:rgba(78, 187, 255, 0.5);'><strong>" . formatCurrency($sale['total_price']) . "</strong></td>
                </tr>";
                $i++;
        }
        
        $html .= "
                <tr>
                    <td style='background:rgba(135, 206, 250, 0.5);'><strong>Total</strong></td>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.5);'>
                    <td style='background:rgba(135, 206, 250, 0.6);'><strong>".formatCurrency($totalsales)."</strong></td>
            </tbody>
        </table>";
    } else {
        $html .= "<div class='no-data'>No sales data found for the selected period.</div>";
    }
    
    $html .= "
    <div class='footer'>
        <p>Bakery Management System - Sales Report </p>

    </div>";
    
    return $html;
}

function generateProductionReport($conn, $date_from, $date_to) {
    // Fetch production data
    $stmt = $conn->prepare("
        SELECT pr.*, p.name AS product_name, u.username as produced_by
        FROM production pr
        JOIN products p ON pr.product_id = p.id
        LEFT JOIN users u ON pr.created_by = u.id
        WHERE DATE(pr.created_at) BETWEEN ? AND ?
        ORDER BY pr.created_at DESC
    ");
    $stmt->execute([$date_from, $date_to]);
    $productions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate summary
    $total_records = count($productions);
    $total_units = array_sum(array_column($productions, 'quantity_produced'));
    
    // Get production by product
    $stmt2 = $conn->prepare("
        SELECT p.name, SUM(pr.quantity_produced) as total_qty
        FROM production pr
        JOIN products p ON pr.product_id = p.id
        WHERE DATE(pr.created_at) BETWEEN ? AND ?
        GROUP BY pr.product_id
        ORDER BY total_qty DESC
        LIMIT 5
    ");
    $stmt2->execute([$date_from, $date_to]);
    $top_products = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total value of ingredients used (from material plans in the same period)
    $stmt3 = $conn->prepare(" 
        SELECT SUM(mo.total_value) AS total_ingredient_value
        FROM product_material_plans pmp
        JOIN material_orders mo ON mo.id = pmp.order_id
        WHERE pmp.plan_date BETWEEN ? AND ?
    ");
    $stmt3->execute([$date_from, $date_to]);
    $ingredients_data = $stmt3->fetch(PDO::FETCH_ASSOC);
    $total_ingredient_value = $ingredients_data['total_ingredient_value'] ?? 0;
    
    $html = getPDFStyles();
    $stmte = $conn->prepare('SELECT business_name FROM system_settings LIMIT 1');
    $stmte->execute();
    $business_name = $stmte->fetchColumn();

    $logoBase64 = '';
    $logoPath = __DIR__ . '/../../logo.png';
    if (file_exists($logoPath)) {
        $logoContent = @file_get_contents($logoPath);
        if ($logoContent !== false) {
            $logoBase64 = base64_encode($logoContent);
        }
    }

    $logoImgTag = $logoBase64
        ? "<div style='float:right;'><img style='width: 100px;' src='data:image/png;base64," . $logoBase64 . "'/>
        <div class='subtitle'>" . $business_name . "</div></div>"
        : '';

    $html .= "
    <div class='header'>
        <div style='float:left;'>
        <br><br>
            <h1>Production Report</h1><br>
            <p>Generated at:</strong> " . date('j / m / Y , g:i A') . "</p>
        </div>
        $logoImgTag
        <div style='clear:both;'></div>
    </div>
    
    <div class='summary-box'>
        <h3>Summary Statistics</h3>
        <p><strong>Value Of Used Ingredients:</strong> " . formatCurrency($total_ingredient_value) . "</p>
        <p><strong>Number Of Productions:</strong> $total_records</p>
        
    </div>
    ";
       
    // Top products
    if (!empty($top_products)) {
        $html .= "
        <h3 style='color: #84cc16; margin: 20px 0 10px 0;'>Top Products Produced</h3>
        <table style='margin-bottom: 30px;'>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th style='text-align: center;'>Total Quantity</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach($top_products as $product) {
            $html .= "
                <tr>
                    <td style='background:rgba(78, 187, 255, 0.5);'>{$product['name']}</td>
                    <td style='text-align: center; background:rgba(144, 113, 255, 0.5);'><strong style='text-align: center;'> ". number_format($product['total_qty']) . "</strong></td>
                </tr>";
        }
        
        $html .= "
            </tbody>
        </table><br><br><br><br><br><br><br><br><br><br><br><br><br>";
    }
    
    // Grouped detailed records by date and product
    if (!empty($productions)) {
            // Build grouped structure: [date][product_id]
            $grouped = [];
            foreach ($productions as $prod) {
                $dateKey = date('Y-m-d', strtotime($prod['created_at']));
                $date = $dateKey;
                $pid = (int)$prod['product_id'];
                if (!isset($grouped[$dateKey])) {
                    $grouped[$dateKey] = [];
                }
                if (!isset($grouped[$dateKey][$pid])) {
                    $grouped[$dateKey][$pid] = [
                        'product_name' => $prod['product_name'],
                        'total_qty'    => 0,
                        'batches'      => [],
                        'materials'    => [],
                    ];
                }

                $grouped[$dateKey][$pid]['total_qty'] += (int)$prod['quantity_produced'];
                $grouped[$dateKey][$pid]['batches'][] = [
                    'id'       => $prod['id'],
                    'qty'      => $prod['quantity_produced'],
                    'time'     => date('H:i', strtotime($prod['created_at'])),
                    'producer' => $prod['produced_by'] ?? 'N/A',
                    'materials'=> $prod['raw_materials_used'],
                ];

                if (!empty($prod['raw_materials_used'])) {
                    $grouped[$dateKey][$pid]['materials'][] = $prod['raw_materials_used'];
                }
            }

            $dates = array_keys($grouped);
            $date_count = count($dates);
            
            // Calculate approximate content height needed for each day
            // This assumes each product entry takes about 150px and each day header takes 50px
            $current_page_height = 0;
            $max_page_height = 1000; // Adjust this based on your page size
            
            $html .= "
    <h3 style='color:#3b82f6; margin-top:20px;'>Detailed Production Records (Grouped by Date & Product)</h3>
    ";

    foreach ($grouped as $dateKey => $productsByDate) {

        // Day container — avoids page breaking inside
        $html .= "
        <div class='day-block'>
            <div style='display:flex; justify-content:space-between; font-size:13px;'>
                <strong>" . date('l', strtotime($dateKey)) . "</strong>
                <span>" . date('M j, Y', strtotime($dateKey)) . "</span>
            </div>
            <hr style='margin:6px 0; border:0; border-top:1px solid #a7f3d0;'>
        ";

        foreach ($productsByDate as $productId => $info) {

            $html .= "
            <div class='product-block'>
                <div style='font-weight:bold; font-size:12px; margin-bottom:4px;'>
                    " . htmlspecialchars($info['product_name']) . "
                </div>

                <div style='font-size:11px; margin-bottom:6px;'>
                    Total Produced: <strong>" . number_format($info['total_qty']) . "</strong>
                </div>
            ";

            if (!empty($info['materials'])) {
                $html .= "<div style='font-size:10px;'><strong>Materials Used:</strong><br>";
                foreach ($info['materials'] as $mat) {
                    // Each $mat is a comma-separated string of materials like "Flour: 10kg, Sugar: 5kg"
                    $parts = preg_split('/\s*,\s*/', $mat, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($parts as $part) {
                        // Split into name and value on the first colon, so we can align nicely
                        $name = $part;
                        $value = '';
                        $colonPos = strpos($part, ':');
                        if ($colonPos !== false) {
                            $name = trim(substr($part, 0, $colonPos));
                            $value = trim(substr($part, $colonPos + 1));
                        }

                        $nameShort = strlen($name) > 120 ? substr($name, 0, 120) . "..." : $name;
                        $valueShort = strlen($value) > 60 ? substr($value, 0, 60) . "..." : $value;

                        $moneyText = '';
                        if ($valueShort !== '') {
                            if (preg_match('/^([0-9]+(?:\.[0-9]+)?)/', $value, $mQty)) {
                                $qtyNum = (float)$mQty[1];
                                $stmtCost = $conn->prepare("SELECT unit_cost FROM raw_materials WHERE name = ? LIMIT 1");
                                $stmtCost->execute([$name]);
                                $unitCost = $stmtCost->fetchColumn();
                                if ($unitCost !== false) {
                                    $moneyVal = $qtyNum * (float)$unitCost;
                                    $moneyText = formatCurrency($moneyVal);
                                }
                            }
                        }

                        $html .= "&bull; <span style='display:inline-block; min-width:120px; margin-right:10px;'>" . htmlspecialchars($nameShort) . "</span>";
                        if ($valueShort !== '') {
                            $html .= "<span style='display:inline-block; margin-right:10px;'>" . htmlspecialchars($valueShort) . "</span>";
                        }
                        if ($moneyText !== '') {
                            $html .= "<span style='display:inline-block;'>" . htmlspecialchars($moneyText) . "</span>";
                        }
                        $html .= "<br>";
                    }
                }
                $html .= "</div>";
            }

            $html .= "</div>"; // end product-block
        }

        $html .= "</div>"; // end day-block
    }

    } else {
        $html .= "<div class='no-data'>No production records found for the selected period.</div>";
    }
    
    $html .= "
    <div class='footer'>
        <p>" . $business_name . " - Production Report</p>
    </div>";
    
    return $html;
}

function generateInventoryReport($conn, $date_from, $date_to) {
    // Fetch products inventory
    $products = $conn->query("SELECT * FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch raw materials
    $materials = $conn->query("SELECT * FROM raw_materials ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate totals
    $total_product_stock = array_sum(array_column($products, 'stock'));
    $total_product_value = 0;
    foreach($products as $p) {
        $total_product_value += $p['stock'] * $p['price'];
    }
    
    $total_material_value = 0;
    foreach($materials as $m) {
        $total_material_value += $m['stock_quantity'] * $m['unit_cost'];
    }
    
    // Calculate value of ingredients used during the selected period (from material plans)
    $usedStmt = $conn->prepare(" 
        SELECT SUM(mo.total_value) AS total_ingredient_value
        FROM product_material_plans pmp
        JOIN material_orders mo ON mo.id = pmp.order_id
        WHERE pmp.plan_date BETWEEN ? AND ?
    ");
    $usedStmt->execute([$date_from, $date_to]);
    $usedRow = $usedStmt->fetch(PDO::FETCH_ASSOC);
    $used_ingredients_value = $usedRow['total_ingredient_value'] ?? 0;
    
    $stmte = $conn->prepare('SELECT business_name FROM system_settings LIMIT 1');
    $stmte->execute();
    $business_name = $stmte->fetchColumn();
    
    $html = getPDFStyles();
    $stmte = $conn->prepare('SELECT business_name FROM system_settings LIMIT 1');
    $stmte->execute();
    $business_name = $stmte->fetchColumn();

    $logoBase64 = '';
    $logoPath = __DIR__ . '/../../logo.png';
    if (file_exists($logoPath)) {
        $logoContent = @file_get_contents($logoPath);
        if ($logoContent !== false) {
            $logoBase64 = base64_encode($logoContent);
        }
    }

    $logoImgTag = $logoBase64
        ? "<div style='float:right;'><img style='width: 100px;' src='data:image/png;base64," . $logoBase64 . "'/>
        <div class='subtitle'>" .$business_name ."</div></div>"
        : '';

    $html .= "
    <div class='header'>
        <div style='float:left;'>
        <br><br>
            <h1>Stock Report</h1><br>
            <p>Generated at:</strong> " . date('j / m / Y , g:i A') . "</p>
        </div>
        $logoImgTag
        <div style='clear:both;'></div>
    </div>
    
    <div class='summary-box'>
        <h3>Inventory Summary</h3>
        <p><strong>Value Of Used Ingredients:</strong> " . formatCurrency($used_ingredients_value) . "</p>
        <p><strong>Value Of Ingredients In Stock:</strong> " . formatCurrency($total_material_value) . "</p>
        <p><strong>Value Of Products In Stock:</strong> " . formatCurrency($total_product_value) . "</p>
        <p><strong>Value Of Products In Stock:</strong> " . formatCurrency($total_product_value + $total_material_value) . "</p>
        <p><strong>Period:</strong> " . date('j / m / Y', strtotime($date_from)) . " <strong>TO</strong> " . date('j / m / Y', strtotime($date_to)) . "</p>
    </div>";
    
    // Products table
    $html .= "
    <h3 style='color: #84cc16; margin: 20px 0 10px 0;'>Products In Stock</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Value</th>
            </tr>
        </thead>
        <tbody>";
    $i=1;
    foreach($products as $product) {
        $value = $product['stock'] * $product['price'];
        $html .= "
            <tr>
                <td style='background:rgba(160, 135, 250, 0.7);'>{$i}</td>
                <td style='background:rgba(160, 135, 250, 0.5);'>{$product['name']}</td>
                <td style='background:rgba(160, 135, 250, 0.5);'>{$product['sku']}</td>
                <td style='background:rgba(135, 206, 250, 0.5);'>" . number_format($product['stock']) . "</td>
                <td style='background:rgba(135, 206, 250, 0.5);'>" . formatCurrency($product['price']) . "</td>
                <td style='background:rgba(78, 187, 255, 0.5);'><strong>" . formatCurrency($value) . "</strong></td>
            </tr>";
            $i++;
    }
    
    $html .= "
        <tr style='border-top:1px solid black;'>
            <td style='background:rgba(135, 206, 250, 0.7);'><strong>Total</td>
            <td style='background:rgba(135, 206, 250, 0.7);'><td style='background:rgba(135, 206, 250, 0.7);'><td style='background:rgba(135, 206, 250, 0.7);'><td style='background:rgba(135, 206, 250, 0.7);'>
            <td style='background:rgba(135, 206, 250, 0.7);'><strong><b>" . formatCurrency($total_product_value) . "</b></td>
        </tr>
        </tbody>
    </table>";
    
    // Materials table
    $html .= "
    <h3 style='color: #84cc16; margin: 20px 0 10px 0;page-break-before: always;'>Raw Materials In Stock</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Material Name</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Total Value</th>
            </tr>
        </thead>
        <tbody>";
    $i=1;
    foreach($materials as $material) {
        $value = $material['stock_quantity'] * $material['unit_cost'];
        $html .= "
            <tr>
                <td style='background:rgba(160, 135, 250, 0.7);'>{$i}</td>
                <td style='background:rgba(160, 135, 250, 0.5);'>{$material['name']}</td>
                <td style='background:rgba(160, 135, 250, 0.5);'>" . number_format($material['stock_quantity'], 0) .' '.$material['unit'] . "</td>
                <td style='background:rgba(135, 206, 250, 0.5);'>" . formatCurrency($material['unit_cost']) . "</td>
                <td style='background:rgba(78, 187, 255, 0.5);'><strong>" . formatCurrency($value) . "</strong></td>
            </tr>";
            $i++;
    }
    
    $html .= "
        <tr style='border-top:1px solid black;'>
            <td style='background:rgba(135, 206, 250, 0.7);'><strong>Total</td>
            <td style='background:rgba(135, 206, 250, 0.7);'><td style='background:rgba(135, 206, 250, 0.7);'><td style='background:rgba(135, 206, 250, 0.7);'>
            <td style='background:rgba(135, 206, 250, 0.7);'><strong><b>" . formatCurrency($total_material_value) . "</b></td>
        </tr>
        </tbody>
    </table>";
    
    $html .= "
    <br><br>
    <div class='footer'>
        <p>" . $business_name . " - Inventory Report</p>
    </div>";
    
    return $html;
}

function generateEmployeeReport($conn, $date_from, $date_to) {
    // Fetch employees from the 'employess' table
    try {
$employees = $conn->prepare("
    SELECT
        e.*,
        COALESCE(COUNT(DISTINCT CASE 
            WHEN DATE(p.created_at) BETWEEN ? AND ? THEN p.id 
        END), 0) AS total_productions,
        COALESCE(COUNT(DISTINCT CASE 
            WHEN DATE(s.created_at) BETWEEN ? AND ? THEN s.id 
        END), 0) AS total_sales
    FROM employees e
    LEFT JOIN users u ON u.email = e.email
    LEFT JOIN production p ON p.created_by = u.id
    LEFT JOIN sales s ON (s.created_by = u.id OR s.sold_by = u.id)
    WHERE e.position NOT IN ('admin')
    GROUP BY e.id
    ORDER BY e.created_at DESC
");
    $employees->execute([$date_from, $date_to, $date_from, $date_to]);
    $employees = $employees->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Return minimal HTML to avoid PDF load errors
        return "<div style='font-family: Arial, sans-serif; padding: 20px;'>
            <h2>Error Generating Employee Report</h2>
            <p>{$e->getMessage()}</p>
        </div>";
    }

    // Compute summary
    $total_employees = is_array($employees) ? count($employees) : 0;

    $html = getPDFStyles();
    $html .= "
    <div class='header'>
        <h1>Employee Report</h1>
        <div class='subtitle'>Bakery Management System</div>
    </div>

    <div class='meta-info'>
        <p><strong>Report Period:</strong> " . date('F j, Y', strtotime($date_from)) . " to " . date('F j, Y', strtotime($date_to)) . "</p>
        <p><strong>Generated:</strong> " . date('F j, Y g:i A') . "</p>
        <p><strong>Report Type:</strong> Employee List and Activity</p>
    </div>

    <div class='summary-box'>
        <h3>Summary</h3>
        <p><strong>Total Employees:</strong> " . number_format($total_employees) . "</p>
    </div>
    ";

    if (!empty($employees)) {
        $html .= "
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Productions</th>
                    <th>Sales</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($employees as $emp) {
            $username = isset($emp['first_name']) ? $emp['first_name'] : 'N/A';
            $email = isset($emp['email']) ? $emp['email'] : 'N/A';
            $role = isset($emp['position']) ? ucfirst($emp['position']) : 'N/A';
            $joined = !empty($emp['created_at']) ? date('M j, Y', strtotime($emp['created_at'])) : 'N/A';

            $html .= "
                <tr>
                    <td>#{$emp['id']}</td>
                    <td><strong>{$username}</strong></td>
                    <td>{$email}</td>
                    <td>{$role}</td>
                    <td>" . number_format((int)$emp['total_productions']) . "</td>
                    <td>" . number_format((int)$emp['total_sales']) . "</td>
                    <td>{$joined}</td>
                </tr>";
        }
        $html .= "
            </tbody>
        </table>";
    } else {
        $html .= "<p style='text-align:center; color:#666; margin-top:20px;'>No employees found.</p>";
    }

    $html .= "
    <div class='footer'>
        <p>Bakery Management System - Employee Report - Page 1</p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>";

    return $html;
}

function generateMaterialsReport($conn, $date_from, $date_to) {
    // Fetch raw materials
    $materials = $conn->query("SELECT * FROM raw_materials ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals
    $total_materials = count($materials);
    $total_value = 0.0;
    $low_stock_count = 0;

    foreach ($materials as $m) {
        $stock = (float)($m['stock_quantity'] ?? 0);
        $unit_cost = (float)($m['unit_cost'] ?? 0);
        $reorder = (float)($m['reorder_level'] ?? 0);

        $total_value += $stock * $unit_cost;
        if ($reorder > 0 && $stock < $reorder) {
            $low_stock_count++;
        }
    }

    $html = getPDFStyles();
    $html .= "
    <div class='header'>
        <h1>Raw Materials Report</h1>
        <div class='subtitle'>Bakery Management System</div>
    </div>

    <div class='meta-info'>
        <p><strong>Report Period:</strong> " . date('F j, Y', strtotime($date_from)) . " to " . date('F j, Y', strtotime($date_to)) . "</p>
        <p><strong>Generated:</strong> " . date('F j, Y g:i A') . "</p>
        <p><strong>Report Type:</strong> Raw Materials Inventory</p>
    </div>

    <div class='summary-box'>
        <h3>Materials Summary</h3>
        <p><strong>Total Materials:</strong> " . number_format($total_materials) . " types</p>
        <p><strong>Total Value:</strong> " . formatCurrency($total_value) . "</p>
        <p><strong>Low Stock Items:</strong> " . number_format($low_stock_count) . " items</p>
    </div>
    ";

    if (!empty($materials)) {
        $html .= "
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Material Name</th>
                    <th>Stock Quantity</th>
                    <th>Unit</th>
                    <th>Unit Cost</th>
                    <th>Total Value</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($materials as $material) {
            $id = (int)$material['id'];
            $name = htmlspecialchars((string)($material['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $unit = htmlspecialchars((string)($material['unit'] ?? ''), ENT_QUOTES, 'UTF-8');
            $stock = (float)($material['stock_quantity'] ?? 0);
            $unit_cost = (float)($material['unit_cost'] ?? 0);
            $reorder = (float)($material['reorder_level'] ?? 0);

            $value = $stock * $unit_cost;
            $is_low = ($reorder > 0 && $stock < $reorder);
            $status = $is_low ? 'LOW' : 'OK';

            $html .= "
                <tr>
                    <td>#{$id}</td>
                    <td><strong>{$name}</strong></td>
                    <td>" . number_format($stock, 0) . "</td>
                    <td>{$unit}</td>
                    <td>" . formatCurrency($unit_cost) . "</td>
                    <td><strong>" . formatCurrency($value) . "</strong></td>
                    <td>" . number_format($reorder, 0) . "</td>
                    <td>{$status}</td>
                </tr>";
        }

        $html .= "
            </tbody>
        </table>";
    } else {
        $html .= "<p style='text-align:center;color:#666;'>No materials found.</p>";
    }

    $html .= "
    <div class='footer'>
        <p>Bakery Management System - Raw Materials Report - Page 1</p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>";
    return $html;
}

function generateComprehensiveReport($conn, $date_from, $date_to) {
    // Get all data
    $sales = $conn->prepare("SELECT COUNT(*) as count, SUM(total_price) as revenue FROM sales WHERE DATE(created_at) BETWEEN ? AND ?");
    $sales->execute([$date_from, $date_to]);
    $sales_data = $sales->fetch(PDO::FETCH_ASSOC);
    
    $production = $conn->prepare("SELECT COUNT(*) as count, SUM(quantity_produced) as total FROM production WHERE DATE(created_at) BETWEEN ? AND ?");
    $production->execute([$date_from, $date_to]);
    $prod_data = $production->fetch(PDO::FETCH_ASSOC);
    
    $products_count = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    // $materials_count = $conn->query("SELECT COUNT(*) FROM raw_materials")->fetchColumn();
    $employees_count = $conn->query("SELECT COUNT(*) FROM employees WHERE position not in ('employee')")->fetchColumn();
    
    $product_value = $conn->query("SELECT SUM(stock * price) FROM products")->fetchColumn() ?? 0;
    $material_value = $conn->query("SELECT SUM(stock_quantity * unit_cost) FROM raw_materials")->fetchColumn() ?? 0;
    
    $html = getPDFStyles();
    $html .= "
    <div class='header'>
        <h1> Comprehensive Business Report</h1>
        <div class='subtitle'>Bakery Management System</div>
    </div>
    
    <div class='meta-info'>
        <p><strong>Report Period:</strong> " . date('F j, Y', strtotime($date_from)) . " to " . date('F j, Y', strtotime($date_to)) . "</p>
        <p><strong>Generated:</strong> " . date('F j, Y g:i A') . "</p>
        <p><strong>Report Type:</strong> Complete Business Overview</p>
    </div>
    
    <div class='summary-box'>
        <h3>Business Summary</h3>
        <table style='border: none; margin: 0;'>
            <tr style='background: transparent; border: none;'>
                <td style='border: none; padding: 5px;'><strong>Sales Transactions:</strong></td>
                <td style='border: none; padding: 5px;'>" . number_format($sales_data['count']) . "</td>
                <td style='border: none; padding: 5px;'><strong>Total Income:</strong></td>
                <td style='border: none; padding: 5px;'>" . formatCurrency($sales_data['revenue']) . "</td>
            </tr>
            <tr style='background: transparent; border: none;'>
                <td style='border: none; padding: 5px;'><strong>Production Records:</strong></td>
                <td style='border: none; padding: 5px;'>" . number_format($prod_data['count']) . "</td>
                <td style='border: none; padding: 5px;'><strong>Products Made:</strong></td>
                <td style='border: none; padding: 5px;'>" . number_format($prod_data['total']) . "</td>
            </tr>
            <tr style='background: transparent; border: none;'>
                <td style='border: none; padding: 5px;'><strong>Total Products:</strong></td>
                <td style='border: none; padding: 5px;'>$products_count types</td>
                <td style='border: none; padding: 5px;'><strong>Total Employees:</strong></td>
                <td style='border: none; padding: 5px;'>$employees_count staff</td>
            </tr>
            <tr style='background: transparent; border: none;'>
                <td style='border: none; padding: 5px;'><strong>Product Inventory Value:</strong></td>
                <td style='border: none; padding: 5px;'>" . formatCurrency($product_value) . "</td>
                <td style='border: none; padding: 5px;'><strong>Value Of Products In Stock:</strong></td>
                <td style='border: none; padding: 5px;'>" . formatCurrency($material_value) . "</td>
            </tr>
        </table>
    </div>";
    
    // Top selling products
    $top_sellers = $conn->prepare("
        SELECT p.name, SUM(s.qty) as total_sold, SUM(s.total_price) as revenue
        FROM sales s
        JOIN products p ON s.product_id = p.id
        WHERE DATE(s.created_at) BETWEEN ? AND ?
        GROUP BY s.product_id
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $top_sellers->execute([$date_from, $date_to]);
    $sellers = $top_sellers->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($sellers)) {
        $html .= "
        <h3 style='color: #84cc16; margin: 20px 0 10px 0;'>Top Best Selling Products</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Units Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach($sellers as $seller) {
            $html .= "
                <tr>
                    <td>{$seller['name']}</td>
                    <td>" . number_format($seller['total_sold']) . "</td>
                    <td><strong>" . formatCurrency($seller['revenue']) . "</strong></td>
                </tr>";
        }
        
        $html .= "
            </tbody>
        </table>";
    }

    // Top customers by revenue
    $top_customers_stmt = $conn->prepare("
        SELECT c.name, c.customer_type, c.phone,
               COUNT(s.id) AS orders_count,
               SUM(s.total_price) AS total_spent
        FROM customers c
        JOIN sales s ON c.id = s.customer_id
        WHERE DATE(s.created_at) BETWEEN ? AND ?
        GROUP BY c.id
        ORDER BY total_spent DESC
        LIMIT 5
    ");
    $top_customers_stmt->execute([$date_from, $date_to]);
    $top_customers = $top_customers_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($top_customers)) {
        $html .= "
        <h3 style='color: #84cc16; margin: 25px 0 10px 0;'>Top Customers (By Revenue)</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($top_customers as $cust) {
            $phone = !empty($cust['phone']) ? $cust['phone'] : 'N/A';
            $html .= "
                <tr>
                    <td><strong>{$cust['name']}</strong></td>
                    <td>{$cust['customer_type']}</td>
                    <td style='font-size: 9px;'>{$phone}</td>
                    <td>" . number_format($cust['orders_count']) . "</td>
                    <td><strong>" . formatCurrency($cust['total_spent']) . "</strong></td>
                </tr>";
        }

        $html .= "
            </tbody>
        </table>";
    }

    // Additional compact summaries from other tables
    $products_summary_total   = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $customers_summary_total  = $conn->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $employees_summary_total  = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'employee'")->fetchColumn();
    $materials_summary_total  = $conn->query("SELECT COUNT(*) FROM raw_materials")->fetchColumn();
    $sales_period_total       = $conn->query("SELECT COUNT(*) FROM sales WHERE DATE(created_at) BETWEEN '" . $date_from . "' AND '" . $date_to . "'")->fetchColumn();
    $production_period_total  = $conn->query("SELECT COUNT(*) FROM production WHERE DATE(created_at) BETWEEN '" . $date_from . "' AND '" . $date_to . "'")->fetchColumn();

    $html .= "
        <h3 style='color: #84cc16; margin: 25px 0 10px 0;'>Key Tables Overview</h3>
        <table>
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Description</th>
                    <th>Total Records</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Products</td>
                    <td>Active products in catalog</td>
                    <td>" . number_format($products_summary_total) . "</td>
                </tr>
                <tr>
                    <td>Raw Materials</td>
                    <td>Ingredients and materials tracked</td>
                    <td>" . number_format($materials_summary_total) . "</td>
                </tr>
                <tr>
                    <td>Customers</td>
                    <td>Registered customers</td>
                    <td>" . number_format($customers_summary_total) . "</td>
                </tr>
                <tr>
                    <td>Employees</td>
                    <td>System users with employee role</td>
                    <td>" . number_format($employees_summary_total) . "</td>
                </tr>
                <tr>
                    <td>Sales (period)</td>
                    <td>Sales rows in selected period</td>
                    <td>" . number_format($sales_period_total) . "</td>
                </tr>
                <tr>
                    <td>Production (period)</td>
                    <td>Production rows in selected period</td>
                    <td>" . number_format($production_period_total) . "</td>
                </tr>
            </tbody>
        </table>";

    $html .= "
    <div class='footer'>
        <p>Bakery Management System - Comprehensive Report - Page 1</p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>";
    
    return $html;
}

function generateCustomerHistoryReport($conn, $date_from, $date_to, $customer_id) {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ? LIMIT 1");
    $stmt->execute([$customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        return getPDFStyles() . "<div class='header'><h1>Customer Not Found</h1></div><div class='no-data'>The requested customer does not exist.</div>";
    }

    $salesStmt = $conn->prepare("
        SELECT s.*, p.name AS product_name
        FROM sales s
        JOIN products p ON s.product_id = p.id
        WHERE s.customer_id = ?
          AND DATE(s.created_at) BETWEEN ? AND ?
        ORDER BY s.created_at DESC
    ");
    $salesStmt->execute([$customer_id, $date_from, $date_to]);
    $sales = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

    $total_orders = count($sales);
    $total_spent = array_sum(array_column($sales, 'total_price'));

    $html = getPDFStyles();
    $stmte = $conn->prepare('SELECT business_name FROM system_settings LIMIT 1');
    $stmte->execute();
    $business_name = $stmte->fetchColumn();

    $customer_name = htmlspecialchars($customer['name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
    $customer_type = htmlspecialchars($customer['customer_type'] ?? '', ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($customer['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8');

    $html .= "
    <div class='header'>
        <h1>Customer Purchase History</h1>
        <div class='subtitle'>" . $business_name . "</div>
    </div>
    <div class='meta-info'>
        <p><strong>Customer:</strong> " . $customer_name . " (" . $customer_type . ")</p>
        <p><strong>Contact:</strong> " . $phone . "</p>
        <p><strong>Period:</strong> " . date('j / m / Y', strtotime($date_from)) . " to " . date('j / m / Y', strtotime($date_to)) . "</p>
        <p><strong>Generated:</strong> " . date('j / m / Y , g:i A') . "</p>
    </div>
    <div class='summary-box'>
        <h3>Summary</h3>
        <p><strong>Number Of Orders:</strong> " . number_format($total_orders) . "</p>
        <p><strong>Total Spent:</strong> " . formatCurrency($total_spent) . "</p>
    </div>
    ";

    if (!empty($sales)) {
        $html .= "
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>";

        $i = 1;
        foreach ($sales as $row) {
            $html .= "
                <tr>
                    <td>" . $i . "</td>
                    <td>" . date('M j, Y', strtotime($row['created_at'])) . "</td>
                    <td>" . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . number_format($row['qty']) . "</td>
                    <td>" . formatCurrency($row['unit_price']) . "</td>
                    <td><strong>" . formatCurrency($row['total_price']) . "</strong></td>
                    <td>" . htmlspecialchars($row['payment_method'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>";
            $i++;
        }

        $html .= "
            </tbody>
        </table>";
    } else {
        $html .= "<div class='no-data'>No purchases found for this customer in the selected period.</div>";
    }

    $html .= "
    <div class='footer'>
        <p>" . $business_name . " - Customer History Report</p>
    </div>";

    return $html;
}

function generateEmployeeProductionReport($conn, $date_from, $date_to, $user_id) {
    $stmt = $conn->prepare("
        SELECT pr.id, pr.product_id, p.name AS product_name, pr.quantity_produced, DATE_FORMAT(pr.created_at, '%Y-%m-%d') AS created_at
        FROM production pr
        JOIN products p ON pr.product_id = p.id
        WHERE pr.created_by = ? AND DATE(pr.created_at) BETWEEN ? AND ?
    ");
    $stmt->execute([$user_id, $date_from, $date_to]);
    $productions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt2 = $conn->prepare("
        SELECT s.id, s.product_id, p.name AS product_name, s.qty, s.total_price, DATE_FORMAT(s.created_at, '%Y-%m-%d') AS created_at
        FROM sales s
        JOIN products p ON s.product_id = p.id
        WHERE (s.sold_by = ? OR s.created_by = ?) AND DATE(s.created_at) BETWEEN ? AND ?
    ");
    $stmt2->execute([$user_id, $user_id, $date_from, $date_to]);
    $sales = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    $user = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $user->execute([$user_id]);
    $username = $user->fetchColumn();
    
    $total_records = count($productions) + count($sales);
    $total_units = array_sum(array_column($productions, 'quantity_produced')) + array_sum(array_column($sales, 'qty'));
    
    $html = getPDFStyles();
    $html .= "
    <div class='header'>
        <h1>👤 My Production Report</h1>
        <div class='subtitle'>Employee: $username</div>
    </div>
    
    <div class='meta-info'>
        <p><strong>Report Period:</strong> " . date('F j, Y', strtotime($date_from)) . " to " . date('F j, Y', strtotime($date_to)) . "</p>
        <p><strong>Generated:</strong> " . date('F j, Y g:i A') . "</p>
        <p><strong>Employee:</strong> $username (ID: $user_id)</p>
    </div>
    
    <div class='summary-box'>
        <h3>My Performance</h3>
        <p><strong>Production Records:</strong> " . count($productions) . "</p>
        <p><strong>Sales Records:</strong> " . count($sales) . "</p>
        <p><strong>Total Records:</strong> $total_records</p>
        <p><strong>Total Units Produced:</strong> " . number_format($total_units) . " units</p>
        <p><strong>Average Per Record:</strong> " . number_format($total_records > 0 ? $total_units / $total_records : 0, 1) . " units</p>
    </div>
    ";
    
    if (!empty($productions) || !empty($sales)) {
        $html .= "
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach(array_merge($productions, $sales) as $record) {
            $html .= "
                <tr>
                    <td>#{$record['id']}</td>
                    <td>" . ($record['product_id'] ? 'Sale' : 'Production') . "</td>
                    <td>{$record['product_name']}</td>
                    <td><strong>" . number_format($record['product_id'] ? $record['qty'] : $record['quantity_produced']) . "</strong></td>
                    <td>" . formatDate($record['created_at']) . "</td>
                </tr>";
        }
        
        $html .= "
            </tbody>
        </table>";
    } else {
        $html .= "<div class='no-data'>No production or sales records found for the selected period.</div>";
    }
    
    $html .= "
    <div class='footer'>
        <p>Bakery Management System - Employee Production Report</p>
    </div>";
    
    return $html;
}

function generateEmployeeSalesReport($conn, $date_from, $date_to, $user_id) {
    $stmt = $conn->prepare("
        SELECT s.id, s.product_id, p.name AS product_name, s.qty, s.total_price, s.customer_name, s.customer_type, s.payment_method, DATE_FORMAT(s.created_at, '%Y-%m-%d') AS created_at
        FROM sales s
        JOIN products p ON s.product_id = p.id
        WHERE (s.sold_by = ? OR s.created_by = ?) AND DATE(s.created_at) BETWEEN ? AND ?
    ");
    $stmt->execute([$user_id, $user_id, $date_from, $date_to]);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $user = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $user->execute([$user_id]);
    $username = $user->fetchColumn();
    
    $total_sales = count($sales);
    $total_revenue = array_sum(array_column($sales, 'total_price'));
    $total_qty = array_sum(array_column($sales, 'qty'));
    
    $html = getPDFStyles();
    $html .= "
    <div class='header'>
        <h1>👤 My Sales Report</h1>
        <div class='subtitle'>Employee: $username</div>
    </div>
    
    <div class='meta-info'>
        <p><strong>Report Period:</strong> " . date('F j, Y', strtotime($date_from)) . " to " . date('F j, Y', strtotime($date_to)) . "</p>
        <p><strong>Generated:</strong> " . date('F j, Y g:i A') . "</p>
        <p><strong>Employee:</strong> $username (ID: $user_id)</p>
    </div>
    
    <div class='summary-box'>
        <h3>My Performance</h3>
        <p><strong>Sales Transactions:</strong> $total_sales</p>
        <p><strong>Total Revenue:</strong> " . formatCurrency($total_revenue) . "</p>
        <p><strong>Items Sold:</strong> " . number_format($total_qty) . " units</p>
        <p><strong>Average Transaction:</strong> " . formatCurrency($total_sales > 0 ? $total_revenue / $total_sales : 0) . "</p>
    </div>
    ";
    
    if (!empty($sales)) {
        $html .= "
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Customer Name</th>
                    <th>Type</th>
                    <th>Payment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach($sales as $sale) {
            $customer_display = !empty($sale['customer_name']) ? $sale['customer_name'] : 'Walk-in';
            $html .= "
                <tr>
                    <td>#{$sale['id']}</td>
                    <td>{$sale['product_name']}</td>
                    <td>{$sale['qty']}</td>
                    <td><strong>" . formatCurrency($sale['total_price']) . "</strong></td>
                    <td>{$customer_display}</td>
                    <td>{$sale['customer_type']}</td>
                    <td>{$sale['payment_method']}</td>
                    <td>" . date('M j, Y', strtotime($sale['created_at'])) . "</td>
                </tr>";
        }
        
        $html .= "
            </tbody>
        </table>";
    } else {
        $html .= "<div class='no-data'>No sales records found for the selected period.</div>";
    }
    
    $html .= "
    <div class='footer'>
        <p>Bakery Management System - Employee Sales Report</p>
    </div>";
    
    return $html;
}

function generateEmployeeCombinedReport($conn, $date_from, $date_to, $user_id) {
    // Get production data
    $prod_stmt = $conn->prepare("SELECT COUNT(*) as count, SUM(quantity_produced) as total FROM production WHERE created_by = ? AND DATE(created_at) BETWEEN ? AND ?");
    $prod_stmt->execute([$user_id, $date_from, $date_to]);
    $prod_data = $prod_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get sales data - use sold_by OR created_by
    $sales_stmt = $conn->prepare("SELECT COUNT(*) as count, SUM(total_price) as revenue FROM sales WHERE (sold_by = ? OR created_by = ?) AND DATE(created_at) BETWEEN ? AND ?");
    $sales_stmt->execute([$user_id, $user_id, $date_from, $date_to]);
    $sales_data = $sales_stmt->fetch(PDO::FETCH_ASSOC);
    
    $user = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $user->execute([$user_id]);
    $username = $user->fetchColumn();
    
    $html = getPDFStyles();
    $html .= "
    <div class='header'>
        <h1> My Combined Report</h1>
        <div class='subtitle'>Employee: $username</div>
    </div>
    
    <div class='meta-info'>
        <p><strong>Report Period:</strong> " . date('F j, Y', strtotime($date_from)) . " to " . date('F j, Y', strtotime($date_to)) . "</p>
        <p><strong>Generated:</strong> " . date('F j, Y g:i A') . "</p>
        <p><strong>Employee:</strong> $username (ID: $user_id)</p>
    </div>
    
    <div class='summary-box'>
        <h3>Performance Summary</h3>
        <p><strong>Production Records:</strong> " . number_format($prod_data['count']) . " | <strong>Units Produced:</strong> " . number_format($prod_data['total']) . "</p>
        <p><strong>Sales Transactions:</strong> " . number_format($sales_data['count']) . " | <strong>Revenue Generated:</strong> " . formatCurrency($sales_data['revenue']) . "</p>
    </div>
    
    <p style='text-align: center; color: #666; margin: 20px 0;'>This combined report includes both production and sales activities. Download separate reports for detailed information.</p>
    ";
    
    $html .= "
    <div class='footer'>
        <p>Bakery Management System - Employee Combined Report</p>
    </div>";
    
    return $html;
}

function generateCustomersReport($conn, $date_from, $date_to) {
    // Fetch all customers
    $stmt = $conn->prepare("
        SELECT c.*, u.username as added_by_name, SUM(s.total_price) as total_spent
        FROM customers c
        LEFT JOIN users u ON c.created_by = u.id
        LEFT JOIN sales s ON c.id = s.customer_id
        WHERE DATE(s.created_at) BETWEEN ? AND ?
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$date_from, $date_to]);
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get top buying customers
    $top_buyers = $conn->prepare("
        SELECT c.id, c.name, c.customer_type, c.phone, 
               COUNT(s.id) as total_purchases,
               SUM(s.total_price) as total_spent,
               MAX(s.created_at) as last_purchase
        FROM customers c
        LEFT JOIN sales s ON c.id = s.customer_id
        WHERE DATE(s.created_at) BETWEEN ? AND ?
        GROUP BY c.id
        ORDER BY total_spent DESC
        LIMIT 10
    ");
    $top_buyers->execute([$date_from, $date_to]);
    $top_customers = $top_buyers->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate statistics
    $total_customers = count($customers);
    $regular_count = count(array_filter($customers, fn($c) => $c['customer_type'] === 'Regular'));
    $wholesale_count = count(array_filter($customers, fn($c) => $c['customer_type'] === 'Wholesale'));
    $vip_count = count(array_filter($customers, fn($c) => $c['customer_type'] === 'VIP'));
    
    // Get total revenue from all customers
    $total_revenue_stmt = $conn->prepare("
        SELECT SUM(s.total_price) as total
        FROM sales s
        WHERE s.customer_id IS NOT NULL AND DATE(s.created_at) BETWEEN ? AND ?
    ");
    $total_revenue_stmt->execute([$date_from, $date_to]);
    $total_customer_revenue = $total_revenue_stmt->fetchColumn() ?? 0;

    $stmte = $conn->prepare("SELECT business_name FROM system_settings");
    $stmte->execute();
    $business_name = $stmte->fetchColumn();
    $html = getPDFStyles();
    $stmte = $conn->prepare('SELECT business_name FROM system_settings LIMIT 1');
    $stmte->execute();
    $business_name = $stmte->fetchColumn();

    $logoBase64 = '';
    $logoPath = __DIR__ . '/../../logo.png';
    if (file_exists($logoPath)) {
        $logoContent = @file_get_contents($logoPath);
        if ($logoContent !== false) {
            $logoBase64 = base64_encode($logoContent);
        }
    }

    $logoImgTag = $logoBase64
        ? "<div style='float:right;'><img style='width: 100px;' src='data:image/png;base64," . $logoBase64 . "'/>
        <div class='subtitle'>" .$business_name ."</div></div>"
        : '';

    $html .= "
    <div class='header'>
        <div style='float:left;'>
        <br><br>
            <h1>Customers Report</h1><br>
            <p>Generated at:</strong> " . date('j / m / Y , g:i A') . "</p>
        </div>
        $logoImgTag
        <div style='clear:both;'></div>
    </div>
    <div class='summary-box'>
       <h3>Customer Statistics</h3>
        <p><strong>Total Number Of Customers:</strong> ".$total_customers." customers</p>
        <p><strong>Total Revenue: </strong>".formatCurrency($total_customer_revenue)."</p>
        <p><strong>Period:</strong> " . date('j / m / Y', strtotime($date_from)) . "  <strong>TO</strong>  " . date('j / m / Y', strtotime($date_to)) . "</p>
    </div>
    ";
    
    // Top 10 Buyers
    if (!empty($top_customers)) {
        $html .= "
        <h3 style='color: #84cc16; margin: 20px 0 10px 0;'>Top Customers (By Revenue)</h3>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Customer Name</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Last Purchase</th>
                    <th>Total Spent</th>
                </tr>
            </thead>
            <tbody>";
        
        $rank = 1;
        foreach($top_customers as $customer) {
            $phone_display = !empty($customer['phone']) ? $customer['phone'] : 'N/A';
            $last_purchase = !empty($customer['last_purchase']) ? date('M j, Y', strtotime($customer['last_purchase'])) : 'N/A';
            $phone_msg = $phone_display == "N/A" ? "No contact" : $phone_display;

            $html .= "
                <tr>
                    <td style='background:rgba(137, 81, 210, 0.7);'>{$rank}</td>
                    <td style='background:rgba(160, 135, 250, 0.6);'><strong>{$customer['name']}</strong></td>
                    <td style='background:rgba(160, 135, 250, 0.4);'>{$customer['customer_type']}</td>
                    <td style='background:rgba(160, 135, 250, 0.5);font-size: 9px;'>{$phone_msg}</td>
                    <td style='background:rgba(160, 135, 250, 0.5);'>{$last_purchase}</td>
                    <td style='background:rgba(78, 187, 255, 0.5);'><strong>" . formatCurrency($customer['total_spent']) . "</strong></td>
                </tr>";
            $rank++;
        }
        
        $html .= "
            </tbody>
        </table>";
    }
    
    // All Customers List
    if (!empty($customers)) {
        $html .= "
        <h3 style='color: #84cc16; margin: 30px 0 10px 0;'>Complete Customer Database</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Total Spent</th>
                </tr>
            </thead>
            <tbody>";
        $i = 1;
        foreach($customers as $customer) {
            $phone_display = !empty($customer['phone']) ? $customer['phone'] : 'N/A';
            $email_display = !empty($customer['email']) ? $customer['email'] : '';
            $contact = $phone_display . ($email_display ? ' / ' . $email_display : '');
            $contact_msg = $contact == "N/A" ? "No Information" : $contact;
            $address = !empty($customer['address']) ? substr($customer['address'], 0, 30) : 'N/A';
            $address_msg = $address == "N/A" ? "No Address" : $address;
            if (strlen($customer['address'] ?? '') > 30) $address .= '...';
            
            $html .= "
                <tr>
                    <td style='background:rgba(137, 81, 210, 0.7);'>{$i}</td>
                    <td style='background:rgba(160, 135, 250, 0.6);'><strong>{$customer['name']}</strong></td>
                    <td style='background:rgba(160, 135, 250, 0.4);'>{$customer['customer_type']}</td>
                    <td style='font-size: 9px;background:rgba(160, 135, 250, 0.5);'>{$contact_msg}</td>
                    <td style='font-size: 9px;background:rgba(160, 135, 250, 0.5);'>{$address_msg}</td>
                    <td style='background:rgba(78, 187, 255, 0.5);'><strong>" . formatCurrency($customer['total_spent']) . "</strong></td>
                </tr>";
                $i++;
        }
        $html .= "
            </tbody>
        </table>";
    } else {
        $html .= "<div class='no-data'>No customers found in the database.</div>";
    }
    
    $html .= "
    <div class='footer'>
        <p>".$business_name." - Customers Analytics Report</p>
    </div>";
    
    return $html;
}

// ==================== ROUTING LOGIC ====================

$html = '';

switch ($report_type) {
    case 'sales':
        $html = generateSalesReport($conn, $date_from, $date_to);
        $filename = "Sales_Report_{$date_from}_to_{$date_to}.pdf";
        break;
    
    case 'production':
        $html = generateProductionReport($conn, $date_from, $date_to);
        $filename = "Production_Report_{$date_from}_to_{$date_to}.pdf";
        break;
    
    case 'inventory':
        $html = generateInventoryReport($conn, $date_from, $date_to);
        $filename = "Inventory_Report_{$date_from}_to_{$date_to}.pdf";
        break;
    
    case 'employees':
        $html = generateEmployeeReport($conn, $date_from, $date_to);
        $filename = "Employee_Report_{$date_from}_to_{$date_to}.pdf";
        break;
    
    case 'materials':
        $html = generateMaterialsReport($conn, $date_from, $date_to);
        $filename = "Materials_Report_{$date_from}_to_{$date_to}.pdf";
        break;
    
    case 'comprehensive':
        $html = generateComprehensiveReport($conn, $date_from, $date_to);
        $filename = "Comprehensive_Report_{$date_from}_to_{$date_to}.pdf";
        break;
    
    case 'customers':
        $html = generateCustomersReport($conn, $date_from, $date_to);
        $filename = "Customers_Report_{$date_from}_to_{$date_to}.pdf";
        break;

    case 'customer_history':
        $cid = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
        if ($cid > 0) {
            $html = generateCustomerHistoryReport($conn, $date_from, $date_to, $cid);
            $filename = "Customer_{$cid}_History_{$date_from}_to_{$date_to}.pdf";
        } else {
            $html = getPDFStyles() . "<div class='header'><h1>⚠️ Error</h1></div><div class='no-data'>Customer ID is required for this report.</div>";
            $filename = "Error_Report.pdf";
        }
        break;
    
    // Employee reports
    case 'employee_production':
        if ($user_id) {
            $html = generateEmployeeProductionReport($conn, $date_from, $date_to, $user_id);
            $filename = "My_Production_Report_{$date_from}_to_{$date_to}.pdf";
        } else {
            $html = getPDFStyles() . "<div class='header'><h1>⚠️ Error</h1></div><div class='no-data'>User ID is required for employee reports.</div>";
            $filename = "Error_Report.pdf";
        }
        break;
    
    case 'employee_sales':
        if ($user_id) {
            $html = generateEmployeeSalesReport($conn, $date_from, $date_to, $user_id);
            $filename = "My_Sales_Report_{$date_from}_to_{$date_to}.pdf";
        } else {
            $html = getPDFStyles() . "<div class='header'><h1>⚠️ Error</h1></div><div class='no-data'>User ID is required for employee reports.</div>";
            $filename = "Error_Report.pdf";
        }
        break;
    
    case 'employee_combined':
        if ($user_id) {
            $html = generateEmployeeCombinedReport($conn, $date_from, $date_to, $user_id);
            $filename = "My_Combined_Report_{$date_from}_to_{$date_to}.pdf";
        } else {
            $html = getPDFStyles() . "<div class='header'><h1>⚠️ Error</h1></div><div class='no-data'>User ID is required for employee reports.</div>";
            $filename = "Error_Report.pdf";
        }
        break;
    
    default:
        $html = generateSalesReport($conn, $date_from, $date_to);
        $filename = "Sales_Report_{$date_from}_to_{$date_to}.pdf";
        break;
}

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($filename, ['Attachment' => false]);
exit;
