document.addEventListener('DOMContentLoaded', function() {
    initializeStockMovement();
    setupThemeHandling();
});

function initializeStockMovement() {
    initializeSelect2();
    setupFormValidation();
    setupEventListeners();
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
    
    // Update Select2 theme
    $('.select2-container').addClass(isDark ? 'select2-dark' : 'select2-light');
    
    // Update form controls
    document.querySelectorAll('.form-control').forEach(control => {
        control.style.backgroundColor = isDark ? 'var(--dark-input-bg)' : 'var(--input-bg)';
        control.style.color = isDark ? 'var(--dark-text)' : 'var(--text-color)';
    });
}

// ... existing JavaScript functions ...
