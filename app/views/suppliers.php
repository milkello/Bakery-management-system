<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <div class="lg:col-span-2 bg-gray-800 rounded-xl p-6 shadow-lg">
        <h3 class="text-xl font-bold text-lime-400 mb-6">Suppliers</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-3 px-4 text-lime-400">Name</th>
                        <th class="text-left py-3 px-4 text-lime-400">Phone</th>
                        <th class="text-left py-3 px-4 text-lime-400">Email</th>
                        <th class="text-left py-3 px-4 text-lime-400">Total Supplied Sales</th>
                        <th class="text-left py-3 px-4 text-lime-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($suppliers)): ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-700">
                                <td class="py-3 px-4 text-gray-200">
                                    <?= htmlspecialchars($supplier['name']) ?>
                                </td>
                                <td class="py-3 px-4 text-gray-300">
                                    <?= htmlspecialchars($supplier['phone'] ?? '') ?>
                                </td>
                                <td class="py-3 px-4 text-gray-300">
                                    <?= htmlspecialchars($supplier['email'] ?? '') ?>
                                </td>
                                <td class="py-3 px-4 text-lime-400 font-semibold">
                                    <?php
                                        $totalVal = $supplierTotals[$supplier['id']] ?? 0;
                                        echo number_format($totalVal, 0) . ' Rwf';
                                    ?>
                                </td>
                                <td class="py-3 px-4">
                                    <button 
                                        type="button"
                                        class="bg-lime-500 hover:bg-lime-600 text-white text-sm font-semibold px-3 py-1 rounded-lg"
                                        onclick="openSupplierHistoryModal(<?= (int)$supplier['id'] ?>, '<?= htmlspecialchars($supplier['name'], ENT_QUOTES) ?>')">
                                        Record Sale / History
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">No suppliers yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 shadow-lg space-y-6">
        <h3 class="text-xl font-bold text-lime-400 mb-2">Top Suppliers</h3>

        <div>
            <h4 class="text-sm font-semibold text-gray-300 mb-2">This Week</h4>
            <ul class="space-y-1 text-sm">
                <?php if (!empty($topWeekly)): ?>
                    <?php foreach ($topWeekly as $row): ?>
                        <li class="flex justify-between text-gray-300">
                            <span><?= htmlspecialchars($row['name']) ?></span>
                            <span class="text-lime-400 font-semibold"><?= number_format($row['total_value'], 0) ?> Rwf</span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-gray-500">No data yet.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div>
            <h4 class="text-sm font-semibold text-gray-300 mb-2">This Month</h4>
            <ul class="space-y-1 text-sm">
                <?php if (!empty($topMonthly)): ?>
                    <?php foreach ($topMonthly as $row): ?>
                        <li class="flex justify-between text-gray-300">
                            <span><?= htmlspecialchars($row['name']) ?></span>
                            <span class="text-fuchsia-400 font-semibold"><?= number_format($row['total_value'], 0) ?> Rwf</span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-gray-500">No data yet.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div>
            <h4 class="text-sm font-semibold text-gray-300 mb-2">This Year</h4>
            <ul class="space-y-1 text-sm">
                <?php if (!empty($topYearly)): ?>
                    <?php foreach ($topYearly as $row): ?>
                        <li class="flex justify-between text-gray-300">
                            <span><?= htmlspecialchars($row['name']) ?></span>
                            <span class="text-lime-400 font-semibold"><?= number_format($row['total_value'], 0) ?> Rwf</span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-gray-500">No data yet.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div id="supplierHistoryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
  <div class="bg-gray-800 rounded-lg p-6 w-full max-w-3xl mx-4">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h3 class="text-lg font-bold text-lime-400" id="supplierHistoryTitle">Supplier</h3>
            <p class="text-gray-400 text-xs">Record a new sale for this supplier and optionally export history as PDF.</p>
        </div>
        <button type="button" class="text-gray-400 hover:text-white" onclick="closeSupplierHistoryModal()">✕</button>
    </div>

    <!-- Per-supplier sales form -->
    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" id="supplierSaleForm">
        <input type="hidden" name="mode" value="create_supplier_sale">
        <input type="hidden" name="supplier_id" id="supplierSaleSupplierId" value="">

        <div class="md:col-span-2">
            <label class="block text-gray-400 text-xs mb-1">Product</label>
            <select name="product_id" required 
                    class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500" id="supplierSaleProduct">
                <option value="">-- Choose Product --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= htmlspecialchars($product['id']) ?>" data-price="<?= htmlspecialchars($product['price'] ?? 0) ?>" data-stock="<?= htmlspecialchars($product['stock'] ?? 0) ?>">
                        <?= htmlspecialchars($product['sku'] ?? 'No Code') ?> — <?= htmlspecialchars($product['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-400 text-xs mb-1">Customer</label>
            <select name="customer_id" id="supplierSaleCustomer" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                <option value="" data-type="Regular">-- Walk-in Customer --</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= htmlspecialchars($customer['id']) ?>" data-type="<?= htmlspecialchars($customer['customer_type']) ?>">
                        <?= htmlspecialchars($customer['name']) ?>
                        <?= !empty($customer['phone']) ? '(' . htmlspecialchars($customer['phone']) . ')' : '' ?>
                        - <?= htmlspecialchars($customer['customer_type']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-gray-400 text-xs mb-1">Quantity</label>
            <input type="number" name="quantity" min="1" required
                   class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-lime-500"
                   id="supplierSaleQty">
        </div>

        <div>
            <label class="block text-gray-400 text-xs mb-1">Unit Price (Rwf)</label>
            <input type="number" name="unit_price" step="0.01" required
                   class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-lime-500"
                   id="supplierSaleUnitPrice">
        </div>

        <div>
            <label class="block text-gray-400 text-xs mb-1">Customer Type</label>
            <select name="customer_type" id="supplierSaleCustomerType" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                <option value="Regular">Regular</option>
                <option value="Wholesale">Wholesale</option>
                <option value="Online">Online</option>
                <option value="VIP">VIP</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-400 text-xs mb-1">Payment Method</label>
            <select name="payment_method" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                <option value="Cash">Cash</option>
                <option value="MoMo">MoMo</option>
                <option value="Card">Card</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <div class="bg-gray-700 rounded-lg p-3 flex justify-between items-center">
                <span class="text-gray-400 text-xs">Total Amount</span>
                <span id="supplierSaleTotal" class="text-lime-400 font-bold text-lg">0.00 Rwf</span>
            </div>
            <div class="mt-3 flex justify-end space-x-2">
                <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Record Sale</button>
            </div>
        </div>
    </form>

    <!-- Export PDF form -->
    <form id="supplierHistoryForm" method="GET" target="_blank" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-2">
        <input type="hidden" name="page" value="suppliers">
        <input type="hidden" name="action" value="export_pdf">
        <input type="hidden" name="supplier_id" id="supplierHistoryId" value="">

        <div>
            <label class="block text-gray-400 text-xs mb-1">Start Date</label>
            <input type="date" name="start_date" value="<?= date('Y-m-01') ?>" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-gray-400 text-xs mb-1">End Date</label>
            <input type="date" name="end_date" value="<?= date('Y-m-d') ?>" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
        </div>
        <div class="md:col-span-2 flex items-end">
            <button type="submit" class="bg-gray-600 hover:bg-gray-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Export History PDF</button>
        </div>
    </form>

    <p class="text-gray-500 text-xs">Sales recorded with this form are automatically linked to the selected supplier. PDF export shows the supplier's sales in the chosen date range.</p>
  </div>
</div>

<script>
function openSupplierHistoryModal(id, name) {
    const modal = document.getElementById('supplierHistoryModal');
    const title = document.getElementById('supplierHistoryTitle');
    const idInput = document.getElementById('supplierHistoryId');
    const saleSupplierInput = document.getElementById('supplierSaleSupplierId');
    if (modal && title && idInput && saleSupplierInput) {
        title.textContent = 'Supplier - ' + name;
        idInput.value = id;
        saleSupplierInput.value = id;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeSupplierHistoryModal() {
    const modal = document.getElementById('supplierHistoryModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('supplierSaleProduct');
    const qtyInput = document.getElementById('supplierSaleQty');
    const unitPriceInput = document.getElementById('supplierSaleUnitPrice');
    const totalDisplay = document.getElementById('supplierSaleTotal');
    const customerSelect = document.getElementById('supplierSaleCustomer');
    const customerTypeSelect = document.getElementById('supplierSaleCustomerType');

    function calcTotal() {
        const q = parseInt(qtyInput.value || '0', 10) || 0;
        const u = parseFloat(unitPriceInput.value || '0') || 0;
        const t = q * u;
        totalDisplay.textContent = t.toFixed(2) + ' Rwf';
    }

    if (productSelect) {
        productSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (!opt) return;
            const p = parseFloat(opt.getAttribute('data-price') || '0');
            if (p > 0 && unitPriceInput) {
                unitPriceInput.value = p.toFixed(2);
                calcTotal();
            }
        });
    }

    if (qtyInput) qtyInput.addEventListener('input', calcTotal);
    if (unitPriceInput) unitPriceInput.addEventListener('input', calcTotal);

    if (customerSelect && customerTypeSelect) {
        customerSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (!opt) return;
            const ctype = opt.getAttribute('data-type');
            if (ctype) customerTypeSelect.value = ctype;
        });
    }
});

if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
