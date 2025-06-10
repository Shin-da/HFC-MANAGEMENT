class DashboardManager {
    constructor() {
        this.initialize();
    }

    async initialize() {
        try {
            await this.loadDashboardData();
        } catch (error) {
            console.error('Failed to initialize dashboard:', error);
            this.showError('Failed to load dashboard data');
        }
    }

    async loadDashboardData() {
        try {
            const response = await fetch(CONFIG.PATHS.dashboard);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            if (!data.success) throw new Error(data.error || 'Failed to load dashboard data');
            
            this.updateDashboard(data);
let dashboardCharts = {};

function initializeCharts(data) {
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    };

    // Sales Chart
    const salesCtx = document.getElementById('salesChart')?.getContext('2d');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: data.salesData.map(item => formatDate(item.date)),
                datasets: [{
                    label: 'Daily Sales',
                    data: data.salesData.map(item => item.count),
                    borderColor: '#4CAF50',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(76, 175, 80, 0.1)'
                }]
            },
            options: chartOptions
        });
    }

    // Activity Chart
    const activityCtx = document.getElementById('activityChart')?.getContext('2d');
    if (activityCtx) {
        new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: data.activityData.map(item => formatDate(item.date)),
                datasets: [{
                    label: 'Daily Activities',
                    data: data.activityData.map(item => item.count),
                    backgroundColor: '#2196F3',
                    borderColor: '#1976D2',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });
    }
}

function initializeCounters(stats) {
    Object.entries(stats).forEach(([key, value]) => {
        const element = document.querySelector(`#${key}Count`);
        if (element) {
            animateCounter(element, 0, value);
        }
    });
}

function animateCounter(element, start, end) {
    let current = start;
    const increment = end / 30;
    const duration = 1000;
    const stepTime = duration / 30;

    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            element.textContent = end;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, stepTime);
}

function setupRefreshButton() {
    const refreshBtn = document.querySelector('.btn-refresh');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', async () => {
            refreshBtn.classList.add('rotating');
            await refreshStats();
            setTimeout(() => refreshBtn.classList.remove('rotating'), 1000);
        });
    }
}

async function refreshStats() {
    try {
        const response = await fetch('get-dashboard-stats.php');
        const data = await response.json();
        updateDashboardStats(data);
    } catch (error) {
        showNotification('Error refreshing stats', 'error');
    }
}

function updateDashboardStats(data) {
    const supervisorCount = document.getElementById('supervisorCount');
    const requestCount = document.getElementById('requestCount');
    
    if (supervisorCount) {
        animateCounter(supervisorCount, 
            parseInt(supervisorCount.textContent), 
            data.supervisor_count);
    }
    
    if (requestCount) {
        animateCounter(requestCount, 
            parseInt(requestCount.textContent), 
            data.pending_requests);
    }
}

function initializeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.addEventListener('click', () => alert.remove());
    });
}

// Chart configurations
const chartColors = {
    primary: '#2196F3',
    secondary: '#607D8B',
    success: '#4CAF50',
    warning: '#FFC107',
    danger: '#F44336'
};

function initSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    window.salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.sales.map(item => formatDate(item.date)),
            datasets: [{
                label: 'Sales',
                data: chartData.sales.map(item => item.total),
                borderColor: '#4CAF50',
                tension: 0.4
            }]
        },
        options: getChartOptions()
    });
}

function initActivityChart() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    window.activityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.activity.map(item => formatDate(item.date)),
            datasets: [{
                label: 'Activities',
                data: chartData.activity.map(item => item.total),
                backgroundColor: '#2196F3'
            }]
        },
        options: getChartOptions()
    });
}

// Event Listeners
function setupEventListeners() {
    // Theme toggle
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }

    // Notifications
    setupNotifications();

    // Chart period selectors
    document.querySelectorAll('.chart-select').forEach(select => {
        select.addEventListener('change', function() {
            const period = this.value;
            const chartType = this.id.replace('Period', '');
            updateChartData(chartType, period);
        });
    });

    // Status toggles
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            updateUserStatus(this.dataset.userId, this.checked ? 'active' : 'inactive');
        });
    });
}

// Theme Management
function toggleTheme() {
    document.body.classList.toggle('dark-theme');
    localStorage.setItem('admin-theme', 
        document.body.classList.contains('dark-theme') ? 'dark' : 'light'
    );
}

// Load saved theme
const savedTheme = localStorage.getItem('admin-theme');
if (savedTheme) {
    document.body.classList.toggle('dark-theme', savedTheme === 'dark');
}

function getChartOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric' 
    });
}

function setupChartControls() {
    document.getElementById('salesPeriod').addEventListener('change', function(e) {
        updateChartData('sales', e.target.value);
    });

    document.getElementById('activityPeriod').addEventListener('change', function(e) {
        updateChartData('activity', e.target.value);
    });
}

async function updateChartData(chartType, days) {
    try {
        const response = await fetch(`/admin/api/get-chart-data.php?type=${chartType}&days=${days}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message);
        }

        const chart = chartType === 'sales' ? 
            Chart.getChart('salesChart') : 
            Chart.getChart('activityChart');

        if (chart) {
            chart.data.labels = data.data.map(item => formatDate(item.date));
            chart.data.datasets[0].data = data.data.map(item => item.total);
            chart.update();
        }

    } catch (error) {
        console.error(`Error updating ${chartType} chart:`, error);
        AdminCore.showNotification(`Failed to update ${chartType} chart`, 'error');
    }
}

// Add event listeners for period selectors
document.addEventListener('DOMContentLoaded', function() {
    const salesPeriod = document.getElementById('salesPeriod');
    const activityPeriod = document.getElementById('activityPeriod');

    if (salesPeriod) {
        salesPeriod.addEventListener('change', (e) => {
            updateChartData('sales', e.target.value);
        });
    }

    if (activityPeriod) {
        activityPeriod.addEventListener('change', (e) => {
            updateChartData('activity', e.target.value);
        });
    }
});

// Add refresh functionality
function refreshDashboard() {
    location.reload();
}

function updateUserStatus(userId, status) {
    const formData = new FormData();
    formData.append('action', 'update_user_status');
    formData.append('user_id', userId);
    formData.append('status', status);

    fetch('/admin/api/admin-actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Status updated successfully', 'success');
        } else {
            showNotification('Error updating status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating status', 'error');
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
