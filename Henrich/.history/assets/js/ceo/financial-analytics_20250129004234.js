class FinancialAnalytics {
    constructor() {
        this.charts = {};
        this.dateRange = {
            start: moment().subtract(30, 'days').format('YYYY-MM-DD'),
            end: moment().format('YYYY-MM-DD')
        };
        this.init();
    }

    async init() {
        this.initializeDatePickers();
        await this.loadFinancialData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadFinancialData() {
        try {
            const response = await fetch(`/api/ceo/metrics/financial.php?start=${this.dateRange.start}&end=${this.dateRange.end}`);
            if (!response.ok) throw new Error('Failed to fetch financial data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Failed to load financial data');
            console.error(error);
        }
    }

    initializeCharts() {
        this.initCashFlowChart();
        this.initRevenueStreamChart();
        this.initExpenseChart();
    }

    initCashFlowChart() {
        const ctx = document.getElementById('cashFlowChart').getContext('2d');
        this.charts.cashFlow = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Cash In',
                    borderColor: 'rgb(75, 192, 192)',
                    data: []
                }, {
                    label: 'Cash Out',
                    borderColor: 'rgb(255, 99, 132)',
                    data: []
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => `₱${value.toLocaleString()}`
                        }
                    }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FinancialAnalytics();
});
