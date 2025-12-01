<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <div class="lg:col-span-2 bg-gray-800 rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-lime-400">Suppliers</h3>
            <button type="button" onclick="openSupplierCreateModal()" class="bg-lime-500 hover:bg-lime-600 text-white text-sm font-semibold px-4 py-2 rounded-lg flex items-center space-x-2">
                <span>+ Add Supplier</span>
            </button>
        </div>

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
                                <td class="py-3 px-4 flex items-center space-x-2">
                                    <button
                                        type="button"
                                        class="text-white text-sm font-semibold px-3 py-1 rounded-lg"
                                        onclick="openSupplierHistoryModal(<?= (int)$supplier['id'] ?>, '<?= htmlspecialchars($supplier['name'], ENT_QUOTES) ?>')">
                                        <i data-feather="clock" class="text-blue-400 hover:text-blue-300 w-4 h-4"></i>
                                    </button>
                                    
                                    <button
                                        type="button"
                                        class="flex items-center space-x-1 px-2 py-1 rounded-full hover:bg-gray-700 transition-colors duration-200"
                                        onclick="openSupplierEditModal(
                                            <?= (int)$supplier['id'] ?>, 
                                            '<?= htmlspecialchars($supplier['name']) ?>', 
                                            '<?= htmlspecialchars($supplier['phone'] ?? '') ?>', 
                                            '<?= htmlspecialchars($supplier['email'] ?? '') ?>', 
                                            '<?= htmlspecialchars($supplier['address'] ?? '') ?>'
                                        )"
                                        title="Edit Supplier">
                                        <i data-feather="edit" class="text-blue-400 hover:text-blue-300 w-4 h-4"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="p-1.5 rounded-full hover:bg-gray-700 transition-colors duration-200"
                                        onclick="if(confirm('Are you sure you want to delete supplier: <?= addslashes($supplier['name']) ?>? This action cannot be undone.')) { deleteSupplier(<?= (int)$supplier['id'] ?>, '<?= htmlspecialchars($supplier['name']) ?>') }"
                                        title="Delete Supplier">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
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

    <!-- Add Supplier Modal -->
    <div id="supplierCreateModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
      <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-lime-400">Add Supplier</h3>
            <button type="button" class="text-gray-400 hover:text-white" onclick="closeSupplierCreateModal()">✕</button>
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="mode" value="create_supplier">
            <div>
                <label class="block text-gray-400 text-xs mb-1">Name *</label>
                <input type="text" name="name" required class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Supplier name">
            </div>
            <div>
                <label class="block text-gray-400 text-xs mb-1">Phone</label>
                <input type="text" name="phone" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Phone number">
            </div>
            <div>
                <label class="block text-gray-400 text-xs mb-1">Email</label>
                <input type="email" name="email" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Email address">
            </div>
            <div>
                <label class="block text-gray-400 text-xs mb-1">Address</label>
                <input type="text" name="address" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Address">
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="closeSupplierCreateModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg text-sm">Save Supplier</button>
            </div>
        </form>
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

    <!-- Edit Supplier Modal -->
    <div id="supplierEditModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
      <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-lime-400">Edit Supplier</h3>
            <button type="button" class="text-gray-400 hover:text-white" onclick="closeSupplierEditModal()">✕</button>
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="mode" value="update_supplier">
            <input type="hidden" name="supplier_id" id="editSupplierId" value="">

            <div>
                <label class="block text-gray-400 text-xs mb-1">Name *</label>
                <input type="text" name="name" id="editSupplierName" required class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Supplier name">
            </div>
            <div>
                <label class="block text-gray-400 text-xs mb-1">Phone</label>
                <input type="text" name="phone" id="editSupplierPhone" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Phone number">
            </div>
            <div>
                <label class="block text-gray-400 text-xs mb-1">Email</label>
                <input type="email" name="email" id="editSupplierEmail" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Email address">
            </div>
            <div>
                <label class="block text-gray-400 text-xs mb-1">Address</label>
                <input type="text" name="address" id="editSupplierAddress" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Address">
            </div>
            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="closeSupplierEditModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg text-sm">Update Supplier</button>
            </div>
        </form>
      </div>
    </div>

    <!-- Main Supplier History Modal -->
<div id="supplierHistoryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
  <div class="bg-gray-800 rounded-xl p-6 w-full max-w-6xl mx-4 max-h-[95vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h3 class="text-lg font-bold text-lime-400" id="supplierHistoryTitle">Supplier</h3>
            <p class="text-gray-400 text-xs">Record new sales, allocate from trips, and review history for this supplier.</p>
        </div>
        <button type="button" class="text-gray-400 hover:text-white text-xl" onclick="closeSupplierHistoryModal()">✕</button>
    </div>

    <!-- Unified Tabs -->
    <div class="border-b border-gray-700 mb-6">
        <nav class="flex space-x-8">
            <button type="button" class="tab-button py-2 border-b-2 font-medium text-sm" data-tab="sale" data-active="true">
                <span class="flex items-center">
                    <span>Record Sale</span>
                </span>
            </button>
            <button type="button" class="tab-button py-2 border-b-2 font-medium text-sm" data-tab="trip-allocation">
                <span class="flex items-center">
                    <span>Trip Allocation</span>
                </span>
            </button>
            <button type="button" class="tab-button py-2 border-b-2 font-medium text-sm" data-tab="trips">
                <span class="flex items-center">
                    <span>Manage Trips</span>
                </span>
            </button>
            <button type="button" class="tab-button py-2 border-b-2 font-medium text-sm" data-tab="history">
                <span class="flex items-center">
                    <span>History & PDF</span>
                </span>
            </button>
            <button type="button" id="reloadTabBtn" class="ml-auto bg-gray-600 hover:bg-gray-500 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1" title="Reload Tab Data">
                <span>↻</span>
                <span>Reload</span>
            </button>
        </nav>
    </div>

    <!-- Tab Content Container -->
    <div id="tab-content">
        <!-- Sale Tab -->
        <div id="tab-sale" class="tab-panel active">
            <form method="POST" action="?page=suppliers" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" id="supplierSaleForm">
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
                    <div id="supplierSaleStockBox" class="mt-2 p-2 rounded-lg hidden text-xs"></div>
                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Record Sale</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Trip Allocation Tab -->
        <div id="tab-trip-allocation" class="tab-panel hidden">
            <h4 class="text-sm font-semibold text-lime-400 mb-4">Allocate Sold Quantities from Trips</h4>
            <form method="POST" action="?page=suppliers" class="grid grid-cols-1 md:grid-cols-2 gap-4" id="tripAllocationForm">
                <input type="hidden" name="mode" value="allocate_trip_sale">
                <input type="hidden" name="supplier_id" id="tripAllocSupplierId" value="">

                <div class="md:col-span-2">
                    <label class="block text-gray-400 text-xs mb-1">Trip Product</label>
                    <select name="supplier_trip_item_id" id="tripItemSelect" required
                            class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                        <option value="">-- Choose Trip Product --</option>
                        <?php if (!empty($supplierTripItems)): ?>
                            <?php foreach ($supplierTripItems as $supId => $items): ?>
                                <?php foreach ($items as $it): ?>
                                    <?php
                                        $label = '#' . htmlspecialchars($it['trip_id']) . ' ' . htmlspecialchars($it['trip_date']) . ' — ' .
                                                 htmlspecialchars($it['product_name']) . ' (sold ' . (int)$it['sold_qty'] . ', remaining ' . (int)$it['remaining_qty'] . ')';
                                    ?>
                                    <option value="<?= (int)$it['item_id'] ?>"
                                            data-supplier-id="<?= (int)$supId ?>"
                                            data-product-id="<?= (int)$it['product_id'] ?>"
                                            data-remaining="<?= (int)$it['remaining_qty'] ?>"
                                            data-price="<?= htmlspecialchars($it['remaining_qty'] > 0 ? ($products[array_search($it['product_id'], array_column($products, 'id'))]['price'] ?? 0) : 0) ?>">
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="text-gray-500 text-xs mt-1">Only products with remaining sold quantity will be shown for this supplier.</p>
                </div>

                <input type="hidden" name="product_id" id="tripProductId" value="">

                <div>
                    <label class="block text-gray-400 text-xs mb-1">Customer</label>
                    <select name="customer_id" id="tripCustomer" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
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
                    <label class="block text-gray-400 text-xs mb-1">Quantity (remaining)</label>
                    <input type="number" name="quantity" min="1" id="tripQty"
                           class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-lime-500">
                    <p class="text-gray-500 text-xs mt-1" id="tripRemainingInfo">Remaining: 0</p>
                </div>

                <div>
                    <label class="block text-gray-400 text-xs mb-1">Unit Price (Rwf)</label>
                    <input type="number" name="unit_price" step="0.01" id="tripUnitPrice"
                           class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>

                <div>
                    <label class="block text-gray-400 text-xs mb-1">Customer Type</label>
                    <select name="customer_type" id="tripCustomerType" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                        <option value="Regular">Regular</option>
                        <option value="Wholesale">Wholesale</option>
                        <option value="Online">Online</option>
                        <option value="VIP">VIP</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 text-xs mb-1">Payment Method</label>
                    <select name="payment_method" id="tripPaymentMethod" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                        <option value="Cash">Cash</option>
                        <option value="MoMo">MoMo</option>
                        <option value="Card">Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex justify-between items-center mt-2">
                    <span id="tripTotalDisplay" class="text-gray-400 text-xs">Total: 0.00 Rwf</span>
                    <button type="submit" class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Allocate &amp; Record Sale</button>
                </div>
            </form>
        </div>

        <!-- Trips Management Tab -->
        <div id="tab-trips" class="tab-panel hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Create Trip Section -->
                <div class="bg-gray-900 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-lime-400 mb-3">Create Supplier Trip</h4>
                    <form method="POST" action="?page=suppliers" class="space-y-3" id="supplierTripCreateForm">
                        <input type="hidden" name="mode" value="create_supplier_trip">
                        <input type="hidden" name="supplier_id" id="tripCreateSupplierId" value="">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-gray-400 text-xs mb-1">Trip Date</label>
                                <input type="date" name="trip_date" value="<?= date('Y-m-d') ?>" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs mb-1">Note</label>
                                <input type="text" name="note" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm" placeholder="Optional note">
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-400 text-xs mb-1">Products to Dispatch</label>
                            <div id="tripCreateProducts" class="space-y-2">
                                <div class="grid grid-cols-12 gap-2 trip-create-row">
                                    <div class="col-span-7">
                                        <select name="product_id[]" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-xs">
                                            <option value="">-- Product --</option>
                                            <?php foreach ($products as $p): ?>
                                                <option value="<?= (int)$p['id'] ?>">
                                                    <?= htmlspecialchars($p['sku'] ?? '') ?> - <?= htmlspecialchars($p['name']) ?> (stock: <?= (int)($p['stock'] ?? 0) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-span-4">
                                        <input type="number" name="qty_dispatched[]" min="1" placeholder="Qty" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-xs">
                                    </div>
                                    <div class="col-span-1 flex items-center justify-center">
                                        <button type="button" class="text-gray-400 hover:text-red-400 text-lg" onclick="removeTripCreateRow(this)">–</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="mt-2 bg-gray-700 hover:bg-gray-600 text-lime-400 px-3 py-1 rounded-lg text-xs" onclick="addTripCreateRow()">+ Add Product</button>
                        </div>

                        <div class="pt-1 flex justify-end">
                            <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-4 py-2 rounded-lg text-xs">Save Trip &amp; Dispatch Stock</button>
                        </div>
                    </form>
                </div>

                <!-- Edit Returns Section -->
                <div class="bg-gray-900 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-lime-400 mb-3">Edit Trip Returns</h4>
                    <div class="space-y-2">
                        <label class="block text-gray-400 text-xs mb-1">Select Trip</label>
                        <select id="supplierTripSelect" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-xs">
                            <option value="">-- Choose Trip --</option>
                            <?php if (!empty($supplierTripsBySupplier)): ?>
                                <?php foreach ($supplierTripsBySupplier as $supId => $trips): ?>
                                    <?php foreach ($trips as $t): ?>
                                        <option value="<?= (int)$t['id'] ?>" data-supplier-id="<?= (int)$supId ?>">
                                            #<?= (int)$t['id'] ?> — <?= htmlspecialchars($t['trip_date']) ?> (<?= htmlspecialchars($t['status']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                        <form method="POST" id="supplierTripReturnsForm" action="?page=suppliers" class="mt-2 space-y-2 hidden">
                            <input type="hidden" name="mode" value="update_supplier_trip_returns">
                            <input type="hidden" name="trip_id" id="returnsTripId" value="">

                            <div class="overflow-x-auto max-h-48 overflow-y-auto border border-gray-700 rounded-lg">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="border-b border-gray-700">
                                            <th class="text-left py-2 px-2 text-lime-400">Product</th>
                                            <th class="text-left py-2 px-2 text-lime-400">Dispatched</th>
                                            <th class="text-left py-2 px-2 text-lime-400">Returned</th>
                                        </tr>
                                    </thead>
                                    <tbody id="returnsItemsBody">
                                        <?php if (!empty($supplierTripItems)): ?>
                                            <?php foreach ($supplierTripItems as $supId => $items): ?>
                                                <?php foreach ($items as $it): ?>
                                                    <tr class="border-b border-gray-800 hidden"
                                                        data-trip-id="<?= (int)$it['trip_id'] ?>"
                                                        data-supplier-id="<?= (int)$supId ?>">
                                                        <td class="py-2 px-2 text-gray-200">
                                                            <?= htmlspecialchars($it['product_name']) ?>
                                                            <span class="text-gray-500 text-[10px]">(<?= htmlspecialchars($it['sku'] ?? '') ?>)</span>
                                                        </td>
                                                        <td class="py-2 px-2 text-gray-300 text-center">
                                                            <?= (int)$it['qty_dispatched'] ?>
                                                        </td>
                                                        <td class="py-2 px-2">
                                                            <input type="hidden" name="item_id[]" value="<?= (int)$it['item_id'] ?>">
                                                            <input type="number" name="qty_returned[]" min="0" value="<?= (int)$it['qty_returned'] ?>" class="w-20 bg-gray-700 text-white px-2 py-1 rounded-lg text-xs">
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white font-semibold px-4 py-2 rounded-lg text-xs">Save Returns &amp; Restock</button>
                            </div>
                        </form>
                        <p id="supplierTripReturnsEmpty" class="text-gray-500 text-xs mt-2">Choose a trip to edit its returns.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div id="tab-history" class="tab-panel hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <h4 class="text-sm font-semibold text-lime-400 mb-3">Recent Sales History</h4>
                    <div class="overflow-x-auto max-h-64 overflow-y-auto mb-4">
                        <table class="w-full text-xs" id="supplierHistoryTable">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-2 px-3 text-lime-400">Date</th>
                                    <th class="text-left py-2 px-3 text-lime-400">Product</th>
                                    <th class="text-left py-2 px-3 text-lime-400">Customer</th>
                                    <th class="text-left py-2 px-3 text-lime-400">Qty</th>
                                    <th class="text-left py-2 px-3 text-lime-400">Total</th>
                                    <th class="text-left py-2 px-3 text-lime-400">Payment</th>
                                    <th class="text-left py-2 px-3 text-lime-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($supplierHistories)): ?>
                                    <?php foreach ($supplierHistories as $supId => $rows): ?>
                                        <?php foreach ($rows as $r): ?>
                                            <tr class="border-b border-gray-800 hidden" data-supplier-id="<?= (int)$supId ?>"
                                                data-sale-id="<?= (int)$r['id'] ?>"
                                                data-qty="<?= (int)$r['qty'] ?>"
                                                data-unit-price="<?= (float)($r['unit_price'] ?? 0) ?>"
                                                data-payment-method="<?= htmlspecialchars($r['payment_method'] ?? '') ?>">
                                                <td class="py-2 px-3 text-gray-300">
                                                    <?= htmlspecialchars($r['created_at'] ?? '') ?>
                                                </td>
                                                <td class="py-2 px-3 text-gray-300">
                                                    <?= htmlspecialchars($r['product_name'] ?? 'N/A') ?>
                                                </td>
                                                <td class="py-2 px-3 text-gray-300">
                                                    <?= htmlspecialchars($r['customer_name'] ?? 'Walk-in') ?>
                                                </td>
                                                <td class="py-2 px-3 text-gray-300">
                                                    <?= intval($r['qty'] ?? 0) ?>
                                                </td>
                                                <td class="py-2 px-3 text-gray-300">
                                                    <?= number_format($r['total_price'] ?? 0, 0) ?> Rwf
                                                </td>
                                                <td class="py-2 px-3 text-gray-300">
                                                    <?= htmlspecialchars($r['payment_method'] ?? '-') ?>
                                                </td>
                                                <td class="py-2 px-3 text-right">
                                                    <button type="button" class="text-xs text-lime-400 hover:text-lime-300 supplier-sale-edit">Edit</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <p id="supplierHistoryEmpty" class="text-gray-500 text-xs mt-2<?= !empty($supplierHistories) ? ' hidden' : '' ?>">No sales history recorded yet for this supplier.</p>
                    </div>

                    <!-- Inline Edit Form -->
                    <form id="supplierHistoryEditForm" method="POST" action="?page=suppliers" class="hidden mt-3 p-3 bg-gray-900 border border-gray-700 rounded-lg text-xs space-y-2">
                        <input type="hidden" name="mode" value="update_supplier_sale">
                        <input type="hidden" name="sale_id" id="editSaleId" value="">

                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-300 font-semibold">Edit Sale</span>
                            <span id="editSaleContext" class="text-gray-500 text-[11px]"></span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label class="block text-gray-400 text-[11px] mb-1">Quantity</label>
                                <input type="number" name="quantity" id="editSaleQty" min="1"
                                       class="w-full bg-gray-800 text-white px-2 py-1 rounded text-xs">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-[11px] mb-1">Unit Price (Rwf)</label>
                                <input type="number" name="unit_price" id="editSaleUnitPrice" step="0.01" min="0"
                                       class="w-full bg-gray-800 text-white px-2 py-1 rounded text-xs">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-[11px] mb-1">Payment</label>
                                <select name="payment_method" id="editSalePayment"
                                        class="w-full bg-gray-800 text-white px-2 py-1 rounded text-xs">
                                    <option value="Cash">Cash</option>
                                    <option value="MoMo">MoMo</option>
                                    <option value="Card">Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-2">
                            <span id="editSaleTotalPreview" class="text-gray-400 text-[11px]"></span>
                            <div class="space-x-2">
                                <button type="button" id="editSaleCancelBtn" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded text-xs">Cancel</button>
                                <button type="submit" class="px-3 py-1 bg-lime-500 hover:bg-lime-600 text-white rounded text-xs">Save</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-lime-400 mb-3">Export PDF</h4>
                    <form id="supplierHistoryForm" method="GET" target="_blank" class="space-y-3">
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
                        <div>
                            <button type="submit" class="w-full bg-gray-600 hover:bg-gray-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Export History PDF</button>
                        </div>
                    </form>
                    <!-- <p class="text-gray-500 text-xs mt-2">History and PDF only include sales where this supplier is set as <code>supplied_by</code>.</p> -->
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<script>
// Notification System
class NotificationSystem {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Create notification container if it doesn't exist
        if (!document.getElementById('notification-container')) {
            this.container = document.createElement('div');
            this.container.id = 'notification-container';
            this.container.className = 'fixed top-4 right-4 z-50 space-y-2 max-w-sm';
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('notification-container');
        }
    }

    show(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        const bgColor = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        }[type] || 'bg-gray-500';

        const icon = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        }[type] || 'ℹ';

        notification.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out opacity-0 translate-x-full`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <span class="font-bold">${icon}</span>
                <span class="text-sm">${message}</span>
            </div>
        `;

        this.container.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('opacity-0', 'translate-x-full');
            notification.classList.add('opacity-100', 'translate-x-0');
        }, 10);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                this.remove(notification);
            }, duration);
        }

        // Click to dismiss
        notification.addEventListener('click', () => this.remove(notification));

        return notification;
    }

    remove(notification) {
        notification.classList.remove('opacity-100', 'translate-x-0');
        notification.classList.add('opacity-0', 'translate-x-full');
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 5000) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }
}

// Confirmation Modal
class ConfirmationModal {
    constructor() {
        this.modal = null;
        this.resolve = null;
        this.init();
    }

    init() {
        if (!document.getElementById('confirmation-modal')) {
            this.modal = document.createElement('div');
            this.modal.id = 'confirmation-modal';
            this.modal.className = 'fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50';
            this.modal.innerHTML = `
                <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-lime-400">Confirmation</h3>
                        <button type="button" class="text-gray-400 hover:text-white text-xl close-btn">✕</button>
                    </div>
                    <div class="mb-6">
                        <p class="text-gray-300" id="confirmation-message">Are you sure?</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm cancel-btn">Cancel</button>
                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm confirm-btn">Confirm</button>
                    </div>
                </div>
            `;
            document.body.appendChild(this.modal);

            // Bind events
            this.modal.querySelector('.close-btn').addEventListener('click', () => this.hide(false));
            this.modal.querySelector('.cancel-btn').addEventListener('click', () => this.hide(false));
            this.modal.querySelector('.confirm-btn').addEventListener('click', () => this.hide(true));
        } else {
            this.modal = document.getElementById('confirmation-modal');
        }
    }

    show(message) {
        return new Promise((resolve) => {
            this.resolve = resolve;
            this.modal.querySelector('#confirmation-message').textContent = message;
            this.modal.classList.remove('hidden');
            this.modal.classList.add('flex');
        });
    }

    hide(confirmed) {
        this.modal.classList.add('hidden');
        this.modal.classList.remove('flex');
        if (this.resolve) {
            this.resolve(confirmed);
            this.resolve = null;
        }
    }
}

// Global instances
let notificationSystem = null;
let confirmationModal = null;
let supplierTabs = null;

// Unified Tab Management
class SupplierModalTabs {
    constructor() {
        this.currentTab = 'sale';
        this.currentSupplierId = null;
        this.init();
    }

    init() {
        this.bindTabEvents();
        this.bindFormEvents();
        this.switchTab('sale');
    }

    bindTabEvents() {
        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.getAttribute('data-tab');
                this.switchTab(targetTab);
            });
        });

        // Bind reload button
        const reloadBtn = document.getElementById('reloadTabBtn');
        if (reloadBtn) {
            reloadBtn.addEventListener('click', () => {
                this.reloadCurrentTab();
            });
        }
    }

    switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            const isActive = btn.getAttribute('data-tab') === tabName;
            btn.setAttribute('data-active', isActive);
            btn.classList.toggle('border-lime-500', isActive);
            btn.classList.toggle('text-lime-400', isActive);
            btn.classList.toggle('border-transparent', !isActive);
            btn.classList.toggle('text-gray-400', !isActive);
        });

        // Update tab panels
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.toggle('active', panel.id === `tab-${tabName}`);
            panel.classList.toggle('hidden', panel.id !== `tab-${tabName}`);
        });

        this.currentTab = tabName;
        
        // Load tab-specific data if needed
        this.loadTabData(tabName);
    }

    loadTabData(tabName) {
        if (!this.currentSupplierId) return;

        switch(tabName) {
            case 'history':
                this.loadSupplierHistory(this.currentSupplierId);
                break;
            case 'trips':
                this.loadSupplierTrips(this.currentSupplierId);
                break;
            case 'trip-allocation':
                this.loadSupplierTrips(this.currentSupplierId);
                break;
        }
    }

    filterTripItems(supplierId) {
        const tripItemSelect = document.getElementById('tripItemSelect');
        if (tripItemSelect) {
            const options = tripItemSelect.options;
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const optionSupplierId = option.getAttribute('data-supplier-id');
                option.style.display = !option.value || optionSupplierId === String(supplierId) ? '' : 'none';
            }
            tripItemSelect.value = '';
        }
    }

    async loadSupplierHistory(supplierId) {
        try {
            const response = await fetch(`?page=suppliers&action=get_supplier_history&supplier_id=${supplierId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch supplier history');
            }

            const data = await response.json();

            if (data.success) {
                this.updateHistoryTable(data.history);
            } else {
                throw new Error(data.message || 'Failed to load history');
            }
        } catch (error) {
            console.error('Error loading supplier history:', error);
            notificationSystem.error('Failed to load supplier history: ' + error.message);
        }
    }

    async loadSupplierTrips(supplierId) {
        try {
            const response = await fetch(`?page=suppliers&action=get_supplier_trips&supplier_id=${supplierId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch supplier trips');
            }

            const data = await response.json();

            if (data.success) {
                this.updateTripsTable(data.trips);
            } else {
                throw new Error(data.message || 'Failed to load trips');
            }
        } catch (error) {
            console.error('Error loading supplier trips:', error);
            notificationSystem.error('Failed to load supplier trips: ' + error.message);
        }
    }

    updateHistoryTable(historyData) {
        const tableBody = document.querySelector('#supplierHistoryTable tbody');
        const emptyMessage = document.getElementById('supplierHistoryEmpty');

        if (!tableBody) return;

        // Clear existing rows
        tableBody.innerHTML = '';

        if (!historyData || historyData.length === 0) {
            if (emptyMessage) {
                emptyMessage.style.display = 'block';
            }
            return;
        }

        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }

        historyData.forEach(row => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-800';
            tr.setAttribute('data-supplier-id', this.currentSupplierId);
            tr.setAttribute('data-sale-id', row.id);
            tr.setAttribute('data-qty', row.qty);
            tr.setAttribute('data-unit-price', row.unit_price || 0);
            tr.setAttribute('data-payment-method', row.payment_method || '');

            tr.innerHTML = `
                <td class="py-2 px-3 text-gray-300">${row.created_at || ''}</td>
                <td class="py-2 px-3 text-gray-300">${row.product_name || 'N/A'}</td>
                <td class="py-2 px-3 text-gray-300">${row.customer_name || 'Walk-in'}</td>
                <td class="py-2 px-3 text-gray-300">${row.qty || 0}</td>
                <td class="py-2 px-3 text-gray-300">${(row.total_price || 0).toLocaleString()} Rwf</td>
                <td class="py-2 px-3 text-gray-300">${row.payment_method || '-'}</td>
                <td class="py-2 px-3 text-right">
                    <button type="button" class="text-xs text-lime-400 hover:text-lime-300 supplier-sale-edit">Edit</button>
                </td>
            `;

            tableBody.appendChild(tr);
        });

        // Re-bind edit events
        this.bindHistoryEditEvents();
    }

    updateTripsTable(tripsData) {
        // Update trip selection dropdown
        const tripSelect = document.getElementById('supplierTripSelect');
        if (tripSelect) {
            // Clear existing options except the first one
            while (tripSelect.options.length > 1) {
                tripSelect.remove(1);
            }

            if (tripsData && tripsData.length > 0) {
                tripsData.forEach(trip => {
                    const option = document.createElement('option');
                    option.value = trip.id;
                    option.setAttribute('data-supplier-id', this.currentSupplierId);
                    option.textContent = `#${trip.id} — ${trip.trip_date} (${trip.status})`;
                    tripSelect.appendChild(option);
                });
            }
        }

        // Update trip allocation select
        const tripItemSelect = document.getElementById('tripItemSelect');
        if (tripItemSelect) {
            // Clear existing options except the first one
            while (tripItemSelect.options.length > 1) {
                tripItemSelect.remove(1);
            }

            if (tripsData && tripsData.length > 0) {
                tripsData.forEach(trip => {
                    if (trip.items && trip.items.length > 0) {
                        trip.items.forEach(item => {
                            if (item.remaining_qty > 0) {
                                const option = document.createElement('option');
                                option.value = item.item_id;
                                option.setAttribute('data-supplier-id', this.currentSupplierId);
                                option.setAttribute('data-product-id', item.product_id);
                                option.setAttribute('data-remaining', item.remaining_qty);
                                option.setAttribute('data-price', item.remaining_qty > 0 ? (item.product_price || 0) : 0);
                                const label = `#${trip.id} ${trip.trip_date} — ${item.product_name} (sold ${item.sold_qty}, remaining ${item.remaining_qty})`;
                                option.textContent = label;
                                tripItemSelect.appendChild(option);
                            }
                        });
                    }
                });
            }
        }

        // Update returns items table
        const returnsBody = document.getElementById('returnsItemsBody');
        if (returnsBody) {
            returnsBody.innerHTML = '';

            if (tripsData && tripsData.length > 0) {
                tripsData.forEach(trip => {
                    if (trip.items && trip.items.length > 0) {
                        trip.items.forEach(item => {
                            const tr = document.createElement('tr');
                            tr.className = 'border-b border-gray-800 hidden';
                            tr.setAttribute('data-trip-id', trip.id);
                            tr.setAttribute('data-supplier-id', this.currentSupplierId);

                            tr.innerHTML = `
                                <td class="py-2 px-2 text-gray-200">
                                    ${item.product_name}
                                    <span class="text-gray-500 text-[10px]">(${item.sku || ''})</span>
                                </td>
                                <td class="py-2 px-2 text-gray-300 text-center">${item.qty_dispatched}</td>
                                <td class="py-2 px-2">
                                    <input type="hidden" name="item_id[]" value="${item.item_id}">
                                    <input type="number" name="qty_returned[]" min="0" value="${item.qty_returned}" class="w-20 bg-gray-700 text-white px-2 py-1 rounded-lg text-xs">
                                </td>
                            `;

                            returnsBody.appendChild(tr);
                        });
                    }
                });
            }
        }
    }

    bindHistoryEditEvents() {
        const table = document.getElementById('supplierHistoryTable');
        if (!table) return;

        table.querySelectorAll('.supplier-sale-edit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const row = e.target.closest('tr');
                if (!row) return;

                const saleId = row.getAttribute('data-sale-id');
                const qty = row.getAttribute('data-qty');
                const unitPrice = row.getAttribute('data-unit-price');
                const paymentMethod = row.getAttribute('data-payment-method') || 'Cash';

                const editForm = document.getElementById('supplierHistoryEditForm');
                const editSaleId = document.getElementById('editSaleId');
                const editSaleQty = document.getElementById('editSaleQty');
                const editSaleUnitPrice = document.getElementById('editSaleUnitPrice');
                const editSalePayment = document.getElementById('editSalePayment');
                const editSaleContext = document.getElementById('editSaleContext');

                if (editSaleId) editSaleId.value = saleId || '';
                if (editSaleQty) editSaleQty.value = qty || '';
                if (editSaleUnitPrice) editSaleUnitPrice.value = unitPrice || '';
                if (editSalePayment) editSalePayment.value = paymentMethod;

                if (editSaleContext) {
                    const dateCell = row.querySelector('td:nth-child(1)');
                    const productCell = row.querySelector('td:nth-child(2)');
                    const dateText = dateCell ? dateCell.textContent.trim() : '';
                    const productText = productCell ? productCell.textContent.trim() : '';
                    editSaleContext.textContent = productText + ' — ' + dateText;
                }

                if (editForm) {
                    editForm.classList.remove('hidden');
                    editForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });
    }

    bindFormEvents() {
        this.bindSaleForm();
        this.bindAllocationForm();
        this.bindTripForm();
        this.bindEditForm();
        this.bindReturnsForm();
    }

    bindSaleForm() {
        const form = document.getElementById('supplierSaleForm');
        if (form) {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        }
    }

    bindAllocationForm() {
        const form = document.getElementById('tripAllocationForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                const qtyInput = document.getElementById('tripQty');
                const maxAttr = qtyInput ? qtyInput.getAttribute('max') : null;
                const max = maxAttr ? parseInt(maxAttr, 10) : null;
                const val = qtyInput ? parseInt(qtyInput.value || '0', 10) : 0;
                if (max !== null && val > max) {
                    e.preventDefault();
                    notificationSystem.error('Quantity cannot exceed remaining amount from this trip.');
                    return;
                }
                this.handleFormSubmit(e);
            });
        }
    }

    bindTripForm() {
        const form = document.getElementById('supplierTripCreateForm');
        if (form) {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        }
    }

    bindEditForm() {
        const form = document.getElementById('supplierHistoryEditForm');
        if (form) {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        }
    }

    bindReturnsForm() {
        const form = document.getElementById('supplierTripReturnsForm');
        if (form) {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const url = form.getAttribute('action') || window.location.href;

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.textContent : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const contentType = response.headers.get('content-type') || '';
            let data;

            if (contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                throw new Error('Server returned non-JSON response');
            }

            if (data.success) {
                notificationSystem.success(data.message);
                this.resetForm(form);
                await this.reloadCurrentTab();
            } else {
                notificationSystem.error(data.message);
            }
        } catch (error) {
            console.error('Form submission error:', error);
            notificationSystem.error('Request failed. Please try again.');
        } finally {
            // Restore button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }
    }

    resetForm(form) {
        form.reset();
        
        // Reset specific fields if needed
        const saleTotal = document.getElementById('supplierSaleTotal');
        if (saleTotal) saleTotal.textContent = '0.00 Rwf';
        
        const tripTotalDisplay = document.getElementById('tripTotalDisplay');
        if (tripTotalDisplay) tripTotalDisplay.textContent = 'Total: 0.00 Rwf';
    }

    async reloadCurrentTab() {
        try {
            await this.loadTabData(this.currentTab);
            // notificationSystem.info('Tab data reloaded successfully');
        } catch (error) {
            console.error('Error reloading tab data:', error);
            notificationSystem.error('Failed to reload tab data. Please try again.');
        }
    }
}

// Supplier modal functions
function openSupplierCreateModal() {
    const modal = document.getElementById('supplierCreateModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSupplierCreateModal() {
    const modal = document.getElementById('supplierCreateModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

async function deleteSupplier(supplierId, supplierName) {
    const confirmed = await confirmationModal.show(`Are you sure you want to delete supplier: ${supplierName}? This action cannot be undone.`);
    
    if (confirmed) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?page=suppliers';
        form.innerHTML = `
            <input type="hidden" name="mode" value="delete_supplier">
            <input type="hidden" name="supplier_id" value="${supplierId}">
        `;
        document.body.appendChild(form);
        notificationSystem.info(`Deleting supplier: ${supplierName}...`);
        form.submit();
    }
}

function openSupplierEditModal(supplierId, name, phone, email, address) {
    document.getElementById('editSupplierId').value = supplierId;
    document.getElementById('editSupplierName').value = name;
    document.getElementById('editSupplierPhone').value = phone || '';
    document.getElementById('editSupplierEmail').value = email || '';
    document.getElementById('editSupplierAddress').value = address || '';
    const modal = document.getElementById('supplierEditModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSupplierEditModal() {
    const modal = document.getElementById('supplierEditModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openSupplierHistoryModal(supplierId, supplierName) {
    const modal = document.getElementById('supplierHistoryModal');
    if (!modal) return;
    
    // Set supplier ID in all relevant forms
    document.getElementById('supplierSaleSupplierId').value = supplierId;
    document.getElementById('tripAllocSupplierId').value = supplierId;
    document.getElementById('tripCreateSupplierId').value = supplierId;
    document.getElementById('supplierHistoryId').value = supplierId;
    
    // Update modal title
    const title = document.getElementById('supplierHistoryTitle');
    if (title) {
        title.textContent = 'Supplier: ' + supplierName;
    }
    
    // Initialize or update tab system
    if (window.supplierTabs) {
        window.supplierTabs.currentSupplierId = supplierId;
        window.supplierTabs.filterTripItems(supplierId);
        window.supplierTabs.switchTab('sale');
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSupplierHistoryModal() {
    const modal = document.getElementById('supplierHistoryModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.querySelectorAll('form').forEach(form => form.reset());
    
    // Reset edit form
    const editForm = document.getElementById('supplierHistoryEditForm');
    if (editForm) editForm.classList.add('hidden');
}

// Trip Management Functions
function addTripCreateRow() {
    const container = document.getElementById('tripCreateProducts');
    if (!container) return;
    
    const rows = container.querySelectorAll('.trip-create-row');
    const lastRow = rows[rows.length - 1];
    const newRow = lastRow.cloneNode(true);
    
    // Clear values
    newRow.querySelector('select').selectedIndex = 0;
    newRow.querySelector('input[type="number"]').value = '';
    
    container.appendChild(newRow);
}

function removeTripCreateRow(button) {
    const row = button.closest('.trip-create-row');
    const container = document.getElementById('tripCreateProducts');
    const rows = container.querySelectorAll('.trip-create-row');
    
    if (rows.length > 1) {
        row.remove();
        notificationSystem.info('Product row removed');
    } else {
        notificationSystem.warning('Cannot remove the last product row');
    }
}

function initializeCalculations() {
    // Sale form calculations
    const saleQty = document.getElementById('supplierSaleQty');
    const saleUnitPrice = document.getElementById('supplierSaleUnitPrice');
    const saleTotal = document.getElementById('supplierSaleTotal');
    
    if (saleQty && saleUnitPrice && saleTotal) {
        const calculateSaleTotal = () => {
            const qty = parseInt(saleQty.value) || 0;
            const price = parseFloat(saleUnitPrice.value) || 0;
            saleTotal.textContent = (qty * price).toFixed(2) + ' Rwf';
        };
        
        saleQty.addEventListener('input', calculateSaleTotal);
        saleUnitPrice.addEventListener('input', calculateSaleTotal);
    }
    
    // Trip allocation calculations
    const tripQty = document.getElementById('tripQty');
    const tripUnitPrice = document.getElementById('tripUnitPrice');
    const tripTotalDisplay = document.getElementById('tripTotalDisplay');
    const tripItemSelect = document.getElementById('tripItemSelect');
    const tripRemainingInfo = document.getElementById('tripRemainingInfo');
    const tripProductId = document.getElementById('tripProductId');
    
    if (tripQty && tripUnitPrice && tripTotalDisplay) {
        const calculateTripTotal = () => {
            const qty = parseInt(tripQty.value) || 0;
            const price = parseFloat(tripUnitPrice.value) || 0;
            tripTotalDisplay.textContent = 'Total: ' + (qty * price).toFixed(2) + ' Rwf';
        };
        
        tripQty.addEventListener('input', calculateTripTotal);
        tripUnitPrice.addEventListener('input', calculateTripTotal);
    }

    if (tripItemSelect) {
        tripItemSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (!opt) return;

            const remaining = parseInt(opt.getAttribute('data-remaining') || '0', 10) || 0;
            const price = parseFloat(opt.getAttribute('data-price') || '0') || 0;
            const pid = parseInt(opt.getAttribute('data-product-id') || '0', 10) || 0;

            if (tripRemainingInfo) {
                tripRemainingInfo.textContent = 'Remaining: ' + remaining;
            }
            if (tripQty) {
                tripQty.max = remaining > 0 ? String(remaining) : '';
                tripQty.value = remaining > 0 ? String(remaining) : '';
            }
            if (tripUnitPrice && price > 0) {
                tripUnitPrice.value = price.toFixed(2);
            }
            if (tripProductId && pid) {
                tripProductId.value = pid;
            }

            if (tripQty && tripUnitPrice && tripTotalDisplay) {
                const qty = parseInt(tripQty.value) || 0;
                const u = parseFloat(tripUnitPrice.value) || 0;
                tripTotalDisplay.textContent = 'Total: ' + (qty * u).toFixed(2) + ' Rwf';
            }
        });
    }
}

function initializeHistoryEdit() {
    const table = document.getElementById('supplierHistoryTable');
    const editForm = document.getElementById('supplierHistoryEditForm');
    if (!table || !editForm) return;

    const editSaleId = document.getElementById('editSaleId');
    const editSaleQty = document.getElementById('editSaleQty');
    const editSaleUnitPrice = document.getElementById('editSaleUnitPrice');
    const editSalePayment = document.getElementById('editSalePayment');
    const editSaleTotalPreview = document.getElementById('editSaleTotalPreview');
    const editSaleContext = document.getElementById('editSaleContext');
    const editSaleCancelBtn = document.getElementById('editSaleCancelBtn');

    if (!editSaleId || !editSaleQty || !editSaleUnitPrice || !editSalePayment || !editSaleTotalPreview) {
        return;
    }

    const updatePreview = function() {
        const qty = parseInt(editSaleQty.value || '0', 10) || 0;
        const price = parseFloat(editSaleUnitPrice.value || '0') || 0;
        const total = qty * price;
        editSaleTotalPreview.textContent = 'New total: ' + total.toFixed(2) + ' Rwf';
    };

    editSaleQty.addEventListener('input', updatePreview);
    editSaleUnitPrice.addEventListener('input', updatePreview);

    table.querySelectorAll('.supplier-sale-edit').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            if (!row) return;

            const saleId = row.getAttribute('data-sale-id');
            const qty = row.getAttribute('data-qty');
            const unitPrice = row.getAttribute('data-unit-price');
            const paymentMethod = row.getAttribute('data-payment-method') || 'Cash';

            editSaleId.value = saleId || '';
            editSaleQty.value = qty || '';
            editSaleUnitPrice.value = unitPrice || '';
            editSalePayment.value = paymentMethod;

            if (editSaleContext) {
                const dateCell = row.querySelector('td:nth-child(1)');
                const productCell = row.querySelector('td:nth-child(2)');
                const dateText = dateCell ? dateCell.textContent.trim() : '';
                const productText = productCell ? productCell.textContent.trim() : '';
                editSaleContext.textContent = productText + ' — ' + dateText;
            }

            updatePreview();
            editForm.classList.remove('hidden');
            editForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    if (editSaleCancelBtn) {
        editSaleCancelBtn.addEventListener('click', function() {
            editForm.classList.add('hidden');
            editSaleId.value = '';
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification system
    notificationSystem = new NotificationSystem();
    confirmationModal = new ConfirmationModal();
    
    // Initialize tab system
    supplierTabs = new SupplierModalTabs();
    window.supplierTabs = supplierTabs;
    
    // Bind trip selection for returns
    const tripSelect = document.getElementById('supplierTripSelect');
    if (tripSelect) {
        tripSelect.addEventListener('change', function() {
            const tripId = this.value;
            const returnsForm = document.getElementById('supplierTripReturnsForm');
            const emptyMessage = document.getElementById('supplierTripReturnsEmpty');
            const returnsTripId = document.getElementById('returnsTripId');
            const body = document.getElementById('returnsItemsBody');

            if (!returnsForm || !emptyMessage || !returnsTripId || !body) return;

            const rows = body.querySelectorAll('tr[data-trip-id]');
            rows.forEach(r => r.classList.add('hidden'));

            if (!tripId) {
                returnsForm.classList.add('hidden');
                emptyMessage.classList.remove('hidden');
                returnsTripId.value = '';
                return;
            }

            let anyVisible = false;
            rows.forEach(r => {
                const rTrip = r.getAttribute('data-trip-id');
                const rSup = r.getAttribute('data-supplier-id');
                const curSup = supplierTabs && supplierTabs.currentSupplierId ? String(supplierTabs.currentSupplierId) : null;
                if (String(rTrip) === String(tripId) && (!curSup || String(rSup) === curSup)) {
                    r.classList.remove('hidden');
                    anyVisible = true;
                }
            });

            returnsTripId.value = tripId;
            returnsForm.classList.toggle('hidden', !anyVisible);
            emptyMessage.classList.toggle('hidden', anyVisible);
        });
    }
    
    // Initialize calculation events
    initializeCalculations();
    initializeHistoryEdit();

    // Test notification system
    setTimeout(() => {
        // notificationSystem.info('Supplier management system loaded successfully');
    }, 1000);
});
</script>