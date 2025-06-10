class ReportGenerator {
    constructor() {
        this.currentReportType = 'consolidated';
        this.init();
    }

    async init() {
        this.setupEventListeners();
        await this.loadRecentReports();
        await this.loadScheduledReports();
    }

    setupEventListeners() {
        document.getElementById('reportType').addEventListener('change', (e) => {
            this.currentReportType = e.target.value;
            this.updatePreview();
        });

        document.getElementById('generateReport').addEventListener('click', () => 
            this.generateReport());

        document.getElementById('scheduleReport').addEventListener('click', () => 
            this.showScheduleModal());

        document.getElementById('scheduleForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.scheduleReport(new FormData(e.target));
        });
    }

    async generateReport() {
        try {
            const loadingToast = showLoadingToast('Generating report...');
            
            const response = await fetch('/api/ceo/reports/generate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: this.currentReportType })
            });

            if (!response.ok) throw new Error('Failed to generate report');
            
            const data = await response.json();
            hideLoadingToast(loadingToast);
            
            if (data.success) {
                showSuccessToast('Report generated successfully');
                window.open(data.downloadUrl, '_blank');
                await this.loadRecentReports();
            }
        } catch (error) {
            showErrorToast('Error generating report');
            console.error(error);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ReportGenerator();
});
