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
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    tabBtns.forEach(btn => {
        if (btn.textContent.toLowerCase().includes(type)) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

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
    // Helper function to safely initialize chart
    function initializeChart(canvasId, config) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;
        
        // Destroy existing chart if it exists
        const existingChart = Chart.getChart(ctx);
        if (existingChart) {
            existingChart.destroy();
        }

        return new Chart(ctx, {
            ...config,
            options: {
                ...config.options,
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Sales Trends Chart with all order types
    if (dashboardData.sales_trends && dashboardData.sales_trends.length > 0) {
        initializeChart('salesTrendsChart', {
            type: 'line',
            data: {
                labels: dashboardData.sales_trends.map(item => item.date),
                datasets: [{
                    label: 'Walk-in Sales',
                    data: dashboardData.sales_trends.map(item => parseFloat(item.walkin_sales) || 0),
                    borderColor: '#4CAF50',
                    fill: false
                }, {
                    label: 'Online/Delivery Sales',
                    data: dashboardData.sales_trends.map(item => parseFloat(item.online_sales) || 0),
                    borderColor: '#2196F3',
                    fill: false
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Sales by Order Type'
                    }
                }
            }
        });
    }

    // Category Performance Chart
    if (dashboardData.category_performance && dashboardData.category_performance.length > 0) {
        initializeChart('categoryPerformanceChart', {
            type: 'bar',
            data: {
                labels: dashboardData.category_performance.map(item => item.productcategory),
                datasets: [{
                    label: 'Revenue',
                    data: dashboardData.category_performance.map(item => parseFloat(item.revenue) || 0),
                    backgroundColor: '#4CAF50'
                }]
            }
        });
    }

    // Inventory Status Chart
    if (dashboardData.inventory_status && dashboardData.inventory_status.length > 0) {
        initializeChart('inventoryHealthChart', {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Stock vs Demand',
                    data: dashboardData.inventory_status.map(item => ({
                        x: parseInt(item.availablequantity) || 0,
                        y: parseInt(item.monthly_demand) || 0
                    })),
                    backgroundColor: '#2196F3'
                }]
            },
            options: {
                scales: {
                    x: {
                        title: { display: true, text: 'Available Stock' }
                    },
                    y: {
                        title: { display: true, text: 'Monthly Demand' }
                    }
                }
            }
        });
    }
}
