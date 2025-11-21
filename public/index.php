<?php
// Start output buffering at the very beginning
ob_start();

require __DIR__ . '/../config/config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple router
$page = $_GET['page'] ?? 'home';

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? 'employee';

// Define allowed pages for each role
$admin_only_pages = ['dashboard','employees','employee_create','raw_materials','products','schedules','admin_reports','reports','material_orders'];
$employee_pages = ['product_boards','production','sales','employee_reports','customers'];
$common_pages = ['notifications','profile','change_password','logout','exports_pdf','export_page_pdf','exports_csv','quick_csv_export','export_schedule','settings'];
$public_pages = ['home','login'];

// All allowed pages
$allowed = array_merge($public_pages, $admin_only_pages, $employee_pages, $common_pages);
if (!in_array($page,$allowed)) $page = 'home';

// Role-based access control
if ($logged_in) {
    // Check if employee is trying to access admin-only pages
    if ($user_role !== 'admin' && in_array($page, $admin_only_pages)) {
        // Redirect employees to product_boards
        header('Location: ?page=product_boards');
        exit;
    }
}

// Pages that should use the dashboard layout (with sidebar/navbar)
$dashboard_pages = array_merge($admin_only_pages, $employee_pages, $common_pages);
$dashboard_pages = array_diff($dashboard_pages, ['logout', 'exports_pdf', 'export_page_pdf', 'exports_csv', 'quick_csv_export', 'export_schedule']); // Remove logout and exports from dashboard pages

// Determine which layout to use
if ($page === 'exports_pdf' && $logged_in) {
    // PDF export page - no layout, just output PDF
    ob_end_clean(); // Clear buffer before PDF output
    include __DIR__ . '/../app/controllers/exports_pdf.php';
} else if ($page === 'export_page_pdf' && $logged_in) {
    // Page PDF export - no layout, just output PDF
    ob_end_clean(); // Clear buffer before PDF output
    include __DIR__ . '/../app/controllers/export_page_pdf.php';
} else if ($page === 'exports_csv' && $logged_in) {
    // CSV export page - no layout, just output CSV
    ob_end_clean(); // Clear buffer before CSV output
    include __DIR__ . '/../app/controllers/exports_csv.php';
} else if ($page === 'quick_csv_export' && $logged_in) {
    // Quick CSV export - no layout, just output CSV
    ob_end_clean(); // Clear buffer before CSV output
    include __DIR__ . '/../app/controllers/quick_csv_export.php';
} else if ($page === 'export_schedule' && $logged_in) {
    // Schedule export - no layout, just output PDF
    ob_end_clean(); // Clear buffer before PDF output
    include __DIR__ . '/../app/controllers/export_schedule.php';
} else if (in_array($page, $dashboard_pages) && $logged_in) {
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

// Only flush if buffer still exists (not cleared by export pages)
if (ob_get_level() > 0) {
    ob_end_flush();
}
?>