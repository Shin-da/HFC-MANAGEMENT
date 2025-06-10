document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    // Function to update sidebar state based on screen width
    function updateSidebarState() {
        if (window.innerWidth <= 767) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('hidden');
        } else if (window.innerWidth <= 991) {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('collapsed');
        } else {
            sidebar.classList.remove('hidden', 'collapsed');
        }
    }

    // Toggle sidebar on button click
    sidebarToggle.addEventListener('click', () => {
        if (window.innerWidth <= 767) {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
        }
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 767 &&
            !sidebar.contains(e.target) &&
            !sidebarToggle.contains(e.target) &&
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            sidebar.classList.add('hidden');
        }
    });

    // Update sidebar state on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(updateSidebarState, 250);
    });

    // Initial state
    updateSidebarState();
});
