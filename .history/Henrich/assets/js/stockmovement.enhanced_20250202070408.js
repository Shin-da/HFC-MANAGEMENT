function initializeMovementPage() {
    setupMovementChart();
    initializeDataTable();
    setupEventListeners();
}

function setupMovementChart() {
    console.log('Setting up movement chart...');
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
            }
        },
        colors: [
            getComputedStyle(document.documentElement).getPropertyValue('--forest-light').trim(),
            getComputedStyle(document.documentElement).getPropertyValue('--rust-light').trim()
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
            type: 'datetime',
            labels: {
                format: 'MMM dd'
            }
        },
        yaxis: {
            title: {
                text: 'Number of Pieces'
            },
            labels: {
                formatter: function(value) {
                    return Math.round(value).toString();
                }
            }
        },
        tooltip: {
            shared: true,
            y: {
                formatter: function(value) {
                    return Math.round(value).toString() + ' pcs';
                }
            }
        }
    };

    console.log('Initializing chart with options:', options);
    window.movementChart = new ApexCharts(document.querySelector("#movementTrendsChart"), options);
    window.movementChart.render();
    
    // Initial data load
    updateChartData(30);
}

function updateChartData(days) {
    showLoadingOverlay();
    console.log('Fetching data for', days, 'days');
    
    fetch(`get_movement_trends.php?days=${days}`)
        .then(response => response.json())
        .then(data => {
            console.log('Received data:', data);
            if (window.movementChart) {
                window.movementChart.updateOptions({
                    series: [{
                        name: 'Stock In',
                        data: data.stock_in
                    }, {
                        name: 'Stock Out',
function hideLoadingOverlay() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

function showErrorMessage(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeMovementPage);
