class DashboardAnalytics {
    constructor() {
        this.charts = {};
        this.dateRange = {
            start: moment().subtract(30, 'days').format('YYYY-MM-DD'),
            end: moment().format('YYYY-MM-DD')
        };
        this.init();
    }

    async init() {
        this.setupDateRangePicker();
        await this.loadAnalytics();
        this.initializeCharts();
    }

    async loadAnalytics() {
        try {
            const params = new URLSearchParams({
                start: this.dateRange.start,
                end: this.dateRange.end
            });

            const response = await fetch(`/api/ceo/analytics/dashboard.php?${params}`);
            if (!response.ok) throw new Error('Failed to fetch analytics');
            
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            console.error('Analytics Error:', error);
            showErrorToast('Failed to load analytics data');
        }
    }

    updateDashboard(data) {
        // Update metrics
        this.updateMetrics(data.metrics);
        // Update charts
        this.updateCharts(data);
        // Update projections
        this.updateProjections(data.projections);
    }

    initializeCharts() {
        // Initialize chart configurations
        this.initRevenueChart();
        this.initOrdersChart();
        this.initProductsChart();
    }
}

// Initialize analytics when document is ready
document.addEventListener('DOMContentLoaded', () => {
    new DashboardAnalytics();
});
