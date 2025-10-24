<!-- Production Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="package" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= number_format($total_production) ?></h3>
        <p class="text-gray-400">Total Productions</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="clock" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= number_format($today_production) ?></h3>
        <p class="text-gray-400">Today's Productions</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="box" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= count($products) ?></h3>
        <p class="text-gray-400">Available Products</p>
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
                    <option value="">-- Choose Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>" data-has-recipe="<?= isset($all_recipes[$product['id']]) ? 'true' : 'false' ?>">
                            <?= htmlspecialchars($product['name']) ?> 
                            <?php if ($product['sku']): ?>
                                (<?= htmlspecialchars($product['sku']) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">Quantity to Produce</label>
                <input type="number" name="quantity" min="1" required 
                       class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500"
                       placeholder="How many units?"
                       id="quantityInput">
            </div>

            <!-- Recipe Preview -->
            <div id="recipePreview" class="p-4 bg-gray-700 rounded-lg hidden">
                <h4 class="text-lime-400 font-semibold mb-2">Recipe Requirements:</h4>
                <div id="recipeDetails" class="text-gray-300 text-sm space-y-1">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Production Check Result -->
            <div id="productionCheck" class="p-4 rounded-lg hidden">
                <div id="checkResult"></div>
            </div>

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
        <div class="flex items-center space-x-2 text-gray-400">
            <i data-feather="calendar" class="w-4 h-4"></i>
            <span>Latest Activities</span>
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
// Preload all recipes data from PHP
const allRecipes = <?= json_encode($all_recipes) ?>;

document.addEventListener("DOMContentLoaded", function() {
    const productSelect = document.getElementById('productSelect');
    const quantityInput = document.getElementById('quantityInput');
    const recipePreview = document.getElementById('recipePreview');
    const recipeDetails = document.getElementById('recipeDetails');
    const productionCheck = document.getElementById('productionCheck');
    const checkResult = document.getElementById('checkResult');
    const produceButton = document.getElementById('produceButton');
    
    console.log('Available recipes:', allRecipes); // Debug log

    // Show recipe when product is selected
    productSelect.addEventListener('change', function() {
        const productId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const hasRecipe = selectedOption.getAttribute('data-has-recipe') === 'true';
        
        if (productId && hasRecipe && allRecipes[productId]) {
            const recipe = allRecipes[productId];
            let html = '';
            recipe.forEach(ingredient => {
                const quantity = ingredient.quantity || 0;
                const unit = ingredient.unit || '';
                const materialName = ingredient.material_name || 'Unknown Material';
                const available = ingredient.available_qty || 0;
                html += `<p>• ${materialName}: ${quantity}${unit} per unit (Available: ${available}${unit})</p>`;
            });
            recipeDetails.innerHTML = html;
            recipePreview.classList.remove('hidden');
            productionCheck.classList.add('hidden');
            
            // Check production feasibility if quantity is already entered
            if (quantityInput.value) {
                checkProductionFeasibility(productId, parseInt(quantityInput.value));
            }
        } else if (productId && !hasRecipe) {
            recipeDetails.innerHTML = '<p class="text-yellow-400">No recipe found for this product</p>';
            recipePreview.classList.remove('hidden');
            productionCheck.classList.add('hidden');
        } else {
            recipePreview.classList.add('hidden');
            productionCheck.classList.add('hidden');
        }
    });

    // Check production feasibility when quantity changes
    quantityInput.addEventListener('input', function() {
        const productId = productSelect.value;
        const quantity = parseInt(this.value);
        
        if (productId && quantity && allRecipes[productId]) {
            checkProductionFeasibility(productId, quantity);
        } else {
            productionCheck.classList.add('hidden');
            produceButton.disabled = false;
            produceButton.className = 'w-full bg-lime-500 hover:bg-lime-600 text-white font-bold py-3 px-4 rounded-lg transition-colors';
        }
    });

    function checkProductionFeasibility(productId, quantity) {
        if (!quantity || quantity < 1) {
            productionCheck.classList.add('hidden');
            return;
        }

        const recipe = allRecipes[productId];
        let impossibleReasons = [];

        recipe.forEach(ingredient => {
            const requiredPerUnit = ingredient.quantity || 0;
            const requiredTotal = requiredPerUnit * quantity;
            const available = parseFloat(ingredient.available_qty) || 0;

            console.log('Checking:', ingredient.material_name, 'Required:', requiredTotal, 'Available:', available);

            if (requiredTotal > available) {
                impossibleReasons.push({
                    material: ingredient.material_name,
                    required: requiredTotal,
                    available: available,
                    unit: ingredient.unit
                });
            }
        });

        // Display results
        if (impossibleReasons.length > 0) {
            productionCheck.className = 'p-4 bg-red-900 border border-red-700 rounded-lg text-red-300';
            let html = '<div class="font-semibold mb-2">🚫 IMPOSSIBLE PRODUCTION</div>';
            html += '<div class="text-sm">Insufficient materials:</div>';
            impossibleReasons.forEach(reason => {
                html += `<div class="text-sm mt-1">• <strong>${reason.material}</strong>: Need ${reason.required}${reason.unit}, Have ${reason.available}${reason.unit}</div>`;
            });
            checkResult.innerHTML = html;
            produceButton.disabled = true;
            produceButton.className = 'w-full bg-gray-500 cursor-not-allowed text-white font-bold py-3 px-4 rounded-lg';
            productionCheck.classList.remove('hidden');
        } else {
            productionCheck.className = 'p-4 bg-green-900 border border-green-700 rounded-lg text-green-300';
            checkResult.innerHTML = '<div class="font-semibold">✅ Production Possible</div><div class="text-sm">All materials are sufficient</div>';
            produceButton.disabled = false;
            produceButton.className = 'w-full bg-lime-500 hover:bg-lime-600 text-white font-bold py-3 px-4 rounded-lg transition-colors';
            productionCheck.classList.remove('hidden');
        }
    }
    
    // Auto-hide alerts after 5 seconds
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
});
</script>