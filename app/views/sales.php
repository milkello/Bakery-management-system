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
                        <option value="<?= htmlspecialchars($product['id']) ?>" data-price="<?= htmlspecialchars($product['price'] ?? 0) ?>" data-stock="<?= htmlspecialchars($product['stock'] ?? 0) ?>">
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
                <!-- Stock availability check -->
                <div id="stockCheck" class="p-3 rounded-lg hidden">
                    <div id="stockResult"></div>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <button type="button" id="payMoMoBtn" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-lg">Pay with MoMo</button>
                    <button type="submit" 
                        class="w-full bg-lime-500 hover:bg-lime-600 text-white font-semibold py-3 rounded-lg">Process Sale</button>
                </div>
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
<!-- Sales Report (PDF/CSV) -->
<div class="mt-6 mb-6 bg-gray-800 rounded-xl p-6 shadow-lg">
    <h3 class="text-lg font-bold text-lime-400 mb-4">Generate Sales Report</h3>
    <form method="GET" action="" target="_blank" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <input type="hidden" name="page" value="sales">
        <input type="hidden" name="action" value="report">
        <div>
            <label class="block text-gray-400 text-sm mb-1">Start Date</label>
            <input type="date" name="start_date" value="<?= date('Y-m-d', strtotime('-6 days')) ?>" required
                   class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none">
        </div>
        <div>
            <label class="block text-gray-400 text-sm mb-1">End Date</label>
            <input type="date" name="end_date" value="<?= date('Y-m-d') ?>" required
                   class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none">
        </div>
        <div class="flex space-x-2">
            <button type="submit" name="format" value="pdf" 
                    class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-4 py-2 rounded-lg">Download PDF</button>
            <button type="submit" name="format" value="csv" 
                    class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-semibold px-4 py-2 rounded-lg">Download CSV</button>
        </div>
    </form>
</div>
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
    const stockCheck = document.getElementById('stockCheck');
    const stockResult = document.getElementById('stockResult');
    const submitButton = document.querySelector('button[type="submit"]');
    const originalSubmitClass = submitButton.className;
    const disabledSubmitClass = 'w-full bg-gray-500 cursor-not-allowed text-white font-semibold py-3 rounded-lg';
    
    // Auto-fill unit price when product is selected
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const productPrice = selectedOption.getAttribute('data-price');
        // check stock whenever product changes
        updateStockCheck();
        
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
    quantityInput.addEventListener('input', updateStockCheck);
    
    function calculateTotal() {
        const quantity = parseInt(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const total = quantity * unitPrice;
        
        totalAmountDisplay.textContent = `$${total.toFixed(2)}`;
    }

    function updateStockCheck() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            stockCheck.classList.add('hidden');
            submitButton.disabled = false;
            submitButton.className = originalSubmitClass;
            return;
        }

        const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
        const qty = parseInt(quantityInput.value) || 0;

        if (!qty) {
            // show available stock
            stockCheck.className = 'p-3 bg-green-900 border border-green-700 rounded-lg text-green-300';
            stockResult.innerHTML = `<div><strong>Available:</strong> ${stock} unit(s)</div>`;
            stockCheck.classList.remove('hidden');
            submitButton.disabled = false;
            submitButton.className = originalSubmitClass;
            return;
        }

        if (qty > stock) {
            stockCheck.className = 'p-3 bg-red-900 border border-red-700 rounded-lg text-red-300';
            stockResult.innerHTML = `<div class="font-semibold">🚫 Insufficient stock</div><div class="text-sm mt-1">Have ${stock} unit(s), requested ${qty} unit(s)</div>`;
            submitButton.disabled = true;
            submitButton.className = disabledSubmitClass;
            stockCheck.classList.remove('hidden');
        } else {
            stockCheck.className = 'p-3 bg-green-900 border border-green-700 rounded-lg text-green-300';
            stockResult.innerHTML = `<div class="font-semibold">✅ Stock available</div><div class="text-sm mt-1">Have ${stock} unit(s), requested ${qty} unit(s)</div>`;
            submitButton.disabled = false;
            submitButton.className = originalSubmitClass;
            stockCheck.classList.remove('hidden');
        }
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
            // update stock check (data-stock is embedded in the option)
            updateStockCheck();
        }
    });
    
    // Initial feather icons render
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

<!-- MoMo Payment Modal (placed after main scripts) -->
<div id="momoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
  <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
    <h3 class="text-lg font-bold text-lime-400 mb-4">Pay with MoMo</h3>
    <div class="space-y-3">
      <div>
        <label class="block text-gray-400 text-sm mb-1">Phone (e.g. 2507XXXXXXXX)</label>
        <input id="momoPhone" type="text" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg" placeholder="Enter phone number">
      </div>
      <div>
        <label class="block text-gray-400 text-sm mb-1">Amount</label>
        <div id="momoAmount" class="text-xl font-bold text-lime-400">$0.00</div>
      </div>
      <div id="momoStatus" class="text-sm text-gray-300"></div>
      <div class="flex justify-end space-x-2 mt-4">
        <button id="momoCancel" type="button" class="px-4 py-2 bg-gray-600 rounded-lg text-white">Cancel</button>
        <button id="momoConfirm" type="button" class="px-4 py-2 bg-emerald-600 rounded-lg text-white">Send Request</button>
      </div>
    </div>
  </div>
</div>

<script>
// MoMo flow: open modal, call momo2/initiate_payment.php, then submit sale form with payment method set to MoMo
document.addEventListener('DOMContentLoaded', function() {
    const payBtn = document.getElementById('payMoMoBtn');
    const momoModal = document.getElementById('momoModal');
    const momoPhone = document.getElementById('momoPhone');
    const momoAmount = document.getElementById('momoAmount');
    const momoStatus = document.getElementById('momoStatus');
    const momoCancel = document.getElementById('momoCancel');
    const momoConfirm = document.getElementById('momoConfirm');
    const totalDisplay = document.getElementById('totalAmount');
    const salesForm = document.querySelector('form');
    const paymentSelect = document.querySelector('select[name="payment_method"]');

    function openModal() {
        // set amount display
        momoAmount.textContent = totalDisplay.textContent || '$0.00';
        momoStatus.textContent = '';
        momoPhone.value = '';
        momoModal.classList.remove('hidden');
        momoModal.classList.add('flex');
    }
    function closeModal() {
        momoModal.classList.add('hidden');
        momoModal.classList.remove('flex');
    }

    payBtn && payBtn.addEventListener('click', function(e) {
        e.preventDefault();
        // ensure product and qty selected
        const productId = document.querySelector('select[name="product_id"]').value;
        const qty = parseInt(document.querySelector('input[name="quantity"]').value) || 0;
        if (!productId || qty < 1) {
            alert('Please select a product and enter quantity before paying.');
            return;
        }
        openModal();
    });

    momoCancel && momoCancel.addEventListener('click', function() { closeModal(); });

    momoConfirm && momoConfirm.addEventListener('click', async function() {
        const phone = momoPhone.value.trim();
        if (!phone) { momoStatus.textContent = 'Please enter phone number.'; return; }

        // amount as number (strip $)
        const amtText = (totalDisplay.textContent || '$0').replace(/[^0-9\.\-]/g, '');
        const amount = parseFloat(amtText) || 0;
        if (amount <= 0) { momoStatus.textContent = 'Invalid amount.'; return; }

        momoStatus.textContent = 'Sending payment request...';
        momoConfirm.disabled = true;

        try {
            const resp = await fetch('momo2/initiate_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ phone: phone, amount: String(amount), currency: 'XAF', externalId: '' })
            });
            const data = await resp.json();

            // success if accepted or mock
            if (data.status === 'accepted' || data.status === 'mock' || data.status === 'accepted') {
                momoStatus.textContent = 'Payment request sent. Recording sale...';
                // set payment method to MoMo and add hidden payment_reference then submit the form
                if (paymentSelect) paymentSelect.value = 'MoMo';
                // add hidden inputs
                let refInput = document.querySelector('input[name="payment_reference"]');
                if (!refInput) {
                    refInput = document.createElement('input');
                    refInput.type = 'hidden';
                    refInput.name = 'payment_reference';
                    salesForm.appendChild(refInput);
                }
                refInput.value = data.reference || data.referenceId || data.reference_id || '';

                // optionally include payer phone
                let phoneInput = document.querySelector('input[name="payer_phone"]');
                if (!phoneInput) {
                    phoneInput = document.createElement('input');
                    phoneInput.type = 'hidden';
                    phoneInput.name = 'payer_phone';
                    salesForm.appendChild(phoneInput);
                }
                phoneInput.value = phone;

                // submit the existing sales form to record the sale (will redirect)
                salesForm.submit();
            } else {
                momoStatus.textContent = 'Failed to send payment request: ' + (data.error || JSON.stringify(data));
                momoConfirm.disabled = false;
            }
        } catch (err) {
            momoStatus.textContent = 'Network error: ' + err.message;
            momoConfirm.disabled = false;
        }
    });
});
</script>