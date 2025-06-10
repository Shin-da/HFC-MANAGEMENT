document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts and data
    initializeDashboard();
    
    // Set up event listeners
    setupEventListeners();
    
    // Start real-time updates
    startRealtimeUpdates();
});

function initializeDashboard() {
    updateSalesData('all');
    initializeCharts();
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
        action: 'fetch_sales_data',
        period: period,
        ...dateRange
    });
    
    fetch(`sales.php?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            updateDashboardMetrics(data.metrics);
            updateCharts(data.sales_data);
            updateInsights(data.metrics);
        })
        .catch(error => console.error('Error fetching sales data:', error));
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
                labels: chartData.dates,
                datasets: [{
                    label: 'Sales Trends',
                    data: chartData.salesData,
                    backgroundColor: chartData.salesData.map(data => 
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
                labels: chartData.productLabels,
                datasets: [{
                    data: chartData.productValues,
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
                labels: chartData.categoryData.labels,
                datasets: [{
                    label: 'Revenue per Category',
                    data: chartData.categoryData.datasets[0].data,
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
                labels: chartData.seasonalData.labels,
                datasets: [{
                    label: 'Monthly Sales',
                    data: chartData.seasonalData.datasets[0].data,
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

