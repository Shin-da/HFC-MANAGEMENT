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