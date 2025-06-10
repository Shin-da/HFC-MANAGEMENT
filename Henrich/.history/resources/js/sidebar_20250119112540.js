const body = document.querySelector("body"),
    sidebar = body.querySelector(".sidebar"),
    toggle = document.getElementById("toggle"),
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
    toggle.classList.toggle("hidden", sidebarHidden);
    console.log("Sidebar is hidden: ", sidebarHidden);
}

if (sidebarClosed) {
    sidebar.classList.toggle("close", sidebarClosed);
    toggle.classList.toggle("close", sidebarClosed);
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

toggle.addEventListener("click", () => {
    if (window.innerWidth < 600) {
      sidebar.classList.toggle("hidden");
      sidebar.classList.remove("open");
      toggle.classList.toggle("hidden");
      sidebarStates.save();
    } else if (window.innerWidth < 992 && window.innerWidth > 600) {
      sidebar.classList.remove("hidden");
      sidebar.classList.toggle("close");
      sidebar.classList.remove("open");
      toggle.classList.toggle("close");
      sidebarStates.save();
    } else {
      sidebar.classList.toggle("open");
      sidebar.classList.toggle("close");
      sidebarStates.save();
    }

    console.log("Sidebar state: ", sidebar.classList);
});

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

toggle.addEventListener('click', () => {
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('open');
    } else {
        sidebar.classList.toggle('collapsed');
    }
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
toggle.addEventListener('click', handleSidebarStateChange);
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