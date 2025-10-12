<h2 class="text-2xl font-bold mb-6">Dashboard</h2>

<!-- Stats Cards -->
<div class="grid md:grid-cols-4 sm:grid-cols-2 gap-6 mb-8">
  <div class="bg-yellow-100 p-4 rounded-lg shadow">
    <h3 class="font-semibold">Total Sales</h3>
    <p class="text-2xl font-bold text-yellow-600">₣<?= number_format($total_sales,0) ?></p>
  </div>
  <div class="bg-blue-100 p-4 rounded-lg shadow">
    <h3 class="font-semibold">Products</h3>
    <p class="text-2xl font-bold text-blue-600"><?= $total_products ?></p>
  </div>
  <div class="bg-green-100 p-4 rounded-lg shadow">
    <h3 class="font-semibold">Employees</h3>
    <p class="text-2xl font-bold text-green-600"><?= $total_employees ?></p>
  </div>
  <div class="bg-red-100 p-4 rounded-lg shadow">
    <h3 class="font-semibold">Low Stock</h3>
    <p class="text-2xl font-bold text-red-600"><?= count($low_stock) ?></p>
  </div>
</div>

<!-- Low Stock Table -->
<?php if(count($low_stock) > 0): ?>
<div class="bg-white p-4 rounded shadow mb-6">
  <h3 class="font-semibold text-red-600 mb-2">⚠ Low Stock Items</h3>
  <table class="table-auto w-full border">
    <thead>
      <tr class="bg-gray-100">
        <th>Product</th>
        <th>Stock</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($low_stock as $ls): ?>
      <tr class="border-t">
        <td><?= htmlspecialchars($ls['name']) ?></td>
        <td class="text-red-600 font-semibold"><?= $ls['stock'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Sales Trend Chart -->
<div class="bg-white p-4 rounded shadow">
  <h3 class="font-semibold mb-2">Sales Trend (Last 7 Days)</h3>
  <canvas id="salesChart" height="120"></canvas>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
const data = {
  labels: <?=  json_encode(array_column($sales_trend, 'sale_date')); ?>,
  datasets: [{
    label: 'Total Sales',
    data: <?= json_encode(array_column($sales_trend, 'total')) ?>,
    borderColor: '#facc15',
    backgroundColor: 'rgba(250, 204, 21, 0.3)',
    fill: true,
    tension: 0.3
  }]
};
new Chart(ctx, { type: 'line', data });
</script> -->
\





<?php
// $user = $_SESSION['user_id'] ?? null;
?>
<!-- 
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bakery Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<nav class="bg-white shadow p-4">
  <div class="container mx-auto flex justify-between">
    <div class="font-bold">BakeryMgmt</div>
    <div>
      <?php // if($user): ?>
        <a href="?page=dashboard" class="mr-3">Dashboard</a>
        <a href="?page=employees" class="mr-3">Employees</a>
        <a href="?page=raw_materials" class="mr-3">Materials</a>
        <a href="?page=products" class="mr-3">Products</a>
        <a href="?page=recipes" class="mr-3">Recipes</a>
        <a href="?page=production" class="mr-3">Production</a>
        <a href="?page=sales" class="mr-3">Sales</a>
        <a href="?page=notifications" class="mr-3">Notifications</a>
        <a href="?page=logout" class="text-red-600">Logout</a>
      <?php // else: ?>
        <a href="?page=login" class="text-blue-600">Login</a>
      <?php // endif; ?>
    </div>
  </div>
</nav>
<main class="container mx-auto p-6"> -->