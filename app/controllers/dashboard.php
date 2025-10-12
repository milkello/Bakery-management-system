<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
// sample stats
$salesStmt = $pdo->query("SELECT COUNT(*) as total_sales, COALESCE(SUM(total_price),0) as revenue FROM sales WHERE DATE(created_at)=CURDATE()");
$sales = $salesStmt->fetch();
?>
<div class="grid grid-cols-3 gap-4">
  <div class="bg-white p-4 rounded shadow">
    <h3 class="text-sm text-gray-500">Today's Sales</h3>
    <p class="text-2xl font-bold"><?=number_format($sales['revenue'],2)?></p>
  </div>
  <div class="bg-white p-4 rounded shadow">
    <h3 class="text-sm text-gray-500">Total Orders</h3>
    <p class="text-2xl font-bold"><?=number_format($sales['total_sales'])?></p>
  </div>
  <div class="bg-white p-4 rounded shadow">
    <h3 class="text-sm text-gray-500">Low Stock Items</h3>
    <?php
    $low = $pdo->query("SELECT COUNT(*) as c FROM raw_materials WHERE stock_quantity <= low_threshold")->fetch();
    ?>
    <p class="text-2xl font-bold"><?= $low['c'] ?></p>
  </div>
</div>
