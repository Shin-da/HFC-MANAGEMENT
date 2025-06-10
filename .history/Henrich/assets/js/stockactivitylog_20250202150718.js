let activityTable = null;
let trendsChart, distributionChart;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking chart containers...');
    console.log('monthlyTrends container:', document.querySelector("#monthlyTrends"));
    console.log('productDistribution container:', document.querySelector("#productDistribution"));
    
    // Add check for ApexCharts
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts is not loaded!');
        return;
    }
    
    initializeCharts();
    initializeActivityTable();
});

function initializeCharts() {
    console.log('Initializing charts...');
    
    fetch('../api/get_activity_stats.php')
        .then(response => {
            console.log('Raw response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Chart data received:', data);
            if (data.status === 'success') {
                // Modify the trends data structure
                const trendsOptions = {
                    series: [{
                        name: 'Total Activities',
                        data: data.trends.map(item => ({
                            x: new Date(item.x).getTime(),
                            y: parseInt(item.y)
                        }))
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        fontFamily: 'inherit',
                        background: 'transparent',
                        toolbar: {
                            show: true
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

                console.log('Rendering trends chart with options:', trendsOptions);
                
                try {
                    if (trendsChart) {
                        console.log('Destroying existing trends chart');
                        trendsChart.destroy();
                    }
                    trendsChart = new ApexCharts(document.querySelector("#monthlyTrends"), trendsOptions);
                    trendsChart.render();
                    console.log('Trends chart rendered successfully');
                } catch (error) {
                    console.error('Error rendering trends chart:', error);
                }

                try {
                    if (distributionChart) {
                        console.log('Destroying existing distribution chart');
                        distributionChart.destroy();
                    }
                    distributionChart = new ApexCharts(
                        document.querySelector("#productDistribution"), 
                        {
                            series: data.distribution.series,
                            labels: data.distribution.labels,
                            chart: {
                                type: 'donut',
                                height: 350,
                                background: 'transparent'
                            }
                        }
                    );
                    distributionChart.render();
                    console.log('Distribution chart rendered successfully');
                } catch (error) {
                    console.error('Error rendering distribution chart:', error);
                }
            }
        })
        .catch(error => console.error('Error in chart initialization:', error));
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
