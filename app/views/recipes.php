<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6">

<h1 class="text-2xl font-bold mb-4">Add Recipe</h1>

<form method="POST" class="mb-6 space-y-4">
    <div>
        <label class="block font-semibold">Product:</label>
        <select name="product_id" required class="border p-2 w-full">
            <option value="">-- Select Product --</option>
            <?php foreach($products as $product): ?>
                <option value="<?= $product['id'] ?>">
                    <?= htmlspecialchars($product['sku']) ?> - <?= htmlspecialchars($product['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="materials-container">
        <div class="material-row flex space-x-2 mb-2">
            <select name="materials[]" class="border p-2">
                <option value="">-- Select Material --</option>
                <?php foreach($materials as $material): ?>
                    <option value="<?= $material['id'] ?>"><?= htmlspecialchars($material['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="quantities[]" placeholder="Quantity" class="border p-2 w-24">
            <input type="text" name="units[]" placeholder="Unit" class="border p-2 w-24">
            <button type="button" class="remove-row bg-red-500 text-white px-2 rounded">X</button>
        </div>
    </div>

    <button type="button" id="add-material" class="bg-blue-500 text-white px-4 py-2 rounded">Add Material</button>
    <br>
    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save Recipe</button>
</form>

<hr class="my-6">

<h2 class="text-xl font-bold mb-4">All Recipes</h2>

<table class="table-auto border-collapse border border-gray-400 w-full">
    <thead>
        <tr class="bg-gray-200">
            <th class="border px-2 py-1">Product ID</th>
            <th class="border px-2 py-1">Product Name</th>
            <th class="border px-2 py-1">Ingredients</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Group recipes by product
        $grouped = [];
        foreach ($recipes as $r) {
            $grouped[$r['recipe_id']]['product_code'] = $r['product_code'] ?? $r['product_name']; // fallback
            $grouped[$r['recipe_id']]['product_name'] = $r['product_name'];
            $grouped[$r['recipe_id']]['ingredients'][] = $r['material_name'] . ' (' . $r['quantity'] . ' ' . $r['unit'] . ')';
        }

        foreach ($grouped as $recipe_id => $data):
        ?>
            <tr>
                <td class="border px-2 py-1"><?= htmlspecialchars($data['product_code'] ?? 'N/A') ?></td>
                <td class="border px-2 py-1"><?= htmlspecialchars($data['product_name']) ?></td>
                <td class="border px-2 py-1"><?= implode(', ', $data['ingredients']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#add-material').click(function() {
        var row = `<div class="material-row flex space-x-2 mb-2">
            <select name="materials[]" class="border p-2">
                <option value="">-- Select Material --</option>
                <?php foreach($materials as $material): ?>
                    <option value="<?= $material['id'] ?>"><?= htmlspecialchars($material['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="quantities[]" placeholder="Quantity" class="border p-2 w-24">
            <input type="text" name="units[]" placeholder="Unit" class="border p-2 w-24">
            <button type="button" class="remove-row bg-red-500 text-white px-2 rounded">X</button>
        </div>`;
        $('#materials-container').append(row);
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('.material-row').remove();
    });
});
</script>

</body>
</html>
