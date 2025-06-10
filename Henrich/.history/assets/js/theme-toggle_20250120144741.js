document.addEventListener('DOMContentLoaded', () => {
    console.log('Theme script loaded');
    
    const themeToggle = document.getElementById('themeToggle');
    console.log('Theme toggle button:', themeToggle);
    
    if (!themeToggle) {
        console.error('Theme toggle button not found!');
        return;
    }

    let currentTheme = localStorage.getItem('theme') || 'light';
    console.log('Current theme:', currentTheme);

    document.documentElement.setAttribute('data-theme', currentTheme);
    updateIcon(currentTheme);

    themeToggle.addEventListener('click', (e) => {
        console.log('Theme toggle clicked');
        currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
        console.log('Switching to theme:', currentTheme);
        
        document.documentElement.setAttribute('data-theme', currentTheme);
        localStorage.setItem('theme', currentTheme);        updateIcon(currentTheme);    });    function updateIcon(theme) {
        const icon = themeToggle.querySelector('i');
        if (icon) {
            icon.className = theme === 'dark' ? 'bx bx-sun' : 'bx bx-moon';
            console.log('Updated icon to:', icon.className);
        }
    }
});