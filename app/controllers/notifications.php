<?php
if (!isset($_SESSION['user_id'])) { header('Location: ?page=login'); exit; }
require_once __DIR__ . '/../../config/config.php';

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_read'])) {
    $notificationId = $_POST['notification_id'];
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->execute([$notificationId]);
    header("Location: ?page=notifications");
    exit;
}

// Handle mark all as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    $stmt->execute();
    header("Location: ?page=notifications");
    exit;
}

// Handle clear all notifications
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_all'])) {
    $stmt = $conn->prepare("DELETE FROM notifications");
    $stmt->execute();
    header("Location: ?page=notifications");
    exit;
}

// Handle delete single notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_notification'])) {
    $notificationId = $_POST['notification_id'] ?? null;
    if ($notificationId !== null) {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->execute([$notificationId]);
    }
    header("Location: ?page=notifications");
    exit;
}

// Check if is_read column exists, if not add it
$checkColumn = $conn->query("SHOW COLUMNS FROM notifications LIKE 'is_read'");
if ($checkColumn->rowCount() === 0) {
    $conn->exec("ALTER TABLE notifications ADD COLUMN is_read BOOLEAN DEFAULT FALSE");
}

// Fetch notifications from the database
$stmt = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$total_notifications = count($notifications);
$unread_count = $conn->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();
$today_notifications = $conn->query("SELECT COUNT(*) FROM notifications WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// Group notifications by type for statistics
$notification_types = $conn->query("SELECT type, COUNT(*) as count FROM notifications GROUP BY type")->fetchAll(PDO::FETCH_ASSOC);

// Pass data to the view
include __DIR__ . '/../views/notifications.php';
?>