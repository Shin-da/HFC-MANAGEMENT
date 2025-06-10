document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const content = document.querySelector('.content-wrapper');
    const BREAKPOINTS = {
        MOBILE: 768,
        TABLET: 992,
        DESKTOP: 1200
    };

    // Get saved state from localStorage
    const savedState = localStorage.getItem('sidebarState') || 'open';
    
    // Initialize sidebar state
    function initSidebar() {
        if (window.innerWidth < 768) {
            sidebar.classList.add('hidden');
            content.style.marginLeft = '0';
        } else {
            sidebar.classList.remove('hidden');
            if (savedState === 'closed') {
                sidebar.classList.add('close');
                content.style.marginLeft = '64px';
            } else {
                content.style.marginLeft = '250px';
            }
        }
    }

    // Toggle sidebar function
    function toggleSidebar() {
        const isMobile = window.innerWidth < 768;
        
        if (isMobile) {
            sidebar.classList.toggle('hidden');
            const isHidden = sidebar.classList.contains('hidden');
            content.style.marginLeft = isHidden ? '0' : '250px';
            localStorage.setItem('sidebarState', isHidden ? 'hidden' : 'open');
        } else {
            sidebar.classList.toggle('close');
            const isClosed = sidebar.classList.contains('close');
            content.style.marginLeft = isClosed ? '64px' : '250px';
            localStorage.setItem('sidebarState', isClosed ? 'closed' : 'open');
        }
        
        // Update toggle button icon
        const toggleIcon = sidebarToggle.querySelector('i');
        if (sidebar.classList.contains('hidden') || sidebar.classList.contains('close')) {
            toggleIcon.classList.replace('bx-menu', 'bx-menu-alt-right');
        } else {
            toggleIcon.classList.replace('bx-menu-alt-right', 'bx-menu');
        }
    }

    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            initSidebar();
        }, 250);
    });

    // Handle clicks outside sidebar on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 
            && !sidebar.contains(e.target) 
            && !sidebarToggle.contains(e.target) 
            && !sidebar.classList.contains('hidden')) {
            toggleSidebar();
        }
    });

    // Initial setup
    initSidebar();
});