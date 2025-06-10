document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const contentWrapper = document.querySelector('.content-wrapper');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mainContent = document.querySelector('.main-content');
    const BREAKPOINTS = {
        MOBILE: 768,
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            sidebar.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            contentWrapper.classList.toggle('collapsed');
        }
    }
    
    // Handle resize events
    function handleResize() {
        const width = window.innerWidth;
        
        if (width > 768) {
            sidebar.classList.remove('active');
            if (width <= 1200) {
                sidebar.classList.add('collapsed');
                contentWrapper.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                contentWrapper.classList.remove('collapsed');
            }
        }
    }
    
    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Handle click outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 
            && !sidebar.contains(e.target) 
            && !sidebarToggle.contains(e.target) 
            && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
    
    // Debounced resize handler
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResize, 250);
    });
    
    // Initial setup
    handleResize();
});