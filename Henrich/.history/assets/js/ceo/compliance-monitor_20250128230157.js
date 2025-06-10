class ComplianceMonitor {
    constructor() {
        this.charts = {};
        this.selectedRiskLevel = 'all';
        this.init();
    }

    async init() {
        await this.loadComplianceData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadComplianceData() {
        try {
            const response = await fetch(`/api/ceo/metrics/compliance.php?risk=${this.selectedRiskLevel}`);
            if (!response.ok) throw new Error('Failed to fetch compliance data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading compliance data');
            console.error(error);
        }
    }

    updateDashboard(data) {
        const { overview } = data;
        document.getElementById('complianceScore').textContent = 
            formatPercentage(overview.compliance_score);
        document.getElementById('openIssues').textContent = 
            overview.open_issues;
        document.getElementById('riskLevel').textContent = 
            overview.current_risk_level;

        this.updateRiskMatrix(data.risks);
        this.updateAuditTimeline(data.audits);
        this.updateRegulationsList(data.regulations);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ComplianceMonitor();
});
