class KPIMonitor {
    constructor() {
        this.charts = {};
        this.updateInterval = 300000; // 5 minutes
        this.init();
    }

    async init() {
        await this.loadKPIData();
        this.initializeCharts();
        this.startAutoRefresh();
    }

    async loadKPIData() {
        try {
            const response = await fetch('/api/ceo/metrics/kpi.php');
            if (!response.ok) throw new Error('Failed to fetch KPI data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            console.error('KPI Error:', error);
            showErrorToast('Failed to load KPI data');
        }
    }

    initializeCharts() {
        this.initKPIChart();
        this.initTrendChart();
    }

    startAutoRefresh() {
        setInterval(() => this.loadKPIData(), this.updateInterval);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new KPIMonitor();
});
