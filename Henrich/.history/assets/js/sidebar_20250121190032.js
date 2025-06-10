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
        // Remove all states
        Object.values(STATES).forEach(s => {
            sidebar.classList.remove(s);
            contentWrapper.classList.remove(s);
        });
        
        // Add new state
        if (state) {
            sidebar.classList.add(state);
            contentWrapper.classList.add(state);
        }
        
        // Save state
        localStorage.setItem('sidebarState', state || '');
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
                

    // Force panel reflow to trigger transition
    panel.offsetHeight;
}

// Clear all previous event listeners
sidebarToggle.replaceWith(sidebarToggle.cloneNode(true));
// Get the new toggle button reference
const newToggle = document.getElementById('sidebar-toggle');
// Add single click handler
newToggle.addEventListener('click', toggleSidebar);

// Initialize sidebar state
function initSidebar() {
    const screenWidth = window.innerWidth;
    
    if (screenWidth <= 768) {
        sidebar.classList.add('hidden');
        sidebar.classList.remove('close', 'open');
    } else if (screenWidth <= 992) {
        sidebar.classList.add('close');
        sidebar.classList.remove('hidden', 'open');
    } else {
        sidebar.classList.add('open');
        sidebar.classList.remove('hidden', 'close');
    }

    // Force panel reflow
    panel.offsetHeight;
}

// Handle resize with debounce
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(initSidebar, 250);
});

// Initialize on load
initSidebar();

// Handle sidebar states
function updateSidebarState() {
    if (window.innerWidth <= 768) {
        sidebar.classList.remove('collapsed');
        sidebar.classList.remove('open');
    } else if (window.innerWidth <= 1024) {
        sidebar.classList.add('collapsed');
        sidebar.classList.remove('open');
    } else {
        sidebar.classList.remove('collapsed');
        sidebar.classList.remove('open');
    }
}

// Event listeners
window.addEventListener('resize', () => {
    clearTimeout(window.resizeTimeout);
    window.resizeTimeout = setTimeout(updateSidebarState, 250);
});

// Initialize sidebar state
updateSidebarState();

// Submenu toggles
document.querySelectorAll('.submenu > a').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        const parent = item.parentElement;
        parent.classList.toggle('active');
    });
});

// Handle dropdowns
document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', (e) => {
        e.preventDefault();
        const dropdown = toggle.parentElement;
        
        // Close other dropdowns
        document.querySelectorAll('.nav-item.dropdown').forEach(item => {
            if (item !== dropdown && item.classList.contains('active')) {
                item.classList.remove('active');
            }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('active');
    });
});

// Enhanced dropdown functionality
document.querySelectorAll('.menu-button').forEach(button => {
    button.addEventListener('click', (e) => {
        const menuItem = button.parentElement;
        const wasOpen = menuItem.classList.contains('open');
        
        // Close all other open menus
        document.querySelectorAll('.has-submenu').forEach(item => {
            if (item !== menuItem && item.classList.contains('open')) {
                item.classList.remove('open');
            }
        });
        
        // Toggle current menu
        menuItem.classList.toggle('open', !wasOpen);
    });
});

// Handle sidebar state changes
function handleSidebarStateChange() {
    const isCollapsed = sidebar.classList.contains('close') || 
                       sidebar.classList.contains('hidden');
    
    if (isCollapsed) {
        document.querySelectorAll('.has-submenu').forEach(menu => {
            menu.classList.remove('open');
        });
    }
}

// Add state change handler to existing events
newToggle.addEventListener('click', handleSidebarStateChange);
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        updateSidebarState();
        handleSidebarStateChange();
    }, 250);
});

// Initialize active submenu
document.querySelectorAll('.has-submenu').forEach(menu => {
    if (menu.querySelector('.active')) {
        menu.classList.add('open');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const contentWrapper = document.querySelector('.content-wrapper');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    // Toggle function
    function toggleSidebar() {
        const screenWidth = window.innerWidth;
        
        if (screenWidth > 1200) {
            // On large screens - toggle between full and collapsed
            sidebar.classList.toggle('collapsed');
            contentWrapper.classList.toggle('collapsed');
        } else if (screenWidth > 768) {
            // On medium screens - toggle between collapsed and hidden
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                contentWrapper.classList.remove('expanded');
            } else {
                sidebar.classList.add('hidden');
                contentWrapper.classList.add('expanded');
            }
        } else {
            // On mobile - toggle visibility
            sidebar.classList.toggle('active');
            contentWrapper.classList.toggle('expanded');
        }
    }

    // Attach click event to toggle button
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const width = window.innerWidth;
            
            if (width > 1200) {
                // Reset to default expanded state
                sidebar.classList.remove('hidden', 'active');
                contentWrapper.classList.remove('expanded');
            } else if (width > 768) {
                // Collapse to icons
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('hidden', 'active');
                contentWrapper.classList.add('collapsed');
                contentWrapper.classList.remove('expanded');
            } else {
                // Hide sidebar on mobile
                sidebar.classList.add('hidden');
                sidebar.classList.remove('collapsed', 'active');
                contentWrapper.classList.add('expanded');
            }
        }, 250);
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768) {
            const clickedOutsideSidebar = !sidebar.contains(e.target);
            const clickedOutsideToggle = !sidebarToggle.contains(e.target);
            
            if (clickedOutsideSidebar && clickedOutsideToggle && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                contentWrapper.classList.add('expanded');
            }
        }
    });
});