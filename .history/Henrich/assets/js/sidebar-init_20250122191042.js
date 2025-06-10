document.addEventListener('DOMContentLoaded', function() {
    // Set initial sidebar state
    const sidebar = document.querySelector('.sidebar');
    if (window.innerWidth > 991) {
        sidebar.classList.add('open');
        sidebar.classList.remove('close', 'hidden');
    } else if (window.innerWidth < 991 && window.innerWidth > 600) {
        sidebar.classList.add('close');
        sidebar.classList.remove('open', 'hidden');
    } else {
        sidebar.classList.add('hidden');
        sidebar.classList.remove('open', 'close');
    }

    // Initialize submenu states
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sub-menu').forEach(submenu => {
        const links = submenu.querySelectorAll('.sub-nav-link a');
        links.forEach(link => {
            if (currentPath.includes(link.getAttribute('href'))) {
                submenu.classList.add('active');
                link.classList.add('active');
            }
        });
    });
});
