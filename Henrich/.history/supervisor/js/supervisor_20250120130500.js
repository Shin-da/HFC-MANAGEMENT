class SupervisorUI {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.mainContent = document.querySelector('.main-content');
        this.sidebarToggle = document.getElementById('sidebar-toggle');
        
        this.initialize();
    }
    
    initialize() {
        // Initialize sidebar state
        this.handleResize();
        
        // Event listeners
        this.sidebarToggle?.addEventListener('click', () => this.toggleSidebar());
        window.addEventListener('resize', () => this.handleResize());
        
        // Handle click outside sidebar on mobile
        document.addEventListener('click', (e) => this.handleOutsideClick(e));
    }
    
    toggleSidebar() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            this.sidebar.classList.toggle('active');
        } else {
            this.sidebar.classList.toggle('collapsed');
            this.mainContent.classList.toggle('expanded');
        }
    }
    
    handleResize() {
        const width = window.innerWidth;
        
        if (width <= 768) {
            this.sidebar.classList.remove('collapsed');
            this.sidebar.classList.remove('active');
            this.mainContent.classList.remove('expanded');
        } else if (width <= 992) {
            this.sidebar.classList.add('collapsed');
            this.mainContent.classList.add('expanded');
        }
    }
    
    handleOutsideClick(e) {
        if (window.innerWidth <= 768) {
            const clickedOutsideSidebar = !this.sidebar.contains(e.target);
            const clickedOutsideToggle = !this.sidebarToggle?.contains(e.target);
            
            if (clickedOutsideSidebar && clickedOutsideToggle && this.sidebar.classList.contains('active')) {
                this.toggleSidebar();
            }
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    new SupervisorUI();
});
