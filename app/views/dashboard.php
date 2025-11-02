<!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Total Sales</p>
                            <h3 class="text-2xl font-bold text-lime-400"><?= number_format($total_sales,0) ?> Frw</h3>
                            <p class="text-green-400 text-sm"><?= $growthFormatted ?>% this month</p>
                        </div>
                        <i data-feather="dollar-sign" class="text-lime-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-fuchsia-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Products</p>
                            <h3 class="text-2xl font-bold text-fuchsia-400"><?= $toal_stock ?></h3>
                            <!-- <h3 class="text-2xl font-bold text-fuchsia-400"><?= $total_products ?></h3> -->
                            <p class="text-green-400 text-sm"><?= number_format($today_production) ?> products made today</p>
                        </div>
                        <i data-feather="shopping-cart" class="text-fuchsia-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Ingredients in stock</p>
                            <h3 class="text-2xl font-bold text-lime-400"><?= $raw_materials ?> types</h3>
                            <p class="text-red-400 text-sm"><?= $low_stock_count ?> low stock</p>
                        </div>
                        <i data-feather="package" class="text-lime-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-fuchsia-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Employees</p>
                            <h3 class="text-2xl font-bold text-fuchsia-400"><?= $total_employees ?></h3>
                            <p class="<?= $statusColor ?> text-sm"><?= $statusMessage ?></p>
                        </div>
                        <i data-feather="users" class="text-fuchsia-500 w-8 h-8"></i>
                    </div>
                </div>
            </div>

            <!-- Quick daily snapshot -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-800 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Today's Material Orders</p>
                        <h4 class="text-lg font-bold text-lime-400"><?= intval($today_material_orders) ?></h4>
                        <p class="text-gray-400 text-sm">Value: <?= number_format($today_materials_value,2) ?> Frw</p>
                    </div>
                    <div>
                        <a href="?page=product_boards" class="bg-lime-500 px-3 py-2 rounded text-white">Open</a>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Products made today</p>
                        <h4 class="text-lg font-bold text-fuchsia-400"><?= intval($today_production_records ?: $today_production) ?></h4>
                        <p class="text-gray-400 text-sm">Recorded production count</p>
                    </div>
                    <div>
                        <a href="?page=production_records" class="bg-fuchsia-500 px-3 py-2 rounded text-white">Open</a>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Inventory Value</p>
                        <h4 class="text-lg font-bold text-lime-400"><?= number_format($pdo->query('SELECT SUM(unit_cost * stock_quantity) FROM raw_materials')->fetchColumn() ?: 0, 2) ?> Frw</h4>
                        <p class="text-gray-400 text-sm">Materials total value</p>
                    </div>
                    <div>
                        <a href="?page=raw_materials" class="bg-gray-700 px-3 py-2 rounded text-white">Open</a>
                    </div>
                </div>
            </div>

            <!-- Export Business Report -->
            <div class="mb-6 flex items-center justify-end">
                <form method="GET" action="" class="flex items-center space-x-2">
                    <input type="hidden" name="page" value="dashboard">
                    <input type="hidden" name="action" value="export_report">
                    <button type="submit" name="format" value="pdf" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-3 py-2 rounded-lg">Export PDF</button>
                    <button type="submit" name="format" value="csv" class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-semibold px-3 py-2 rounded-lg">Export CSV</button>
                </form>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 text-lime-400">Sales Trends</h3>
                    <!-- Buttons -->
                    <div class="flex gap-3 mb-4">
                        <button data-range="daily" class="range-btn bg-lime-500 px-3 py-1 rounded text-white hover:bg-lime-600">Daily</button>
                        <button data-range="weekly" class="range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Weekly</button>
                        <button data-range="monthly" class="range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Monthly</button>
                        <button data-range="yearly" class="range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Yearly</button>
                    </div>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 text-fuchsia-400">Best Sold</h3>
                    <!-- Buttons -->
                    <div class="flex gap-3 mb-4">
                        <button data-range="daily" class="product-range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Daily</button>
                        <button data-range="weekly" class="product-range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Weekly</button>
                        <button data-range="monthly" class="product-range-btn bg-lime-500 px-3 py-1 rounded text-white hover:bg-lime-600">Monthly</button>
                        <button data-range="yearly" class="product-range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Yearly</button>
                    </div>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                <h3 class="text-xl font-bold mb-4 text-lime-400">Recent Activity</h3>
                <ul class="space-y-4">
                    <?php foreach($recentActivities as $act): ?>
                    <li class="flex justify-between items-start bg-gray-700 p-3 rounded-lg">
                        <!-- Icon Circle -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-600 text-xl">
                                <?= $iconMap[$act['type']] ?? '⚪' ?>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-200">
                                    <?= htmlspecialchars($act['action']) ?>
                                    <?php if($act['meta']): ?>
                                        - <?= htmlspecialchars($act['meta']) ?>
                                    <?php endif; ?>
                                </p>
                                <p class="text-gray-400 text-sm"><?= date('g:i a, M d', strtotime($act['created_at'])) ?></p>
                            </div>
                        </div>

                        <!-- Amount or quantity -->
                        <div class="text-green-400 font-bold">
                            <?= $act['amount'] ?? ($act['quantity_change'] > 0 ? "+":"") . $act['quantity_change'] ?? '' ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>





<script>
    document.addEventListener('DOMContentLoaded', function() {
        // All product sales data from PHP
        const allProductData = {
            daily: <?= json_encode($productSalesDaily) ?>,
            weekly: <?= json_encode($productSalesWeekly) ?>,
            monthly: <?= json_encode($productSalesMonthly) ?>,
            yearly: <?= json_encode($productSalesYearly) ?>
        };

        // Initial chart (monthly)
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        let salesChart = new Chart(salesCtx, {
            type: 'doughnut',
            data: {
                labels: allProductData.monthly.labels,
                datasets: [{
                    data: allProductData.monthly.sales,
                    backgroundColor: ['#83cc16c9', '#d846efd8', '#3b83f6d7', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'],
                    borderWidth: 0,
                    hoverOffset: 30,
                    spacing: 10,
                    cutout: '30%',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#9ca3af', padding: 20 }
                    }
                }
            }
        });

        // Handle product range buttons
        document.querySelectorAll('.product-range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active state from all buttons
                document.querySelectorAll('.product-range-btn').forEach(b => {
                    b.classList.remove('bg-lime-500', 'hover:bg-lime-600');
                    b.classList.add('bg-gray-700', 'hover:bg-gray-600');
                });
                
                // Add active state to clicked button
                this.classList.remove('bg-gray-700', 'hover:bg-gray-600');
                this.classList.add('bg-lime-500', 'hover:bg-lime-600');

                const range = this.dataset.range;
                salesChart.data.labels = allProductData[range].labels;
                salesChart.data.datasets[0].data = allProductData[range].sales;
                salesChart.update();
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make the canvas take more vertical space so ticks spread out
        const revenueCanvas = document.getElementById('revenueChart');
        revenueCanvas.style.width = '100%';
        revenueCanvas.style.height = '360px'; // adjust height as needed
        const ctx = revenueCanvas.getContext('2d');

        // All data from PHP
        const allData = {
            daily: <?= json_encode($revenueDaily) ?>,
            weekly: <?= json_encode($revenueWeekly) ?>,
            monthly: <?= json_encode($revenueMonthly) ?>,
            yearly: <?= json_encode($revenueYearly) ?>
        };

        // Initial chart (daily)
        let revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: allData.daily.labels,
                datasets: [{
                    label: 'Revenue (Frw)',
                    data: allData.daily.data,
                    // backgroundColor: '#84cc16'
                    borderColor: '#84cc16',
                    backgroundColor: 'rgba(132, 204, 22, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#84cc16',
                    // pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#84cc16',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    pointHitRadius: 10,
                    pointStyle: 'circle',
                    showLine: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true, // allow explicit canvas height
                plugins: { legend: { display: false } },
                layout: { padding: { top: 12, bottom: 12 } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                        //reduce line spacing
                            color: '#9ca3af',
                            stepSize: 5000,

                            // maxTicksLimit: 6, // fewer ticks => more spacing
                            // padding: 8
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.06)',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Handle range buttons
        document.querySelectorAll('.range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active state from all buttons
                document.querySelectorAll('.range-btn').forEach(b => {
                    b.classList.remove('bg-lime-500', 'hover:bg-lime-600');
                    b.classList.add('bg-gray-700', 'hover:bg-gray-600');
                });
                
                // Add active state to clicked button
                this.classList.remove('bg-gray-700', 'hover:bg-gray-600');
                this.classList.add('bg-lime-500', 'hover:bg-lime-600');

                const range = this.dataset.range;
                revenueChart.data.labels = allData[range].labels;
                revenueChart.data.datasets[0].data = allData[range].data;
                revenueChart.update();
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const sidebarToggleIcon = document.querySelector('.sidebar-toggle-icon');
    
    // Check if sidebar state is saved in localStorage
    const isSidebarMinimized = localStorage.getItem('sidebarMinimized') === 'true';
    
    // Initialize sidebar state
    if (isSidebarMinimized) {
        minimizeSidebar();
    } else {
        expandSidebar();
    }
    
    // Desktop toggle button
    sidebarToggle.addEventListener('click', function() {
        if (sidebar.classList.contains('sidebar-expanded')) {
            minimizeSidebar();
        } else {
            expandSidebar();
        }
    });
    
    // Mobile toggle button
    mobileSidebarToggle.addEventListener('click', function() {
        if (sidebar.classList.contains('sidebar-expanded')) {
            minimizeSidebar();
        } else {
            expandSidebar();
        }
    });
    
    function minimizeSidebar() {
        sidebar.classList.remove('sidebar-expanded');
        sidebar.classList.add('sidebar-minimized');
        mainContent.classList.remove('main-expanded');
        mainContent.classList.add('main-minimized');
        sidebarToggleIcon.setAttribute('data-feather', 'chevron-right');
        localStorage.setItem('sidebarMinimized', 'true');
        feather.replace();
    }
    
    function expandSidebar() {
        sidebar.classList.remove('sidebar-minimized');
        sidebar.classList.add('sidebar-expanded');
        mainContent.classList.remove('main-minimized');
        mainContent.classList.add('main-expanded');
        sidebarToggleIcon.setAttribute('data-feather', 'chevron-left');
        localStorage.setItem('sidebarMinimized', 'false');
        feather.replace();
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth < 1024) {
            // On mobile, start with minimized sidebar
            if (!sidebar.classList.contains('sidebar-minimized')) {
                minimizeSidebar();
            }
        } else {
            // On desktop, restore saved state
            if (localStorage.getItem('sidebarMinimized') === 'true') {
                minimizeSidebar();
            } else {
                expandSidebar();
            }
        }
    });
    
    // Initialize on load based on screen size
    if (window.innerWidth < 1024) {
        minimizeSidebar();
    }
    
    // Initial feather icons render
    feather.replace();
});
</script>
</body>
</html>


</body>
</html>