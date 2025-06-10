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
        // Clear all states
        ['expanded', 'collapsed', 'hidden'].forEach(cls => {
            this.sidebar?.classList.remove(cls);
            this.navbar?.classList.remove(cls);
            this.pageWrapper?.classList.remove(cls);
            this.content?.classList.remove(cls);
        });

        // Remove overlay
        this.pageWrapper?.classList.remove('overlay');

        // Apply new state
        const isMobile = window.innerWidth <= this.BREAKPOINTS.MOBILE;

        switch (state) {
            case 'expanded':
                [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                    el?.classList.add('expanded'));
                if (isMobile) {
                    this.pageWrapper?.classList.add('overlay');
                }
                break;
                
            case 'collapsed':
                if (!isMobile) {
                    [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                        el?.classList.add('collapsed'));
                }
                break;
                
            case 'hidden':
                [this.sidebar, this.navbar, this.pageWrapper, this.content].forEach(el => 
                    el?.classList.add('hidden'));
                break;
        }

        localStorage.setItem('sidebarState', state);
    }

    toggleSidebar() {
        const width = window.innerWidth;
        const currentState = this.getCurrentState();

        if (width <= this.BREAKPOINTS.MOBILE) {
            this.setState(currentState === 'hidden' ? 'expanded' : 'hidden');
            this.pageWrapper.classList.toggle('overlay');
        } else {
            this.setState(currentState === 'expanded' ? 'collapsed' : 'expanded');
        }
    }

    handleResize() {
        const width = window.innerWidth;
        if (width <= this.BREAKPOINTS.MOBILE) {
            this.setState('hidden');
        } else if (width <= this.BREAKPOINTS.TABLET) {
            this.setState('collapsed');
        }
    }

    handleClickOutside(e) {
        if (window.innerWidth <= this.BREAKPOINTS.MOBILE) {
            if (!this.sidebar?.contains(e.target) && 
                !this.sidebarToggle?.contains(e.target) && 
                this.getCurrentState() !== 'hidden') {
                this.setState('hidden');
                this.pageWrapper.classList.remove('overlay');
            }
        }
    }

    getCurrentState() {
        if (this.sidebar?.classList.contains('expanded')) return 'expanded';
        if (this.sidebar?.classList.contains('collapsed')) return 'collapsed';
        return 'hidden';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.layoutManager = new LayoutManager();
});
