<!-- Employee Reports Page -->
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-fuchsia-400 mb-2">My Reports  (<strong class="text-fuchsia-400"><?= htmlspecialchars($user_name) ?></strong>)</h2>
        <!-- <p class="text-gray-400">View and download your production and sales reports</p>
        <p class="text-gray-500 text-sm mt-1">Showing activity for: <strong class="text-fuchsia-400"><?= htmlspecialchars($user_name) ?></strong></p> -->
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

    <!-- My Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Production Records</p>
                    <h3 class="text-2xl font-bold text-blue-400"><?= $total_productions ?></h3>
                    <p class="text-gray-500 text-xs mt-1">Total entries</p>
                </div>
                <i data-feather="settings" class="text-blue-500 w-8 h-8"></i>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-fuchsia-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Units Produced</p>
                    <h3 class="text-2xl font-bold text-fuchsia-400"><?= number_format($total_units_produced) ?></h3>
                    <p class="text-gray-500 text-xs mt-1">Total quantity</p>
                </div>
                <i data-feather="package" class="text-fuchsia-500 w-8 h-8"></i>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Sales Transactions</p>
                    <h3 class="text-2xl font-bold text-green-400"><?= $total_sales ?></h3>
                    <p class="text-gray-500 text-xs mt-1">Total sales</p>
                </div>
                <i data-feather="shopping-cart" class="text-green-500 w-8 h-8"></i>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-lime-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm">Revenue Generated</p>
                    <h3 class="text-2xl font-bold text-lime-400"><?= number_format($today_revenue,0) ?> <span class="text-sm"> RWF</span></h3>
                    <p class="text-gray-500 text-xs mt-1">Total earnings</p>
                </div>
                <i data-feather="dollar-sign" class="text-lime-500 w-8 h-8"></i>
            </div>
        </div>
    </div>

    <!-- Report Generation and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Generate Report Card -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
            <div class="flex items-center space-x-2 mb-6">
                <i data-feather="download" class="text-fuchsia-400 w-6 h-6"></i>
                <h3 class="text-xl font-bold text-fuchsia-400">Generate My Report</h3>
            </div>

            <form method="POST" class="space-y-4" id="reportForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <!-- Report Type -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Report Type</label>
                    <select name="report_type" required 
                            class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-fuchsia-500">
                        <option value="">— Select Report Type —</option>
                        <option value="production">My Production Records</option>
                        <option value="sales">My Sales Records</option>
                        <option value="combined">Combined Report (Production + Sales)</option>
                    </select>
                </div>


                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">From Date</label>
                        <input type="date" name="date_from" required 
                               value="<?= date('Y-m-d', strtotime('-30 days')) ?>"
                               max="<?= date('Y-m-d') ?>"
                               onchange="validateDateRange(this.value, document.querySelector('input[name=\'date_to\']').value)"
                               class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-fuchsia-500">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">To Date</label>
                        <input type="date" name="date_to" required 
                               value="<?= date('Y-m-d') ?>"
                               max="<?= date('Y-m-d') ?>"
                               onchange="validateDateRange(document.querySelector('input[name="date_from"]').value, this.value)"
                               class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-fuchsia-500">
                    </div>
                </div>
                
                <script>
                function validateDateRange(fromDate, toDate) {
                    if (new Date(fromDate) > new Date(toDate)) {
                        alert('Invalid date range. From date cannot be later than To date.');
                        event.preventDefault();
                        return false;
                    }
                    return true;
                }
                </script>

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
                        <button type="button" onclick="setDateRange(180)" 
                                class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                            Last 6 Months
                        </button>
                    </div>
                </div>

                <!-- Generate Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" name="generate_pdf" 
                            class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2"
                            onclick="return validateReportForm()">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        <span>PDF Report</span>
                    </button>
                    <button type="button" onclick="generateEmployeeCSV()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                        <i data-feather="download" class="w-4 h-4"></i>
                        <span>CSV Export</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Activity -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
            <div class="flex items-center space-x-2 mb-6">
                <i data-feather="activity" class="text-fuchsia-400 w-6 h-6"></i>
                <h3 class="text-xl font-bold text-fuchsia-400">My Recent Activity</h3>
            </div>

            <div class="space-y-3 max-h-96 overflow-y-auto">
                <?php if (!empty($activities)): ?>
                    <?php foreach ($activities as $activity): ?>
                        <?php
                            $is_production = $activity['type'] === 'production';
                            $icon = $is_production ? 'settings' : 'shopping-cart';
                            $color = $is_production ? 'blue' : 'green';
                            $label = $is_production ? 'Produced' : 'Sold';
                        ?>
                        <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-650 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-<?= $color ?>-500 rounded-full flex items-center justify-center">
                                    <i data-feather="<?= $icon ?>" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="text-white font-semibold text-sm"><?= $label ?> Product #<?= $activity['product_id'] ?></p>
                                    <p class="text-gray-400 text-xs"><?= date('M j, Y g:i A', strtotime($activity['created_at'])) ?></p>
                                </div>
                            </div>
                            <div class="text-<?= $color ?>-400 font-bold text-lg">
                                <?= $activity['quantity'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i data-feather="inbox" class="w-16 h-16 mx-auto mb-3 opacity-50"></i>
                        <p>No activity recorded yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Report Info -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 rounded-xl p-6 shadow-lg border border-gray-700 mt-8">
        <div class="flex items-start space-x-4">
            <i data-feather="info" class="text-fuchsia-400 w-6 h-6 flex-shrink-0 mt-1"></i>
            <div>
                <h4 class="text-white font-semibold mb-2">About Your Reports</h4>
                <p class="text-gray-400 text-sm mb-2">
                    These reports contain your personal work records including production entries and sales transactions. 
                    You can download these reports as PDF documents for your records or performance review.
                </p>
                <ul class="text-gray-400 text-sm space-y-1">
                    <li>• <strong>Production Records:</strong> Shows all products you've produced with quantities and dates</li>
                    <li>• <strong>Sales Records:</strong> Shows all sales transactions you've processed</li>
                    <li>• <strong>Combined Report:</strong> Comprehensive report with all your activities</li>
                </ul>
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

function validateReportForm() {
    const reportType = document.querySelector('select[name="report_type"]');
    if (!reportType.value) {
        alert('Please select a report type');
        reportType.focus();
        return false;
    }
    
    const dateFrom = document.querySelector('input[name="date_from"]').value;
    const dateTo = document.querySelector('input[name="date_to"]').value;
    
    if (new Date(dateFrom) > new Date(dateTo)) {
        alert('Invalid date range. From date cannot be later than To date.');
        return false;
    }
    
    return true;
}

function generateEmployeeCSV() {
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
    
    // Get user ID from PHP session
    const userId = <?= json_encode($user_id) ?>;
    
    // Open CSV export in new window
    const url = `?page=exports_csv&type=employee_${reportType}&from=${dateFrom}&to=${dateTo}&user_id=${userId}`;
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
