class PayrollAnalytics {
    constructor() {
        this.charts = {};
        this.currentPeriod = 'current';
        this.init();
    }

    async init() {
        await this.loadPayrollData();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadPayrollData() {
        try {
            const response = await fetch(`/api/ceo/metrics/payroll.php?period=${this.currentPeriod}`);
            if (!response.ok) throw new Error('Failed to fetch payroll data');
            const data = await response.json();
            this.updateDashboard(data);
            return data;
        } catch (error) {
            showErrorToast('Error loading payroll data');
            console.error(error);
        }
    }

    updateDashboard(data) {
        document.getElementById('totalPayroll').textContent = 
            formatCurrency(data.summary.total_payroll);
        document.getElementById('activeEmployees').textContent = 
            data.summary.employee_count;
        document.getElementById('avgSalary').textContent = 
            formatCurrency(data.summary.average_salary);
            
        this.updateCharts(data);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new PayrollAnalytics();
});
