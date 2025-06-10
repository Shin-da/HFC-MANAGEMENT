document.addEventListener('DOMContentLoaded', function() {
    initializeOrderDashboard();
    setupThemeHandling();
});

function initializeOrderDashboard() {
    setupFilters();
    setupTableControls();
    initializeRealTimeUpdates();
}

function setupThemeHandling() {
    // Update UI colors based on theme
    const currentTheme = document.documentElement.getAttribute('data-theme');
    updateUITheme(currentTheme);
    
    // Listen for theme changes
    document.addEventListener('themeChanged', (e) => {
        updateUITheme(e.detail.theme);
    });
}

function updateUITheme(theme) {
    const isDark = theme === 'dark';
    
    // Update status badges
    document.querySelectorAll('.status-badge').forEach(badge => {
        badge.style.opacity = isDark ? '0.9' : '1';
    });
    
    // Update table styles
    const table = document.querySelector('.order-table');
    if (table) {
        table.style.backgroundColor = isDark ? 'var(--card-bg)' : 'var(--background-color)';
    }
}

function initializeRealTimeUpdates() {
    // Check for new orders every minute
    setInterval(() => {
        fetch('check_new_orders.php')
            .then(response => response.json())
            .then(data => updateOrdersDisplay(data))
            .catch(error => console.error('Order update error:', error));
    }, 60000);
}

// ...existing table control and filter functions...
