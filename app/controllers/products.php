<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf($_POST['csrf'] ?? '')) {
  $stmt = $pdo->prepare('INSERT INTO products (name,sku,price,unit) VALUES (?,?,?,?)');
  $stmt->execute([$_POST['name'],$_POST['sku'],$_POST['price'],$_POST['unit']]);
  header('Location: ?page=products'); exit;
}
$prods = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();
?>
<div class="bg-white p-4 rounded shadow">
  <h2 class="text-xl font-bold mb-3">Products</h2>
  <form method="POST" class="grid grid-cols-2 gap-3 mb-4">
    <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'])?>">
    <input name="name" placeholder="Product name" class="p-2 border rounded" required>
    <input name="sku" placeholder="SKU" class="p-2 border rounded">
    <input name="price" placeholder="Price" class="p-2 border rounded" type="number" step="0.01">
    <input name="unit" placeholder="Unit (piece)" class="p-2 border rounded">
    <div class="col-span-2"><button class="bg-green-600 text-white px-4 py-2 rounded">Add product</button></div>
  </form>
  <table class="w-full text-sm">
    <thead class="text-left text-gray-500"><tr><th>Name</th><th>Price</th></tr></thead>
    <tbody>
    <?php foreach($prods as $p): ?>
      <tr class="border-t"><td><?=htmlspecialchars($p['name'])?></td><td><?=htmlspecialchars($p['price'])?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
