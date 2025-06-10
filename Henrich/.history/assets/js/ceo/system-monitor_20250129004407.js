class SystemMonitor {
    constructor() {
        this.charts = {};
        this.isMonitoring = true;
        this.refreshInterval = 15000;
        this.init();
    }

    async init() {
        this.initializeCharts();
        this.setupEventListeners();
        await this.startMonitoring();
    }

    setupEventListeners() {
        document.getElementById('refreshInterval').addEventListener('change', (e) => {
            this.refreshInterval = parseInt(e.target.value);
            this.restartMonitoring();
        });

        document.getElementById('pauseMonitoring').addEventListener('click', () => {
            this.toggleMonitoring();
        });
    }

    async startMonitoring() {
        while (this.isMonitoring) {
            await this.updateStatus();
            await new Promise(resolve => setTimeout(resolve, this.refreshInterval));
        }
    }

    async updateStatus() {
        try {
            const response = await fetch('/api/ceo/monitor/status.php');
            if (!response.ok) throw new Error('Failed to fetch status');
            const data = await response.json();
            this.updateDashboard(data);
        } catch (error) {
            console.error('Monitoring error:', error);
            showErrorToast('Failed to update system status');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new SystemMonitor();
});
