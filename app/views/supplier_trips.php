<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <div class="lg:col-span-2 bg-gray-800 rounded-xl p-6 shadow-lg">
        <h3 class="text-xl font-bold text-lime-400 mb-6">Create Supplier Trip</h3>

        <form method="POST" class="space-y-4" id="createTripForm">
            <input type="hidden" name="mode" value="create_trip">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-400 text-sm mb-1">Supplier</label>
                    <select name="supplier_id" required class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <option value="">-- Choose Supplier --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Trip Date</label>
                    <input type="date" name="trip_date" value="<?= date('Y-m-d') ?>" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-1">Note (optional)</label>
                <textarea name="note" rows="2" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none text-sm" placeholder="Notes about this trip..."></textarea>
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">Products to Dispatch</label>
                <div id="tripProducts" class="space-y-2">
                    <div class="grid grid-cols-12 gap-2 trip-row">
                        <div class="col-span-7">
                            <select name="product_id[]" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                                <option value="">-- Product --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= (int)$p['id'] ?>">
                                        <?= htmlspecialchars($p['sku'] ?? '') ?> 
                                        - <?= htmlspecialchars($p['name']) ?>
                                        (stock: <?= (int)$p['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-4">
                            <input type="number" name="qty_dispatched[]" min="1" placeholder="Qty" class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg text-sm">
                        </div>
                        <div class="col-span-1 flex items-center justify-center">
                            <button type="button" class="text-gray-400 hover:text-red-400 text-lg" onclick="removeTripRow(this)">–</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="mt-3 bg-gray-700 hover:bg-gray-600 text-lime-400 px-3 py-1 rounded-lg text-sm" onclick="addTripRow()">
                    + Add Product
                </button>
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-4 py-2 rounded-lg">Save Trip &amp; Dispatch Stock</button>
            </div>
        </form>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
        <h3 class="text-xl font-bold text-lime-400 mb-4">Recent Trips</h3>
        <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
            <?php if (!empty($trips)): ?>
                <?php foreach ($trips as $t): ?>
                    <div class="p-3 bg-gray-700 rounded-lg text-sm flex justify-between items-center">
                        <div>
                            <div class="text-gray-200 font-semibold"><?= htmlspecialchars($t['supplier_name']) ?></div>
                            <div class="text-gray-400 text-xs">
                                Date: <?= htmlspecialchars($t['trip_date']) ?>
                                &nbsp;•&nbsp;
                                Status: <?= htmlspecialchars($t['status']) ?>
                            </div>
                        </div>
                        <a href="?page=supplier_trips&amp;trip_id=<?= (int)$t['id'] ?>" class="text-lime-400 hover:text-lime-300 text-xs font-semibold">Edit Returns</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500 text-sm">No trips yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($edit_trip): ?>
<div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
    <h3 class="text-xl font-bold text-lime-400 mb-2">Update Returns for Trip #<?= (int)$edit_trip['id'] ?></h3>
    <p class="text-gray-400 text-sm mb-4">
        Supplier: <span class="text-gray-200 font-semibold"><?= htmlspecialchars($edit_trip['supplier_name']) ?></span>
        &nbsp;•&nbsp; Date: <?= htmlspecialchars($edit_trip['trip_date']) ?>
    </p>

    <form method="POST" class="space-y-3">
        <input type="hidden" name="mode" value="update_returns">
        <input type="hidden" name="trip_id" value="<?= (int)$edit_trip['id'] ?>">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2 px-3 text-lime-400">Product</th>
                        <th class="text-left py-2 px-3 text-lime-400">Dispatched</th>
                        <th class="text-left py-2 px-3 text-lime-400">Returned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($edit_items as $item): ?>
                        <tr class="border-b border-gray-700">
                            <td class="py-2 px-3 text-gray-200">
                                <?= htmlspecialchars($item['product_name']) ?>
                                <span class="text-gray-500 text-xs">(<?= htmlspecialchars($item['sku'] ?? '') ?>)</span>
                            </td>
                            <td class="py-2 px-3 text-gray-300"><?= (int)$item['qty_dispatched'] ?></td>
                            <td class="py-2 px-3">
                                <input type="hidden" name="item_id[]" value="<?= (int)$item['id'] ?>">
                                <input type="number" name="qty_returned[]" min="0" value="<?= (int)$item['qty_returned'] ?>" class="w-24 bg-gray-700 text-white px-2 py-1 rounded-lg text-sm">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-4 py-2 rounded-lg">Save Returns &amp; Restock</button>
            <a href="?page=supplier_trips" class="ml-3 text-gray-400 hover:text-gray-200 text-sm">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<script>
function addTripRow() {
    const container = document.getElementById('tripProducts');
    if (!container) return;
    const rows = container.getElementsByClassName('trip-row');
    if (!rows.length) return;
    const last = rows[rows.length - 1];
    const clone = last.cloneNode(true);
    const selects = clone.getElementsByTagName('select');
    const inputs = clone.getElementsByTagName('input');
    for (let s of selects) s.selectedIndex = 0;
    for (let i of inputs) if (i.name === 'qty_dispatched[]') i.value = '';
    container.appendChild(clone);
}

function removeTripRow(btn) {
    const row = btn.closest('.trip-row');
    const container = document.getElementById('tripProducts');
    if (!row || !container) return;
    const rows = container.getElementsByClassName('trip-row');
    if (rows.length <= 1) return; // keep at least one row
    container.removeChild(row);
}

if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
