document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const sidebar = document.getElementById('sidebar');
    const contentWrapper = document.querySelector('.content-wrapper');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    // State Management
    const STATES = {
        EXPANDED: 'expanded',
        COLLAPSED: 'collapsed',
        HIDDEN: 'hidden'
    };
    
    function setSidebarState(state) {
        Object.values(STATES).forEach(s => {
            sidebar.classList.remove(s);
            contentWrapper.classList.remove(s);
        });
        
        if (state) {
            sidebar.classList.add(state);
            contentWrapper.classList.add(state);
        }
        
        localStorage.setItem('sidebarState', state || '');
    }
    
    // Core functionality
    function handleResize() {
        const width = window.innerWidth;
        if (width > 1200) setSidebarState(null);
        else if (width > 768) setSidebarState(STATES.COLLAPSED);
        else setSidebarState(STATES.HIDDEN);
    }

    function init() {
        // Restore saved state or set default
        const savedState = localStorage.getItem('sidebarState');
        savedState ? setSidebarState(savedState) : handleResize();
        
        // Event Listeners
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                const screenWidth = window.innerWidth;
                setSidebarState(
                    screenWidth <= 768 
                        ? (sidebar.classList.contains(STATES.HIDDEN) ? null : STATES.HIDDEN)
                        : (sidebar.classList.contains(STATES.COLLAPSED) ? null : STATES.COLLAPSED)
                );
            });
        }
        
        // Debounced resize handler
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(handleResize, 250);
        });
    }
    
    init();
});