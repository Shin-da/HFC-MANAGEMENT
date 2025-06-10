document.addEventListener('DOMContentLoaded', function() {
    const appWrapper = document.querySelector('.app-wrapper');
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    function updateLayout(sidebarState) {
        // Update app wrapper data attribute
        appWrapper.setAttribute('data-sidebar-state', sidebarState);
        
        // Update sidebar classes
        sidebar.classList.remove('collapsed', 'hidden', 'active');
        if (sidebarState === 'collapsed') {
            sidebar.classList.add('collapsed');
        } else if (sidebarState === 'hidden') {
            sidebar.classList.add('hidden');
        }
        
        // Save state to localStorage
        localStorage.setItem('sidebarState', sidebarState);
    }
    
    function updateSidebarState() {
        if (window.innerWidth <= 767) {
            updateLayout('hidden');
        } else if (window.innerWidth <= 991) {
            updateLayout('collapsed');
        } else {
            updateLayout('expanded');
        }
    }

    // Toggle sidebar on button click
    sidebarToggle.addEventListener('click', () => {
        const currentState = appWrapper.getAttribute('data-sidebar-state');
        
        if (window.innerWidth <= 767) {
            // Mobile: toggle between hidden and active
            if (currentState === 'hidden') {
                sidebar.classList.add('active');
                sidebar.classList.remove('hidden');
            } else {
                sidebar.classList.remove('active');
                sidebar.classList.add('hidden');
            }
        } else {
            // Desktop/Tablet: toggle between expanded and collapsed
            const newState = currentState === 'expanded' ? 'collapsed' : 'expanded';
            updateLayout(newState);
        }
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 767 &&
            !sidebar.contains(e.target) &&
            !sidebarToggle.contains(e.target) &&
            sidebar.classList.contains('active')) {
            updateLayout('hidden');
        }
    });

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(updateSidebarState, 250);
    });

    // Initialize state
    const savedState = localStorage.getItem('sidebarState');
    if (savedState) {