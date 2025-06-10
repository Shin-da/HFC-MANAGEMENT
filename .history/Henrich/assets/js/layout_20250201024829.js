document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const navbar = document.querySelector('.navbar');
    const content = document.querySelector('.content-wrapper');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    // Store initial states
    let isMobile = window.innerWidth <= 768;
    let sidebarState = localStorage.getItem('sidebarState') || 'open';
    
    function updateLayout() {
        isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            sidebar.classList.remove('close');
            sidebar.classList.remove('open');
            sidebar.classList.add('hidden');
        } else {
            sidebar.classList.remove('hidden');
            sidebar.classList.toggle('close', sidebarState === 'closed');
        }
    }
    
    // Toggle sidebar
    sidebarToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        
        if (isMobile) {
            sidebar.classList.toggle('active');
        } else {
            sidebar.classList.toggle('close');
            sidebarState = sidebar.classList.contains('close') ? 'closed' : 'open';
            localStorage.setItem('sidebarState', sidebarState);
        }
    });
    
    // Close sidebar when clicking overlay on mobile
    content.addEventListener('click', (e) => {
        if (isMobile && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(updateLayout, 250);
    });
    
    // Initial setup
    updateLayout();
});
