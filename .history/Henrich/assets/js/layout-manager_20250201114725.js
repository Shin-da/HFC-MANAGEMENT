class LayoutManager {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.navbar = document.querySelector('.navbar');
        this.pageWrapper = document.querySelector('.page-wrapper');
        this.content = document.querySelector('.content-wrapper');
        this.sidebarToggle = document.getElementById('sidebar-toggle');
        
        this.init();
    }

    init() {
        // Initialize state
        const initialState = LayoutState.getInitialState();
        this.setState(initialState);

        // Add event listeners
        this.bindEvents();
        
        // Handle initial responsive behavior
        this.handleResponsiveLayout();
    }

    bindEvents() {
        // Toggle event
        this.sidebarToggle?.addEventListener('click', () => this.handleToggle());

        // Responsive events
        window.addEventListener('resize', () => this.handleResponsiveLayout());

        // Outside click on mobile
        document.addEventListener('click', (e) => this.handleOutsideClick(e));
    }

    handleResponsiveLayout() {
        const width = window.innerWidth;
        
        if (width <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE) {
            this.setState(LAYOUT_CONFIG.STATES.HIDDEN);
        } else if (width <= LAYOUT_CONFIG.BREAKPOINTS.TABLET) {
            this.setState(LAYOUT_CONFIG.STATES.COLLAPSED);
        }
    }

    handleToggle() {
        const width = window.innerWidth;
        const currentState = this.getCurrentState();
        
        if (width <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE) {
            // Mobile toggle behavior
            const newState = currentState === LAYOUT_CONFIG.STATES.HIDDEN ? 
                LAYOUT_CONFIG.STATES.EXPANDED : LAYOUT_CONFIG.STATES.HIDDEN;
            this.setState(newState);
            this.toggleOverlay(newState === LAYOUT_CONFIG.STATES.EXPANDED);
        } else {
            // Desktop toggle behavior
            const newState = currentState === LAYOUT_CONFIG.STATES.EXPANDED ? 
                LAYOUT_CONFIG.STATES.COLLAPSED : LAYOUT_CONFIG.STATES.EXPANDED;
            this.setState(newState);
        }
    }

    setState(state) {
        // Clear existing states
        Object.values(LAYOUT_CONFIG.STATES).forEach(s => {
            [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                el?.classList.remove(s));
        });

        // Apply new state
        [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
            el?.classList.add(state));

        // Update dimensions based on state
        this.updateDimensions(state);

        // Save state
        localStorage.setItem('sidebarState', state);
    }

    updateDimensions(state) {
        switch(state) {
            case LAYOUT_CONFIG.STATES.EXPANDED:
                this.navbar.style.left = `${LAYOUT_CONFIG.DIMENSIONS.SIDEBAR_EXPANDED}px`;
                this.content.style.marginLeft = `${LAYOUT_CONFIG.DIMENSIONS.SIDEBAR_EXPANDED}px`;
                break;
            case LAYOUT_CONFIG.STATES.COLLAPSED:
                this.navbar.style.left = `${LAYOUT_CONFIG.DIMENSIONS.SIDEBAR_COLLAPSED}px`;
                this.content.style.marginLeft = `${LAYOUT_CONFIG.DIMENSIONS.SIDEBAR_COLLAPSED}px`;
                break;
            case LAYOUT_CONFIG.STATES.HIDDEN:
                this.navbar.style.left = '0';
                this.content.style.marginLeft = '0';
                break;
        }
    }

    getCurrentState() {
        return Object.values(LAYOUT_CONFIG.STATES).find(state => 
            this.sidebar?.classList.contains(state)) || LAYOUT_CONFIG.STATES.HIDDEN;
    }

    toggleOverlay(show) {
        this.pageWrapper?.classList.toggle('overlay', show);
    }

    handleOutsideClick(e) {
        if (window.innerWidth <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE) {
            if (!this.sidebar?.contains(e.target) && 
                !this.sidebarToggle?.contains(e.target) && 
                this.getCurrentState() !== LAYOUT_CONFIG.STATES.HIDDEN) {
                this.setState(LAYOUT_CONFIG.STATES.HIDDEN);
                this.toggleOverlay(false);
            }
        }
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    window.layoutManager = new LayoutManager();
});
