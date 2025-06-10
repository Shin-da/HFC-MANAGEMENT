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

        // Restore submenu states
        this.restoreSubmenuStates();

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
        const isMobile = width <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE;
        
        if (isMobile) {
            const newState = currentState === LAYOUT_CONFIG.STATES.HIDDEN ? 
                LAYOUT_CONFIG.STATES.EXPANDED : LAYOUT_CONFIG.STATES.HIDDEN;
            this.setState(newState);
            this.toggleOverlay(newState === LAYOUT_CONFIG.STATES.EXPANDED);
        } else {
            const newState = currentState === LAYOUT_CONFIG.STATES.EXPANDED ? 
                LAYOUT_CONFIG.STATES.COLLAPSED : LAYOUT_CONFIG.STATES.EXPANDED;
            this.setState(newState);
        }

        // Save submenu states when toggling
        this.saveSubmenuStates();
    }

    setState(state) {
        // Remove all states first
        ['expanded', 'collapsed', 'hidden'].forEach(cls => {
            [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                el?.classList.remove(cls));
        });

        // Remove overlay
        this.pageWrapper?.classList.remove('overlay');

        const isMobile = window.innerWidth <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE;

        switch (state) {
            case LAYOUT_CONFIG.STATES.EXPANDED:
                if (!isMobile) {
                    [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                        el?.classList.add('expanded'));
                } else {
                    this.sidebar?.classList.add('expanded');
                    this.pageWrapper?.classList.add('overlay');
                }
                break;
                
            case LAYOUT_CONFIG.STATES.COLLAPSED:
                if (!isMobile) {
                    [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                        el?.classList.add('collapsed'));
                }
                break;
                
            case LAYOUT_CONFIG.STATES.HIDDEN:
                [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                    el?.classList.add('hidden'));
                break;
        }

        localStorage.setItem('sidebarState', state);

        // Save state with context
        LayoutState.saveState(state, isMobile);
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

    handleSubMenu(submenu) {
        if (this.isMobile() || !this.isCollapsed()) {
            // Toggle submenu
            submenu.classList.toggle('active');
        }
    }

    restoreSubmenuStates() {
        const states = LayoutState.getSubmenuStates();
        const submenus = document.querySelectorAll('.sub-menu');
        
        submenus.forEach(submenu => {
            const id = submenu.dataset.menuId;
            if (states[id]) {
                submenu.classList.add('active');
                const arrow = submenu.querySelector('.arrow');
                if (arrow) {
                    arrow.style.transform = 'rotate(180deg)';
                }
            }
        });
    }

    saveSubmenuStates() {
        const states = {};
        document.querySelectorAll('.sub-menu').forEach(submenu => {
            const id = submenu.dataset.menuId;
            states[id] = submenu.classList.contains('active');
        });
        LayoutState.saveSubmenuStates(states);
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    window.layoutManager = new LayoutManager();
});
