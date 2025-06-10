class HRAnalytics {
    constructor() {
        this.charts = {};
        this.currentPeriod = 'month';
        this.init();
    }

    async init() {
        await this.loadHRData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadHRData() {
        try {
            const response = await fetch(`/api/ceo/metrics/hr.php?period=${this.currentPeriod}`);
            if (!response.ok) throw new Error('Failed to fetch HR data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading HR data');
            console.error(error);
        }
    }

    updateDashboard(data) {
        const { workforce } = data;
        document.getElementById('totalEmployees').textContent = 
            workforce.total_employees;
        document.getElementById('turnoverRate').textContent = 
            formatPercentage(workforce.turnover_rate);
        document.getElementById('satisfaction').textContent = 
            formatPercentage(workforce.satisfaction);

        this.updateCharts(data);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new HRAnalytics();
});
