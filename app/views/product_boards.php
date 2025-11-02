<?php
// Product boards view: shows product cards where admin can plan ingredients and staff can record production/sales
?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach($products as $product):
        $pid = $product['id'];
        $planList = $plansByProduct[$pid] ?? [];
        $stat = $statsByProduct[$pid] ?? null;
    ?>
    <div class="bg-gray-800 rounded-xl p-4 shadow-lg">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-lg font-bold text-lime-400"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="text-gray-400 text-sm">SKU: <?= htmlspecialchars($product['sku'] ?? '') ?> • Unit: <?= htmlspecialchars($product['unit'] ?? '') ?></p>
                <p class="text-gray-300 mt-2">Stock: <span class="font-semibold"><?= number_format($product['stock'],0) ?></span></p>
            </div>
            <div class="flex flex-col items-end space-y-2">
                <?php
                    // prepare JSON for first plan items (if any) to prefill modal
                    $firstPlan = $planList[0] ?? null;
                    $planJson = '';
                    if ($firstPlan) {
                        $pitemsStmt = $conn->prepare('SELECT moi.material_id, moi.qty, moi.unit_price FROM material_order_items moi WHERE moi.order_id = ?');
                        $pitemsStmt->execute([$firstPlan['order_id']]);
                        $pitems = $pitemsStmt->fetchAll(PDO::FETCH_ASSOC);
                        $planJson = htmlspecialchars(json_encode($pitems), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    }

                    $statJson = htmlspecialchars(json_encode($stat ?? []), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                ?>
                <?php if($isAdmin): ?>
                <button data-product="<?= $pid ?>" data-plan='<?= $planJson ?>' class="openPlanBtn bg-lime-500 px-3 py-1 rounded text-sm">Plan Ingredients</button>
                <?php endif; ?>
                <button data-product="<?= $pid ?>" data-stat='<?= $statJson ?>' data-price="<?= htmlspecialchars($product['price'] ?? 0) ?>" class="openRecordBtn bg-fuchsia-500 px-3 py-1 rounded text-sm">Record Prod/Sales</button>
            </div>
        </div>

        <div class="mt-4">
            <h4 class="text-sm text-gray-400">Today's plan</h4>
            <?php if(empty($planList)): ?>
                <p class="text-gray-500 text-sm">No plan for today</p>
            <?php else: ?>
                <?php foreach($planList as $pl): ?>
                    <?php
                        $items = $conn->prepare('SELECT moi.*, rm.name, rm.unit FROM material_order_items moi LEFT JOIN raw_materials rm ON rm.id = moi.material_id WHERE moi.order_id = ?');
                        $items->execute([$pl['order_id']]);
                        $it = $items->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="bg-gray-700 p-2 rounded my-2">
                        <div class="text-sm text-gray-300">Plan #<?= $pl['order_id'] ?> • Value: <?= number_format($pl['total_value'],2) ?></div>
                        <?php foreach($it as $ii): ?>
                            <div class="text-sm text-gray-400">- <?= htmlspecialchars($ii['name']) ?>: <?= number_format($ii['qty'],3) ?> <?= htmlspecialchars($ii['unit']) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="mt-3 border-t border-gray-700 pt-3">
            <h4 class="text-sm text-gray-400">Today's stats</h4>
            <div class="grid grid-cols-3 gap-2 mt-2 text-center">
                <div>
                    <div class="text-gray-300 font-semibold"><?= intval($stat['produced'] ?? 0) ?></div>
                    <div class="text-gray-400 text-xs">Produced</div>
                </div>
                <div>
                    <div class="text-gray-300 font-semibold"><?= intval($stat['sold'] ?? 0) ?></div>
                    <div class="text-gray-400 text-xs">Sold</div>
                </div>
                <div>
                    <div class="text-gray-300 font-semibold"><?= number_format($stat['revenue'] ?? 0,2) ?></div>
                    <div class="text-gray-400 text-xs">Revenue</div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Plan modal -->
<div id="planModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-3xl mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-lime-400">Plan Ingredients</h3>
            <button id="closePlanModal" class="text-gray-400"><i data-feather="x" class="w-6 h-6"></i></button>
        </div>
        <form id="planForm" method="POST">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="plan_ingredients">
            <input type="hidden" name="product_id" id="planProductId">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <div>
                    <label class="text-gray-400 text-sm">Plan Date</label>
                    <input type="date" name="plan_date" value="<?= date('Y-m-d') ?>" class="w-full bg-gray-700 text-white px-3 py-2 rounded" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-gray-400 text-sm">Note</label>
                    <input type="text" name="note" class="w-full bg-gray-700 text-white px-3 py-2 rounded" />
                </div>
            </div>
            <div class="mb-3">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-lime-300">Items</h4>
                    <button id="planAddRow" type="button" class="bg-lime-500 text-white px-3 py-1 rounded">Add Item</button>
                </div>
                <div id="planItems" class="space-y-2"></div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="planCancel" class="bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-lime-500 text-white px-4 py-2 rounded">Save Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Record modal -->
<div id="recordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-fuchsia-400">Record Production & Sales</h3>
            <button id="closeRecordModal" class="text-gray-400"><i data-feather="x" class="w-6 h-6"></i></button>
        </div>
        <form id="recordForm" method="POST">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="record_stats">
            <input type="hidden" name="product_id" id="recordProductId">
            <div class="grid grid-cols-1 gap-3">
                <label class="text-gray-400 text-sm">Date</label>
                <input type="date" name="stat_date" value="<?= date('Y-m-d') ?>" class="w-full bg-gray-700 text-white px-3 py-2 rounded" />
                <label class="text-gray-400 text-sm">Produced</label>
                <input id="recordProduced" type="number" name="produced" min="0" class="w-full bg-gray-700 text-white px-3 py-2 rounded" />
                <label class="text-gray-400 text-sm">Sold</label>
                <input id="recordSold" type="number" name="sold" min="0" class="w-full bg-gray-700 text-white px-3 py-2 rounded" />
                <label class="text-gray-400 text-sm">Revenue</label>
                <input id="recordRevenue" type="number" name="revenue" step="0.01" class="w-full bg-gray-700 text-white px-3 py-2 rounded" readonly />
            </div>
            <div class="mt-4 flex justify-end space-x-3">
                <button type="button" id="recordCancel" class="bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-fuchsia-500 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const openPlanBtns = document.querySelectorAll('.openPlanBtn');
    const openRecordBtns = document.querySelectorAll('.openRecordBtn');
    const planModal = document.getElementById('planModal');
    const recordModal = document.getElementById('recordModal');
    const closePlan = document.getElementById('closePlanModal');
    const closeRecord = document.getElementById('closeRecordModal');
    const planCancel = document.getElementById('planCancel');
    const recordCancel = document.getElementById('recordCancel');
    const planItems = document.getElementById('planItems');
    const planAddRow = document.getElementById('planAddRow');
    const planProductId = document.getElementById('planProductId');
    const recordProductId = document.getElementById('recordProductId');

    function createPlanRow(item = null) {
        const div = document.createElement('div');
        div.className = 'grid grid-cols-3 gap-2';
        const materialsOptions = `
            <?php foreach($conn->query('SELECT id,name,unit,stock_quantity FROM raw_materials ORDER BY name')->fetchAll(PDO::FETCH_ASSOC) as $m): ?>
                <option value="<?= $m['id'] ?>" data-stock="<?= htmlspecialchars($m['stock_quantity']) ?>"><?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['unit']) ?>, avail: <?= number_format($m['stock_quantity'],3) ?>)</option>
            <?php endforeach; ?>
        `;
        div.innerHTML = `
            <select name="material_id[]" class="bg-gray-700 text-white px-3 py-2 rounded">${materialsOptions}</select>
            <input name="qty[]" type="number" step="0.001" placeholder="qty" class="bg-gray-700 text-white px-3 py-2 rounded" required />
            <input name="unit_price[]" type="number" step="0.0001" placeholder="unit price" class="bg-gray-700 text-white px-3 py-2 rounded" required />
        `;
        // add a small warning area under the row
        const warn = document.createElement('div');
        warn.className = 'ingredient-warning text-sm text-red-400 mt-1 hidden';
        div.appendChild(warn);
    // if item provided, set values after attaching
    planItems.appendChild(div);
    // attach listeners for availability checks
    const selEl = div.querySelector('select');
    const qtyEl = div.querySelector('input[name="qty[]"]');
    if (selEl) selEl.addEventListener('change', () => checkRowAvailability(div));
    if (qtyEl) qtyEl.addEventListener('input', () => checkRowAvailability(div));
        if (item) {
            const sel = div.querySelector('select');
            const qty = div.querySelector('input[name="qty[]"]');
            const up = div.querySelector('input[name="unit_price[]"]');
            sel.value = item.material_id;
            qty.value = item.qty;
            up.value = item.unit_price;
            // run availability check for prefilled item
            checkRowAvailability(div);
        }
    }

    function checkRowAvailability(row) {
        const sel = row.querySelector('select');
        const qty = row.querySelector('input[name="qty[]"]');
        const warn = row.querySelector('.ingredient-warning');
        if (!sel || !qty || !warn) return;
        const opt = sel.selectedOptions[0];
        const avail = parseFloat(opt ? (opt.dataset.stock || '0') : '0');
        const val = parseFloat(qty.value || '0');
        if (val > avail) {
            warn.textContent = `Requested quantity (${val}) exceeds available stock (${avail}).`;
            warn.classList.remove('hidden');
        } else {
            warn.textContent = '';
            warn.classList.add('hidden');
        }
    }

    planAddRow.addEventListener('click', () => createPlanRow());

    openPlanBtns.forEach(b => b.addEventListener('click', (e) => {
        const pid = e.currentTarget.getAttribute('data-product');
        const planData = e.currentTarget.getAttribute('data-plan');
        planProductId.value = pid;
        // clear existing rows
        planItems.innerHTML = '';
        if (planData) {
            try {
                const parsed = JSON.parse(planData);
                if (Array.isArray(parsed) && parsed.length) {
                    parsed.forEach(it => createPlanRow(it));
                } else {
                    createPlanRow();
                }
            } catch (err) {
                createPlanRow();
            }
        } else {
            createPlanRow();
        }
        planModal.classList.remove('hidden');
    }));

    openRecordBtns.forEach(b => b.addEventListener('click', (e) => {
        const btn = e.currentTarget;
        const pid = btn.getAttribute('data-product');
        const statData = btn.getAttribute('data-stat');
        const price = parseFloat(btn.getAttribute('data-price') || '0');
        recordProductId.value = pid;
        // attach price to modal for live calc
        recordModal.dataset.price = price;
        // prefill form
        try {
            const parsed = statData ? JSON.parse(statData) : {};
            document.querySelector('#recordForm input[name="stat_date"]').value = parsed.stat_date || new Date().toISOString().slice(0,10);
            document.querySelector('#recordForm input[name="produced"]').value = parsed.produced || '';
            document.querySelector('#recordForm input[name="sold"]').value = parsed.sold || '';
            // compute revenue from price and sold (if any)
            const soldVal = parsed.sold ? Number(parsed.sold) : 0;
            document.querySelector('#recordForm input[name="revenue"]').value = (Math.round(soldVal * price * 100) / 100).toFixed(2);
        } catch (err) {
            // ignore
            document.querySelector('#recordForm input[name="revenue"]').value = '0.00';
        }
        recordModal.classList.remove('hidden');
    }));

    closePlan.addEventListener('click', () => planModal.classList.add('hidden'));
    planCancel.addEventListener('click', () => planModal.classList.add('hidden'));
    closeRecord.addEventListener('click', () => recordModal.classList.add('hidden'));
    recordCancel.addEventListener('click', () => recordModal.classList.add('hidden'));
    // live calculate revenue when sold changes
    const recordSoldInput = document.querySelector('#recordForm input[name="sold"]');
    if (recordSoldInput) {
        recordSoldInput.addEventListener('input', function () {
            const price = parseFloat(recordModal.dataset.price || '0');
            const sold = parseFloat(this.value || '0');
            const rev = Math.round(sold * price * 100) / 100;
            document.querySelector('#recordForm input[name="revenue"]').value = rev.toFixed(2);
        });
    }

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
