<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">💰 Sales Management</h1>

        <!-- Sale Form -->
        <form method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
            <div class="md:col-span-2">
                <label class="block text-gray-700 font-medium mb-1">Select Product</label>
                <select name="product_id" class="w-full border rounded-lg p-2" required>
                    <option value="">-- Choose Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['id']) ?>">
                            <?= htmlspecialchars($product['sku'] ?? 'No Code') ?> — <?= htmlspecialchars($product['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Quantity</label>
                <input type="number" name="quantity" class="w-full border rounded-lg p-2" min="1" required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Unit Price</label>
                <input type="number" name="unit_price" step="0.01" class="w-full border rounded-lg p-2" required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Customer Type</label>
                <select name="customer_type" class="w-full border rounded-lg p-2">
                    <option>Regular</option>
                    <option>Wholesale</option>
                    <option>Online</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Payment Method</label>
                <select name="payment_method" class="w-full border rounded-lg p-2">
                    <option>Cash</option>
                    <option>MoMo</option>
                    <option>Card</option>
                </select>
            </div>

            <div class="flex items-end justify-center">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    💾 Save Sale
                </button>
            </div>
        </form>

        <!-- Sales Logs -->
        <h2 class="text-xl font-semibold text-gray-700 mb-3">🧾 Sales Records</h2>

        <?php if (empty($sales_logs)): ?>
            <p class="text-gray-500">No sales records found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="p-2 text-left">#</th>
                            <th class="p-2 text-left">Product</th>
                            <th class="p-2 text-left">Quantity</th>
                            <th class="p-2 text-left">Unit Price</th>
                            <th class="p-2 text-left">Total</th>
                            <th class="p-2 text-left">Customer</th>
                            <th class="p-2 text-left">Payment</th>
                            <th class="p-2 text-left">Sold By</th>
                            <th class="p-2 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales_logs as $sale): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2"><?= htmlspecialchars($sale['id']) ?></td>
                                <td class="p-2">
                                    <?= htmlspecialchars($sale['product_code'] ?? '') ?> — <?= htmlspecialchars($sale['product_name']) ?>
                                </td>
                                <td class="p-2"><?= htmlspecialchars($sale['quantity_sold']) ?></td>
                                <td class="p-2"><?= number_format($sale['unit_price'], 2) ?></td>
                                <td class="p-2 font-semibold text-green-600">
                                    <?= number_format($sale['total_price'], 2) ?>
                                </td>
                                <td class="p-2"><?= htmlspecialchars($sale['customer_type']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($sale['payment_method']) ?></td>
                                <td class="p-2"><?= htmlspecialchars($sale['sold_by'] ?? 'Unknown') ?></td>
                                <td class="p-2 text-gray-500"><?= date('Y-m-d H:i', strtotime($sale['created_at'] ?? 'now')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
