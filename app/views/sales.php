<!-- Sales Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="shopping-cart" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= number_format($total_sales) ?></h3>
        <p class="text-gray-400">Total Sales</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="dollar-sign" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400">$<?= number_format($total_revenue, 2) ?></h3>
        <p class="text-gray-400">Total Revenue</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="trending-up" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400">$<?= number_format($today_revenue, 2) ?></h3>
        <p class="text-gray-400">Today's Revenue</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="bar-chart-2" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400">$<?= number_format($avg_sale_value, 2) ?></h3>
        <p class="text-gray-400">Avg. Sale Value</p>
    </div>
</div>

<!-- Sales Management -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Sales Form -->
    <div class="lg:col-span-2 bg-gray-800 rounded-xl p-6 shadow-lg">
        <h3 class="text-xl font-bold text-lime-400 mb-6">Record New Sale</h3>
        
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-gray-400 text-sm mb-2">Select Product</label>
                <select name="product_id" required 
                        class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                    <option value="">-- Choose Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['id']) ?>" data-price="<?= htmlspecialchars($product['price'] ?? 0) ?>">
                            <?= htmlspecialchars($product['sku'] ?? 'No Code') ?> — <?= htmlspecialchars($product['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">Quantity</label>
                <input type="number" name="quantity" min="1" required 
                       class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500"
                       placeholder="Enter quantity">
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">Unit Price ($)</label>
                <input type="number" name="unit_price" step="0.01" required 
                       class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500"
                       placeholder="0.00" id="unitPrice">
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">Customer Type</label>
                <select name="customer_type" 
                        class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                    <option value="Regular">Regular</option>
                    <option value="Wholesale">Wholesale</option>
                    <option value="Online">Online</option>
                    <option value="VIP">VIP</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">Payment Method</label>
                <select name="payment_method" 
                        class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                    <option value="Cash">Cash</option>
                    <option value="MoMo">MoMo</option>
                    <option value="Card">Card</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <div class="bg-gray-700 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Total Amount:</span>
                        <span id="totalAmount" class="text-2xl font-bold text-lime-400">$0.00</span>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-lime-500 hover:bg-lime-600 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center space-x-2">
                    <i data-feather="credit-card" class="w-5 h-5"></i>
                    <span>Process Sale</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Stats -->
    <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
        <h3 class="text-xl font-bold text-lime-400 mb-6">Sales Overview</h3>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                        <i data-feather="clock" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Last Sale</p>
                        <p class="text-white font-semibold text-sm">
                            <?php if(!empty($sales_logs)): ?>
                                <?= date('M j, g:i A', strtotime($sales_logs[0]['created_at'])) ?>
                            <?php else: ?>
                                No sales yet
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-fuchsia-500 rounded-full flex items-center justify-center">
                        <i data-feather="users" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Top Customer</p>
                        <p class="text-white font-semibold text-sm">Regular</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                        <i data-feather="credit-card" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Popular Payment</p>
                        <p class="text-white font-semibold text-sm">Cash</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-fuchsia-500 rounded-full flex items-center justify-center">
                        <i data-feather="package" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Available Products</p>
                        <p class="text-white font-semibold text-sm"><?= count($products) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales History -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">Sales History</h3>
        <div class="flex items-center space-x-2 text-gray-400">
            <i data-feather="calendar" class="w-4 h-4"></i>
            <span>Recent Transactions</span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 px-4 text-lime-400">Product</th>
                    <th class="text-left py-3 px-4 text-lime-400">Quantity</th>
                    <th class="text-left py-3 px-4 text-lime-400">Unit Price</th>
                    <th class="text-left py-3 px-4 text-lime-400">Total</th>
                    <th class="text-left py-3 px-4 text-lime-400">Customer</th>
                    <th class="text-left py-3 px-4 text-lime-400">Payment</th>
                    <th class="text-left py-3 px-4 text-lime-400">Sold By</th>
                    <th class="text-left py-3 px-4 text-lime-400">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($sales_logs as $sale): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-700">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-lime-500 rounded-full flex items-center justify-center">
                                <i data-feather="package" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($sale['product_name']) ?></p>
                                <p class="text-gray-400 text-sm"><?= htmlspecialchars($sale['product_code'] ?? 'No Code') ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="bg-lime-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            <?= number_format($sale['qty']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-fuchsia-400 font-bold">$<?= number_format($sale['unit_price'], 2) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-lime-400 font-bold">$<?= number_format($sale['total_price'], 2) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-full text-xs">
                            <?= htmlspecialchars($sale['customer_type']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="bg-fuchsia-500 text-white px-2 py-1 rounded-full text-xs">
                            <?= htmlspecialchars($sale['payment_method']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-fuchsia-500 rounded-full flex items-center justify-center">
                                <i data-feather="user" class="w-3 h-3 text-white"></i>
                            </div>
                            <span class="text-gray-300"><?= htmlspecialchars($sale['sold_by'] ?? 'Unknown') ?></span>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-gray-300">
                            <div class="font-medium text-sm"><?= date('M j, Y', strtotime($sale['created_at'])) ?></div>
                            <div class="text-gray-400 text-xs"><?= date('g:i A', strtotime($sale['created_at'])) ?></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($sales_logs)): ?>
                <tr>
                    <td colspan="8" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <i data-feather="shopping-cart" class="w-16 h-16 mb-4"></i>
                            <h4 class="text-lg font-semibold mb-2">No Sales Records</h4>
                            <p>Start recording sales to see them here.</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if(!empty($sales_logs)): ?>
    <div class="mt-6 flex justify-between items-center text-gray-400 text-sm">
        <div>
            Showing <?= count($sales_logs) ?> recent sales records
        </div>
        <div class="flex items-center space-x-1">
            <i data-feather="info" class="w-4 h-4"></i>
            <span>All transactions are recorded in real-time</span>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const productSelect = document.querySelector('select[name="product_id"]');
    const quantityInput = document.querySelector('input[name="quantity"]');
    const unitPriceInput = document.getElementById('unitPrice');
    const totalAmountDisplay = document.getElementById('totalAmount');
    
    // Auto-fill unit price when product is selected
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const productPrice = selectedOption.getAttribute('data-price');
        
        if (productPrice && productPrice > 0) {
            unitPriceInput.value = parseFloat(productPrice).toFixed(2);
            calculateTotal();
        } else {
            unitPriceInput.value = '';
            totalAmountDisplay.textContent = '$0.00';
        }
    });
    
    // Calculate total when quantity or price changes
    quantityInput.addEventListener('input', calculateTotal);
    unitPriceInput.addEventListener('input', calculateTotal);
    
    function calculateTotal() {
        const quantity = parseInt(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const total = quantity * unitPrice;
        
        totalAmountDisplay.textContent = `$${total.toFixed(2)}`;
    }
    
    // Form validation
    const salesForm = document.querySelector('form');
    
    salesForm.addEventListener('submit', function() {
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.innerHTML = `
            <i data-feather="loader" class="w-5 h-5 animate-spin"></i>
            <span>Processing...</span>
        `;
        submitButton.disabled = true;
        
        // Re-render feather icons
        feather.replace();
    });
    
    // Stock validation (you might want to implement this with AJAX)
    productSelect.addEventListener('change', async function() {
        const productId = this.value;
        if (productId) {
            // You could add AJAX call here to check stock availability
            // and show warnings if quantity exceeds available stock
        }
    });
    
    // Initial feather icons render
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>