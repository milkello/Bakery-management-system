<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

require_once __DIR__ . '/../../config/config.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user preferences
$pref_stmt = $conn->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
$pref_stmt->execute([$user_id]);
$user_prefs = $pref_stmt->fetch(PDO::FETCH_ASSOC);

// If no preferences exist, create default ones
if (!$user_prefs) {
    $create_pref = $conn->prepare("
        INSERT INTO user_preferences (user_id, email_notifications, sms_notifications, theme, language)
        VALUES (?, 1, 0, 'dark', 'en')
    ");
    $create_pref->execute([$user_id]);
    
    $pref_stmt->execute([$user_id]);
    $user_prefs = $pref_stmt->fetch(PDO::FETCH_ASSOC);
}

// Get system settings (admin only)
$system_settings = [];
if ($user_role === 'admin') {
    $sys_stmt = $conn->query("SELECT * FROM system_settings LIMIT 1");
    $system_settings = $sys_stmt->fetch(PDO::FETCH_ASSOC);
    
    // If no system settings exist, create defaults
    if (!$system_settings) {
        $conn->exec("
            INSERT INTO system_settings (business_name, currency, timezone, date_format, low_stock_threshold)
            VALUES ('Bakery Management System', 'Frw', 'Africa/Kigali', 'Y-m-d', 10)
        ");
        $sys_stmt = $conn->query("SELECT * FROM system_settings LIMIT 1");
        $system_settings = $sys_stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Update Profile
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone'] ?? '');
        
        // IMPORTANT: Never allow role change!
        $update = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
        $update->execute([$username, $email, $phone, $user_id]);
        
        $_SESSION['success'] = "Profile updated successfully!";
        header('Location: ?page=settings');
        exit;
    }
    
    // Change Password
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        if (password_verify($current_password, $current_user['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $pwd_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $pwd_update->execute([$hashed, $user_id]);
                    
                    $_SESSION['success'] = "Password changed successfully!";
                } else {
                    $_SESSION['error'] = "Password must be at least 6 characters!";
                }
            } else {
                $_SESSION['error'] = "New passwords do not match!";
            }
        } else {
            $_SESSION['error'] = "Current password is incorrect!";
        }
        
        header('Location: ?page=settings');
        exit;
    }
    
    // Update Preferences
    if (isset($_POST['action']) && $_POST['action'] === 'update_preferences') {
        $email_notif = isset($_POST['email_notifications']) ? 1 : 0;
        $sms_notif = isset($_POST['sms_notifications']) ? 1 : 0;
        $theme = $_POST['theme'] ?? 'dark';
        $language = $_POST['language'] ?? 'en';
        
        $pref_update = $conn->prepare("
            UPDATE user_preferences 
            SET email_notifications = ?, sms_notifications = ?, theme = ?, language = ?
            WHERE user_id = ?
        ");
        $pref_update->execute([$email_notif, $sms_notif, $theme, $language, $user_id]);
        
        // Refresh session preferences so they take effect immediately
        $_SESSION['theme'] = $theme;
        $_SESSION['language'] = $language;

        $_SESSION['success'] = "Preferences updated successfully!";
        header('Location: ?page=settings');
        exit;
    }
    
    // Update System Settings (Admin only)
    if (isset($_POST['action']) && $_POST['action'] === 'update_system' && $user_role === 'admin') {
        $business_name = trim($_POST['business_name']);
        $currency = trim($_POST['currency']);
        $timezone = trim($_POST['timezone']);
        $date_format = trim($_POST['date_format']);
        $low_stock = intval($_POST['low_stock_threshold']);
        
        $sys_update = $conn->prepare("
            UPDATE system_settings 
            SET business_name = ?, currency = ?, timezone = ?, date_format = ?, low_stock_threshold = ?
            WHERE id = 1
        ");
        $sys_update->execute([$business_name, $currency, $timezone, $date_format, $low_stock]);
        
        $_SESSION['success'] = "System settings updated successfully!";
        header('Location: ?page=settings');
        exit;
    }
}

// Get notification count for badge
$notif_count = $conn->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();

include __DIR__ . '/../views/settings.php';
