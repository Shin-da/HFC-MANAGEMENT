let activityTable = null;
let trendsChart, distributionChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeActivityTable();
});

function initializeCharts() {
    // Ensure ApexCharts is loaded
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts not loaded!');
        return;
    }

    fetch('../api/get_activity_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const trendsOptions = {
                    series: [{
                        name: 'Total Activities',
                        data: data.trends
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        zoom: {
                            enabled: true
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        type: 'datetime'
                    },
                    tooltip: {
                        x: {
                            format: 'dd MMM yyyy'
                        }
                    },
                    fill: {
                        type: 'gradient'
                    }
                };

                const distributionOptions = {
                    series: data.distribution.series,
                    chart: {
                        type: 'donut',
                        height: 350
                    },
                    labels: data.distribution.labels,
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                // Clear existing charts if they exist
                if (trendsChart) trendsChart.destroy();
                if (distributionChart) distributionChart.destroy();

                // Initialize new charts
                trendsChart = new ApexCharts(document.querySelector("#monthlyTrends"), trendsOptions);
                distributionChart = new ApexCharts(document.querySelector("#productDistribution"), distributionOptions);

                trendsChart.render();
                distributionChart.render();
            }
        })
        .catch(error => console.error('Error fetching chart data:', error));
}

function initializeActivityTable() {
    const tableElement = $('#activityTable');
    
    if (activityTable) {
        activityTable.destroy();
        activityTable = null;
    }

    activityTable = tableElement.DataTable({
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
                    return moment(data).format('MMM D, YYYY HH:mm');
                }
            },
            { data: 'productcode' },
            { data: 'productname' },
            { 
                data: 'movement_type',
                render: function(data) {
                data: 'dateencoded',
                render: function(data) {
                    return moment(data).format('MMM D, YYYY HH:mm');
                }
            },
            { data: 'productcode' },
            { data: 'productname' },
            { 
                data: 'movement_type',
                render: function(data) {
                    return `<span class="activity-status ${data.toLowerCase()}">${data}</span>`;
                }
            },
            { data: 'totalpacks' },
            { data: 'encoder' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });

    // Setup filter handlers after table initialization
    setupFilterHandlers();
}

function setupFilterHandlers() {
    const filters = $('#startDate, #endDate, #activityType');
    
    // Remove existing handlers
    filters.off('change');
    
    // Add new handlers
    filters.on('change', function() {
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
