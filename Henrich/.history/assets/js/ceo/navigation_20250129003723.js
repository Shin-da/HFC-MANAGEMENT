class CEONavigation {
    constructor() {
        this.currentPage = window.location.pathname.split('/').pop().replace('.php', '');
        this.init();
    }

    init() {
        this.updateActiveMenu();
        this.setupSidebarToggles();
        this.handlePageTransitions();
    }

    updateActiveMenu() {
        document.querySelectorAll('.nav-link').forEach(link => {
            const href = link.querySelector('a')?.getAttribute('href');
            if (href?.includes(this.currentPage)) {
                link.classList.add('active');
            }
        });
    }

    handlePageTransitions() {
        document.querySelectorAll('.nav-link a').forEach(link => {
            link.addEventListener('click', (e) => {
                const target = e.currentTarget.getAttribute('href');
                if (target !== '#' && !target.includes('javascript:')) {
                    localStorage.setItem('lastPage', this.currentPage);
                }
            });
        });
    }

    setupSidebarToggles() {
        const sidebar = document.querySelector('.sidebar');
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarState', 
                sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CEONavigation();
});
