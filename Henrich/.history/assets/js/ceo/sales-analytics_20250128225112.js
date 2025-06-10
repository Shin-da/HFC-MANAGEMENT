class SalesAnalytics {
    constructor() {
        this.charts = {};
        this.dateRange = {
            start: null,
            end: null
        };
        this.init();
    }

    async init() {
        this.setupDatePickers();
        await this.loadSalesData();
        this.initializeCharts();
        this.loadTopProducts();
    }

    setupDatePickers() {
        const today = new Date();
        const thirtyDaysAgo = new Date(today);
        thirtyDaysAgo.setDate(today.getDate() - 30);

        document.getElementById('startDate').valueAsDate = thirtyDaysAgo;
        document.getElementById('endDate').valueAsDate = today;
        
        document.getElementById('applyDateRange').addEventListener('click', () => {
            this.updateDateRange();
        });
    }

    async loadSalesData() {
        try {
            const response = await fetch('/api/ceo/metrics/sales.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.dateRange)
            });
            
            if (!response.ok) throw new Error('Failed to fetch sales data');
            const data = await response.json();
            this.updateMetrics(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading sales data');
            console.error(error);
        }
    }

    updateMetrics(data) {
        document.getElementById('totalSales').textContent = 
            formatCurrency(data.summary.totalSales);
        document.getElementById('avgOrderValue').textContent = 
            formatCurrency(data.summary.averageOrder);
        document.getElementById('conversionRate').textContent = 
            data.summary.conversionRate + '%';
    }
}

// Initialize analytics when document is ready
document.addEventListener('DOMContentLoaded', () => {
    new SalesAnalytics();
});
