document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const contentWrapper = document.querySelector('.content-wrapper');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mainContent = document.querySelector('.main-content');
    const BREAKPOINTS = {
        MOBILE: 768,
        TABLET: 992,
        DESKTOP: 1200
    };

    function updateSidebarState() {
        const width = window.innerWidth;
        
        if (width <= BREAKPOINTS.MOBILE) {
            // Mobile view - hide sidebar
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('hidden');
            contentWrapper.style.marginLeft = '0';
        } else if (width <= BREAKPOINTS.TABLET) {
            // Tablet view - collapse sidebar
            sidebar.classList.add('collapsed');
            sidebar.classList.remove('hidden');
            contentWrapper.style.marginLeft = '70px';
        } else {
            // Desktop view - expanded sidebar
            sidebar.classList.remove('collapsed', 'hidden');
            contentWrapper.style.marginLeft = '260px';
        }
        
        // Save state
        localStorage.setItem('sidebarState', sidebar.classList.contains('hidden') ? 'hidden' : 
                                          sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
    }

    function toggleSidebar() {
        const width = window.innerWidth;
        
        if (width <= BREAKPOINTS.MOBILE) {
            sidebar.classList.toggle('hidden');
            contentWrapper.style.marginLeft = sidebar.classList.contains('hidden') ? '0' : '260px';
        } else {
            sidebar.classList.toggle('collapsed');
            contentWrapper.style.marginLeft = sidebar.classList.contains('collapsed') ? '70px' : '260px';
        }
        
        // Save state
        localStorage.setItem('sidebarState', sidebar.classList.contains('hidden') ? 'hidden' : 
                                          sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
    }

    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    // Handle click outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= BREAKPOINTS.MOBILE 
            && !sidebar.contains(e.target) 
            && !sidebarToggle.contains(e.target) 
            && !sidebar.classList.contains('hidden')) {
            sidebar.classList.add('hidden');
            contentWrapper.style.marginLeft = '0';
        }
    });

    // Debounced resize handler
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(updateSidebarState, 250);
    });

    // Initial setup
    updateSidebarState();

    // Restore saved state if applicable
    const savedState = localStorage.getItem('sidebarState');
    if (savedState && window.innerWidth > BREAKPOINTS.MOBILE) {
        if (savedState === 'collapsed') {
            sidebar.classList.add('collapsed');
            contentWrapper.style.marginLeft = '70px';
        } else if (savedState === 'expanded') {