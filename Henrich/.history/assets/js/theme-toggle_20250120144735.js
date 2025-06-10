document.addEventListener('DOMContentLoaded', () => {
    console.log('Theme script loaded');
    
    const themeToggle = document.getElementById('themeToggle');
    console.log('Theme toggle button:', themeToggle);
    
    if (!themeToggle) {
        console.error('Theme toggle button not found!');
        return;
    }
    themeToggle.addEventListener('click', () => {
        currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', currentTheme);
        localStorage.setItem('theme', currentTheme);
        updateIcon(currentTheme);
    });

    function updateIcon(theme) {
        const icon = themeToggle.querySelector('i');
        if (icon) {
            icon.className = theme === 'dark' ? 'bx bx-sun' : 'bx bx-moon';
        }
    }
});