<!-- things were here -->
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
        .sidebar:hover {
            width: 16rem;
        }
        .sidebar {
            transition: width 0.3s ease;
        }
        .page-transition {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex">
    <!-- Sidebar -->
    <div class="sidebar w-20 hover:w-64 bg-gray-800 fixed h-full overflow-hidden transition-all duration-300 z-50">
        <div class="p-4 flex items-center">
            <i data-feather="star" class="text-lime-500 w-8 h-8"></i>
            <span class="ml-4 text-xl font-bold hidden lg:block">DoughLight 🍪</span>
        </div>
        <nav class="mt-8">
            <a href="public/?page=dashboard" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
                <i data-feather="pie-chart" class="w-5 h-5"></i>
                <span class="ml-4 hidden lg:block">Dashboard</span>
            </a>
            <a href="public/?page=employees" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
                <i data-feather="users" class="w-5 h-5"></i>
                <span class="ml-4 hidden lg:block">Employees</span>
            </a>
            <a href="public/?page=raw_materials" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
                <i data-feather="dollar-sign" class="w-5 h-5"></i>
                <span class="ml-4 hidden lg:block">Materials</span>
            </a>
            <a href="public/?page=products" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
                <i data-feather="package" class="w-5 h-5"></i>
                <span class="ml-4 hidden lg:block">Products</span>
            </a>
            <a href="public/?page=recipes" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
                <i data-feather="calendar" class="w-5 h-5"></i>
                <span class="ml-4 hidden lg:block">Recipes</span>
            </a>
            <a href="public/?page=production" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
                <i data-feather="settings" class="w-5 h-5"></i>
                <span class="ml-4 hidden lg:block">Production</span>
            </a>
            <a href="public/?page=sales" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
                <i data-feather="settings" class="w-5 h-5"></i>
                <span class="ml-4 hidden lg:block">Sales</span>
            </a>
            <a href="public/?page=notifications" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
                <i data-feather="settings" class="w-5 h-5"></i>
                <span class="ml-4 hidden lg:block">Notifications</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 ml-20 transition-all duration-300">
        <!-- Header -->
        <header class="bg-gray-800 shadow-lg p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-lime-400">Dashboard</h1>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text" placeholder="Search..." class="bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                    <i data-feather="search" class="absolute right-3 top-2.5 text-gray-400"></i>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                        <i data-feather="user" class="w-5 h-5 text-white"></i>
                    </div>
                    <span>Admin</span>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">



    