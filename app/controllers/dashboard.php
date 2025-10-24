<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

// === Basic Stats ===
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_employees = $conn->query("SELECT COUNT(*) FROM employees")->fetchColumn();
$total_sales = $conn->query("SELECT SUM(total_price) FROM sales")->fetchColumn() ?? 0;
$toal_stock = $conn->query("SELECT SUM(stock) FROM products")->fetchColumn() ?? 0;

$today = date('Y-m-d');
$todayProductsQuery = $conn->prepare("SELECT COUNT(*) as added_today FROM products WHERE DATE(created_at) = :today");
$todayProductsQuery->execute(['today' => $today]);
$productsToday = $todayProductsQuery->fetch(PDO::FETCH_ASSOC)['added_today'];

$thisMonth = date('Y-m'); // e.g., 2025-10
$stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE_FORMAT(created_at, '%Y-%m') = :month");
$stmt->execute(['month' => $thisMonth]);
$totalThisMonth = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$lastMonth = date('Y-m', strtotime('-1 month'));
$stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE_FORMAT(created_at, '%Y-%m') = :month");
$stmt->execute(['month' => $lastMonth]);
$totalLastMonth = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

if ($totalLastMonth > 0) {
    $growth = (($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100;
} else {
    $growth = 100; // if no sales last month, treat as 100% growth
}
$growthFormatted = number_format($growth, 1); // e.g., 12.3%


$totalEmployeesQuery = $conn->query("SELECT COUNT(*) as total FROM employees");
$totalEmployees = $totalEmployeesQuery->fetch(PDO::FETCH_ASSOC)['total'];
$today = date('Y-m-d');
$presentQuery = $conn->prepare("SELECT COUNT(*) as present_today FROM attendance WHERE date = :today AND status = 'present'");
$presentQuery->execute(['today' => $today]);
$presentToday = $presentQuery->fetch(PDO::FETCH_ASSOC)['present_today'];
$statusMessage = ($presentToday == $totalEmployees) ? "All present" : "$presentToday present";


// charts data
// Prepare months
// $months = [];
// $revenues = [];
// for ($i = 5; $i >= 0; $i--) {
//     $month = date('Y-m', strtotime("-$i month"));
//     $months[] = date('M', strtotime($month.'-01'));

//     $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE_FORMAT(created_at, '%Y-%m') = :month");
//     $stmt->execute(['month' => $month]);
//     $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
//     $revenues[] = $total;
// }

$productLabels = [];
$productSales = [];

$products = $conn->query("SELECT id, name FROM products")->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $product) {
    $productLabels[] = $product['name'];

    $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE product_id = :pid");
    $stmt->execute(['pid' => $product['id']]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $productSales[] = $total;
}

function getRevenueData($conn, $range) {
    $labels = [];
    $data = [];

    switch ($range) {
        case 'daily':
            for ($i = 6; $i >= 0; $i--) {
                $day = date('Y-m-d', strtotime("-$i day"));
                $labels[] = date('D', strtotime($day));
                $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE(created_at) = :day");
                $stmt->execute(['day' => $day]);
                $data[] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            }
            break;

        case 'weekly':
            for ($i = 5; $i >= 0; $i--) {
                $start = date('Y-m-d', strtotime("last sunday -$i week"));
                $end = date('Y-m-d', strtotime("next saturday -$i week"));
                $labels[] = "Wk ".date('W', strtotime($start));
                $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE(created_at) BETWEEN :start AND :end");
                $stmt->execute(['start' => $start, 'end' => $end]);
                $data[] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            }
            break;

        case 'monthly':
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i month"));
                $labels[] = date('M', strtotime($month.'-01'));
                $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE_FORMAT(created_at, '%Y-%m') = :month");
                $stmt->execute(['month' => $month]);
                $data[] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            }
            break;

        case 'yearly':
            for ($i = 5; $i >= 0; $i--) {
                $year = date('Y', strtotime("-$i year"));
                $labels[] = $year;
                $stmt = $conn->prepare("SELECT SUM(total_price) as total FROM sales WHERE DATE_FORMAT(created_at, '%Y') = :year");
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

// 1. Raw material added
logActivity($conn, 1, "Raw material added", "raw_material", 50, null, "Flour");

// 2. Product sold
logActivity($conn, 1, "Product sold", "product_sold", null, 45.99, "Bread");

// 3. Product made
logActivity($conn, 1, "Product made", "product_made", 20, null, "Chocolate Cake");

// 4. Special activity
logActivity($conn, 1, "Special discount applied", "special", null, null, "Black Friday Sale");

// --- Fetch recent activities ---
$recentActivities = $conn->query("
    SELECT action, type, quantity_change, amount, meta, created_at
    FROM logs
    ORDER BY created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// --- Map icons ---
$iconMap = [
    'raw_material' => '🟢',
    'product_sold' => '🔵',
    'product_made' => '🟡',
    'special' => '🟣'
];



// === Low Stock Items ===
$low_stock = $conn->query("SELECT name, stock FROM products WHERE stock < 10")->fetchAll();
$low_stock_count = $pdo->query('SELECT COUNT(*) FROM raw_materials WHERE stock_quantity <= low_threshold')->fetchColumn();
$today_production = $conn->query("SELECT COUNT(*) FROM production WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// === Sales Trend (last 7 days) ===
$sales_trend_stmt = $conn->query("
    SELECT DATE(created_at) AS sale_date, SUM(total_price) AS total
    FROM sales
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY sale_date ASC
");
$sales_trend = $sales_trend_stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../views/dashboard.php';
?>