<?php
// if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
// require_once __DIR__ . '/../../config/config.php';

// // === Basic Stats ===
// $total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
// $total_employees = $conn->query("SELECT COUNT(*) FROM employees")->fetchColumn();
// $total_sales = $conn->query("SELECT SUM(total_price) FROM sales")->fetchColumn() ?? 0;
// $toal_stock = $conn->query("SELECT SUM(stock) FROM products")->fetchColumn() ?? 0;

// $today = date('Y-m-d');
// $todayProductsQuery = $conn->prepare("SELECT COUNT(*) as added_today FROM products WHERE DATE(created_at) = :today");
// $todayProductsQuery->execute(['today' => $today]);
// $productsToday = $todayProductsQuery->fetch(PDO::FETCH_ASSOC)['added_today'];

// $thisMonth = date('Y-m'); // e.g., 2025-10
// $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE_FORMAT(created_at, '%Y-%m') = :month");
// $stmt->execute(['month' => $thisMonth]);
// $totalThisMonth = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// $lastMonth = date('Y-m', strtotime('-1 month'));
// $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE_FORMAT(created_at, '%Y-%m') = :month");
// $stmt->execute(['month' => $lastMonth]);
// $totalLastMonth = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// if ($totalLastMonth > 0) {
//     $growth = (($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100;
// } else {
//     $growth = 100; // if no sales last month, treat as 100% growth
// }
// $growthFormatted = number_format($growth, 1); // e.g., 12.3%


// $totalEmployeesQuery = $conn->query("SELECT COUNT(*) as total FROM employees");
// $totalEmployees = $totalEmployeesQuery->fetch(PDO::FETCH_ASSOC)['total'];
// $today = date('Y-m-d');
// $presentQuery = $conn->prepare("SELECT COUNT(*) as present_today FROM attendance WHERE date = :today AND status = 'present'");
// $presentQuery->execute(['today' => $today]);
// $presentToday = $presentQuery->fetch(PDO::FETCH_ASSOC)['present_today'];
// $statusMessage = ($presentToday == $totalEmployees) ? "All present" : "$presentToday present";


if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

// === Basic Stats ===
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_employees = $conn->query("SELECT COUNT(*) FROM employees")->fetchColumn();
$total_sales = $conn->query("SELECT SUM(revenue) FROM product_daily_stats")->fetchColumn() ?? 0;
$toal_stock = $conn->query("SELECT SUM(stock) FROM products")->fetchColumn() ?? 0;

$today = date('Y-m-d');
$todayProductsQuery = $conn->prepare("SELECT COUNT(*) as added_today FROM products WHERE DATE(created_at) = :today");
$todayProductsQuery->execute(['today' => $today]);
$productsToday = $todayProductsQuery->fetch(PDO::FETCH_ASSOC)['added_today'];

$thisMonth = date('Y-m'); // e.g., 2025-10
$stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE DATE_FORMAT(stat_date, '%Y-%m') = :month");
$stmt->execute(['month' => $thisMonth]);
$totalThisMonth = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$lastMonth = date('Y-m', strtotime('-1 month'));
$stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE DATE_FORMAT(stat_date, '%Y-%m') = :month");
$stmt->execute(['month' => $lastMonth]);
$totalLastMonth = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

if ($totalLastMonth > 0) {
    $growth = (($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100;
} else {
    $growth = 100; // if no sales last month, treat as 100% growth
}
$growthFormatted = number_format($growth, 1); // e.g., 12.3%

// === CORRECTED ATTENDANCE LOGIC ===
$totalEmployeesQuery = $conn->query("SELECT COUNT(*) as total FROM employees WHERE status = 'Active'");
$totalEmployees = $totalEmployeesQuery->fetch(PDO::FETCH_ASSOC)['total'];

$today = date('Y-m-d');
$presentQuery = $conn->prepare("
    SELECT COUNT(DISTINCT a.employee_id) as present_today 
    FROM attendance a 
    INNER JOIN employees e ON a.employee_id = e.id 
    WHERE a.date = :today 
    AND a.status = 'present'
    AND e.status = 'Active'
");
$presentQuery->execute(['today' => $today]);
$presentToday = $presentQuery->fetch(PDO::FETCH_ASSOC)['present_today'];

// Calculate attendance percentage
$attendancePercentage = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;

// Set status message based on attendance
if ($presentToday == $totalEmployees) {
    $statusMessage = "All present";
    $statusColor = "text-green-400";
} elseif ($presentToday == 0) {
    $statusMessage = "No attendance recorded";
    $statusColor = "text-yellow-400";
} else {
    // Add this to get absent count
    $absentQuery = $conn->prepare("
        SELECT COUNT(DISTINCT a.employee_id) as absent_today 
        FROM attendance a 
        INNER JOIN employees e ON a.employee_id = e.id 
        WHERE a.date = :today 
        AND a.status = 'absent'
        AND e.status = 'Active'
    ");
    $absentQuery->execute(['today' => $today]);
    $absentToday = $absentQuery->fetch(PDO::FETCH_ASSOC)['absent_today'];

    $statusMessage = "$presentToday/$totalEmployees present (" . number_format($attendancePercentage, 1) . "%)";
    $statusColor = $attendancePercentage >= 80 ? "text-green-400" : "text-yellow-400";
}

// === Sales by Product ===

// Function to get product sales by time period
function getProductSalesData($conn, $range) {
    $labels = [];
    $sales = [];
    
    $products = $conn->query("SELECT id, name FROM products")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $labels[] = $product['name'];
        
        switch ($range) {
            case 'daily':
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE product_id = :pid AND stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
                break;
            case 'weekly':
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE product_id = :pid AND stat_date >= DATE_SUB(CURDATE(), INTERVAL 6 WEEK)");
                break;
            case 'monthly':
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE product_id = :pid AND stat_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)");
                break;
            case 'yearly':
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE product_id = :pid AND stat_date >= DATE_SUB(CURDATE(), INTERVAL 6 YEAR)");
                break;
            default: // all time
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE product_id = :pid");
                break;
        }
        
        $stmt->execute(['pid' => $product['id']]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $sales[] = $total;
    }
    
    return ['labels' => $labels, 'sales' => $sales];
}

// Get product sales for all time periods
$productSalesDaily = getProductSalesData($conn, 'daily');
$productSalesWeekly = getProductSalesData($conn, 'weekly');
$productSalesMonthly = getProductSalesData($conn, 'monthly');
$productSalesYearly = getProductSalesData($conn, 'yearly');

// Keep original for backward compatibility
$productLabels = $productSalesMonthly['labels'];
$productSales = $productSalesMonthly['sales'];

function getRevenueData($conn, $range) {
    $labels = [];
    $data = [];

    switch ($range) {
        case 'daily':
            for ($i = 6; $i >= 0; $i--) {
                $day = date('Y-m-d', strtotime("-$i day"));
                $labels[] = date('D', strtotime($day));
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE stat_date = :day");
                $stmt->execute(['day' => $day]);
                $data[] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            }
            break;

        case 'weekly':
            for ($i = 5; $i >= 0; $i--) {
                $start = date('Y-m-d', strtotime("last sunday -$i week"));
                $end = date('Y-m-d', strtotime("next saturday -$i week"));
                $labels[] = "Wk ".date('W', strtotime($start));
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE stat_date BETWEEN :start AND :end");
                $stmt->execute(['start' => $start, 'end' => $end]);
                $data[] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            }
            break;

        case 'monthly':
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i month"));
                $labels[] = date('M', strtotime($month.'-01'));
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE DATE_FORMAT(stat_date, '%Y-%m') = :month");
                $stmt->execute(['month' => $month]);
                $data[] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            }
            break;

        case 'yearly':
            for ($i = 5; $i >= 0; $i--) {
                $year = date('Y', strtotime("-$i year"));
                $labels[] = $year;
                $stmt = $conn->prepare("SELECT SUM(revenue) as total FROM product_daily_stats WHERE DATE_FORMAT(stat_date, '%Y') = :year");
                $stmt->execute(['year' => $year]);
                $data[] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            }
            break;
    }

    return ['labels' => $labels, 'data' => $data];
}

// Prepare JSON data for JS
$revenueDaily = getRevenueData($conn, 'daily');
$revenueWeekly = getRevenueData($conn, 'weekly');
$revenueMonthly = getRevenueData($conn, 'monthly');
$revenueYearly = getRevenueData($conn, 'yearly');

// Function to log activities safely
function logActivity($conn, $userId, $action, $type, $quantityChange = null, $amount = null, $meta = null) {
    $stmt = $conn->prepare("
        INSERT INTO logs (user_id, action, type, quantity_change, amount, meta, created_at)
        VALUES (:user_id, :action, :type, :quantity_change, :amount, :meta, NOW())
    ");

    $stmt->execute([
        'user_id' => $userId,
        'action' => $action,
        'type' => $type,
        'quantity_change' => $quantityChange,
        'amount' => $amount,
        'meta' => $meta
    ]);
}

// recent activities

// --- Log recent activities ---

// // 1. Raw material added
// logActivity($conn, 1, "Raw material added", "raw_material", 50, null, "Flour");

// // 2. Product sold
// logActivity($conn, 1, "Product sold", "product_sold", null, 45.99, "Bread");

// // 3. Product made
// logActivity($conn, 1, "Product made", "product_made", 20, null, "Chocolate Cake");

// // 4. Special activity
// logActivity($conn, 1, "Special discount applied", "special", null, null, "Black Friday Sale");

// // --- Fetch recent activities ---
$recentActivities = $conn->query("
    SELECT action, type, quantity_change, amount, meta, created_at
    FROM logs
    ORDER BY created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// // --- Map icons ---
$iconMap = [
    'raw_material' => '🟢',
    'product_sold' => '🔵',
    'product_made' => '🟡',
    'special' => '🟣'
];



// === Low Stock Items ===
$low_stock = $conn->query("SELECT name, stock FROM products WHERE stock < 10")->fetchAll();
$low_stock_count = $pdo->query('SELECT COUNT(*) FROM raw_materials WHERE stock_quantity <= low_threshold')->fetchColumn();
$today_production = $conn->query("SELECT SUM(produced) FROM product_daily_stats WHERE stat_date = CURDATE()")->fetchColumn();
//make selection for raw materials in stock currently
$raw_materials = $conn->query("SELECT COUNT(*) FROM raw_materials WHERE stock_quantity > 0")->fetchColumn();

// New quick metrics: material orders and production records (new tables)
$today_material_orders = 0;
$today_materials_value = 0;
$today_production_records = 0;
try {
    $today_material_orders = $conn->query("SELECT COUNT(*) FROM material_orders WHERE DATE(order_date) = CURDATE()")->fetchColumn();
    $today_materials_value = $conn->query("SELECT SUM(total_value) FROM material_orders WHERE DATE(order_date) = CURDATE()")->fetchColumn() ?: 0;
    $today_production_records = $conn->query("SELECT SUM(quantity) FROM production_records WHERE DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;
} catch (Exception $e) {
    // tables may not exist yet on older installations; ignore errors
}

// === Sales Trend (last 7 days) ===
$sales_trend_stmt = $conn->query("
    SELECT stat_date AS sale_date, SUM(revenue) AS total
    FROM product_daily_stats
    WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY stat_date
    ORDER BY sale_date ASC
");
$sales_trend = $sales_trend_stmt->fetchAll(PDO::FETCH_ASSOC);

// === Export full business report (CSV or PDF) ===
if (isset($_GET['action']) && $_GET['action'] === 'export_report') {
    // helper to format amounts without trailing zeros and append currency
    $fmtAmount = function($v) {
        $n = floatval($v ?: 0);
        // format with up to 2 decimals then trim trailing zeros
        $s = number_format($n, 2, '.', '');
        $s = rtrim(rtrim($s, '0'), '.');
        return $s . ' Frw';
    };

    // gather snapshot data
    $report = [];
    $report['generated_at'] = date('Y-m-d H:i:s');
    $report['totals'] = [
        'total_products' => $total_products ?? $conn->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'total_employees' => $total_employees ?? $conn->query("SELECT COUNT(*) FROM employees")->fetchColumn(),
        'total_sales_amount' => $total_sales ?? $conn->query("SELECT SUM(revenue) FROM product_daily_stats")->fetchColumn(),
        'total_stock_units' => $toal_stock ?? $conn->query("SELECT SUM(stock) FROM products")->fetchColumn(),
        'today_production' => $today_production ?? $conn->query("SELECT SUM(produced) FROM product_daily_stats WHERE stat_date = CURDATE()")->fetchColumn(),
    ];

    // low stock products (threshold 50)
    $low_stock_items = $conn->query("SELECT id, name, stock FROM products WHERE stock <= 50 ORDER BY stock ASC")->fetchAll(PDO::FETCH_ASSOC);
    $report['low_stock'] = $low_stock_items;

    // low stock raw materials 
    $low_stock_raw_materials = $conn->query("SELECT id, name, stock_quantity , unit , low_threshold FROM raw_materials WHERE stock_quantity <= low_threshold ORDER BY stock_quantity ASC")->fetchAll(PDO::FETCH_ASSOC);
    $report['low_stock_raw_materials'] = $low_stock_raw_materials;

    // raw materials summary (merge stock+unit)
    $raw_materials_list = $conn->query("SELECT id, name, stock_quantity, unit FROM raw_materials ORDER BY stock_quantity ASC")->fetchAll(PDO::FETCH_ASSOC);
    $report['raw_materials'] = $raw_materials_list;

    // notifications (replace recent activities)
    $notifications = $conn->query("SELECT type, message, created_at FROM notifications ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $report['notifications'] = $notifications;

    // top products by sales
    $topProducts = $conn->query("SELECT p.id, p.name, SUM(pds.sold) as qty_sold, SUM(pds.revenue) as revenue FROM product_daily_stats pds JOIN products p ON pds.product_id = p.id GROUP BY p.id ORDER BY revenue DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $report['top_products'] = $topProducts;

    // attendance snapshot (horizontal)
    $report['attendance'] = [
        'total_employees' => $totalEmployees ?? $conn->query("SELECT COUNT(*) FROM employees WHERE status='Active'")->fetchColumn(),
        'present_today' => $presentToday ?? 0,
        'attendance_percentage' => $attendancePercentage ?? 0
    ];

    $format = strtolower($_GET['format'] ?? 'pdf');

    // CSV output
    if ($format === 'csv') {
        $filename = 'business_report_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');

        // Header row (horizontal headings)
        fputcsv($out, ['Business Report Snapshot', $report['generated_at']]);
        fputcsv($out, []);

        // Totals - horizontal: keys as headings, values as next row
        $headings = array_map(function($k){ return ucwords(str_replace('_',' ',$k)); }, array_keys($report['totals']));
        fputcsv($out, $headings);
        $values = [];
        foreach ($report['totals'] as $k => $v) {
            if (strpos($k, 'sales') !== false || strpos($k, 'stock') !== false || strpos($k, 'production') !== false) {
                // format monetary/number fields sensibly
                if ($k === 'total_sales_amount') {
                    $values[] = $fmtAmount($v);
                } else {
                    // integer-like
                    $values[] = $v;
                }
            } else {
                $values[] = $v;
            }
        }
        fputcsv($out, $values);
        fputcsv($out, []);

        // Low stock
        fputcsv($out, ['Low Stock Products (stock < 10)']);
        if (empty($report['low_stock'])) {
            fputcsv($out, ['no data yet here']);
        } else {
            fputcsv($out, ['id','name','stock']);
            foreach ($report['low_stock'] as $row) {
                fputcsv($out, [$row['id'],$row['name'],$row['stock']]);
            }
        }
        fputcsv($out, []);

        // Raw materials (merge stock+unit)
        fputcsv($out, ['Raw Materials']);
        if (empty($report['raw_materials'])) {
            fputcsv($out, ['no data yet here']);
        } else {
            fputcsv($out, ['id','name','available']);
            foreach ($report['raw_materials'] as $row) {
                $available = $row['stock_quantity'] . ($row['unit'] ? ' ' . $row['unit'] : '');
                fputcsv($out, [$row['id'],$row['name'],$available]);
            }
        }
        fputcsv($out, []);

        // Top products
        fputcsv($out, ['Top Products']);
        if (empty($report['top_products'])) {
            fputcsv($out, ['no data yet here']);
        } else {
            fputcsv($out, ['id','name','qty_sold','revenue']);
            foreach ($report['top_products'] as $row) {
                $rev = $fmtAmount($row['revenue']);
                fputcsv($out, [$row['id'],$row['name'],$row['qty_sold'],$rev]);
            }
        }
        fputcsv($out, []);

        // Notifications (replace recent activities)
        fputcsv($out, ['Notifications']);
        if (empty($report['notifications'])) {
            fputcsv($out, ['no data yet here']);
        } else {
            fputcsv($out, ['type','message','created_at']);
            foreach ($report['notifications'] as $n) {
                fputcsv($out, [$n['type'],$n['message'],$n['created_at']]);
            }
        }
        fputcsv($out, []);

        // Attendance (horizontal)
        fputcsv($out, ['Attendance Snapshot']);
        $att_head = ['Total Employees','Present Today','Attendance %'];
        fputcsv($out, $att_head);
        $att_row = [$report['attendance']['total_employees'],$report['attendance']['present_today'], rtrim(rtrim(number_format($report['attendance']['attendance_percentage'],1), '0'),'.') . '%'];
        fputcsv($out, $att_row);

        fclose($out);
        exit;
    }

    // PDF output
    if (!class_exists('Dompdf\\Dompdf')) {
        @require_once __DIR__ . '/../../vendor/autoload.php';
    }
    if (!class_exists('Dompdf\\Dompdf')) {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'PDF generator not available.';
        exit;
    }

    $dompdf = new \Dompdf\Dompdf();
    // improved styling: transparent light blue headers and subtle borders
    $html = '<html><head><meta charset="utf-8"><title>Business Report</title>';
    $html .= '<style>body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#1f2937}h2{background:rgba(59,130,246,0.08);padding:8px;color:#0b3d91;border-radius:4px}table{width:100%;border-collapse:collapse;margin-bottom:10px}th,td{border:1px solid rgba(0,0,0,0.06);padding:8px;text-align:left}th{background:rgba(56,189,248,0.12);color:#064e7a}caption{font-weight:bold;margin-bottom:6px}td.center{text-align:center}</style>';
    $html .= '</head><body>';
    $html .= '<h2>Business Report Snapshot</h2>';
    $html .= '<p>Generated: ' . $report['generated_at'] . '</p>';

    // Totals horizontal: header row then value row
    $html .= '<h3>Totals</h3>';
    $html .= '<table><thead><tr>';
    foreach ($report['totals'] as $k => $v) {
        $html .= '<th>' . htmlspecialchars(ucwords(str_replace('_',' ',$k))) . '</th>';
    }
    $html .= '</tr></thead><tbody><tr>';
    foreach ($report['totals'] as $k => $v) {
        if ($k === 'total_sales_amount') {
            $html .= '<td>' . htmlspecialchars($fmtAmount($v)) . '</td>';
        } else {
            $html .= '<td>' . htmlspecialchars($v) . '</td>';
        }
    }
    $html .= '</tr></tbody></table>';

    // Low stock products
    $html .= '<h3>Low Stock Products (stock < 50)</h3>';
    $html .= '<table><thead><tr><th>ID</th><th>Name</th><th>Stock</th></tr></thead><tbody>';
    if (empty($report['low_stock'])) {
        $html .= '<tr><td class="center" colspan="3">no data yet here</td></tr>';
    } else {
        foreach ($report['low_stock'] as $row) {
            $html .= '<tr><td>' . htmlspecialchars($row['id']) . '</td><td>' . htmlspecialchars($row['name']) . '</td><td>' . htmlspecialchars($row['stock']) . '</td></tr>';
        }
    }
    $html .= '</tbody></table>';

    // Low stock raw materials
    $html .= '<h3>Low Stock Raw materials </h3>';
    $html .= '<table><thead><tr><th>ID</th><th>Name</th><th>Stock</th><th>Minimum Stock</th></tr></thead><tbody>';
    if (empty($report['low_stock_raw_materials'])) {
        $html .= '<tr><td class="center" colspan="3">no data yet here</td></tr>';
    } else {
        foreach ($report['low_stock_raw_materials'] as $row) {
            $html .= '<tr><td>' . htmlspecialchars($row['id']) . '</td><td>' . htmlspecialchars($row['name']) . '</td><td>' . htmlspecialchars($row['stock_quantity']) .' ' .htmlspecialchars($row['unit']). '</td><td>' . htmlspecialchars($row['low_threshold']) . '</td><</tr>';
        }
    }
    $html .= '</tbody></table>';

    // Raw materials merge stock+unit
    $html .= '<h3>Raw Materials</h3>';
    $html .= '<table><thead><tr><th>ID</th><th>Name</th><th>Available</th></tr></thead><tbody>';
    if (empty($report['raw_materials'])) {
        $html .= '<tr><td class="center" colspan="3">no data yet here</td></tr>';
    } else {
        foreach ($report['raw_materials'] as $row) {
            $avail = htmlspecialchars($row['stock_quantity']) . ($row['unit'] ? ' ' . htmlspecialchars($row['unit']) : '');
            $html .= '<tr><td>' . htmlspecialchars($row['id']) . '</td><td>' . htmlspecialchars($row['name']) . '</td><td>' . $avail . '</td></tr>';
        }
    }
    $html .= '</tbody></table>';

    // Top products
    $html .= '<h3>Top Products</h3>';
    $html .= '<table><thead><tr><th>ID</th><th>Name</th><th>Qty Sold</th><th>Revenue</th></tr></thead><tbody>';
    if (empty($report['top_products'])) {
        $html .= '<tr><td class="center" colspan="4">no data yet here</td></tr>';
    } else {
        foreach ($report['top_products'] as $row) {
            $html .= '<tr><td>' . htmlspecialchars($row['id']) . '</td><td>' . htmlspecialchars($row['name']) . '</td><td>' . htmlspecialchars($row['qty_sold']) . '</td><td>' . htmlspecialchars($fmtAmount($row['revenue'])) . '</td></tr>';
        }
    }
    $html .= '</tbody></table>';

    // Notifications instead of recent activities
    $html .= '<h3>Notifications</h3>';
    $html .= '<table><thead><tr><th>Type</th><th>Message</th><th>When</th></tr></thead><tbody>';
    if (empty($report['notifications'])) {
        $html .= '<tr><td class="center" colspan="3">no data yet here</td></tr>';
    } else {
        foreach ($report['notifications'] as $n) {
            $html .= '<tr><td>' . htmlspecialchars($n['type']) . '</td><td>' . htmlspecialchars($n['message']) . '</td><td>' . htmlspecialchars($n['created_at']) . '</td></tr>';
        }
    }
    $html .= '</tbody></table>';

    // Attendance horizontal
    $html .= '<h3>Attendance Snapshot</h3>';
    $html .= '<table><thead><tr><th>Total Employees</th><th>Present Today</th><th>Attendance %</th></tr></thead><tbody>';
    $html .= '<tr><td>' . htmlspecialchars($report['attendance']['total_employees']) . '</td><td>' . htmlspecialchars($report['attendance']['present_today']) . '</td><td>' . htmlspecialchars(rtrim(rtrim(number_format($report['attendance']['attendance_percentage'],1), '0'),'.')) . '%</td></tr>';
    $html .= '</tbody></table>';

    $html .= '</body></html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    if (ob_get_length()) { ob_end_clean(); }
    $dompdf->stream('business_report_' . date('Ymd_His') . '.pdf', ['Attachment' => 1]);
    exit;
}

include __DIR__ . '/../views/dashboard.php';
?>