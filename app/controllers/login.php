<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
  $stmt->execute([$username, $password]);
  $user = $stmt->fetch();

  if ($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    header('Location: ?page=dashboard'); exit;
  } else {
    $error = 'Invalid credentials';
  }
}
?>
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
  <h2 class="text-xl font-bold mb-4">Login</h2>
  <?php if(!empty($error)): ?><div class="text-red-600 mb-3"><?=htmlspecialchars($error)?></div><?php endif; ?>
  <form method="POST" class="space-y-3">
    <input name="username" placeholder="Username" class="w-full p-2 border rounded" required>
    <input name="password" type="password" placeholder="Password" class="w-full p-2 border rounded" required>
    <button class="w-full bg-blue-600 text-white p-2 rounded">Login</button>
  </form>
</div>
