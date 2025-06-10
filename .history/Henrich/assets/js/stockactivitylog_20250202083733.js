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
            id: 'activity-trends',
            height: 350,
            type: 'area',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
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

function setupDistributionChart(data) {
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