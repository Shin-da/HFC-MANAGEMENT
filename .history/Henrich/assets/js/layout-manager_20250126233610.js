class LayoutManager {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.navbar = document.querySelector('.navbar');
        this.content = document.querySelector('.content-wrapper');
        this.toggleBtn = document.getElementById('sidebar-toggle');
        this.mobileBreakpoint = 768;
        
        this.init();
    }

    init() {
        this.loadSavedState();
        this.attachEventListeners();
        this.updateLayout();
    }

    loadSavedState() {
        const savedState = localStorage.getItem('sidebarState') || 'open';
        if (window.innerWidth > this.mobileBreakpoint) {
            this.setState(savedState);
        } else {
            this.setState('hidden');
        }
    }

    setState(state) {
        this.sidebar.classList.remove('close', 'hidden', 'open');
        this.sidebar.classList.add(state);
        localStorage.setItem('sidebarState', state);
        this.updateLayout();
    }

    updateLayout() {
        const isMobile = window.innerWidth <= this.mobileBreakpoint;
        const state = this.sidebar.classList.contains('hidden') ? 'hidden' :
                     this.sidebar.classList.contains('close') ? 'close' : 'open';

        // Update layout based on state
        switch (state) {
            case 'hidden':
                this.content.style.marginLeft = '0';
                this.navbar.style.left = '0';
                break;
            case 'close':
                this.content.style.marginLeft = `${var(--sidebar-mini-width)}`;
                this.navbar.style.left = `${var(--sidebar-mini-width)}`;
                break;
            default: // open
                if (!isMobile) {
                    this.content.style.marginLeft = `${var(--sidebar-width)}`;
                    this.navbar.style.left = `${var(--sidebar-width)}`;
                }
        }

        // Update toggle button
        if (this.toggleBtn) {
            const icon = this.toggleBtn.querySelector('i');
            icon.className = state === 'open' ? 'bx bx-menu' : 'bx bx-menu-alt-right';
        }
    }

    attachEventListeners() {
        // Toggle button click
        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', () => {
                const isMobile = window.innerWidth <= this.mobileBreakpoint;
                const currentState = this.sidebar.classList.contains('hidden') ? 'hidden' :
                                   this.sidebar.classList.contains('close') ? 'close' : 'open';
                
                if (isMobile) {
                    this.setState(currentState === 'hidden' ? 'open' : 'hidden');
                } else {
                    this.setState(currentState === 'open' ? 'close' : 'open');
                }
            });
        }

        // Window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.innerWidth <= this.mobileBreakpoint) {
                    this.setState('hidden');
                } else {
                    const savedState = localStorage.getItem('sidebarState') || 'open';
                    this.setState(savedState);
                }
            }, 250);
        });

        // Click outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= this.mobileBreakpoint &&
                !this.sidebar.contains(e.target) &&
                !this.toggleBtn.contains(e.target) &&
                !this.sidebar.classList.contains('hidden')) {
                this.setState('hidden');
            }
        });
    }
}
