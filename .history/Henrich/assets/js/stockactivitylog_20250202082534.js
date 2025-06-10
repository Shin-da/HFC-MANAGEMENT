let activityChart, distributionChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupActivityTable();
    setupEventListeners();
});

function initializeCharts() {
    fetch('../api/get_activity_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                setupTrendsChart(data.trends);
                setupDistributionChart(data.distribution);
                updateRecentActivities(data.recent);
            }
        })
        .catch(error => console.error('Failed to load charts:', error));
}

function setupTrendsChart(data) {
    const options = {
        series: [{
            name: 'Activities',
            data: data
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: true
            },
            animations: {
                enabled: true
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            type: 'datetime'
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return Math.round(val);
                }
            }
        },
        tooltip: {
            shared: true,
            y: {
                formatter: function(val) {
                    return Math.round(val) + ' activities';
                }
            }
        }
    };

    activityChart = new ApexCharts(document.querySelector("#monthlyTrends"), options);
    activityChart.render();
}

function setupDistributionChart(data) {
    const options = {
        series: data.map(item => item.count),
        labels: data.map(item => item.type),
        chart: {
            type: 'donut',
            height: 350
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%'
                }
            }
        },
        legend: {
            position: 'bottom'
        }
    };

    distributionChart = new ApexCharts(document.querySelector("#productDistribution"), options);
    distributionChart.render();
}

function setupActivityTable() {
    $('#activityTable').DataTable({
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
        columns: [
            { 
                data: 'dateencoded',
                render: function(data) {
                    return new Date(data).toLocaleString();
                }
            },
            { data: 'productcode' },
            { data: 'productname' },
            { 
                data: 'movement_type',
                render: function(data) {
                    return `<span class="badge badge-${data.toLowerCase()}">${data}</span>`;
                }
            },
            { 
                data: 'totalpacks',
                render: function(data) {
                    return data.toLocaleString();
                }
            },
            { data: 'encoder' }
        ],
        order: [[0, 'desc']]
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

function applyFilters() {
    $('#activityTable').DataTable().ajax.reload();
}
