<?php
$user = $_SESSION['user_id'] ?? null;
$theme = $_SESSION['theme'] ?? 'dark';
$language = $_SESSION['language'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $conn->query("SELECT business_name FROM system_settings LIMIT 1")->fetchColumn(); ?> - Bakery Management System</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#84cc16',
                        secondary: '#d946ef'
                    }
                }
            }
        }
    </script>
</head>
<body class="<?= $theme === 'dark' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-900' ?>">
    <!-- Public pages content will be inserted here -->