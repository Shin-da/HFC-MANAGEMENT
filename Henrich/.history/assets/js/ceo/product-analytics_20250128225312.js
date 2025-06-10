class ProductAnalytics {
    constructor() {
        this.charts = {};
        this.filters = {
            timeRange: 30,
            category: ''
        };
        this.init();
    }

    async init() {
        this.setupEventListeners();
        await this.loadCategories();
        await this.loadProductData();
        this.initializeCharts();
    }

    setupEventListeners() {
        document.getElementById('timeRange').addEventListener('change', (e) => {
            this.filters.timeRange = e.target.value;
            this.refreshData();
        });

        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            this.filters.category = e.target.value;
            this.refreshData();
        });