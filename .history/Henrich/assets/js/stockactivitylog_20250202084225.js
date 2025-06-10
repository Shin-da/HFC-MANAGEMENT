let activityChart, distributionChart, activityTable;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    initializeCharts();
    initializeDataTable();
});

function initializeCharts() {
    console.log('Initializing charts');
    const trendsContainer = document.querySelector("#monthlyTrends");
    const distributionContainer = document.querySelector("#productDistribution");

    if (!trendsContainer || !distributionContainer) {
        console.error('Chart containers not found');
        return;
    }

    fetch('../api/get_activity_stats.php')
        .then(response => response.json())
        .then(data => {
            console.log('Chart data:', data);
            if (data.status === 'success') {
                initializeTrendsChart(data.trends);
                initializeDistributionChart(data.distribution);
                updateRecentActivities(data.recent);
            }
        })
        .catch(error => console.error('Chart data fetch error:', error));
}

function initializeTrendsChart(data) {
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
            height: 350,
            type: 'area',
            toolbar: {
                show: true
            }
        },
        colors: ['#00E396', '#FF4560'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
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
            title: {
                text: 'Number of Activities'
            }
        }
    };

    if (activityChart) {
        activityChart.destroy();
    }

    activityChart = new ApexCharts(document.querySelector("#monthlyTrends"), options);
    activityChart.render();
}

function initializeDistributionChart(data) {
    const options = {
        series: data.map(item => item.count),
        chart: {
            id: 'activity-distribution',
            type: 'donut',
            height: 350
        },
        labels: data.map(item => item.type),
        colors: ['#00E396', '#FF4560', '#FEB019'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Activities'
                        }
                    }
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
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

    distributionChart = new ApexCharts(document.querySelector("#productDistribution"), options);
    distributionChart.render();
}

function initializeDataTable() {
    console.log('Initializing DataTable');
    
    // Destroy existing instance if it exists
    if ($.fn.DataTable.isDataTable('#activityTable')) {
        $('#activityTable').DataTable().destroy();
    }

    // Clear the table body
    $('#activityTable tbody').empty();

    // Initialize new DataTable
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
                    return moment(data).format('MMM D, YYYY HH:mm');
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
                    return data ? data.toLocaleString() : '0';
                }
            },
            { data: 'encoder' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        drawCallback: function() {
            // Additional callback after table is drawn
            $('.badge').addClass('animated fadeIn');
        }
    });

    // Setup filter event handlers
    setupFilterHandlers();
}

function setupFilterHandlers() {
    $('#startDate, #endDate, #activityType').off('change').on('change', function() {
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
