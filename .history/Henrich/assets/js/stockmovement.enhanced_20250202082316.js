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
                text: 'Number of Packs'
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
                    return Math.round(value).toString() + ' packs';
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

function initializeDataTable() {
    $('#movementTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '../api/get_stockmovement_table.php',
            type: 'POST',
            data: function(d) {
                d.dateFilter = $('#dateFilter').val();
                d.typeFilter = $('#movementTypeFilter').val();
            }
        },
        columns: [
            { data: 'ibdid' },
            { data: 'batchid' },
            { data: 'productcode' },
            { data: 'productname' },
            { data: 'numberofbox' },
            { data: 'totalpacks' },
            { data: 'totalweight' },
            { 
                data: 'movement_type',
                render: function(data) {
                    return `<span class="movement-badge ${data.toLowerCase()}">${data}</span>`;
                }
            },
            { 
                data: 'dateencoded',
                render: function(data) {
                    return new Date(data).toLocaleString();
                }
            },
            { data: 'encoder' },
            {
                data: null,
                render: function(data) {
                    let buttons = `
                        <button class="btn-icon" onclick="viewMovement('${data.ibdid}')">
                            <i class='bx bx-show'></i>
                        </button>`;
                    
                    if (new Date(data.dateencoded) > new Date(Date.now() - 86400000)) {
                        buttons += `
                            <button class="btn-icon" onclick="editMovement('${data.ibdid}')">
                                <i class='bx bx-edit'></i>
                            </button>`;
                    }
                    return buttons;
                }
            }
        ],
        order: [[8, 'desc']],
        pageLength: 10
    });
}

function viewMovement(ibdid) {
    window.location.href = `view.movement.php?id=${ibdid}`;
}

function editMovement(ibdid) {
    window.location.href = `edit.movement.php?id=${ibdid}`;
}

function initializeCharts() {
    fetch('../api/get_stockmovement_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                initializeTrendsChart(data.trends);
                initializeDistributionChart(data.distribution);
            }
        })
        .catch(error => console.error('Error loading charts:', error));
}

function initializeTrendsChart(data) {
    const options = {
        series: [{
            name: 'Stock In',
            data: data.map(item => ({ x: item.x, y: item.in }))
        }, {
            name: 'Stock Out',
            data: data.map(item => ({ x: item.x, y: item.out }))
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
        xaxis: {
            type: 'datetime'
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#movementTrendsChart"), options);
    chart.render();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeMovementPage);
