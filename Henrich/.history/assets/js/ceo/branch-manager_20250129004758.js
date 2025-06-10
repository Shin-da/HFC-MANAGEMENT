class BranchManager {
    constructor() {
        this.charts = {};
        this.map = null;
        this.dataTable = null;
        this.compareInterval = 30;
        this.init();
    }

    async init() {
        await this.loadBranchData();
        this.initializeCharts();
        this.initializeMap();
        this.initializeTable();
        this.setupEventListeners();
    }

    async loadBranchData() {
        try {
            const response = await fetch(`/api/ceo/branches/performance.php?days=${this.compareInterval}`);
            if (!response.ok) throw new Error('Failed to fetch branch data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Failed to load branch data');
            console.error(error);
        }
    }

    initializeCharts() {
        const ctx = document.getElementById('branchComparisonChart').getContext('2d');
        this.charts.comparison = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue',
                    data: [],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',