<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">📢 Notifications</h1>

        <?php if (empty($notifications)): ?>
            <p class="text-gray-500">No notifications found.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($notifications as $note): ?>
                    <div class="p-4 border-l-4 
                        <?php echo $note['type'] === 'over_usage' ? 'border-red-500 bg-red-50' : 'border-blue-500 bg-blue-50'; ?> 
                        rounded-lg shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-700">
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $note['type']))) ?>
                        </h2>
                        <p class="text-gray-600"><?= htmlspecialchars($note['message']) ?></p>
                        <p class="text-xs text-gray-400 mt-1">
                            <?= date("F j, Y, g:i a", strtotime($note['created_at'] ?? 'now')) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
