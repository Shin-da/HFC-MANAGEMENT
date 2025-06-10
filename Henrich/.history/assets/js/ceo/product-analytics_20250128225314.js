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

        document.getElementById('exportProductReport').addEventListener('click', () => {
            this.exportReport();
        });
    }

    async refreshData() {
        await this.loadProductData();
        this.updateCharts();
        this.updateTopProducts();
        this.updateStockLevels();
    }

    async loadProductData() {
        try {
            const queryParams = new URLSearchParams({
                days: this.filters.timeRange,
                category: this.filters.category
            });
            
            const response = await fetch(`/api/ceo/metrics/products.php?${queryParams}`);
            if (!response.ok) throw new Error('Failed to fetch product data');
            return await response.json();
        } catch (error) {
            showErrorToast('Error loading product data');
            console.error(error);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ProductAnalytics();
});
