let trendsChart, distributionChart, activityTable;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing charts...');
    initializeCharts();
    initializeActivityTable();
    setupEventListeners();
});

function initializeCharts() {
    fetch('../api/get_activity_stats.php')
        .then(response => response.json())
        .then(data => {
            console.log('Chart data received:', data);
            if (data.status === 'success') {
                setupTrendsChart(data.trends);
                setupDistributionChart(data.distribution);
                updateRecentActivities(data.recent);
            }
        })
        .catch(error => console.error('Chart initialization error:', error));
}

function setupTrendsChart(data) {
    const options = {
        series: [{
            name: 'Stock In',
            data: data.map(item => ({
                x: new Date(item.x).getTime(),
                y: item.ins
            }))
        }, {
            name: 'Stock Out',
            data: data.map(item => ({
                x: new Date(item.x).getTime(),
                y: item.outs
            }))
        }],
        chart: {
            type: 'area',
            height: 350,
            stacked: true,
            toolbar: {
                show: true
            }
        },
        colors: ['#2E7D32', '#C62828'],
        fill: {
            type: 'gradient',
            gradient: {
                opacityFrom: 0.6,
                opacityTo: 0.1
            }
        },
        xaxis: {
            type: 'datetime',
            labels: {
                formatter: function(val) {
                    return moment(val).format('MMM DD');
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return Math.round(val);
                }
            }
        }
    };

    if (trendsChart) {
        trendsChart.destroy();
    }

    console.log('Creating trends chart with options:', options);
    trendsChart = new ApexCharts(document.querySelector("#activityTrendsChart"), options);
    trendsChart.render();
}

function setupDistributionChart(data) {
    const options = {
        series: data.map(item => item.count),
        labels: data.map(item => item.type),
        chart: {
            type: 'donut',
            height: 350
        },
        colors: ['#2E7D32', '#C62828', '#1976D2'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%'
                }
            }
        },
        legend: {
            position: 'bottom'
        },
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

    if (distributionChart) {
        distributionChart.destroy();
    }

    console.log('Creating distribution chart with options:', options);
    distributionChart = new ApexCharts(document.querySelector("#distributionChart"), options);
    distributionChart.render();
}

function initializeActivityTable() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#activityTable')) {
        $('#activityTable').DataTable().destroy();
    }

    activityTable = $('#activityTable').DataTable({
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
            { data: 'encoder' },
            {
                data: null,
                render: function(data) {
                    return `<button class="btn-icon" onclick="viewActivityDetails('${data.ibdid}')">
                        <i class='bx bx-info-circle'></i>
                    </button>`;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
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
    if (activityTable) {
        activityTable.ajax.reload();
    }
}

function updateTrendsPeriod(days) {
    fetch(`../api/get_activity_stats.php?days=${days}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                trendsChart.updateSeries([{
                    name: 'Stock In',
                    data: data.trends.map(item => ({
                        x: new Date(item.x).getTime(),
                        y: item.ins
                    }))
                }, {
                    name: 'Stock Out',
                    data: data.trends.map(item => ({
                        x: new Date(item.x).getTime(),
                        y: item.outs
                    }))
                }]);
            }
        })
        .catch(error => console.error('Error updating trends:', error));
}
