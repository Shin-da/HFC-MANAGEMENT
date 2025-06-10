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
    const table = $('#movementTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[8, 'desc']], // Sort by date column by default
        language: {
            emptyTable: "No movement records found"
        },
        initComplete: function() {
            // Add custom filtering
            this.api().columns().every(function() {
                let column = this;
                if(column.index() === 7) { // Movement Type column
                    let select = $('#movementTypeFilter')
                        .on('change', function() {
                            let val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? `^${val}$` : '', true, false).draw();
                        });
                }
            });

            // Date filter
            $('#dateFilter').on('change', function() {
                table.draw();
            });
        },
        columnDefs: [{
            targets: -1, // Actions column
            orderable: false,
            className: 'text-center'
        }, {
            targets: 7, // Movement Type column
            className: 'text-center',
            render: function(data, type, row) {
                return `<span class="movement-type ${data.toLowerCase()}">${data}</span>`;
            }
        }],
        drawCallback: function() {
            // Enhance table UI after each draw
            $('.table-action-btn').hover(
                function() { $(this).find('i').addClass('bx-tada'); },
                function() { $(this).find('i').removeClass('bx-tada'); }
            );
        }
    });

    // Custom date filtering
    $.fn.dataTable.ext.search.push(function(settings, data) {
        let dateFilter = $('#dateFilter').val();
        let dateCol = data[8]; // Date column index
        
        if (!dateFilter) return true;
        
        let filterDate = new Date(dateFilter);
        let rowDate = new Date(dateCol);
        
        return filterDate.toDateString() === rowDate.toDateString();
    });
}

function viewMovement(ibdid) {
    window.location.href = `view.movement.php?id=${ibdid}`;
}

function editMovement(ibdid) {
    window.location.href = `edit.movement.php?id=${ibdid}`;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeMovementPage);
