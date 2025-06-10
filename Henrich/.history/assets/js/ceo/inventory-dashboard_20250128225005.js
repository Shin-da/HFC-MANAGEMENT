class InventoryDashboard {
    constructor() {
        this.period = 'monthly';
        this.charts = {};
        this.init();
    }

    async init() {
        await this.loadInventoryData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadInventoryData() {
        try {
            const response = await fetch(`/api/ceo/metrics/inventory.php?period=${this.period}`);
            const data = await response.json();
            this.updateMetrics(data);
            return data;
        } catch (error) {
            showErrorToast('Failed to load inventory data');
        }
    }

    initializeCharts() {
        this.initStockTrendsChart();
        this.initCategoryChart();
        this.loadInventoryAlerts();
    }

    setupEventListeners() {
        document.getElementById('periodFilter').addEventListener('change', (e) => {
            this.period = e.target.value;
            this.loadInventoryData();
        });

        document.getElementById('exportInventory').addEventListener('click', () => {
            this.exportInventoryReport();
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new InventoryDashboard();
});
