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
        STATE: 'sidebarState',
        LAST_STATE: 'lastSidebarState',
        SUBMENU_STATES: 'submenuStates'
    },
    DEFAULT_STATES: {
        DESKTOP: 'expanded',
        TABLET: 'collapsed',
        MOBILE: 'hidden'
    }
};

class LayoutState {
    static getInitialState() {
        const width = window.innerWidth;
        const savedState = localStorage.getItem(LAYOUT_CONFIG.STORAGE_KEYS.STATE);
        
        // On first load or no saved state
        if (!savedState) {
            if (width <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE) {
                return LAYOUT_CONFIG.DEFAULT_STATES.MOBILE;
            } else if (width <= LAYOUT_CONFIG.BREAKPOINTS.TABLET) {
                return LAYOUT_CONFIG.DEFAULT_STATES.TABLET;
            }
            return LAYOUT_CONFIG.DEFAULT_STATES.DESKTOP;
        }

        // Return saved state but respect breakpoints
        if (width <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE) {
            return LAYOUT_CONFIG.DEFAULT_STATES.MOBILE;
        }
        
        return savedState;
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
