class QualityControl {
    constructor() {
        this.charts = {};
        this.currentPeriod = 'monthly';
        this.init();
    }

    async init() {
        await this.loadQualityData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadQualityData() {
        try {
            const response = await fetch(`/api/ceo/metrics/quality.php?period=${this.currentPeriod}`);
            if (!response.ok) throw new Error('Failed to fetch quality data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading quality data');
            console.error(error);
        }
    }

    updateDashboard(data) {
        const { metrics } = data;
        document.getElementById('qualityScore').textContent = 
            formatPercentage(metrics.quality_score);
        document.getElementById('defectRate').textContent = 
            formatPercentage(metrics.defect_rate);
        document.getElementById('complaintCount').textContent = 
            metrics.complaint_count;

        this.updateTimeline(data.incidents);
        this.updateCharts(data.trends);
        this.updateActionsList(data.actions);
    }

    initializeCharts() {
        this.initQualityTrendsChart();
        this.setupIncidentsTimeline();
        this.setupActionsList();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new QualityControl();
});
