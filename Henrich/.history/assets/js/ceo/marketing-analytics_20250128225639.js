class MarketingAnalytics {
    constructor() {
        this.charts = {};
        this.currentFilter = 'all';
        this.init();
    }

    async init() {
        await this.loadMarketingData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadMarketingData() {
        try {
            const response = await fetch(`/api/ceo/metrics/marketing.php?type=${this.currentFilter}`);
            if (!response.ok) throw new Error('Failed to fetch marketing data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading marketing data');
            console.error(error);
        }
    }

    updateDashboard(data) {
        const { overview } = data;
        document.getElementById('activeCampaigns').textContent = 
            overview.active_campaigns;
        document.getElementById('totalRoi').textContent = 
            formatPercentage(overview.avg_roi);
        document.getElementById('conversionRate').textContent = 
            formatPercentage(overview.conversion_rate);

        this.updateCharts(data);
    }

    initializeCharts() {
        this.initRoiChart();
        this.initChannelChart();
        this.initBudgetBreakdown();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new MarketingAnalytics();
});
