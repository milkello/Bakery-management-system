<!-- Notifications Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="bell" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $total_notifications ?></h3>
        <p class="text-gray-400">Total Notifications</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="alert-circle" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= $unread_count ?></h3>
        <p class="text-gray-400">Unread</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="calendar" class="w-12 h-12 text-lime-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-lime-400"><?= $today_notifications ?></h3>
        <p class="text-gray-400">Today</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <i data-feather="layers" class="w-12 h-12 text-fuchsia-500 mx-auto mb-4"></i>
        <h3 class="text-2xl font-bold text-fuchsia-400"><?= count($notification_types) ?></h3>
        <p class="text-gray-400">Notification Types</p>
    </div>
</div>

<!-- Notification Actions -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-bold text-lime-400">Notification Center</h3>
        <div class="flex space-x-3">
            <form method="POST" class="inline">
                <input type="hidden" name="mark_all_read" value="1">
                <button type="submit" 
                        class="bg-lime-500 hover:bg-lime-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <i data-feather="check-circle" class="w-4 h-4"></i>
                    <span>Mark All as Read</span>
                </button>
            </form>
            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to clear all notifications?');">
                <input type="hidden" name="clear_all" value="1">
                <button type="submit" 
                        class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <i data-feather="trash-2" class="w-4 h-4"></i>
                    <span>Clear All</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Notification Types Overview -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg mb-8">
    <h3 class="text-xl font-bold text-lime-400 mb-6">Notification Types</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php foreach($notification_types as $type): 
            $typeColor = getNotificationColor($type['type']);
            $typeIcon = getNotificationIcon($type['type']);
        ?>
        <div class="bg-gray-700 rounded-lg p-4 text-center">
            <div class="w-10 h-10 <?= $typeColor['bg'] ?> rounded-full flex items-center justify-center mx-auto mb-2">
                <i data-feather="<?= $typeIcon ?>" class="w-5 h-5 text-white"></i>
            </div>
            <p class="text-white font-semibold text-sm mb-1"><?= ucwords(str_replace('_', ' ', $type['type'])) ?></p>
            <p class="text-gray-400 text-xs"><?= $type['count'] ?> notifications</p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Notifications List -->
<div class="bg-gray-800 rounded-xl p-6 shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-lime-400">All Notifications</h3>
        <div class="flex items-center space-x-2 text-gray-400">
            <i data-feather="filter" class="w-4 h-4"></i>
            <span>Sorted by Latest</span>
        </div>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="text-center py-12">
            <i data-feather="bell-off" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
            <h4 class="text-xl font-semibold text-gray-400 mb-2">No Notifications</h4>
            <p class="text-gray-500">You're all caught up! No notifications to display.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($notifications as $note): 
                $notificationColor = getNotificationColor($note['type']);
                $notificationIcon = getNotificationIcon($note['type']);
                $isUnread = !$note['is_read'];
            ?>
            <div class="bg-gray-700 rounded-lg p-4 border-l-4 <?= $notificationColor['border'] ?> hover:bg-gray-600 transition-colors <?= $isUnread ? 'ring-2 ring-lime-500' : '' ?>">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 <?= $notificationColor['bg'] ?> rounded-full flex items-center justify-center">
                            <i data-feather="<?= $notificationIcon ?>" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-white">
                                <?= ucwords(str_replace('_', ' ', $note['type'])) ?>
                                <?php if($isUnread): ?>
                                    <span class="bg-lime-500 text-white text-xs px-2 py-1 rounded-full ml-2">New</span>
                                <?php endif; ?>
                            </h4>
                            <p class="text-gray-300"><?= htmlspecialchars($note['message']) ?></p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <?php if($isUnread): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="notification_id" value="<?= $note['id'] ?>">
                            <input type="hidden" name="mark_as_read" value="1">
                            <button type="submit" 
                                    class="text-lime-400 hover:text-lime-300 transition-colors"
                                    title="Mark as read">
                                <i data-feather="check" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                        <button class="text-gray-400 hover:text-white transition-colors"
                                onclick="showNotificationDetails(<?= htmlspecialchars(json_encode($note)) ?>)"
                                title="View details">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-3">
                    <div class="flex items-center space-x-4 text-sm text-gray-400">
                        <div class="flex items-center space-x-1">
                            <i data-feather="calendar" class="w-3 h-3"></i>
                            <span><?= date("M j, Y", strtotime($note['created_at'])) ?></span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i data-feather="clock" class="w-3 h-3"></i>
                            <span><?= date("g:i A", strtotime($note['created_at'])) ?></span>
                        </div>
                    </div>
                    <span class="text-xs <?= $notificationColor['text'] ?> px-2 py-1 rounded-full">
                        <?= ucwords($note['type']) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-6 flex justify-between items-center text-gray-400 text-sm">
            <div>
                Showing <?= count($notifications) ?> notifications
            </div>
            <div class="flex items-center space-x-1">
                <i data-feather="info" class="w-4 h-4"></i>
                <span><?= $unread_count ?> unread notifications</span>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Notification Details Modal -->
<div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold text-lime-400">Notification Details</h3>
            <button id="closeModal" class="text-gray-400 hover:text-white">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <div id="modalContent" class="space-y-4">
            <!-- Content will be populated by JavaScript -->
        </div>
        
        <div class="flex justify-end space-x-4 pt-4 mt-4 border-t border-gray-700">
            <button id="closeModalBtn" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<?php
// Helper functions for notification styling
function getNotificationColor($type) {
    $colors = [
        'sale' => ['bg' => 'bg-green-500', 'border' => 'border-green-500', 'text' => 'text-green-400'],
        'production' => ['bg' => 'bg-blue-500', 'border' => 'border-blue-500', 'text' => 'text-blue-400'],
        'over_usage' => ['bg' => 'bg-red-500', 'border' => 'border-red-500', 'text' => 'text-red-400'],
        'low_stock' => ['bg' => 'bg-yellow-500', 'border' => 'border-yellow-500', 'text' => 'text-yellow-400'],
        'system' => ['bg' => 'bg-purple-500', 'border' => 'border-purple-500', 'text' => 'text-purple-400'],
        'default' => ['bg' => 'bg-gray-500', 'border' => 'border-gray-500', 'text' => 'text-gray-400']
    ];
    
    return $colors[$type] ?? $colors['default'];
}

function getNotificationIcon($type) {
    $icons = [
        'sale' => 'shopping-cart',
        'production' => 'package',
        'over_usage' => 'alert-triangle',
        'low_stock' => 'alert-octagon',
        'system' => 'settings',
        'default' => 'bell'
    ];
    
    return $icons[$type] ?? $icons['default'];
}
?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const notificationModal = document.getElementById('notificationModal');
    const closeModal = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');

    // Close modal functions
    closeModal.addEventListener('click', () => {
        notificationModal.classList.add('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
        notificationModal.classList.add('hidden');
    });

    notificationModal.addEventListener('click', (e) => {
        if (e.target === notificationModal) {
            notificationModal.classList.add('hidden');
        }
    });

    // Global function to show notification details
    window.showNotificationDetails = function(notification) {
        const typeColors = {
            'sale': 'text-green-400',
            'production': 'text-blue-400', 
            'over_usage': 'text-red-400',
            'low_stock': 'text-yellow-400',
            'system': 'text-purple-400',
            'default': 'text-gray-400'
        };

        const colorClass = typeColors[notification.type] || typeColors['default'];
        
        modalTitle.textContent = 'Notification Details';
        modalContent.innerHTML = `
            <div class="space-y-3">
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Type</label>
                    <span class="${colorClass} font-semibold">${notification.type.replace('_', ' ').toUpperCase()}</span>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Message</label>
                    <p class="text-white">${notification.message}</p>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Status</label>
                    <span class="${notification.is_read ? 'text-gray-400' : 'text-lime-400'} font-semibold">
                        ${notification.is_read ? 'READ' : 'UNREAD'}
                    </span>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Created</label>
                    <p class="text-white">${new Date(notification.created_at).toLocaleString()}</p>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Notification ID</label>
                    <p class="text-gray-300 font-mono">${notification.id}</p>
                </div>
            </div>
        `;
        
        notificationModal.classList.remove('hidden');
    };

    // Auto-mark as read when notification is viewed (optional)
    document.querySelectorAll('[onclick*="showNotificationDetails"]').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.closest('.bg-gray-700').querySelector('input[name="notification_id"]');
            if (notificationId) {
                // You could add AJAX call here to automatically mark as read when viewed
            }
        });
    });

    // Initial feather icons render
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>