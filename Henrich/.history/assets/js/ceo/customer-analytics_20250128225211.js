class CustomerAnalytics {
    constructor() {
        this.charts = {};
        this.selectedRange = 30;
        this.init();
    }

    async init() {
        this.setupEventListeners();
        await this.loadCustomerData();
        this.initializeCharts();
    }

    async loadCustomerData() {
        try {
            const response = await fetch(`/api/ceo/metrics/customers.php?days=${this.selectedRange}`);
            if (!response.ok) throw new Error('Failed to fetch customer data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading customer data');
            console.error(error);
        }
    }

    initializeCharts() {
        this.initSegmentationChart();
        this.initLoyaltyChart();
        this.initRegionalMap();
    }

    updateDashboard(data) {
        // Update metrics
        document.getElementById('totalCustomers').textContent = 
            formatNumber(data.overview.total_customers);
        document.getElementById('avgCustomerValue').textContent = 
            formatCurrency(data.overview.avg_value);
        document.getElementById('retentionRate').textContent = 
            formatPercentage(data.overview.retention_rate);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CustomerAnalytics();
});
