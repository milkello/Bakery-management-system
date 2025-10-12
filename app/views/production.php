<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">🏭 Production Management</h1>

        <!-- Production Form -->
        <form method="POST" class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Select Product</label>
                <select name="product_id" class="w-full border rounded-lg p-2" required>
                    <option value="">-- Choose Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['id']) ?>">
                            <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['sku'] ?? 'No Code') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Quantity Produced</label>
                <input type="number" name="quantity_produced" class="w-full border rounded-lg p-2" required min="1">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    ➕ Record Production
                </button>
            </div>
        </form>

        <!-- Production History -->
        <h2 class="text-xl font-semibold text-gray-700 mb-3">Production History</h2>

        <?php if (empty($productions)): ?>
            <p class="text-gray-500">No production records found.</p>
        <?php else: ?>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="p-2 text-left">ID</th>
                        <th class="p-2 text-left">Product</th>
                        <th class="p-2 text-left">Quantity</th>
                        <th class="p-2 text-left">Produced By</th>
                        <th class="p-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productions as $prod): ?>
                        <tr class="border-t">
                            <td class="p-2"><?= htmlspecialchars($prod['id']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($prod['product_name']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($prod['quantity_produced']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($prod['produced_by']) ?></td>
                            <td class="p-2"><?= date('Y-m-d H:i', strtotime($prod['created_at'] ?? 'now')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>
