let activityChart, distributionChart, activityTable;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    // Remove DataTable initialization from other files
    if (document.querySelector('#activityTable')) {
        initializeDataTable();
    }
    initializeCharts();
});

function initializeCharts() {
    console.log('Initializing charts');
    // Add console logs for debugging
    console.log('Chart containers:', {
        trends: document.querySelector("#monthlyTrends"),
        distribution: document.querySelector("#productDistribution")
    });

    fetch('../api/get_activity_stats.php')
        .then(response => response.json())
        .then(data => {
            console.log('Received chart data:', data);
            if (data.status === 'success') {
                renderTrendsChart(data.trends);
                renderDistributionChart(data.distribution);
                updateRecentActivities(data.recent);
            }
        })
        .catch(error => console.error('Chart data fetch error:', error));
}

function renderTrendsChart(data) {
    const options = {
        series: [{
            name: 'Total Activities',
            data: data.map(item => ({
                x: new Date(item.x).getTime(),
                y: item.y
            }))
        }],
        chart: {
            id: 'trends-chart',
            height: 350,
            type: 'area',
            animations: {
                enabled: true
            },
            toolbar: {
                show: true
            }
        },
        // ...existing chart options...
    };

    try {
        const trendsChart = new ApexCharts(document.querySelector("#monthlyTrends"), options);
        trendsChart.render();
    } catch (error) {
        console.error('Error rendering trends chart:', error);
    }
}

function renderDistributionChart(data) {
    const options = {
        series: data.map(item => item.count),
        labels: data.map(item => item.type),
        chart: {
            id: 'distribution-chart',
            type: 'donut',
            height: 350
        },
        // ...existing chart options...
    };

    try {
        const distributionChart = new ApexCharts(document.querySelector("#productDistribution"), options);
        distributionChart.render();
    } catch (error) {
        console.error('Error rendering distribution chart:', error);
    }
}

function initializeDataTable() {
    const table = $('#activityTable');
    
    // Destroy existing instance if it exists
    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy();
    }

    activityTable = table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '../api/get_activity_logs.php',
            type: 'POST',
            data: function(d) {
                return {
                    ...d,
                    startDate: $('#startDate').val(),
                    endDate: $('#endDate').val(),
                    activityType: $('#activityType').val()
                };
            }
        },
        // ...existing DataTable options...
    });

    // Setup filter handlers
    setupFilterHandlers();
}

function setupFilterHandlers() {
    // Remove any existing handlers first
    $('#startDate, #endDate, #activityType').off('change');
    
    // Add new handlers
    $('#startDate, #endDate, #activityType').on('change', function() {
        if (activityTable) {
            activityTable.ajax.reload();
        }
    });
}

function updateRecentActivities(activities) {
    const container = document.querySelector('#recentActivities');
    if (!container) return;

    const content = activities.map(activity => `
        <div class="activity-item ${activity.movement_type.toLowerCase()}">
            <div class="activity-icon">
                <i class='bx bx-${activity.movement_type === 'IN' ? 'log-in' : 'log-out'}'></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">${activity.productname}</div>
                <div class="activity-details">
                    ${activity.totalpacks} packs - ${activity.encoder}
                </div>
                <div class="activity-time">
                    ${new Date(activity.dateencoded).toLocaleString()}
                </div>
            </div>
        </div>
    `).join('');

    container.innerHTML = content;
}
