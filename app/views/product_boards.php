<?php
// Product boards view: shows product cards where admin can plan ingredients and staff can record production/sales
?>
<div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-gray-800 rounded-xl p-4 shadow-lg border-l-4 border-lime-500 flex items-center justify-between">
        <div>
            <p class="text-gray-400 text-sm">Value Of Ingredients Used</p>
            <p class="text-2xl font-bold text-lime-400 mt-1"><?= number_format($daily_total_value_used ?? 0, 0) ?> <span class="text-sm">RWF</span></p>
        </div>
        <i data-feather="box" class="w-8 h-8 text-lime-400"></i>
    </div>
    <div class="bg-gray-800 rounded-xl p-4 shadow-lg border-l-4 border-fuchsia-500 flex items-center justify-between">
        <div>
            <p class="text-gray-400 text-sm">Value Of Produced Products</p>
            <p class="text-2xl font-bold text-fuchsia-400 mt-1"><?= number_format($daily_total_revenue ?? 0, 0) ?></p>
        </div>
        <i data-feather="settings" class="w-8 h-8 text-fuchsia-400"></i>
    </div>
    <div class="bg-gray-800 rounded-xl p-4 shadow-lg border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <p class="text-gray-400 text-sm">Value of Sold Products</p>
            <p class="text-2xl font-bold text-blue-400 mt-1"><?= number_format($daily_total_revenue_used ?? 0, 0) ?></p>
        </div>
        <i data-feather="shopping-cart" class="w-8 h-8 text-blue-400"></i>
    </div>
    <div class="bg-gray-800 rounded-xl p-4 shadow-lg border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <p class="text-gray-400 text-sm">Balance</p>
            <?php
            $balance = $daily_total_revenue_used - $daily_total_value_used;
            $message = 'Profit of ';
            switch (true) {
                case $balance < 0:
                    $message = 'Loss of ';
                    break;
                case $balance == 0:
                    $message = 'No Profit / Loss';
                    break;
                case $balance > 0:
                    $message = 'Profit of ';
                    break;
            }
            ?>
            <p class="text-2xl font-bold text-blue-400 mt-1"><?= $message . ' (' . abs($balance) . ' RWF)' ?></p>
        </div>
        <i data-feather="shopping-cart" class="w-8 h-8 text-blue-400"></i>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                        <div class="text-sm text-gray-300">Plan #<?= $pl['order_id'] ?> • Value: <?= number_format($pl['total_value'],0) ?></div>
                        <?php foreach($it as $ii): ?>
                            <div class="text-sm text-gray-400">- <?= htmlspecialchars($ii['name']) ?>: <?= number_format($ii['qty'],0) ?> <?= htmlspecialchars($ii['unit']) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="mt-3 border-t border-gray-700 pt-3">
            <h4 class="text-sm text-gray-400">Today's stats</h4>
            <?php if (!empty($stat['plan_value'])): ?>
                <p class="text-gray-400">
                    <!-- display loss or profit and difference) due to comparison between plan and production: -->
                    <?php
                    $diff =  $stat['product_value'] - $pl['total_value'];
                    $message = 'Loss of ';
                    switch (true) {
                        case $diff < 0:
                            $message = 'Loss of ';
                            break;
                        case $diff > 0:
                            $message = 'Profit of ';
                            break;
                    }
                    ?>
                    Estimation: <span class="text-fuchsia-400 font-semibold"><?= $message . ' ' . abs($diff) ?></span>
                </p>
            <?php endif; ?>
            <div class="grid grid-cols-4 gap-2 mt-2 text-center">
                <div>
                    <div class="text-gray-300 font-semibold"><?= intval($stat['produced'] ?? 0) ?></div>
                    <div class="text-gray-400 text-xs">Produced</div>
                </div>
                <div>
                    <div class="text-gray-300 font-semibold"><?= number_format($stat['product_value'], 0) ?></div>
                    <div class="text-gray-400 text-xs">Value</div>
                </div>
                <div>
                    <div class="text-gray-300 font-semibold"><?= intval($stat['sold'] ?? 0) ?></div>
                    <div class="text-gray-400 text-xs">Sold</div>
                </div>
                <div>
                    <div class="text-gray-300 font-semibold"><?= number_format($stat['revenue'] ?? 0,0) ?></div>
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

<!-- Production and Sales are now handled via their respective pages -->

<script>
document.addEventListener('DOMContentLoaded', () => {
    const openPlanBtns = document.querySelectorAll('.openPlanBtn');
    const planModal = document.getElementById('planModal');
    const closePlan = document.getElementById('closePlanModal');
    const planCancel = document.getElementById('planCancel');
    const planItems = document.getElementById('planItems');
    const planAddRow = document.getElementById('planAddRow');
    const planProductId = document.getElementById('planProductId');

    function createPlanRow(item = null) {
        const div = document.createElement('div');
        div.className = 'space-y-1';

        const materialsOptions = `
            <?php foreach($conn->query('SELECT id,name,unit,stock_quantity FROM raw_materials ORDER BY name')->fetchAll(PDO::FETCH_ASSOC) as $m): ?>
                <option value="<?= $m['id'] ?>" data-stock="<?= htmlspecialchars($m['stock_quantity']) ?>"><?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['unit']) ?>, avail: <?= number_format($m['stock_quantity'],0) ?>)</option>
            <?php endforeach; ?>
        `;

        div.innerHTML = `
            <div class="ingredient-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5">
                    <label class="block text-gray-400 text-sm mb-2">Material</label>
                    <select name="material_id[]" 
                            class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <option value="">Select material...</option>
                        ${materialsOptions}
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-gray-400 text-sm mb-2">Quantity</label>
                    <input type="number" name="qty[]" placeholder="0.000"
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500" required>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-gray-400 text-sm mb-2">Unit Price</label>
                    <input type="number" name="unit_price[]" placeholder="0.0000"
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500" required>
                </div>
                <div class="md:col-span-1 flex md:justify-end">
                    <button type="button"
                            class="removePlanRow w-full md:w-auto inline-flex items-center justify-center bg-gray-700/40 hover:bg-gray-700 text-fuchsia-400 hover:text-fuchsia-300 px-3 py-2 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-fuchsia-500"
                            title="Remove" aria-label="Remove">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                            <path d="M10 11v6"></path>
                            <path d="M14 11v6"></path>
                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        <span class="sr-only">Remove</span>
                    </button>
                </div>
            </div>
        `;

        const warn = document.createElement('div');
        warn.className = 'ingredient-warning text-sm text-pink-400 mt-1 hidden';
        div.appendChild(warn);

        planItems.appendChild(div);

        const selEl = div.querySelector('select[name="material_id[]"]');
        const qtyEl = div.querySelector('input[name="qty[]"]');
        const removeBtn = div.querySelector('.removePlanRow');

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                div.remove();
            });
        }
        if (selEl) selEl.addEventListener('change', () => checkRowAvailability(div));
        if (qtyEl) qtyEl.addEventListener('input', () => checkRowAvailability(div));

        if (item) {
            const sel = selEl;
            const qty = qtyEl;
            const up = div.querySelector('input[name="unit_price[]"]');
            if (sel) sel.value = item.material_id;
            if (qty) qty.value = item.qty;
            if (up) up.value = item.unit_price;
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

    closePlan.addEventListener('click', () => planModal.classList.add('hidden'));
    planCancel.addEventListener('click', () => planModal.classList.add('hidden'));

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
