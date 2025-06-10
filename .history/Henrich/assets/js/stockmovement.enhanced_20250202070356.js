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
    showLoadingOverlay();
    fetch(`get_movement_trends.php?days=${days}`)
        .then(response => response.json())
        .then(data => {
            if (chart) {
                chart.updateOptions({
                    series: [{
                        name: 'Stock In',
                        data: data.stock_in
                    }, {
                        name: 'Stock Out',
                        data: data.stock_out
                    }]
                });
            }
            hideLoadingOverlay();
        })
        .catch(error => {
            console.error('Error updating chart:', error);
            hideLoadingOverlay();
            showErrorMessage('Failed to update chart data');
        });
}

function showLoadingOverlay() {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="loading-spinner"></div>';
    document.querySelector('.chart-card').appendChild(overlay);
}

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
