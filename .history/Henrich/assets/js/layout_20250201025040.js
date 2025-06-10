class LayoutManager {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.navbar = document.querySelector('.navbar');
        this.pageWrapper = document.querySelector('.page-wrapper');
        this.sidebarToggle = document.getElementById('sidebar-toggle');
        this.BREAKPOINTS = {
            MOBILE: 768,