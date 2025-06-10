document.addEventListener('DOMContentLoaded', function() {
    // Navbar toggle functionality
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const navbar = document.querySelector('.navbar');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            document.body.classList.toggle('sidebar-collapsed');
        });
    }

    // Dropdown handling
    const dropdownButtons = document.querySelectorAll('.nav-button');
    
    dropdownButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const navItem = button.closest('.nav-item');
            const wasActive = navItem.classList.contains('active');

            // Close all other dropdowns
            document.querySelectorAll('.nav-item.active').forEach(item => {
                if (item !== navItem) {
                    item.classList.remove('active');
                }
            });

            // Toggle current dropdown
            navItem.classList.toggle('active', !wasActive);
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.nav-item.active').forEach(item => {
            item.classList.remove('active');
        });
    });

    // Prevent dropdown from closing when clicking inside
    document.querySelectorAll('.dropdown-panel, .notification-dropdown-content')
        .forEach(panel => {
            panel.addEventListener('click', (e) => e.stopPropagation());
        });

    // Theme toggle functionality
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
        });
    }
});
