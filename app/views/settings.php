<!-- Settings Page -->
<?php if (isset($_SESSION['success'])): ?>
<div class="mb-4 bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded-lg">
    <div class="flex items-center">
        <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
        <span><?= htmlspecialchars($_SESSION['success']) ?></span>
    </div>
</div>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="mb-4 bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg">
    <div class="flex items-center">
        <i data-feather="alert-circle" class="w-5 h-5 mr-2"></i>
        <span><?= htmlspecialchars($_SESSION['error']) ?></span>
    </div>
</div>
<?php unset($_SESSION['error']); endif; ?>

<div class="mb-6">
    <h2 class="text-3xl font-bold text-lime-400">⚙️ Settings</h2>
    <p class="text-gray-400 mt-2">Manage your account and system preferences</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Settings Area -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Personal Information -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold text-lime-400 mb-6 flex items-center">
                <i data-feather="user" class="w-5 h-5 mr-2"></i>
                Personal Information
            </h3>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_profile">
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($current_user['username']) ?>" required
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($current_user['email'] ?? '') ?>"
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($current_user['phone'] ?? '') ?>" placeholder="+250 XXX XXX XXX"
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                
                <!-- Role Display (NOT editable) -->
                <div class="bg-gray-700 p-4 rounded-lg border-2 border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-semibold flex items-center">
                                <i data-feather="shield" class="w-4 h-4 mr-2"></i>
                                Account Role
                            </p>
                            <p class="text-gray-400 text-sm mt-1">Your current role in the system</p>
                        </div>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold <?= $user_role === 'admin' ? 'bg-lime-500 text-white' : 'bg-fuchsia-500 text-white' ?>">
                            <?= htmlspecialchars(ucfirst($user_role)) ?>
                        </span>
                    </div>
                    <div class="mt-3 flex items-center text-gray-500 text-xs">
                        <i data-feather="lock" class="w-3 h-3 mr-1"></i>
                        <span>Role cannot be changed by users for security reasons</span>
                    </div>
                </div>
                
                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center space-x-2">
                        <i data-feather="save" class="w-4 h-4"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Change Password -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold text-fuchsia-400 mb-6 flex items-center">
                <i data-feather="lock" class="w-5 h-5 mr-2"></i>
                Change Password
            </h3>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="change_password">
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-fuchsia-500">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">New Password</label>
                    <input type="password" name="new_password" required minlength="6"
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-fuchsia-500">
                    <p class="text-gray-500 text-xs mt-1">At least 6 characters</p>
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Confirm New Password</label>
                    <input type="password" name="confirm_password" required minlength="6"
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-fuchsia-500">
                </div>
                
                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-fuchsia-500 hover:bg-fuchsia-600 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center space-x-2">
                        <i data-feather="key" class="w-4 h-4"></i>
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Preferences -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold text-lime-400 mb-6 flex items-center">
                <i data-feather="sliders" class="w-5 h-5 mr-2"></i>
                Preferences
            </h3>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_preferences">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Theme</label>
                        <select name="theme" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                            <option value="dark" <?= $user_prefs['theme'] === 'dark' ? 'selected' : '' ?>>🌙 Dark</option>
                            <option value="light" <?= $user_prefs['theme'] === 'light' ? 'selected' : '' ?>>☀️ Light</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Language</label>
                        <select name="language" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                            <option value="en" <?= $user_prefs['language'] === 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
                            <option value="rw" <?= $user_prefs['language'] === 'rw' ? 'selected' : '' ?>>🇷🇼 Kinyarwanda</option>
                        </select>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <label class="block text-gray-400 text-sm mb-2">Notifications</label>
                    
                    <label class="flex items-center space-x-3 cursor-pointer p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                        <input type="checkbox" name="email_notifications" <?= $user_prefs['email_notifications'] ? 'checked' : '' ?>
                               class="w-5 h-5 bg-gray-600 border-gray-500 rounded focus:ring-2 focus:ring-lime-500 text-lime-500">
                        <div class="flex-1">
                            <p class="text-white font-medium">Email Notifications</p>
                            <p class="text-gray-400 text-sm">Receive updates via email</p>
                        </div>
                        <i data-feather="mail" class="w-5 h-5 text-gray-400"></i>
                    </label>
                    
                    <label class="flex items-center space-x-3 cursor-pointer p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                        <input type="checkbox" name="sms_notifications" <?= $user_prefs['sms_notifications'] ? 'checked' : '' ?>
                               class="w-5 h-5 bg-gray-600 border-gray-500 rounded focus:ring-2 focus:ring-lime-500 text-lime-500">
                        <div class="flex-1">
                            <p class="text-white font-medium">SMS Notifications</p>
                            <p class="text-gray-400 text-sm">Receive alerts via text message</p>
                        </div>
                        <i data-feather="message-square" class="w-5 h-5 text-gray-400"></i>
                    </label>
                </div>
                
                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center space-x-2">
                        <i data-feather="save" class="w-4 h-4"></i>
                        <span>Save Preferences</span>
                    </button>
                </div>
            </form>
        </div>
        
        <?php if ($user_role === 'admin'): ?>
        <!-- System Settings (Admin Only) -->
        <div class="bg-gradient-to-br from-lime-900 to-gray-800 rounded-xl p-6 shadow-lg border-2 border-lime-500">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-lime-400 flex items-center">
                    <i data-feather="settings" class="w-5 h-5 mr-2"></i>
                    System Settings
                </h3>
                <span class="px-3 py-1 bg-lime-500 text-white text-xs font-semibold rounded-full flex items-center space-x-1">
                    <i data-feather="shield" class="w-3 h-3"></i>
                    <span>ADMIN ONLY</span>
                </span>
            </div>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_system">
                
                <div>
                    <label class="block text-gray-300 text-sm mb-2">Business Name</label>
                    <input type="text" name="business_name" value="<?= htmlspecialchars($system_settings['business_name']) ?>" required
                           class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                </div>
                
                <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">Currency</label>
                        <input type="text" name="currency" value="<?= htmlspecialchars($system_settings['currency']) ?>" required
                               class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500"
                               placeholder="Frw">
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">Timezone</label>
                        <select name="timezone" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                            <option value="Africa/Kigali" <?= $system_settings['timezone'] === 'Africa/Kigali' ? 'selected' : '' ?>>Africa/Kigali (EAT)</option>
                            <option value="UTC" <?= $system_settings['timezone'] === 'UTC' ? 'selected' : '' ?>>UTC</option>
                            <option value="Africa/Nairobi" <?= $system_settings['timezone'] === 'Africa/Nairobi' ? 'selected' : '' ?>>Africa/Nairobi (EAT)</option>
                            <option value="Africa/Kampala" <?= $system_settings['timezone'] === 'Africa/Kampala' ? 'selected' : '' ?>>Africa/Kampala (EAT)</option>
                        </select>
                    </div>
                </div> -->
                
                <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">Date Format</label>
                        <select name="date_format" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                            <option value="Y-m-d" <?= $system_settings['date_format'] === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD (2025-11-08)</option>
                            <option value="d/m/Y" <?= $system_settings['date_format'] === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY (08/11/2025)</option>
                            <option value="m/d/Y" <?= $system_settings['date_format'] === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY (11/08/2025)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="<?= htmlspecialchars($system_settings['low_stock_threshold']) ?>" 
                               required min="1" class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <p class="text-gray-400 text-xs mt-1">Alert when stock falls below this number</p>
                    </div>
                </div>
                
                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-lime-500 hover:bg-lime-600 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center space-x-2">
                        <i data-feather="save" class="w-4 h-4"></i>
                        <span>Update System Settings</span>
                    </button>
                </div> -->
            </form>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar Info -->
    <div class="space-y-6">
        <!-- Account Info Card -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold text-lime-400 mb-4">Account Info</h3>
            
            <div class="flex flex-col items-center text-center mb-4">
                <div class="w-20 h-20 bg-gradient-to-br from-lime-500 to-fuchsia-500 rounded-full flex items-center justify-center mb-3">
                    <i data-feather="user" class="w-10 h-10 text-white"></i>
                </div>
                <p class="text-white font-semibold text-lg"><?= htmlspecialchars($current_user['username']) ?></p>
                <p class="text-gray-400 text-sm"><?= htmlspecialchars($current_user['email'] ?? 'No email set') ?></p>
                <?php if (!empty($current_user['phone'])): ?>
                <p class="text-gray-500 text-sm mt-1"><?= htmlspecialchars($current_user['phone']) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between p-2 bg-gray-700 rounded-lg">
                    <span class="text-gray-400">Member Since</span>
                    <span class="text-white font-medium"><?= date('M Y', strtotime($current_user['created_at'])) ?></span>
                </div>
                <div class="flex items-center justify-between p-2 bg-gray-700 rounded-lg">
                    <span class="text-gray-400">Role</span>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $user_role === 'admin' ? 'bg-lime-500 text-white' : 'bg-fuchsia-500 text-white' ?>">
                        <?= htmlspecialchars(ucfirst($user_role)) ?>
                    </span>
                </div>
                <div class="flex items-center justify-between p-2 bg-gray-700 rounded-lg">
                    <span class="text-gray-400">User ID</span>
                    <span class="text-white font-medium">#<?= $user_id ?></span>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold text-fuchsia-400 mb-4">Quick Links</h3>
            
            <div class="space-y-2">
                <!-- <a href="?page=profile" class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <i data-feather="user" class="w-5 h-5 text-lime-400"></i>
                    <span class="text-white">View Profile</span>
                </a> -->
                
                <a href="?page=notifications" class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <i data-feather="bell" class="w-5 h-5 text-fuchsia-400"></i>
                    <span class="text-white">Notifications</span>
                </a>
                
                <?php if ($user_role === 'admin'): ?>
                <a href="?page=admin_reports" class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <i data-feather="file-text" class="w-5 h-5 text-lime-400"></i>
                    <span class="text-white">Reports</span>
                </a>
                <?php else: ?>
                <a href="?page=employee_reports" class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    <i data-feather="file-text" class="w-5 h-5 text-fuchsia-400"></i>
                    <span class="text-white">My Reports</span>
                </a>
                <?php endif; ?>
                
                <a href="?page=logout" class="flex items-center space-x-3 p-3 bg-red-900 rounded-lg hover:bg-red-800 transition">
                    <i data-feather="log-out" class="w-5 h-5 text-red-400"></i>
                    <span class="text-white">Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Security Notice -->
        <div class="bg-gray-800 rounded-xl p-6 shadow-lg border-l-4 border-yellow-500">
            <div class="flex items-start space-x-3">
                <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-500 mt-1"></i>
                <div>
                    <h4 class="text-white font-semibold mb-1">Security Notice</h4>
                    <p class="text-gray-400 text-sm">
                        Your account role is protected and cannot be changed through settings. 
                        Contact system administrator for role modifications.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
