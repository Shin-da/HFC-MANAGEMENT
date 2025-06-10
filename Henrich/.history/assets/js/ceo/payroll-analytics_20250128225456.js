class PayrollAnalytics {
    constructor() {
        this.charts = {};
        this.currentPeriod = 'current';
        this.init();
    }

    async init() {
        await this.loadPayrollData();
        this.initializeCharts();