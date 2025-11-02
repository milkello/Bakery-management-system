<?php
require __DIR__ . '/../config/config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple router
$page = $_GET['page'] ?? 'home';
$allowed = ['home','login','logout','dashboard','employees','employee_create','raw_materials','products',
            'recipes','production','sales','notifications','profile','change_password','schedules','exports_pdf','reports','material_orders' , 'production_records', 'product_boards'];
if (!in_array($page,$allowed)) $page = 'home';

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);

// Pages that should use the dashboard layout (with sidebar/navbar)
$dashboard_pages = ['dashboard','employees','employee_create','raw_materials','products',
                   'recipes','production','sales','notifications','profile','change_password','schedules','logout', 'exports_pdf','reports','material_orders' , 'production_records', 'product_boards'];

// Pages that should use public layout (no sidebar/navbar)
$public_pages = ['home','login'];

// Determine which layout to use
if (in_array($page, $dashboard_pages) && $logged_in) {
    // Use dashboard layout with sidebar and navbar
    include __DIR__ . '/../app/views/header.php'; // This includes sidebar and navbar
    include __DIR__ . '/../app/controllers/' . $page . '.php';
    include __DIR__ . '/../app/views/footer.php';
} else if (in_array($page, $public_pages)) {
    // Use public layout (no sidebar/navbar)
    if ($page === 'home') {
        // Homepage has its own complete HTML structure
        include __DIR__ . '/../app/controllers/home.php';
    } else {
        // Other public pages (like login) use simple layout
        include __DIR__ . '/../app/views/public_header.php';
        include __DIR__ . '/../app/controllers/' . $page . '.php';
        include __DIR__ . '/../app/views/public_footer.php';
    }
} else {
    // Redirect to login if trying to access dashboard pages without authentication
    if (in_array($page, $dashboard_pages) && !$logged_in) {
        header('Location: ?page=login');
        exit;
    }
    // Fallback for any other cases
    include __DIR__ . '/../app/views/public_header.php';
    include __DIR__ . '/../app/controllers/' . $page . '.php';
    include __DIR__ . '/../app/views/public_footer.php';
}

ob_end_flush();
?>