class CEODashboard {
    constructor() {
        this.charts = {};
        this.init();
    }

    async init() {
        await this.loadDashboardData();
        this.initializeCharts();
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    async loadDashboardData() {
        try {
            const response = await fetch('/api/ceo/metrics/dashboard.php');
            if (!response.ok) throw new Error('Failed to fetch dashboard data');
            const data = await response.json();
            this.updateMetrics(data);
            return data;
        } catch (error) {
            showErrorToast('Failed to load dashboard data');
        }
    }

    updateMetrics(data) {
        const { sales, inventory } = data;
        
        document.getElementById('totalRevenue').textContent = 
            formatCurrency(sales.total_revenue);
        document.getElementById('totalOrders').textContent = 
            formatNumber(sales.total_orders);
        document.getElementById('inventoryValue').textContent = 
            formatCurrency(inventory.inventory_value);
    }

    initializeCharts(data) {
        this.initSalesChart(data.trends);
        this.initInventoryChart(data.inventory);
    }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
    new DashboardCharts();
});
