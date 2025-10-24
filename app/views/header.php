<?php
    ob_start(); 
    $user = $_SESSION['user_id'] ?? null;
    // Get current page for active state
    $current_page = $_GET['page'] ?? 'dashboard';
if ($user):
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoughLight Delights - Dashboard</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.globe.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#84cc16',
                        secondary: '#d946ef'
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }
        .sidebar-minimized {
            width: 5rem;
        }
        .sidebar-expanded {
            width: 16rem;
        }
        .main-content {
            transition: all 0.3s ease;
        }
        .main-minimized {
            margin-left: 5rem;
        }
        .main-expanded {
            margin-left: 16rem;
        }
        .nav-item-text {
            transition: opacity 0.2s ease;
        }
        .sidebar-minimized .nav-item-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .sidebar-expanded .nav-item-text {
            opacity: 1;
            width: auto;
        }
        .logo-text {
            transition: all 0.3s ease;
        }
        .sidebar-minimized .logo-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar sidebar-expanded bg-gray-800 fixed h-full overflow-hidden z-50">
        <div class="p-4 flex items-center justify-between">
            <div class="flex items-center min-w-0">
                <i data-feather="star" class="text-lime-500 w-8 h-8 flex-shrink-0"></i>
                <span class="logo-text ml-4 text-xl font-bold truncate">DoughLight 🍪</span>
            </div>
            <button id="sidebarToggle" class="text-gray-400 hover:text-white ml-2 flex-shrink-0">
                <i data-feather="chevron-left" class="w-5 h-5 sidebar-toggle-icon"></i>
            </button>
        </div>
        <nav class="mt-8">
            <a href="?page=dashboard" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'dashboard' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="pie-chart" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Dashboard</span>
            </a>
            <a href="?page=employees" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'employees' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="users" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Employees</span>
            </a>
            <a href="?page=raw_materials" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'raw_materials' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="dollar-sign" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Materials</span>
            </a>
            <a href="?page=products" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'products' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="package" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Products</span>
            </a>
            <a href="?page=recipes" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'recipes' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="calendar" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Recipes</span>
            </a>
            <a href="?page=production" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'production' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="settings" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Production</span>
            </a>
            <a href="?page=sales" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'sales' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="trending-up" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Sales</span>
            </a>
            <a href="?page=schedules" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'sales' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="trending-up" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Schedules</span>
            </a>
            <a href="?page=exports_pdf" class="nav-item flex items-center px-4 py-3 text-blue-500 hover:bg-gray-700 hover:text-blue-400 transition-colors">
                <i data-feather="log-in" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Reports in pdf</span>
            </a>
            <a href="?page=notifications" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors <?= $current_page === 'notifications' ? 'bg-gray-700 text-lime-400' : '' ?>">
                <i data-feather="bell" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Notifications</span>
            </a>
            <?php if(!$user): ?>
            <a href="?page=login" class="nav-item flex items-center px-4 py-3 text-blue-500 hover:bg-gray-700 hover:text-blue-400 transition-colors">
                <i data-feather="log-in" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Login</span>
            </a>
            <?php endif; ?>
            <a href="?page=logout" class="nav-item flex items-center px-4 py-3 text-red-500 hover:bg-gray-700 hover:text-red-400 transition-colors mt-8">
                <i data-feather="log-out" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-item-text ml-4 truncate">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div id="mainContent" class="main-content main-expanded flex-1 transition-all duration-300">
        <!-- Header -->
        <header class="bg-gray-800 shadow-lg p-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button id="mobileSidebarToggle" class="lg:hidden text-gray-400 hover:text-white">
                    <i data-feather="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-2xl font-bold text-lime-400 capitalize"><?= str_replace('_', ' ', $current_page) ?></h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative hidden md:block">
                    <input type="text" placeholder="Search..." class="bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500 w-64">
                    <i data-feather="search" class="absolute right-3 top-2.5 text-gray-400"></i>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                        <i data-feather="user" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="hidden sm:block">Admin</span>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
<?php else: ?>
<!-- If not logged in, this file shouldn't be included at all -->
<?php endif; ?>