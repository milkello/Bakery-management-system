<!-- Production Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="package" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= number_format($total_production) ?></h3>
        <p class="text-gray-400">Today's Productions</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="clock" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= number_format($today_production) ?></h3>
        <p class="text-gray-400">Number of Productions</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="box" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $toal_stock ?></h3>
        <p class="text-gray-400">Products in Stock</p>
    </div>
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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Production Form -->
    <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
        <h3 class="text-xl font-bold text-lime-400 mb-6">Produce Products</h3>
        
        <form method="POST" class="space-y-4" id="productionForm">
            <div>
                <label class="block text-gray-400 text-sm mb-2">Select Product</label>
                <select name="product_id" required 
                        class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500"
                        id="productSelect">
                    <option value="">— Choose Product —</option>
                    <?php foreach ($products as $product): ?>
                        <?php 
                            $has_plan = isset($today_plans[$product['id']]);
                            $has_production = isset($today_production_data[$product['id']]);
                            $prod_qty = $has_production ? $today_production_data[$product['id']]['quantity_produced'] : 0;
                            
                            // Indicators: ✅ has plan, 📝 has production, ⚠️ no plan
                            if ($has_production) {
                                $indicator = ' 📝 (' . $prod_qty . ' recorded)';
                            } elseif ($has_plan) {
                                $indicator = ' ✅';
                            } else {
                                $indicator = ' ⚠️';
                            }
                        ?>
                        <option value="<?= $product['id'] ?>" 
                                data-has-plan="<?= $has_plan ? 'true' : 'false' ?>"
                                data-has-production="<?= $has_production ? 'true' : 'false' ?>"
                                data-prod-qty="<?= $prod_qty ?>">
                            <?= htmlspecialchars($product['name']) ?><?= $indicator ?>
                            <?php if ($product['sku']): ?>
                                (<?= htmlspecialchars($product['sku']) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2" id="quantityLabel">Quantity to Produce</label>
                <input type="number" name="quantity" min="1" required 
                       class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500"
                       placeholder="How many units?"
                       id="quantityInput">
            </div>

            <!-- Material Plan Status -->
            <div id="planStatus" class="p-4 rounded-lg hidden">
                <div id="planDetails" class="text-sm"></div>
            </div>

            <!-- Production Status (if exists) -->
            <div id="productionStatus" class="p-4 rounded-lg hidden">
                <div id="productionDetails" class="text-sm"></div>
            </div>

            <input type="hidden" name="is_update" id="isUpdateInput" value="0">
            
            <button type="submit" name="produce" id="produceButton"
                    class="w-full bg-lime-500 hover:bg-lime-600 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                🚀 Start Production
            </button>
        </form>
    </div>

    <!-- Current Inventory -->
    <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
        <h3 class="text-xl font-bold text-lime-400 mb-6">Raw Materials Inventory</h3>
        
        <div class="space-y-3">
            <?php foreach ($raw_materials as $material): ?>
                <div class="flex justify-between items-center p-3 bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                            <i data-feather="box" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-white font-semibold"><?= htmlspecialchars($material['name']) ?></p>
                            <p class="text-gray-400 text-sm"><?= htmlspecialchars($material['unit']) ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-white font-bold text-lg"><?= htmlspecialchars($material['stock_quantity']) ?></p>
                        <p class="text-gray-400 text-sm">in stock</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Recent Production -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">Recent Production</h3>
        <div class="flex items-center space-x-4">
            <a href="?page=export_page_pdf&type=production" target="_blank"
               class="flex items-center space-x-2 bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-4 py-2 rounded-lg transition">
                <i data-feather="download" class="w-4 h-4"></i>
                <span>Export PDF</span>
            </a>
            <div class="flex items-center space-x-2 text-gray-400">
                <i data-feather="calendar" class="w-4 h-4"></i>
                <span>Latest Activities</span>
            </div>
        </div>
    </div>
    
    <?php if (!empty($recent_production)): ?>
        <div class="space-y-4">
            <?php foreach ($recent_production as $production): ?>
                <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg border border-gray-600">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-lime-500 rounded-full flex items-center justify-center">
                            <i data-feather="package" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <p class="text-white font-semibold"><?= htmlspecialchars($production['product_name']) ?></p>
                            <p class="text-gray-400 text-sm">
                                <?= $production['quantity_produced'] ?> units produced
                                <?php if ($production['raw_materials_used']): ?>
                                    • <?= htmlspecialchars($production['raw_materials_used']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-300 text-sm"><?= date('M j, g:i A', strtotime($production['created_at'])) ?></p>
                        <p class="text-gray-400 text-xs">by <?= htmlspecialchars($production['username'] ?? 'System') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8">
            <div class="flex flex-col items-center justify-center text-gray-500">
                <i data-feather="package" class="w-16 h-16 mb-4"></i>
                <h4 class="text-lg font-semibold mb-2">No Production Yet</h4>
                <p>Start producing products to see them here</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const productSelect = document.getElementById('productSelect');
    const planStatus = document.getElementById('planStatus');
    const planDetails = document.getElementById('planDetails');
    const productionStatus = document.getElementById('productionStatus');
    const productionDetails = document.getElementById('productionDetails');
    const produceButton = document.getElementById('produceButton');
    const isUpdateInput = document.getElementById('isUpdateInput');
    const quantityInput = document.getElementById('quantityInput');
    const quantityLabel = document.getElementById('quantityLabel');
    
    // Show plan status when product is selected
    productSelect.addEventListener('change', function() {
        const productId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const hasPlan = selectedOption.getAttribute('data-has-plan') === 'true';
        const hasProduction = selectedOption.getAttribute('data-has-production') === 'true';
        const prodQty = selectedOption.getAttribute('data-prod-qty');
        
        if (productId) {
            // Check if production already exists
            if (hasProduction) {
                // SHOW UPDATE MODE
                productionStatus.className = 'p-4 bg-blue-900 border border-blue-700 rounded-lg';
                productionDetails.innerHTML = '<div class="text-blue-300"><strong>📝 Production Already Recorded</strong><br><span class="text-sm">Current: ' + prodQty + ' units. You can UPDATE to correct mistakes.</span></div>';
                productionStatus.classList.remove('hidden');
                
                // Set form to update mode
                isUpdateInput.value = '1';
                quantityInput.value = prodQty;
                quantityLabel.textContent = 'Update Quantity';
                produceButton.innerHTML = '✏️ Update Production';
                produceButton.className = 'w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-colors';
                
                // Hide plan status when showing production status
                planStatus.classList.add('hidden');
            } else {
                // SHOW NEW PRODUCTION MODE
                productionStatus.classList.add('hidden');
                isUpdateInput.value = '0';
                quantityInput.value = '';
                quantityLabel.textContent = 'Quantity to Produce';
                produceButton.innerHTML = '🚀 Start Production';
                
                if (hasPlan) {
                    planStatus.className = 'p-4 bg-green-900 border border-green-700 rounded-lg';
                    planDetails.innerHTML = '<div class="text-green-300"><strong>✅ Material plan exists</strong><br><span class="text-sm">You can proceed with production</span></div>';
                    planStatus.classList.remove('hidden');
                    produceButton.disabled = false;
                    produceButton.className = 'w-full bg-lime-500 hover:bg-lime-600 text-white font-bold py-3 px-4 rounded-lg transition-colors';
                } else {
                    planStatus.className = 'p-4 bg-yellow-900 border border-yellow-700 rounded-lg';
                    planDetails.innerHTML = '<div class="text-yellow-300"><strong>⚠️ No material plan for today</strong><br><span class="text-sm">Please create an ingredient plan in Product Boards before production</span></div>';
                    planStatus.classList.remove('hidden');
                    produceButton.disabled = false; // Allow submission to show proper error
                    produceButton.className = 'w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-lg transition-colors';
                }
            }
        } else {
            planStatus.classList.add('hidden');
            productionStatus.classList.add('hidden');
            isUpdateInput.value = '0';
            quantityInput.value = '';
            quantityLabel.textContent = 'Quantity to Produce';
            produceButton.innerHTML = '🚀 Start Production';
            produceButton.disabled = false;
            produceButton.className = 'w-full bg-lime-500 hover:bg-lime-600 text-white font-bold py-3 px-4 rounded-lg transition-colors';
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.mb-6.p-4');
        alerts.forEach(alert => {
            if (alert.classList.contains('bg-green-900') || alert.classList.contains('bg-red-900')) {
                alert.style.display = 'none';
            }
        });
    }, 5000);
    
    // Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>