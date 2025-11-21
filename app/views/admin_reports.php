<!-- Admin Reports Page -->
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-lime-400 mb-2">Admin PDF Reports</h2>
        <p class="text-gray-400">Generate comprehensive reports with custom date ranges</p>
    </div>

    <!-- Alerts -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 bg-green-900 border border-green-700 rounded-lg text-green-300">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-900 border border-red-700 rounded-lg text-red-300">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Total Employees</p>
                    <h3 class="text-2xl font-bold text-lime-400"><?= $total_employees ?></h3>
                </div>
                <i data-feather="user-check" class="text-lime-500 w-8 h-8"></i>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-fuchsia-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Total Customers</p>
                    <h3 class="text-2xl font-bold text-fuchsia-400"><?= $total_customers ?></h3>
                </div>
                <i data-feather="users" class="text-fuchsia-500 w-8 h-8"></i>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-purple-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Total Products</p>
                    <h3 class="text-2xl font-bold text-purple-400"><?= $total_products ?></h3>
                </div>
                <i data-feather="package" class="text-purple-500 w-8 h-8"></i>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Raw Materials</p>
                    <h3 class="text-2xl font-bold text-blue-400"><?= $total_materials ?></h3>
                </div>
                <i data-feather="box" class="text-blue-500 w-8 h-8"></i>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Total Sales</p>
                    <h3 class="text-2xl font-bold text-green-400"><?= number_format($total_sales) ?> Frw</h3>
                </div>
                <i data-feather="dollar-sign" class="text-green-500 w-8 h-8"></i>
            </div>
        </div>
    </div>

    <!-- Report Generation Form -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Generate Report Card -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
            <div class="flex items-center space-x-2 mb-6">
                <i data-feather="file-text" class="text-lime-400 w-6 h-6"></i>
                <h3 class="text-xl font-bold text-lime-400">Generate New Report</h3>
            </div>

            <form method="POST" class="space-y-4">
                <!-- Report Type -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Report Type</label>
                    <select name="report_type" required 
                            class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <option value="">— Select Report Type —</option>
                        <option value="sales">Sales Report</option>
                        <option value="production">Production Report</option>
                        <option value="inventory">Inventory Report</option>
                        <option value="customers">Customers Analytics Report</option>
                        <option value="employees">Employee Report</option>
                        <option value="materials">Materials Report</option>
                        <option value="comprehensive">Comprehensive Report (All Data)</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">From Date</label>
                        <input type="date" name="date_from" required 
                               value="<?= date('Y-m-d', strtotime('-30 days')) ?>"
                               max="<?= date('Y-m-d') ?>"
                               class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">To Date</label>
                        <input type="date" name="date_to" required 
                               value="<?= date('Y-m-d') ?>"
                               max="<?= date('Y-m-d') ?>"
                               class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                    </div>
                </div>

                <!-- Quick Date Ranges -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Quick Select</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="setDateRange(7)" 
                                class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                            Last 7 Days
                        </button>
                        <button type="button" onclick="setDateRange(30)" 
                                class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                            Last 30 Days
                        </button>
                        <button type="button" onclick="setDateRange(90)" 
                                class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                            Last 90 Days
                        </button>
                        <button type="button" onclick="setDateRange(365)" 
                                class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                            Last Year
                        </button>
                    </div>
                </div>

                <!-- Generate Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" name="generate_pdf" 
                            class="bg-lime-500 hover:bg-lime-600 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        <span>PDF Report</span>
                    </button>
                    <button type="button" onclick="generateCSV()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                        <i data-feather="download" class="w-4 h-4"></i>
                        <span>CSV Export</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Types Info -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
            <div class="flex items-center space-x-2 mb-6">
                <i data-feather="info" class="text-fuchsia-400 w-6 h-6"></i>
                <h3 class="text-xl font-bold text-fuchsia-400">Available Reports</h3>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-gray-700 rounded-lg border-l-4 border-green-500">
                    <h4 class="font-semibold text-white mb-2 flex items-center">
                        <i data-feather="trending-up" class="w-4 h-4 mr-2 text-green-400"></i>
                        Sales Report
                    </h4>
                    <p class="text-gray-400 text-sm">Complete sales transactions, revenue analysis, and product performance.</p>
                </div>

                <div class="p-4 bg-gray-700 rounded-lg border-l-4 border-blue-500">
                    <h4 class="font-semibold text-white mb-2 flex items-center">
                        <i data-feather="settings" class="w-4 h-4 mr-2 text-blue-400"></i>
                        Production Report
                    </h4>
                    <p class="text-gray-400 text-sm">Production records, quantities, and efficiency metrics.</p>
                </div>

                <div class="p-4 bg-gray-700 rounded-lg border-l-4 border-yellow-500">
                    <h4 class="font-semibold text-white mb-2 flex items-center">
                        <i data-feather="box" class="w-4 h-4 mr-2 text-yellow-400"></i>
                        Inventory Report
                    </h4>
                    <p class="text-gray-400 text-sm">Stock levels, material usage, and inventory valuation.</p>
                </div>

                <div class="p-4 bg-gray-700 rounded-lg border-l-4 border-fuchsia-500">
                    <h4 class="font-semibold text-white mb-2 flex items-center">
                        <i data-feather="users" class="w-4 h-4 mr-2 text-fuchsia-400"></i>
                        Customers Analytics Report
                    </h4>
                    <p class="text-gray-400 text-sm">Customer database, top buyers, purchase history, and revenue analytics.</p>
                </div>

                <div class="p-4 bg-gray-700 rounded-lg border-l-4 border-purple-500">
                    <h4 class="font-semibold text-white mb-2 flex items-center">
                        <i data-feather="user-check" class="w-4 h-4 mr-2 text-purple-400"></i>
                        Employee Report
                    </h4>
                    <p class="text-gray-400 text-sm">Employee list, schedules, and activity logs.</p>
                </div>

                <div class="p-4 bg-gray-700 rounded-lg border-l-4 border-lime-500">
                    <h4 class="font-semibold text-white mb-2 flex items-center">
                        <i data-feather="layers" class="w-4 h-4 mr-2 text-lime-400"></i>
                        Comprehensive Report
                    </h4>
                    <p class="text-gray-400 text-sm">All-in-one report with complete business overview.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setDateRange(days) {
    const today = new Date();
    const fromDate = new Date(today);
    fromDate.setDate(today.getDate() - days);
    
    document.querySelector('input[name="date_from"]').value = fromDate.toISOString().split('T')[0];
    document.querySelector('input[name="date_to"]').value = today.toISOString().split('T')[0];
}

function generateCSV() {
    const reportType = document.querySelector('select[name="report_type"]').value;
    const dateFrom = document.querySelector('input[name="date_from"]').value;
    const dateTo = document.querySelector('input[name="date_to"]').value;
    
    if (!reportType) {
        alert('Please select a report type!');
        return;
    }
    
    if (!dateFrom || !dateTo) {
        alert('Please select both start and end dates!');
        return;
    }
    
    if (new Date(dateFrom) > new Date(dateTo)) {
        alert('Start date cannot be after end date!');
        return;
    }
    
    // Open CSV export in new window
    const url = `?page=exports_csv&type=${reportType}&from=${dateFrom}&to=${dateTo}`;
    window.open(url, '_blank');
}

// Auto-hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.bg-green-900, .bg-red-900');
    alerts.forEach(alert => {
        alert.style.display = 'none';
    });
}, 5000);

// Feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
