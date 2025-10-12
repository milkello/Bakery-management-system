<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf($_POST['csrf'] ?? '')) {
  $stmt = $pdo->prepare('INSERT INTO raw_materials (name,unit,category,unit_cost,stock_quantity,low_threshold) VALUES (?,?,?,?,?,?)');
  $stmt->execute([$_POST['name'],$_POST['unit'],$_POST['category'],$_POST['unit_cost'],$_POST['stock_quantity'],$_POST['low_threshold']]);
  header('Location: ?page=raw_materials'); exit;
}
$mats = $pdo->query('SELECT * FROM raw_materials ORDER BY created_at DESC')->fetchAll();
?>
<div class="bg-white p-4 rounded shadow">
  <h2 class="text-xl font-bold mb-3">Raw Materials</h2>
  <form method="POST" class="grid grid-cols-3 gap-3 mb-4">
    <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'])?>">
    <input name="name" placeholder="Name" class="p-2 border rounded" required>
    <input name="unit" placeholder="Unit (kg,liter)" class="p-2 border rounded">
    <input name="category" placeholder="Category" class="p-2 border rounded">
    <input name="unit_cost" placeholder="Unit cost" class="p-2 border rounded" type="number" step="0.0001">
    <input name="stock_quantity" placeholder="Stock qty" class="p-2 border rounded" type="number" step="0.001">
    <input name="low_threshold" placeholder="Low threshold" class="p-2 border rounded" type="number" step="0.001">
    <div class="col-span-3"><button class="bg-green-600 text-white px-4 py-2 rounded">Add material</button></div>
  </form>
  <table class="w-full text-sm">
    <thead class="text-left text-gray-500"><tr><th>Name</th><th>Stock</th><th>Unit cost</th></tr></thead>
    <tbody>
    <?php foreach($mats as $m): ?>
      <tr class="border-t"><td><?=htmlspecialchars($m['name'])?></td><td><?=htmlspecialchars($m['stock_quantity'].' '.$m['unit'])?></td><td><?=htmlspecialchars($m['unit_cost'])?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
