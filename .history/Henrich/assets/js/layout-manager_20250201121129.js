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
        } else {
            const newState = currentState === LAYOUT_CONFIG.STATES.EXPANDED ? 
                LAYOUT_CONFIG.STATES.COLLAPSED : LAYOUT_CONFIG.STATES.EXPANDED;
            this.setState(newState);
        }
        
        // Update button icon
        const toggleIcon = this.sidebarToggle?.querySelector('i');
        if (toggleIcon) {
            const isCollapsed = [LAYOUT_CONFIG.STATES.COLLAPSED, LAYOUT_CONFIG.STATES.HIDDEN]
                .includes(this.getCurrentState());
            toggleIcon.className = isCollapsed ? 'bx bx-menu-alt-right' : 'bx bx-menu';
        }

        // Save submenu states when toggling
        this.saveSubmenuStates();
    }

    setState(state, saveState = true) {
        // Remove all states first
        Object.values(LAYOUT_CONFIG.STATES).forEach(s => {
            [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                el?.classList.remove(s));
        });

        // Remove overlay
        this.pageWrapper?.classList.remove('overlay');

        const isMobile = window.innerWidth <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE;

        // Apply new state
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
                [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                    el?.classList.add('collapsed'));
                break;
            
            case LAYOUT_CONFIG.STATES.HIDDEN:
                [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                    el?.classList.add('hidden'));
                break;
        }

        // Update dimensions
        this.updateDimensions(state);

        // Save state if needed
        if (saveState) {
            localStorage.setItem(LAYOUT_CONFIG.STORAGE_KEYS.STATE, state);
            localStorage.setItem(LAYOUT_CONFIG.STORAGE_KEYS.LAST_STATE, state);
        }
    }

    updateDimensions(state) {
        switch(state) {
            case LAYOUT_CONFIG.STATES.EXPANDED:
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
