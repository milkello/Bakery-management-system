<?php
$user = $_SESSION['user_id'] ?? null;
?>
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
      <?php if($user): ?>
        <a href="?page=dashboard" class="mr-3">Dashboard</a>
        <a href="?page=employees" class="mr-3">Employees</a>
        <a href="?page=raw_materials" class="mr-3">Materials</a>
        <a href="?page=products" class="mr-3">Products</a>
        <a href="?page=recipes" class="mr-3">Recipes</a>
        <a href="?page=production" class="mr-3">Production</a>
        <a href="?page=sales" class="mr-3">Sales</a>
        <a href="?page=notifications" class="mr-3">Notifications</a>
        <a href="?page=logout" class="text-red-600">Logout</a>
      <?php else: ?>
        <a href="?page=login" class="text-blue-600">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<main class="container mx-auto p-6">
