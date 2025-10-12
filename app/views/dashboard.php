




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
                            <h3 class="text-2xl font-bold text-fuchsia-400"><?= $total_products ?></h3>
                            <p class="text-green-400 text-sm"><?= $productsToday ?> added today</p>
                        </div>
                        <i data-feather="shopping-cart" class="text-fuchsia-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Inventory Items</p>
                            <h3 class="text-2xl font-bold text-lime-400"><?= $toal_stock ?></h3>
                            <p class="text-red-400 text-sm"><?= count($low_stock) ?> low stock</p>
                        </div>
                        <i data-feather="package" class="text-lime-500 w-8 h-8"></i>
                    </div>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-fuchsia-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400">Employees</p>
                            <h3 class="text-2xl font-bold text-fuchsia-400"><?= $total_employees ?></h3>
                            <p class="text-green-400 text-sm"><?= $statusMessage ?></p>
                        </div>
                        <i data-feather="users" class="text-fuchsia-500 w-8 h-8"></i>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold mb-4 text-lime-400">Revenue Trends</h3>
                    <!-- Buttons -->
                    <div class="flex gap-3 mb-4">
                        <button data-range="daily" class="range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Daily</button>
                        <button data-range="weekly" class="range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Weekly</button>
                        <button data-range="monthly" class="range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Monthly</button>
                        <button data-range="yearly" class="range-btn bg-gray-700 px-3 py-1 rounded text-white hover:bg-gray-600">Yearly</button>
                    </div>
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
    const productLabels = <?= json_encode($productLabels) ?>;
    const productSales = <?= json_encode($productSales) ?>;

    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'doughnut',
        data: {
            labels: productLabels,
            datasets: [{
                data: productSales,
                backgroundColor: ['#84cc16', '#d946ef', '#3b82f6', '#f59e0b'],
                borderWidth: 0
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
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // All data from PHP
        const allData = {
            daily: <?= json_encode($revenueDaily) ?>,
            weekly: <?= json_encode($revenueWeekly) ?>,
            monthly: <?= json_encode($revenueMonthly) ?>,
            yearly: <?= json_encode($revenueYearly) ?>
        };

        // Initial chart (daily)
        let revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: allData.daily.labels,
                datasets: [{
                    label: 'Revenue (Frw)',
                    data: allData.daily.data,
                    backgroundColor: '#84cc16'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Handle range buttons
        document.querySelectorAll('.range-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const range = this.dataset.range;
                revenueChart.data.labels = allData[range].labels;
                revenueChart.data.datasets[0].data = allData[range].data;
                revenueChart.update();
            });
        });
    });
</script>


</body>
</html>