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
    }
};

class LayoutState {
    static getInitialState() {
        const width = window.innerWidth;
        const savedState = localStorage.getItem('sidebarState');
        
        if (width <= LAYOUT_CONFIG.BREAKPOINTS.MOBILE) {
            return LAYOUT_CONFIG.STATES.HIDDEN;
        } else if (width <= LAYOUT_CONFIG.BREAKPOINTS.TABLET) {
            return LAYOUT_CONFIG.STATES.COLLAPSED;
        }
        return savedState || LAYOUT_CONFIG.STATES.EXPANDED;
    }
}
