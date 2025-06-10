class FinancialDashboard {
    constructor() {
        this.charts = {};
        this.currentPeriod = 'monthly';
        this.init();
    }

    async init() {
        await this.loadData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadData() {
        try {
            const response = await fetch(`/api/ceo/metrics/financial.php?period=${this.currentPeriod}`);
            if (!response.ok) throw new Error('Failed to fetch financial data');
            return await response.json();
        } catch (error) {
            console.error('Error:', error);
            showErrorToast('Failed to load financial data');
        }
    }

    initializeCharts() {
        this.initRevenueChart();
        this.initExpenseChart();
        this.initProfitChart();
    }

    initRevenueChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        this.charts.revenue = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue',
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    async updateCharts() {
        const data = await this.loadData();
        if (!data) return;

        this.updateRevenueChart(data.trends);
        this.updateMetrics(data.summary);
    }

    setupEventListeners() {
        document.getElementById('periodSelect')?.addEventListener('change', (e) => {
            this.currentPeriod = e.target.value;
            this.updateCharts();
        });
    }
}

// Initialize dashboard when document is ready
document.addEventListener('DOMContentLoaded', () => {
    new FinancialDashboard();
});
