class SupplyChainAnalytics {
    constructor() {
        this.charts = {};
        this.selectedTimeframe = 'monthly';
        this.init();
    }

    async init() {
        await this.loadSupplyChainData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadSupplyChainData() {
        try {
            const response = await fetch(`/api/ceo/metrics/supply-chain.php?timeframe=${this.selectedTimeframe}`);
            if (!response.ok) throw new Error('Failed to fetch supply chain data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading supply chain data');
            console.error(error);
        }
    }

    initializeCharts() {
        this.initSupplierPerformanceChart();
        this.initInventoryAnalytics();
        this.initLogisticsMetrics();
    }

    updateDashboard(data) {
        const { overview } = data;
        document.getElementById('otdRate').textContent = 
            formatPercentage(overview.otd_rate);
        document.getElementById('turnoverRate').textContent = 
            formatNumber(overview.turnover_rate, 2);
        document.getElementById('fulfillmentRate').textContent = 
            formatPercentage(overview.fulfillment_rate);

        this.updateCharts(data);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new SupplyChainAnalytics();
});
