<?php
$user = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoughLight Delights - Bakery Management System</title>
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
<body class="bg-gray-900 text-white">
    <!-- Public pages content will be inserted here -->