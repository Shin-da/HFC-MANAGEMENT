// Initialize charts when the page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    renderCharts(dashboardData);
    setupEventListeners();
}

function setupEventListeners() {
    // Time range selector
    document.getElementById('timeRange').addEventListener('change', function(e) {
        updateDashboard(e.target.value);
    });

    // Tab navigation
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

function updateDashboard(days) {
    fetch(`get_dashboard_data.php?days=${days}`)
        .then(response => response.json())
        .then(data => {
            updateMetrics(data.metrics);
            renderCharts(data);
            updateRecentOrders(data.recent_orders);
        })
        .catch(error => console.error('Error updating dashboard:', error));
}

function exportSalesData() {
    const timeRange = document.getElementById('timeRange').value;
    window.location.href = `export_sales.php?days=${timeRange}`;
}

function showOrders(type) {
    const walkInOrders = document.querySelector('.orders-list:nth-child(1)');
    const onlineOrders = document.querySelector('.orders-list:nth-child(2)');
    
    if (type === 'walk-in') {
        walkInOrders.style.display = 'block';
        onlineOrders.style.display = 'none';
    } else {
        walkInOrders.style.display = 'none';
        onlineOrders.style.display = 'block';
    }
}

// Helper function to update metrics
function updateMetrics(metrics) {
    document.querySelector('.stat-card.warning .stat-value').textContent = metrics.low_stock_count;
    document.querySelector('.stat-card.danger .stat-value').textContent = metrics.out_of_stock_count;
    document.querySelector('.stat-card.success .stat-value').textContent = metrics.today_orders;
    document.querySelector('.stat-card.info .stat-value').textContent = metrics.today_online_orders;
}

function renderCharts(dashboardData) {
    // Function to safely initialize charts
    function initializeChart(chartId, config) {
        const canvas = document.getElementById(chartId);
        if (!canvas) return null;
        return new Chart(canvas.getContext('2d'), {
            ...config,
            options: {
                ...config.options,
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Sales Trends Chart
    initializeChart('salesTrendsChart', {
        type: 'line',
        data: {
            labels: dashboardData.sales_trends.map(item => item.date),
            datasets: [{
                label: 'Daily Sales',
                data: dashboardData.sales_trends.map(item => item.daily_sales),
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Order Count',
                data: dashboardData.sales_trends.map(item => item.order_count),
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Last 7 Days Performance' }
            }
        }
    });

    // Category Performance Chart
    initializeChart('categoryPerformanceChart', {
        type: 'bar',
        data: {
            labels: dashboardData.category_performance.map(item => item.productcategory),
            datasets: [{
                label: 'Revenue',
                data: dashboardData.category_performance.map(item => item.revenue),
                backgroundColor: '#4CAF50'
            }, {
                label: 'Order Count',
                data: dashboardData.category_performance.map(item => item.order_count),
                backgroundColor: '#2196F3'
            }]
        }
    });

    // Inventory Health Chart
    initializeChart('inventoryHealthChart', {
        type: 'bubble',
        data: {
            datasets: [{
                label: 'Stock vs Demand',
                data: dashboardData.inventory_health.map(item => ({
                    x: item.availablequantity,
                    y: item.monthly_demand,
                    r: Math.max(5, Math.min(20, item.availablequantity / 10))
                })),
                backgroundColor: 'rgba(75, 192, 192, 0.6)'
            }]
        },
        options: {
            scales: {
                x: { title: { display: true, text: 'Available Quantity' } },
                y: { title: { display: true, text: 'Monthly Demand' } }
            }
        }
    });

    // Order Fulfillment Chart
            }
        }
    });
});
