<?php
// Simple view for material orders
?>
<div class="bg-gray-800 rounded-xl p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-lime-400">Material Orders (Stock removals)</h3>
        <?php if($isAdmin): ?>
        <div class="flex items-center space-x-3">
            <button id="addOrderBtn" class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                <span>New Order</span>
            </button>
            <button id="showDeleteRange" class="bg-fuchsia-600 hover:bg-fuchsia-700 text-white px-4 py-2 rounded-lg">Delete by Date Range</button>
        </div>
        <?php endif; ?>
    </div>

    <?php if(!empty($message)): ?>
    <div class="mb-4 p-3 bg-green-700 text-white rounded"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if(!empty($error)): ?>
    <div class="mb-4 p-3 bg-fuchsia-700 text-white rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 px-4 text-lime-400">Date</th>
                    <th class="text-left py-3 px-4 text-lime-400">Items</th>
                    <th class="text-left py-3 px-4 text-lime-400">Total Value</th>
                    <th class="text-left py-3 px-4 text-lime-400">Created By</th>
                    <th class="text-left py-3 px-4 text-lime-400">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-700">
                    <td class="py-3 px-4"><?= htmlspecialchars($o['order_date']) ?></td>
                    <td class="py-3 px-4">
                        <?php
                        $items = $conn->prepare('SELECT moi.*, rm.name FROM material_order_items moi LEFT JOIN raw_materials rm ON rm.id = moi.material_id WHERE moi.order_id = ?');
                        $items->execute([$o['id']]);
                        $it = $items->fetchAll(PDO::FETCH_ASSOC);
                        foreach($it as $ii) {
                            echo '<div class="text-sm">' . htmlspecialchars($ii['name'] ?? 'Material') . ': ' . number_format($ii['qty'],0) . '</div>';
                        }
                        ?>
                        </td>
                        <td class="py-3 px-4"><?= number_format($o['total_value'],0) ?> Rwf</td>
                        <td class="py-3 px-4"><?= htmlspecialchars($o['created_by']) ?></td>
                        <td class="py-3 px-4">
                        <?php if($isAdmin): ?>
                        <a href="?page=material_orders&action=delete&id=<?= $o['id'] ?>" class="text-fuchsia-400 hover:text-fuchsia-300">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if($isAdmin): ?>
<!-- New Order Modal -->
<div id="orderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-3xl mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-lime-400">Create Material Order</h3>
            <button id="closeOrderModal" class="text-gray-400"><i data-feather="x" class="w-6 h-6"></i></button>
        </div>
        <form id="orderForm" method="POST">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="text-gray-400 text-sm">Order Date</label>
                    <input type="date" name="order_date" value="<?= date('Y-m-d') ?>" class="w-full bg-gray-700 text-white px-3 py-2 rounded" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-gray-400 text-sm">Note (optional)</label>
                    <input type="text" name="note" class="w-full bg-gray-700 text-white px-3 py-2 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-lime-300">Items</h4>
                    <button id="addRow" type="button" class="bg-lime-500 text-white px-3 py-1 rounded">Add Item</button>
                </div>
                <div id="itemsContainer" class="space-y-2">
                    <!-- rows inserted here -->
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelOrder" class="bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-lime-500 text-white px-4 py-2 rounded">Save Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete range modal -->
<div id="deleteRangeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg text-lime-400 mb-2">Delete Orders by Date Range</h3>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="delete_range" value="1">
            <div class="grid grid-cols-1 gap-3">
                <label class="text-gray-400 text-sm">Start Date</label>
                <input type="date" name="start_date" class="w-full bg-gray-700 text-white px-3 py-2 rounded" required>
                <label class="text-gray-400 text-sm">End Date</label>
                <input type="date" name="end_date" class="w-full bg-gray-700 text-white px-3 py-2 rounded" required>
            </div>
            <div class="mt-4 flex justify-end space-x-3">
                <button type="button" id="cancelDeleteRange" class="bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-fuchsia-600 text-white px-4 py-2 rounded">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addOrderBtn = document.getElementById('addOrderBtn');
    const orderModal = document.getElementById('orderModal');
    const closeOrderModal = document.getElementById('closeOrderModal');
    const cancelOrder = document.getElementById('cancelOrder');
    const addRowBtn = document.getElementById('addRow');
    const itemsContainer = document.getElementById('itemsContainer');
    const showDeleteRange = document.getElementById('showDeleteRange');
    const deleteRangeModal = document.getElementById('deleteRangeModal');
    const cancelDeleteRange = document.getElementById('cancelDeleteRange');

    function createRow() {
        const div = document.createElement('div');
        div.className = 'grid grid-cols-3 gap-2';
        div.innerHTML = `
            <select name="material_id[]" class="bg-gray-700 text-white px-3 py-2 rounded">
                <?php foreach($materials as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['unit']) ?>, avail: <?= number_format($m['stock_quantity'],3) ?>)</option>
                <?php endforeach; ?>
            </select>
            <input name="qty[]" type="number" step="0.001" placeholder="qty" class="bg-gray-700 text-white px-3 py-2 rounded" required />
            <input name="unit_price[]" type="number" step="0.0001" placeholder="unit price" class="bg-gray-700 text-white px-3 py-2 rounded" required />
        `;
        itemsContainer.appendChild(div);
    }

    // initial row
    createRow();

    addRowBtn.addEventListener('click', () => createRow());

    addOrderBtn && addOrderBtn.addEventListener('click', () => orderModal.classList.remove('hidden'));
    closeOrderModal && closeOrderModal.addEventListener('click', () => orderModal.classList.add('hidden'));
    cancelOrder && cancelOrder.addEventListener('click', () => orderModal.classList.add('hidden'));

    showDeleteRange && showDeleteRange.addEventListener('click', () => deleteRangeModal.classList.remove('hidden'));
    cancelDeleteRange && cancelDeleteRange.addEventListener('click', () => deleteRangeModal.classList.add('hidden'));

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
<?php endif; ?>
