document.addEventListener('DOMContentLoaded', function() {
    if (typeof dashboardData === 'undefined') {
        console.error('Dashboard data not found');
        return;
    }

    // Initialize all dashboard charts
    initializeMainCharts();
    initializeFoodAnalytics();
    initializePredictiveCharts();
});

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts and data
    initializeDashboard();
    
    // Set up event listeners
    setupEventListeners();
    
    // Start real-time updates
    startRealtimeUpdates();
});

function initializeDashboard() {
    // Main dashboard charts
    initializeMainCharts();
    // Food analytics charts
    initializeFoodAnalytics();
}

function initializeMainCharts() {
    updateSalesData('all');
    initializeCharts();
}

function initializeFoodAnalytics() {
    // Food Category Performance Chart
    const foodCategoryCtx = document.getElementById('foodCategoryChart');
    if (foodCategoryCtx) {
        new Chart(foodCategoryCtx, {
            type: 'bar',
            data: {
                labels: dashboardData.categories.map(cat => cat.productcategory),
                datasets: [{
                    label: 'Revenue per Order',
                    data: dashboardData.categories.map(cat => cat.revenue_per_order),
                    backgroundColor: 'rgba(52, 152, 219, 0.7)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₱' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    }

    // Food Seasonal Trends Chart
    const foodSeasonalCtx = document.getElementById('foodSeasonalChart');
    if (foodSeasonalCtx) {
        new Chart(foodSeasonalCtx, {
            type: 'line',
            data: {
                labels: dashboardData.seasonal.map(item => 
                    new Date(2023, item.month - 1).toLocaleString('default', { month: 'long' })),
                datasets: [{
                    label: 'Monthly Sales',
                    data: dashboardData.seasonal.map(item => item.total_sales),
                    fill: true,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₱' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    }
}

function initializePredictiveCharts() {
    console.log('Initializing predictive charts with:', window.salesPredictions); // Debug log
    
    if (!window.salesPredictions) {
        console.error('No sales predictions data available');
        return;
    }
    
    Object.entries(window.salesPredictions).forEach(([productCode, prediction]) => {
        if (!prediction || !Array.isArray(prediction) || prediction.length === 0) {
            console.warn(`Invalid prediction data for product ${productCode}`);
            return;
        }

        const canvasId = `forecast_${productCode}`;
        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.warn(`Canvas not found for product ${productCode}`);
            return;
        }

        try {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: prediction.map(p => new Date(p.date).toLocaleDateString()),
                    datasets: [{
                        label: 'Predicted Sales',
                        data: prediction.map(p => p.predicted_sales),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: false
                    }, {
                        label: 'Upper Bound',
                        data: prediction.map(p => p.confidence_interval.upper),
                        borderColor: 'rgba(75, 192, 192, 0.3)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        fill: '+1',
                        tension: 0.1
                    }, {
                        label: 'Lower Bound',
                        data: prediction.map(p => p.confidence_interval.lower),
                        borderColor: 'rgba(75, 192, 192, 0.3)',
                        fill: false,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: '30-Day Sales Forecast'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ₱${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => '₱' + value.toLocaleString()
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error(`Error creating chart for product ${productCode}:`, error);
        }
    });
}

function setupEventListeners() {
    // Period filter change
    document.getElementById('period-filter').addEventListener('change', function(e) {
        const period = e.target.value;
        if (period === 'custom') {
            document.getElementById('custom-date-range').style.display = 'block';
        } else {
            document.getElementById('custom-date-range').style.display = 'none';
            updateSalesData(period);
        }
    });
    
    // Custom date range
    const dateInputs = ['start-date', 'end-date'].map(id => 
        document.getElementById(id));
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (dateInputs.every(input => input.value)) {
                updateSalesData('custom', {
                    start_date: dateInputs[0].value,
                    end_date: dateInputs[1].value
                });
            }
        });
    });
}

function updateSalesData(period, dateRange = {}) {
    const params = new URLSearchParams({
        period: period,
        ...dateRange
    });
    
    fetch(`../api/sales-data.php?${params}`)
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response');
            }
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}`);
            }
            return response.json();
        })
        .then(response => {
            if (response.status === 'error') {
                throw new Error(response.message);
            }
            updateDashboardMetrics(response.metrics);
            updateCharts(response.data);
        })
        .catch(error => {
            console.error('Error fetching sales data:', error);
            showErrorMessage('Failed to update sales data. Please try again.');
        });
}

function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger';
    alertDiv.textContent = message;
    
    const container = document.querySelector('.dashboard-wrapper');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

function startRealtimeUpdates() {
    setInterval(() => {
        updateSalesData(document.getElementById('period-filter').value);
    }, 60000); // Update every minute
}

// Helper functions
function updateDashboardMetrics(metrics) {
    Object.entries(metrics).forEach(([key, value]) => {
        const element = document.querySelector(`[data-metric="${key}"] .stat-value`);
        if (element) {
            element.textContent = formatMetricValue(key, value);
        }
    });
}

function formatMetricValue(key, value) {
    switch(key) {
        case 'total_sales':
            return formatCurrency(value);
        case 'growth_rate':
            return `${value.toFixed(1)}%`;
        default:
            return value.toLocaleString();
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}

function initializeSalesCharts(chartData) {
    console.log('Initializing charts with data:', chartData); // Debug log

    // Sales Trends Chart
    const salesCtx = document.getElementById('myChart');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: chartData.sales.dates,
                datasets: [{
                    label: chartData.sales.label,
                    data: chartData.sales.data,
                    backgroundColor: chartData.sales.data.map(data => 
                        data > 1000 ? 'rgba(54, 162, 235, 0.7)' : 'rgba(255, 99, 132, 0.8)'
                    ),
                    borderRadius: 5,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₱' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    }

    // Product Distribution Chart
    const polarCtx = document.getElementById('polarAreaChart');
    if (polarCtx) {
        new Chart(polarCtx, {
            type: 'polarArea',
            data: {
                labels: chartData.products.labels,
                datasets: [{
                    data: chartData.products.values,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Category Performance Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: chartData.categories.map(cat => cat.productcategory),
                datasets: [{
                    label: 'Revenue per Category',
                    data: chartData.categories.map(cat => cat.revenue_per_order),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Seasonal Trends Chart
    const seasonalCtx = document.getElementById('seasonalChart');
    if (seasonalCtx) {
        new Chart(seasonalCtx, {
            type: 'line',
            data: {
                labels: chartData.seasonal.map(item => 
                    new Date(2023, item.month - 1).toLocaleString('default', { month: 'long' })
                ),
                datasets: [{
                    label: 'Monthly Sales',
                    data: chartData.seasonal.map(item => item.total_sales),
                    fill: true,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}

// Handle chart data updates
function updateChartData(year, month, day) {
    fetch(`../api/sales-data.php?updateChartData=true&year=${year}&month=${month}&day=${day}`)
        .then(response => response.json())
        .then(data => {
            // Update charts with new data
            charts.forEach(chart => {
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.data;
                chart.update();
            });
        })
        .catch(error => console.error('Error:', error));
}
