document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const contentWrapper = document.querySelector('.content-wrapper');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        contentWrapper.classList.toggle('expanded');
        
        // Store sidebar state
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('active'));
    }
    
    // Attach click event to toggle button
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Handle responsive behavior
    function checkWidth() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('active');
            contentWrapper.classList.add('expanded');
        } else {
            // Restore previous state on desktop
            const wasCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            sidebar.classList.toggle('active', wasCollapsed);
            contentWrapper.classList.toggle('expanded', wasCollapsed);
        }
    }

    // Initialize sidebar state
    checkWidth();
    
    // Update on resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(checkWidth, 250);
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768) {
            const clickedOutsideSidebar = !sidebar.contains(e.target);
            const clickedOutsideToggle = !sidebarToggle.contains(e.target);
            
            if (clickedOutsideSidebar && clickedOutsideToggle && sidebar.classList.contains('active')) {
                toggleSidebar();
            }