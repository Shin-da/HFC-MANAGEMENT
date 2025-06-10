class LayoutManager {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.navbar = document.querySelector('.navbar');
        this.pageWrapper = document.querySelector('.page-wrapper');
        this.sidebarToggle = document.getElementById('sidebar-toggle');
        this.BREAKPOINTS = {
            MOBILE: 768,
            TABLET: 991
        };
        
        this.init();
    }

    init() {
        // Set initial states based on screen size
        this.setInitialState();
        
        // Add event listeners
        this.sidebarToggle?.addEventListener('click', () => this.toggleSidebar());
        window.addEventListener('resize', () => this.handleResize());
        document.addEventListener('click', (e) => this.handleClickOutside(e));
        
        // Handle initial window width
        this.handleResize();
    }

    setInitialState() {
        const savedState = localStorage.getItem('sidebarState');
        const width = window.innerWidth;

        if (width <= this.BREAKPOINTS.MOBILE) {
            this.setState('hidden');
        } else if (width <= this.BREAKPOINTS.TABLET) {
            this.setState('collapsed');
        } else {
            this.setState(savedState || 'expanded');
        }
    }

    setState(state) {
        // Remove all states first
        ['expanded', 'collapsed', 'hidden'].forEach(cls => {
            this.sidebar?.classList.remove(cls);
            this.pageWrapper?.classList.remove(cls);
        });

        // Apply new state
        switch (state) {
            case 'expanded':