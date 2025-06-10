class InventoryDashboard {
    constructor() {
        this.period = 'monthly';
        this.charts = {};
        this.init();
    }

    async init() {
        await this.loadInventoryData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadInventoryData() {
        try {