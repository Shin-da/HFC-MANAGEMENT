class BusinessIntelligence {
    constructor() {
        this.charts = {};
        this.selectedRange = '1y';
        this.init();
    }

    async init() {
        await this.loadIntelligenceData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadIntelligenceData() {
        try {
            const response = await fetch(`/api/ceo/metrics/intelligence.php?range=${this.selectedRange}`);
            if (!response.ok) throw new Error('Failed to fetch BI data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading business intelligence data');
            console.error(error);
        }
    }

    initializeCharts() {
        this.initTrendAnalysisChart();
        this.initPredictionMatrix();
        this.initPatternChart();
        this.setupRecommendations();
    }

    initTrendAnalysisChart() {
        const ctx = document.getElementById('trendAnalysisChart').getContext('2d');
        this.charts.trends = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Market Trends',
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
}

document.addEventListener('DOMContentLoaded', () => {
    new BusinessIntelligence();
});
