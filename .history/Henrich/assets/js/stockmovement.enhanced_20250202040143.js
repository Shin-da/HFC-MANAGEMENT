function initializeMovementPage() {
    setupMovementChart();
    initializeDataTable();
    setupEventListeners();
}

function setupMovementChart() {
    const options = {
        series: [{
            name: 'Stock In',
            data: []
        }, {
            name: 'Stock Out',
            data: []
        }],
        chart: {
            type: 'area',
            height: 350,
            stacked: true,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
        },
        colors: [
            getComputedStyle(document.documentElement).getPropertyValue('--forest-light'),
            getComputedStyle(document.documentElement).getPropertyValue('--rust-light')
        ],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                opacityFrom: 0.6,
                opacityTo: 0.1
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        },
        xaxis: {
            type: 'datetime'
        },
        tooltip: {
            shared: true
        }
    };

    const chart = new ApexCharts(document.querySelector("#movementTrendsChart"), options);
    chart.render();
    
    // Initial data load
    updateChartData(30);
}

function updateChartData(days) {
    fetch(`get_movement_trends.php?days=${days}`)
        .then(response => response.json())
        .then(data => {
            chart.updateSeries([{
                name: 'Stock In',
                data: data.stock_in
            }, {
                name: 'Stock Out',
                data: data.stock_out
            }]);
        });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeMovementPage);
