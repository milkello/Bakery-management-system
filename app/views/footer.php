<?php
// Only show footer content if user is logged in
if (isset($_SESSION['user_id'])):
?>
        </main>
    </div>

    <!-- <footer class="text-center text-sm text-gray-500 p-6">Bakery Management System · Scaffold</footer> -->

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const sidebarToggleIcon = document.querySelector('.sidebar-toggle-icon');
        
        // Check if sidebar state is saved in localStorage
        const isSidebarMinimized = localStorage.getItem('sidebarMinimized') === 'true';
        
        // Initialize sidebar state
        if (isSidebarMinimized) {
            minimizeSidebar();
        } else {
            expandSidebar();
        }
        
        // Desktop toggle button
        sidebarToggle.addEventListener('click', function() {
            if (sidebar.classList.contains('sidebar-expanded')) {
                minimizeSidebar();
            } else {
                expandSidebar();
            }
        });
        
        // Mobile toggle button
        mobileSidebarToggle.addEventListener('click', function() {
            if (sidebar.classList.contains('sidebar-expanded')) {
                minimizeSidebar();
            } else {
                expandSidebar();
            }
        });
        
        function minimizeSidebar() {
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-minimized');
            mainContent.classList.remove('main-expanded');
            mainContent.classList.add('main-minimized');
            sidebarToggleIcon.setAttribute('data-feather', 'chevron-right');
            localStorage.setItem('sidebarMinimized', 'true');
            feather.replace();
        }
        
        function expandSidebar() {
            sidebar.classList.remove('sidebar-minimized');
            sidebar.classList.add('sidebar-expanded');
            mainContent.classList.remove('main-minimized');
            mainContent.classList.add('main-expanded');
            sidebarToggleIcon.setAttribute('data-feather', 'chevron-left');
            localStorage.setItem('sidebarMinimized', 'false');
            feather.replace();
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth < 1024) {
                // On mobile, start with minimized sidebar
                if (!sidebar.classList.contains('sidebar-minimized')) {
                    minimizeSidebar();
                }
            } else {
                // On desktop, restore saved state
                if (localStorage.getItem('sidebarMinimized') === 'true') {
                    minimizeSidebar();
                } else {
                    expandSidebar();
                }
            }
        });
        
        // Initialize on load based on screen size
        if (window.innerWidth < 1024) {
            minimizeSidebar();
        }
        
        // Initial feather icons render
        feather.replace();
    });
    </script>
</body>
</html>
<?php endif; ?>