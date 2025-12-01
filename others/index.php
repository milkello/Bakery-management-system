<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $conn->query("SELECT business_name FROM system_settings LIMIT 1")->fetchColumn(); ?> - Dashboard</title>
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
            <span class="ml-4 text-xl font-bold hidden lg:block"><?php echo $conn->query("SELECT business_name FROM system_settings LIMIT 1")->fetchColumn(); ?></span>
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
            <a href="public/?page=suppliers" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-lime-400 transition-colors">
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
                        <i data-feather="user" class="w-5 h-5"></i>
                    </div>
                    <span>Admin</span>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Total Revenue</p>
                            <h3 class="text-2xl font-bold text-lime-400">12,489 Rwf</h3>
                            <p class="text-green-400 text-sm">+12% from last month</p>
                        </div>
                        <i data-feather="dollar-sign" class="text-lime-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-fuchsia-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Active Orders</p>
                            <h3 class="text-2xl font-bold text-fuchsia-400">24</h3>
                            <p class="text-green-400 text-sm">+4 today</p>
                        </div>
                        <i data-feather="shopping-cart" class="text-fuchsia-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Inventory Items</p>
                            <h3 class="text-2xl font-bold text-lime-400">156</h3>
                            <p class="text-red-400 text-sm">-8 low stock</p>
                        </div>
                        <i data-feather="package" class="text-lime-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-fuchsia-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Employees</p>
                            <h3 class="text-2xl font-bold text-fuchsia-400">18</h3>
                            <p class="text-green-400 text-sm">All present</p>
                        </div>
                        <i data-feather="users" class="text-fuchsia-500 w-8 h-8"></i>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 text-lime-400">Revenue Trends</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 text-fuchsia-400">Product Sales</h3>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                <h3 class="text-xl font-bold mb-4 text-lime-400">Recent Activity</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-lime-500 rounded-full flex items-center justify-center">
                                <i data-feather="shopping-cart" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="font-medium">New order #2841</p>
                                <p class="text-gray-400 text-sm">2 minutes ago</p>
                            </div>
                        </div>
                        <span class="text-lime-400 font-bold">45.99 Rwf</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-fuchsia-500 rounded-full flex items-center justify-center">
                                <i data-feather="package" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="font-medium">Flour stock updated</p>
                                <p class="text-gray-400 text-sm">1 hour ago</p>
                            </div>
                        </div>
                        <span class="text-fuchsia-400 font-bold">+50kg</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue ($)',
                        data: [8500, 9200, 11000, 12489, 11800, 13500],
                        borderColor: '#84cc16',
                        backgroundColor: 'rgba(132, 204, 22, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            });

            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Bread', 'Pastries', 'Cakes', 'Cookies'],
                    datasets: [{
                        data: [35, 25, 20, 20],
                        backgroundColor: [
                            '#84cc16',
                            '#d946ef',
                            '#3b82f6',
                            '#f59e0b'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#9ca3af',
                                padding: 20
                            }
                        }
                    }
                }
            });

            feather.replace();
        });
    </script>
</body>
</html>