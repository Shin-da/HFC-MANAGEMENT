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