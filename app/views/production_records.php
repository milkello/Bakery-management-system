<?php
// View for recording production quantities
?>
<div class="bg-gray-800 rounded-xl p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-lime-400">Record Production</h3>
    </div>

    <?php if(!empty($message)): ?>
    <div class="mb-4 p-3 bg-green-700 text-white rounded"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if(!empty($error)): ?>
    <div class="mb-4 p-3 bg-fuchsia-700 text-white rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-gray-900 p-4 rounded mb-6">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="text-gray-400 text-sm">Product</label>
                <select name="product_id" class="w-full bg-gray-700 text-white px-3 py-2 rounded" required>
                    <?php foreach($products as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (stock: <?= intval($p['stock']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-gray-400 text-sm">Quantity produced</label>
                <input type="number" name="quantity" min="1" class="w-full bg-gray-700 text-white px-3 py-2 rounded" required>
            </div>
            <div>
                <label class="text-gray-400 text-sm">Note (optional)</label>
                <input type="text" name="note" class="w-full bg-gray-700 text-white px-3 py-2 rounded">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-lime-500 text-white px-4 py-2 rounded">Record</button>
        </div>
    </form>

    <div class="bg-gray-900 p-4 rounded">
        <h4 class="text-lime-300 mb-2">Recent Production Records</h4>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2 px-3 text-lime-400">When</th>
                        <th class="text-left py-2 px-3 text-lime-400">Product</th>
                        <th class="text-left py-2 px-3 text-lime-400">Qty</th>
                        <th class="text-left py-2 px-3 text-lime-400">By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent as $r): ?>
                    <tr class="border-b border-gray-700 hover:bg-gray-700">
                        <td class="py-2 px-3 text-gray-300"><?= htmlspecialchars($r['created_at']) ?></td>
                        <td class="py-2 px-3"><?= htmlspecialchars($r['product_name'] ?? 'N/A') ?></td>
                        <td class="py-2 px-3"><?= intval($r['quantity']) ?></td>
                        <td class="py-2 px-3"><?= htmlspecialchars($r['username'] ?? $r['created_by']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
