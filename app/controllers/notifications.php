<?php
require_once __DIR__ . '/../../config/config.php';

// Fetch notifications from the database
$stmt = $conn->query("SELECT * FROM notifications ORDER BY id DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pass data to the view
include __DIR__ . '/../views/notifications.php';
?>
