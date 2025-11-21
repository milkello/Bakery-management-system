<?php

// Simple translation helper for English and Kinyarwanda
// Usage: t('dashboard'), t('employees'), etc.

if (!function_exists('t')) {
    function t(string $key): string
    {
        $language = $_SESSION['language'] ?? 'en';

        $translations = [
            'en' => [
                'app_name' => 'Bakery Management System',
                'dashboard' => 'Dashboard',
                'employees' => 'Employees',
                'raw_materials' => 'Raw Materials',
                'products' => 'Products',
                'product_boards' => 'Product Boards',
                'sales' => 'Sales',
                'schedules' => 'Schedules',
                'customers' => 'Customers',
                'admin_reports' => 'PDF Reports',
                'notifications' => 'Notifications',
                'logout' => 'Logout',
                'settings' => 'Settings',
                'change_password' => 'Change Password',
                'preferences' => 'Preferences',
                'theme' => 'Theme',
                'language' => 'Language',
                'email_notifications' => 'Email Notifications',
                'sms_notifications' => 'SMS Notifications',
                'save_changes' => 'Save Changes',
                'save_preferences' => 'Save Preferences',
                'login' => 'Login',
                'username' => 'Username',
                'password' => 'Password',

                // Notifications / generic labels
                'total_notifications' => 'Total Notifications',
                'unread' => 'Unread',
                'today' => 'Today',
                'notification_types' => 'Notification Types',
                'notification_center' => 'Notification Center',
                'mark_all_read' => 'Mark All as Read',
                'clear_all' => 'Clear All',
                'all_notifications' => 'All Notifications',
                'sorted_by_latest' => 'Sorted by Latest',
                'no_notifications' => 'No Notifications',
                'no_notifications_subtitle' => "You're all caught up! No notifications to display.",
                'new' => 'New',
                'showing_notifications' => 'Showing :count notifications',
                'unread_notifications' => ':count unread notifications',
                'notification_details' => 'Notification Details',
                'type' => 'Type',
                'message' => 'Message',
                'status' => 'Status',
                'read' => 'READ',
                'unread_upper' => 'UNREAD',
                'created' => 'Created',
                'notification_id' => 'Notification ID',
                'close' => 'Close',
            ],
            'rw' => [
                'app_name' => 'Sisitemu yo Gucunga Boulangerie',
                'dashboard' => 'Ibikoresho by’ingenzi',
                'employees' => 'Abakozi',
                'raw_materials' => 'Stock',
                'products' => 'Ibicuruzwa',
                'product_boards' => 'Ordres',
                'sales' => 'Ubucuruzi',
                'schedules' => 'Igenamigambi',
                'customers' => 'Abakiriya',
                'admin_reports' => 'Raporo za PDF',
                'notifications' => 'Ubutumwa',
                'logout' => 'Gusohoka',
                'settings' => 'Settings',
                'change_password' => 'Guhindura ijambo ry’ibanga',
                'preferences' => 'Ibyo ukunda',
                'theme' => 'theme',
                'language' => 'Ururimi',
                'email_notifications' => 'Ubutumwa kuri email',
                'sms_notifications' => 'Ubutumwa bugufi (SMS)',
                'save_changes' => 'Bika impinduka',
                'save_preferences' => 'Bika ibyo ukunda',
                'login' => 'Kwinjira',
                'username' => 'Izina ukoresha',
                'password' => 'Ijambo ry’ibanga',

                // Notifications / generic labels
                'total_notifications' => 'Ubutumwa bwose',
                'unread' => 'Butarasomwa',
                'today' => 'Uyu munsi',
                'notification_types' => 'Ubwoko bw’ubutumwa',
                'notification_center' => 'Ahantu habikwa ubutumwa',
                'mark_all_read' => 'Andika ko bwose bwasomwe',
                'clear_all' => 'Siba ubutumwa bwose',
                'all_notifications' => 'Ubutumwa bwose',
                'sorted_by_latest' => 'Bwahanzweho uhereye ku bushya',
                'no_notifications' => 'Nta butumwa buraboneka',
                'no_notifications_subtitle' => 'Ugezweho neza! Nta butumwa bushya.',
                'new' => 'Bushya',
                'showing_notifications' => 'Urabona ubutumwa :count',
                'unread_notifications' => 'Ubutumwa :count butarasomwa',
                'notification_details' => 'Amakuru y’ubutumwa',
                'type' => 'Ubwoko',
                'message' => 'Ubutumwa',
                'status' => 'Imiterere',
                'read' => 'BWASOMWE',
                'unread_upper' => 'NTIBWASOMWE',
                'created' => 'Byoherejwe',
                'notification_id' => 'Nomero y’ubutumwa',
                'close' => 'Funga',
            ],
        ];

        // Fallback logic: try requested language, else English, else key
        if (isset($translations[$language][$key])) {
            return $translations[$language][$key];
        }
        if (isset($translations['en'][$key])) {
            return $translations['en'][$key];
        }

        return $key;
    }
}
