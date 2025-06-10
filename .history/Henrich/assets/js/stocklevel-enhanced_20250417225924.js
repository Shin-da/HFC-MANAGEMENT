let autoRefreshInterval;
let chart;

function initializeEnhancedStockPage() {
    setupEventListeners();
    initializeDataTable();
    setupCharts();
    initializeAlerts();
}

function setupEventListeners() {
    // Auto-refresh toggle
    document.getElementById('autoRefresh')?.addEventListener('change', function(e) {
        if (e.target.checked) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });

    // Refresh interval change
    document.getElementById('refreshInterval')?.addEventListener('change', function() {
        if (document.getElementById('autoRefresh')?.checked) {
            startAutoRefresh();
        }
    });

    // Add chart period change handler
    document.getElementById('chartPeriod')?.addEventListener('change', function(e) {
        const days = parseInt(e.target.value);
        updateChartPeriod(days);
    });
}

function initializeDataTable() {
    const table = $('#inventoryTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[3, 'asc']], // Sort by available quantity
        language: {
            emptyTable: "No inventory records found"
        },
        createdRow: function(row, data) {
            if (parseInt(data[3]) === 0) {
                $(row).addClass('out-of-stock');
            } else if (parseInt(data[3]) <= 10) {
                $(row).addClass('low-stock');
            }
        },
        initComplete: function() {
            // Add category filter options
            const categoryColumn = this.api().column(2);
            const categories = new Set();
            categoryColumn.data().each(function(value) {
                categories.add(value);
            });
            
            const categoryFilter = $('#categoryFilter');
            categories.forEach(category => {
                categoryFilter.append(`<option value="${category}">${category}</option>`);
            });
        }
    });

    // Apply filters
    $('#categoryFilter, #stockStatus').on('change', function() {
        table.draw();
    });

    // Custom filtering function
    $.fn.dataTable.ext.search.push(function(_, data) {
        const category = $('#categoryFilter').val();
        const status = $('#stockStatus').val();
        const rowCategory = data[2];
        const rowQuantity = parseInt(data[3]);
        
        const categoryMatch = !category || rowCategory === category;
        let statusMatch = true;
        
        if (status === 'out') {
            statusMatch = rowQuantity === 0;
        } else if (status === 'low') {
            statusMatch = rowQuantity > 0 && rowQuantity <= 10;
        }
        
        return categoryMatch && statusMatch;
    });
}

function setupCharts() {
    if (!window.stockData?.trends?.dates?.length) {
        console.error('No stock trends data available');
        return;
    }

    const chartOptions = {
        series: [{
            name: 'Total Stock Value',
            data: window.stockData.trends.values
        }],
        chart: {
            type: 'area',
            height: 350,
            zoom: {
                enabled: true
            },
            toolbar: {
                show: true
            }
        },
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
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: window.stockData.trends.dates,
            type: 'datetime',
            labels: {
                datetimeFormatter: {
                    year: 'yyyy',
                    month: 'MMM \'yy',
                    day: 'dd MMM',
                    hour: 'HH:mm'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return '₱' + val.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            },
            y: {
                formatter: function(val) {
                    return '₱' + val.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        },
        theme: {
            mode: document.body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light'
        }
    };

    if (chart) {
        chart.destroy();
    }

    const chartElement = document.querySelector("#stockTrendsChart");
    if (chartElement) {
        chart = new ApexCharts(chartElement, chartOptions);
        chart.render();
    } else {
        console.error('Stock trends chart element not found');
    }
}

function updateChartPeriod(days) {
    fetch(`get_stock_trends.php?days=${days}`)
        .then(response => response.json())
        .then(data => {
            if (chart) {
                chart.updateSeries([{
                    name: 'Stock Value',
                    data: data.values
                }]);
                chart.updateOptions({
                    xaxis: {
                        categories: data.dates
                    }
                });
            }
        })
        .catch(error => console.error('Error updating chart:', error));
}

function refreshData() {
    showLoadingOverlay();
    fetch('get_stock_data.php')
        .then(response => response.json())
        .then(data => {
            updateDashboard(data);
            hideLoadingOverlay();
            updateLastRefreshTime();
            Swal.fire({
                icon: 'success',
                title: 'Data Updated',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoadingOverlay();
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: 'Could not refresh data',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
}

function filterAlerts(type, clickEvent) {
    const alertsList = document.querySelector('.alerts-list');
    if (!alertsList) {
        console.error('Alerts list element not found');
        return;
    }

    const buttons = document.querySelectorAll('.btn-filter');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    if (clickEvent?.target) {
        clickEvent.target.classList.add('active');
    }
    
    if (type === 'critical') {
        alertsList.querySelectorAll('.alert-item').forEach(item => {
            if (item.classList.contains('out-of-stock')) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    } else {
        alertsList.querySelectorAll('.alert-item').forEach(item => {
            item.style.display = 'flex';
        });
    }
}

function initializeAlerts() {
    // Initialize alerts container
    const alertsContainer = document.querySelector('.alerts-list');
    if (!alertsContainer) {
        console.error('Alerts container not found');
        return;
    }

    // Set up alert filter buttons
    const filterButtons = document.querySelectorAll('.btn-filter');
    filterButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const type = button.getAttribute('data-filter-type') || 'all';
            filterAlerts(type, e);
        });
    });

    // Initial filter (show all)
    filterAlerts('all');
}

function updateDashboard(data) {
    // Update stats
    if (data.stats) {
        document.getElementById('totalProducts')?.textContent = data.stats.total_products.toLocaleString();
        document.getElementById('lowStock')?.textContent = data.stats.low_stock.toLocaleString();
        document.getElementById('outOfStock')?.textContent = data.stats.out_of_stock.toLocaleString();
        document.getElementById('totalValue')?.textContent = '₱' + data.stats.total_value.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Update charts if data available
    if (data.trends) {
        if (chart) {
            chart.updateSeries([{
                name: 'Total Stock Value',
                data: data.trends.values
            }]);
            chart.updateOptions({
                xaxis: {
                    categories: data.trends.dates
                }
            });
        }
    }

    // Update category chart if it exists
    const categoryChart = document.getElementById('categoryChart');
    if (categoryChart && data.categories) {
        const ctx = categoryChart.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.categories.map(item => item.productcategory),
                datasets: [{
                    data: data.categories.map(item => item.total_value),
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#fd7e14', '#6f42c1', '#20c9a6', '#5a5c69', '#858796'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return label + ': ₱' + Number(value).toLocaleString('en-PH', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                }
            }
        });
    }
}

function showLoadingOverlay() {
    let overlay = document.getElementById('loadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.innerHTML = '<div class="spinner"></div><p>Refreshing data...</p>';
        document.body.appendChild(overlay);
    }
    overlay.style.display = 'flex';
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function updateLastRefreshTime() {
    const lastUpdate = document.getElementById('lastUpdate');
    if (lastUpdate) {
        lastUpdate.textContent = new Date().toLocaleString();
    }
}

function startAutoRefresh() {
    stopAutoRefresh();
    const interval = parseInt(document.getElementById('refreshInterval')?.value || '60') * 1000;
    autoRefreshInterval = setInterval(refreshData, interval);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeEnhancedStockPage);
