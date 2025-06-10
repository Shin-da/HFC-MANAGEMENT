document.addEventListener('DOMContentLoaded', function() {
    initializeActivityLog();
    setupCharts();
    setupExport();
});

function initializeActivityLog() {
    setupFilters();
    setupTableSorting();
    setupThemeHandling();
}

function setupCharts() {
    setupMonthlyTrendsChart();
    setupDistributionChart();
    loadRecentActivities();
}

function setupMonthlyTrendsChart() {
    const ctx = document.getElementById('monthlyTrends');
    if (!ctx) return;

    fetch('../api/get_monthly_trends.php')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Stock Activities',
                        data: data.values,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Activity Trends'
                        }
                    }
                }
            });
        });
}

function setupDistributionChart() {
    const ctx = document.getElementById('productDistribution');
    if (!ctx) return;

    fetch('../api/get_activity_distribution.php')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        });
}

function loadRecentActivities() {
    const activityList = document.querySelector('.activity-list');
    if (!activityList) return;

    fetch('../api/get_recent_activities.php')
        .then(response => response.json())
        .then(activities => {
            activityList.innerHTML = activities.map(activity => `
                <div class="activity-item">
                    <div class="activity-icon ${activity.type.toLowerCase()}">
                        <i class='bx ${getActivityIcon(activity.type)}'></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-text">${activity.description}</div>
                        <div class="activity-time">${formatTimeAgo(activity.timestamp)}</div>
                    </div>
                </div>
            `).join('');
        });
}

function getActivityIcon(type) {
    const icons = {
        'STOCK_IN': 'bx-package',
        'STOCK_OUT': 'bx-exit',
        'ADJUSTMENT': 'bx-revision',
        'TRANSFER': 'bx-transfer'
    };
    return icons[type] || 'bx-activity';
}

function formatTimeAgo(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    let interval = Math.floor(seconds / 31536000);
    if (interval > 1) return interval + ' years ago';
    
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) return interval + ' months ago';
    
    interval = Math.floor(seconds / 86400);
    if (interval > 1) return interval + ' days ago';
    
    interval = Math.floor(seconds / 3600);
    if (interval > 1) return interval + ' hours ago';
    
    interval = Math.floor(seconds / 60);
    if (interval > 1) return interval + ' minutes ago';
    
    return Math.floor(seconds) + ' seconds ago';
}

// Add more functions as needed
