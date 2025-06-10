const body = document.querySelector("body"),
    sidebar = document.querySelector('.sidebar'),
    sidebarToggle = document.getElementById('sidebar-toggle'),
    panel = document.querySelector(".panel"),
    searchBtn = body.querySelector(".search-box"),
    modeSwitch = body.querySelector(".toggle-switch"),
    modeText = body.querySelector(".mode-text");

// State Management
const sidebarStates = {
    load: () => ({
        hidden: localStorage.getItem("sidebarHidden") === "true",
        closed: localStorage.getItem("sidebarClosed") === "true",
        open: localStorage.getItem("sidebarOpen") === "true"
    }),
    save: () => {
        localStorage.setItem("sidebarOpen", sidebar.classList.contains("open"));
        localStorage.setItem("sidebarClosed", sidebar.classList.contains("close"));
        localStorage.setItem("sidebarHidden", sidebar.classList.contains("hidden"));
    }
};

const currentTheme = localStorage.getItem("theme");
const { hidden: sidebarHidden, closed: sidebarClosed, open: sidebarOpen } = sidebarStates.load();

if (sidebarHidden) {
    sidebar.classList.toggle("hidden", sidebarHidden);
    sidebarToggle.classList.toggle("hidden", sidebarHidden);
    console.log("Sidebar is hidden: ", sidebarHidden);
}

if (sidebarClosed) {
    sidebar.classList.toggle("close", sidebarClosed);
    sidebarToggle.classList.toggle("close", sidebarClosed);
    console.log("Sidebar is closed: ", sidebarClosed);
}

console.log("Window width: ", window.innerWidth);

let resizeTimeout;

window.addEventListener("resize", () => {
  clearTimeout(resizeTimeout);
  resizeTimeout = setTimeout(() => {
    if (window.innerWidth > 992) {
      sidebar.classList.remove("hidden");
      sidebar.classList.remove("close");
      sidebar.classList.add("open");
      sidebarStates.save();
    } else if (window.innerWidth > 600 && window.innerWidth < 992) {
      sidebar.classList.remove("hidden");
      sidebar.classList.toggle("close");
      sidebar.classList.remove("open");
      sidebarStates.save();
    } else {
      sidebar.classList.toggle("hidden");
      sidebar.classList.remove("close");
      sidebar.classList.remove("open");
      sidebarStates.save();
    }
  }, 500); // 500ms delay
});

// Clean up existing handlers
function toggleSidebar() {
    const screenWidth = window.innerWidth;
    
    if (screenWidth <= 768) {
        // Mobile view - toggle hidden state
        sidebar.classList.toggle('hidden');
        sidebar.classList.remove('close', 'open');
    } else {
        // Desktop/tablet view - toggle between open and close states
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            sidebar.classList.add('close');
        } else {
            sidebar.classList.remove('close');
            sidebar.classList.add('open');
        }
        sidebar.classList.remove('hidden');
    }

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