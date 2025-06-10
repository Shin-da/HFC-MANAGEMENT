document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    
    if (!themeToggle) {
        console.error('Theme toggle button not found! ID: themeToggle');
        return;
    }

    // Log for debugging
    console.log('Theme script initialized');
    
    // Get current theme
    let currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply theme on load
    setTheme(currentTheme);
    
    // Add click handler
    themeToggle.addEventListener('click', function() {
        // Log for debugging
        console.log('Theme toggle clicked');
        
        // Toggle theme
        currentTheme = currentTheme === 'light' ? 'dark' : 'light';
        setTheme(currentTheme);
    });
    
    function setTheme(theme) {
        // Log for debugging
        console.log('Setting theme to:', theme);
        
        document.documentElement.setAttribute('data-theme', theme);
    }

    updateToggleIcon(theme) {
        const icon = this.themeToggle.querySelector('i');
        icon.className = theme === 'dark' ? 'bx bx-sun' : 'bx bx-moon';
    }

    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.applyTheme(newTheme);
        this.updateToggleIcon(newTheme);
    }
}

// Initialize theme manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ThemeManager();
});
