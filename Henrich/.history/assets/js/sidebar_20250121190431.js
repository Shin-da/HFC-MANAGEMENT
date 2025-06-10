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
    }
    
    // Toggle Handler
    function toggleSidebar() {
        const screenWidth = window.innerWidth;
        
        if (screenWidth <= 768) {
            setSidebarState(sidebar.classList.contains(STATES.HIDDEN) ? null : STATES.HIDDEN);
        } else {
            setSidebarState(sidebar.classList.contains(STATES.COLLAPSED) ? null : STATES.COLLAPSED);
        }
    }
    
    // Resize Handler
    function handleResize() {
        const width = window.innerWidth;
        
        if (width > 1200) {
            setSidebarState(null); // Expanded
        } else if (width > 768) {
            setSidebarState(STATES.COLLAPSED);
        } else {
            setSidebarState(STATES.HIDDEN);
        }
    }
    
    // Initialize
    function init() {
        // Restore saved state or set default
        const savedState = localStorage.getItem('sidebarState');
        if (savedState) {
            setSidebarState(savedState);
        } else {
            handleResize();
        }
        
        // Event Listeners
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        
        // Debounced resize handler
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(handleResize, 250);
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                const clickedOutsideSidebar = !sidebar.contains(e.target);
                const clickedOutsideToggle = !sidebarToggle.contains(e.target);
                
                if (clickedOutsideSidebar && clickedOutsideToggle && !sidebar.classList.contains(STATES.HIDDEN)) {
                    setSidebarState(STATES.HIDDEN);
                }
            }
        });
    }
    
    // Initialize
    init();
});