const LAYOUT_CONFIG = {
    BREAKPOINTS: {
        MOBILE: 768,
        TABLET: 991,
        DESKTOP: 1200
    },
    DIMENSIONS: {
        SIDEBAR_EXPANDED: 260,
        SIDEBAR_COLLAPSED: 70,
        NAVBAR_HEIGHT: 64
    },
    STATES: {
        EXPANDED: 'expanded',
        COLLAPSED: 'collapsed',
        HIDDEN: 'hidden'
    },
    STORAGE_KEYS: {
        STATE: 'layoutState',
        SUBMENU_STATES: 'submenuStates',
        LAST_DESKTOP_STATE: 'lastDesktopState',
        LAST_MOBILE_STATE: 'lastMobileState'
    }
};

class LayoutState {
    static getInitialState() {
        const width = window.innerWidth;
        const isMobile = width <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE;
        
        // Get appropriate saved state
        const lastDesktopState = localStorage.getItem(LAYOUT_CONFIG.STORAGE_KEYS.LAST_DESKTOP_STATE);
        const lastMobileState = localStorage.getItem(LAYOUT_CONFIG.STORAGE_KEYS.LAST_MOBILE_STATE);
        const currentState = localStorage.getItem(LAYOUT_CONFIG.STORAGE_KEYS.STATE);
        
        if (isMobile) {
            return lastMobileState || LAYOUT_CONFIG.STATES.HIDDEN;
        } else if (width <= LAYOUT_CONFIG.BREAKPOINTS.TABLET) {
            return LAYOUT_CONFIG.STATES.COLLAPSED;
        }
        return lastDesktopState || currentState || LAYOUT_CONFIG.STATES.EXPANDED;
    }

    static saveState(state, isMobile = false) {
        localStorage.setItem(LAYOUT_CONFIG.STORAGE_KEYS.STATE, state);
        if (isMobile) {
            localStorage.setItem(LAYOUT_CONFIG.STORAGE_KEYS.LAST_MOBILE_STATE, state);
        } else {
            localStorage.setItem(LAYOUT_CONFIG.STORAGE_KEYS.LAST_DESKTOP_STATE, state);
        }
    }

    static saveSubmenuStates(states) {
        localStorage.setItem(LAYOUT_CONFIG.STORAGE_KEYS.SUBMENU_STATES, JSON.stringify(states));
    }

    static getSubmenuStates() {
        const saved = localStorage.getItem(LAYOUT_CONFIG.STORAGE_KEYS.SUBMENU_STATES);
        return saved ? JSON.parse(saved) : {};
    }
}
