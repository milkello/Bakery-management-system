<!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Total Monthly Sales</p>
                            <h3 class="text-2xl font-bold text-lime-400"><?= number_format($total_sales,0) ?> Frw </h3>
                            <p class="text-green-400 text-sm"><?= $growthFormatted ?>% of last month <br> <?= number_format($today_revenue ?? 0, 0) ?> Frw of today
                        </div>
                        <i data-feather="dollar-sign" class="text-lime-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-fuchsia-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Products Value</p>
                            <h3 class="text-2xl font-bold text-fuchsia-400"><?= number_format($total_value,0) ?> Frw</h3>
                            <p class="text-green-400 text-sm"><?= number_format($daily_total_revenue) ?> Frw of today</p>
                            
                        </div>
                        <i data-feather="shopping-cart" class="text-fuchsia-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Stock Value</p>
                            <!-- <h3 class="text-2xl font-bold text-green-400"><?= $raw_materials ?> types</h3> -->
                            <h3 class="text-2xl font-bold text-lime-400"><?= number_format($pdo->query('SELECT SUM(unit_cost * stock_quantity) FROM raw_materials')->fetchColumn() ?: 0, 0) ?> Frw</h3>
                            <p class="text-green-400 text-sm"><?= number_format($daily_total_value_used, 0) ?> Frw used today</p>
                            <p class="text-red-400 text-sm"><?= $low_stock_count == '0' ? 'No' : $low_stock_count ?> low stock products</p>
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
            <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-800 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Today's Production</p>
                        <h4 class="text-lg font-bold text-fuchsia-400"><?= intval($today_production ?? 0) ?></h4>
                        <p class="text-gray-400 text-sm">Products made today</p>
                    </div>
                    <div>
                        <a href="?page=production" class="bg-fuchsia-500 px-3 py-2 rounded text-white">Open</a>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Today's Sales Revenue</p>
                        <h4 class="text-lg font-bold text-lime-400"><?= number_format($today_revenue ?? 0, 0) ?> Frw</h4>
                        <p class="text-gray-400 text-sm">Revenue from sales today</p>
                    </div>
                    <div>
                        <a href="?page=sales" class="bg-lime-500 px-3 py-2 rounded text-white">Open</a>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Inventory Value</p>
                        <h4 class="text-lg font-bold text-blue-400"><?= number_format($pdo->query('SELECT SUM(unit_cost * stock_quantity) FROM raw_materials')->fetchColumn() ?: 0, 0) ?> Frw</h4>
                        <p class="text-gray-400 text-sm">Ingredients total value</p>
                    </div>
                    <div>
                        <a href="?page=raw_materials" class="bg-blue-500 px-3 py-2 rounded text-white">Open</a>
                    </div>
                </div>
            </div> -->

            <!-- Export Business Report
            <div class="mb-6 flex items-center justify-end">
                <form method="GET" action="" class="flex items-center space-x-2">
                    <input type="hidden" name="page" value="dashboard">
                    <input type="hidden" name="action" value="export_report">
                    <button type="submit" name="format" value="pdf" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-3 py-2 rounded-lg">Export PDF</button>
                    <button type="submit" name="format" value="csv" class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-semibold px-3 py-2 rounded-lg">Export CSV</button>
                </form>
            </div> -->

            <!-- Time Period Filter (Shared for both charts) -->
            <div class="bg-gradient-to-r from-gray-800 to-gray-700 rounded-xl p-4 mb-4 shadow-lg border border-gray-700">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <i data-feather="calendar" class="text-lime-400 w-5 h-5"></i>
                        <h4 class="text-white font-semibold">Time Period:</h4>
                    </div>
                    <div class="flex gap-2">
                        <button data-range="daily" class="range-btn bg-lime-500 px-4 py-2 rounded-lg text-white font-medium hover:bg-lime-600 transition-all transform hover:scale-105 shadow-md">Daily</button>
                        <button data-range="weekly" class="range-btn bg-gray-700 px-4 py-2 rounded-lg text-white font-medium hover:bg-gray-600 transition-all transform hover:scale-105 shadow-md">Weekly</button>
                        <button data-range="monthly" class="range-btn bg-gray-700 px-4 py-2 rounded-lg text-white font-medium hover:bg-gray-600 transition-all transform hover:scale-105 shadow-md">Monthly</button>
                        <button data-range="yearly" class="range-btn bg-gray-700 px-4 py-2 rounded-lg text-white font-medium hover:bg-gray-600 transition-all transform hover:scale-105 shadow-md">Yearly</button>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center space-x-2 mb-4">
                        <i data-feather="trending-up" class="text-lime-400 w-6 h-6"></i>
                        <h3 class="text-xl font-bold text-lime-400">Sales Trends</h3>
                    </div>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center space-x-2 mb-4">
                        <i data-feather="pie-chart" class="text-fuchsia-400 w-6 h-6"></i>
                        <h3 class="text-xl font-bold text-fuchsia-400">Best Selling Products</h3>
                    </div>
                    <div class="relative" style="height: 360px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <!-- <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                <div class="flex items-center space-x-2 mb-6">
                    <i data-feather="activity" class="text-lime-400 w-6 h-6"></i>
                    <h3 class="text-xl font-bold text-lime-400">Recent Activity</h3>
                </div>
                <div class="space-y-3">
                    <?php foreach($recentActivities as $act): 
                        // Parse meta data if it's JSON
                        $metaData = null;
                        if ($act['meta']) {
                            $decoded = json_decode($act['meta'], true);
                            if ($decoded) {
                                $metaData = $decoded;
                            }
                        }
                        
                        // Determine icon and colors based on type
                        $iconConfig = [
                            'product_sold' => ['icon' => 'shopping-cart', 'bg' => 'bg-green-500', 'text' => 'text-green-400'],
                            'product_made' => ['icon' => 'package', 'bg' => 'bg-blue-500', 'text' => 'text-blue-400'],
                            'raw_material' => ['icon' => 'box', 'bg' => 'bg-yellow-500', 'text' => 'text-yellow-400'],
                            'sale' => ['icon' => 'dollar-sign', 'bg' => 'bg-lime-500', 'text' => 'text-lime-400'],
                            'production' => ['icon' => 'settings', 'bg' => 'bg-fuchsia-500', 'text' => 'text-fuchsia-400'],
                            'over_usage' => ['icon' => 'alert-triangle', 'bg' => 'bg-red-500', 'text' => 'text-red-400'],
                            'special' => ['icon' => 'star', 'bg' => 'bg-purple-500', 'text' => 'text-purple-400']
                        ];
                        $config = $iconConfig[$act['type']] ?? ['icon' => 'circle', 'bg' => 'bg-gray-500', 'text' => 'text-gray-400'];
                    ?>
                    <!-- <div class="flex items-center justify-between bg-gradient-to-r from-gray-700 to-gray-750 p-4 rounded-lg border border-gray-600 hover:border-lime-500 transition-all">
                        <div class="flex items-center gap-4 flex-1">
                            <!-- Icon -->
                            <!-- <div class="w-12 h-12 flex items-center justify-center rounded-full <?= $config['bg'] ?> shadow-lg">
                                <i data-feather="<?= $config['icon'] ?>" class="w-6 h-6 text-white"></i>
                            </div> -->
                            
                            <!-- Content -->
                            <!-- <div class="flex-1">
                                <p class="font-semibold text-white text-sm">
                                    <?= htmlspecialchars($act['action']) ?>
                                </p>
                                <?php if ($metaData): ?>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        <?php if (isset($metaData['product_id'])): ?>
                                            <span class="text-xs px-2 py-1 bg-gray-600 rounded text-gray-300">
                                                <?php
                                                $stmt = $conn->prepare("SELECT name, sku FROM products WHERE id = :id");
                                                $stmt->execute(['id' => $metaData['product_id']]);
                                                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                                                ?>
                                                <i data-feather="tag" class="w-3 h-3 inline"></i> <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['sku']) ?>)
                                            </span>
                                        <?php endif; ?>
                                        <?php if (isset($metaData['qty'])): ?>
                                            <span class="text-xs px-2 py-1 bg-gray-600 rounded text-gray-300">
                                                <i data-feather="package" class="w-3 h-3 inline"></i> Qty: <?= $metaData['qty'] ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (isset($metaData['total'])): ?>
                                            <span class="text-xs px-2 py-1 bg-lime-600 rounded text-white font-semibold">
                                                <i data-feather="dollar-sign" class="w-3 h-3 inline"></i> <?= number_format($metaData['total']) ?> Frw
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($act['meta']): ?>
                                    <p class="text-gray-400 text-xs mt-1"><?= htmlspecialchars($act['meta']) ?></p>
                                <?php endif; ?>
                                <p class="text-gray-500 text-xs mt-1">
                                    <i data-feather="clock" class="w-3 h-3 inline"></i> <?= date('g:i a, M d, Y', strtotime($act['created_at'])) ?>
                                </p>
                            </div>
                        </div>

                        Amount/Quantity Badge -->
                        <!-- <div class="flex flex-col items-end gap-1">
                            <?php if ($act['amount']): ?>
                                <span class="text-lime-400 font-bold text-lg">
                                    <?= number_format($act['amount']) ?> Frw
                                </span>
                            <?php endif; ?>
                            <?php if ($act['quantity_change']): ?>
                                <span class="<?= $act['quantity_change'] > 0 ? 'text-green-400' : 'text-red-400' ?> font-semibold text-sm">
                                    <?= $act['quantity_change'] > 0 ? '+' : '' ?><?= $act['quantity_change'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div> -->
                    <?php endforeach; ?>
                    
                    <?php if (empty($recentActivities)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <!-- <i data-feather="inbox" class="w-16 h-16 mx-auto mb-3 opacity-50"></i> -->
                            <p>No recent activities</p>
                        </div>
                    <?php endif; ?>
                <!--</div>
            </div> -->





<script>
    // Product sales data for all time periods (donut chart)
    const productSalesData = {
        daily: <?= json_encode($productSalesDaily) ?>,
        weekly: <?= json_encode($productSalesWeekly) ?>,
        monthly: <?= json_encode($productSalesMonthly) ?>,
        yearly: <?= json_encode($productSalesYearly) ?>
    };

    // Initialize donut chart
    const salesCanvas = document.getElementById('salesChart');
    salesCanvas.style.width = '100%';
    salesCanvas.style.height = '100%';
    const salesCtx = salesCanvas.getContext('2d');
    
    let salesChart = new Chart(salesCtx, {
        type: 'doughnut',
        data: {
            labels: productSalesData.daily.labels,
            datasets: [{
                data: productSalesData.daily.sales,
                backgroundColor: [
                    '#84cc16',
                    '#d946ef',
                    '#3b82f6',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#14b8a6',
                    '#f97316'
                ],
                borderWidth: 2,
                borderColor: '#1f2937',
                hoverOffset: 20,
                spacing: 2,
                cutout: '60%',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        color: '#9ca3af', 
                        padding: 12,
                        font: {
                            size: 11
                        },
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        boxHeight: 8
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value.toFixed(0) + ' Frw (' + percentage + '%)';
                        }
                    }
                }
            }
        }
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

        // Handle range buttons - update BOTH charts
        document.querySelectorAll('.range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const range = this.dataset.range;
                
                // Remove active state from all buttons
                document.querySelectorAll('.range-btn').forEach(b => {
                    b.classList.remove('bg-lime-500', 'bg-lime-600');
                    b.classList.add('bg-gray-700');
                });
                
                // Add active state to clicked button
                this.classList.remove('bg-gray-700');
                this.classList.add('bg-lime-500');
                
                // Update revenue chart (line chart)
                revenueChart.data.labels = allData[range].labels;
                revenueChart.data.datasets[0].data = allData[range].data;
                revenueChart.update('active');
                
                // Update sales chart (donut chart)
                salesChart.data.labels = productSalesData[range].labels;
                salesChart.data.datasets[0].data = productSalesData[range].sales;
                salesChart.update('active');
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