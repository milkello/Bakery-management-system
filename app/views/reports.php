<h2 class="text-2xl font-bold mb-6">Reports & Exports</h2>

<form method="GET" class="bg-white p-4 rounded shadow mb-6 flex flex-wrap gap-4">
  <div>
    <label class="block font-semibold">Start Date</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="border rounded p-2">
  </div>
  <div>
    <label class="block font-semibold">End Date</label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="border rounded p-2">
  </div>
  <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Filter</button>
  <a href="?page=export_pdf&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="bg-green-600 text-white px-4 py-2 rounded">Export PDF</a>
</form>

<div class="grid md:grid-cols-2 gap-6 mb-8">
  <div class="bg-blue-100 p-4 rounded shadow">
    <h3 class="font-semibold">Total Sales</h3>
    <p class="text-2xl font-bold text-blue-700">₣<?= number_format($total_sales, 0) ?></p>
  </div>
  <div class="bg-green-100 p-4 rounded shadow">
    <h3 class="font-semibold">Total Productions</h3>
    <p class="text-2xl font-bold text-green-700"><?= $total_production ?></p>
  </div>
</div>

<!-- Sales Table -->
<div class="bg-white p-4 rounded shadow mb-8">
  <h3 class="font-semibold mb-2">Sales Report</h3>
  <table class="table-auto w-full border">
    <thead>
      <tr class="bg-gray-100">
        <th>ID</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Total</th>
        <th>Customer</th>
        <th>Payment</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($sales_data as $s): ?>
      <tr class="border-t">
        <td><?= $s['id'] ?></td>
        <td><?= htmlspecialchars($s['product_name']) ?></td>
        <td><?= $s['quantity_sold'] ?></td>
        <td><?= $s['total_price'] ?></td>
        <td><?= $s['customer_type'] ?></td>
        <td><?= $s['payment_method'] ?></td>
        <td><?= $s['created_at'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Production Table -->
<div class="bg-white p-4 rounded shadow">
  <h3 class="font-semibold mb-2">Production Report</h3>
  <table class="table-auto w-full border">
    <thead>
      <tr class="bg-gray-100">
        <th>ID</th>
        <th>Product</th>
        <th>Yield</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($production_data as $p): ?>
      <tr class="border-t">
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['product_name']) ?></td>
        <td><?= $p['yield_quantity'] ?></td>
        <td><?= $p['created_at'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
