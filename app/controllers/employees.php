<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
// simple list and create link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf($_POST['csrf'] ?? '')) {
  // create employee minimal
  $stmt = $pdo->prepare('INSERT INTO employees (first_name,last_name,phone,email,salary_type,salary_amount) VALUES (?,?,?,?,?,?)');
  $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['phone'], $_POST['email'], $_POST['salary_type'], $_POST['salary_amount']]);
  header('Location: ?page=employees'); exit;
}
$emps = $pdo->query('SELECT * FROM employees ORDER BY created_at DESC')->fetchAll();
?>
<div class="bg-white p-4 rounded shadow">
  <h2 class="text-xl font-bold mb-3">Employees</h2>
  <form method="POST" class="grid grid-cols-2 gap-3 mb-4">
    <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'])?>">
    <input name="first_name" placeholder="First name" class="p-2 border rounded" required>
    <input name="last_name" placeholder="Last name" class="p-2 border rounded" required>
    <input name="phone" placeholder="Phone" class="p-2 border rounded">
    <input name="email" type="email" placeholder="Email" class="p-2 border rounded">
    <select name="salary_type" class="p-2 border rounded"><option value="monthly">Monthly</option><option value="hourly">Hourly</option></select>
    <input name="salary_amount" type="number" step="0.01" placeholder="Salary" class="p-2 border rounded">
    <div class="col-span-2"><button class="bg-green-600 text-white px-4 py-2 rounded">Add employee</button></div>
  </form>
  <table class="w-full text-sm">
    <thead class="text-left text-gray-500"><tr><th>Name</th><th>Phone</th><th>Email</th><th>Salary</th></tr></thead>
    <tbody>
    <?php foreach($emps as $e): ?>
      <tr class="border-t"><td><?=htmlspecialchars($e['first_name'].' '.$e['last_name'])?></td><td><?=htmlspecialchars($e['phone'])?></td><td><?=htmlspecialchars($e['email'])?></td><td><?=htmlspecialchars($e['salary_amount'])?> (<?=htmlspecialchars($e['salary_type'])?>)</td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
