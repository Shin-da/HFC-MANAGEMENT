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

    // Sales Trends Chart
    if (dashboardData.sales_trends && dashboardData.sales_trends.length > 0) {
        initializeChart('salesTrendsChart', {
            type: 'line',
            data: {
                labels: dashboardData.sales_trends.map(item => item.date),
                datasets: [{
                    label: 'Daily Sales',
                    data: dashboardData.sales_trends.map(item => parseFloat(item.daily_sales) || 0),
                    borderColor: '#4CAF50',
                    fill: false
                }]
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
                datasets: [{
                    label: 'Revenue',
                    data: dashboardData.category_performance.map(item => parseFloat(item.revenue)),
                    backgroundColor: 'rgba(76, 175, 80, 0.6)',
                    borderColor: 'rgba(76, 175, 80, 1)',
                    borderWidth: 1
                }, {
                    label: 'Orders',
                    data: dashboardData.category_performance.map(item => parseInt(item.order_count)),
                    backgroundColor: 'rgba(33, 150, 243, 0.6)',
                    borderColor: 'rgba(33, 150, 243, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Category Performance'
                    }
                }
            }
        });
    }

    // Inventory Status Chart
    const inventoryCtx = document.getElementById('inventoryHealthChart');
    if (inventoryCtx) {
        destroyChart('inventoryHealthChart');
        new Chart(inventoryCtx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Inventory Status',
                    data: dashboardData.inventory_status.map(item => ({
                        x: parseInt(item.availablequantity),
                        y: parseInt(item.monthly_demand)
                    })),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    pointRadius: 8,
                    pointHoverRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Available Quantity'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Monthly Demand'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Inventory Health Analysis'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const item = dashboardData.inventory_status[context.dataIndex];
                                return [
                                    `Product: ${item.productname}`,
                                    `Category: ${item.productcategory}`,
                                    `Stock: ${item.availablequantity}`,
                                    `Demand: ${item.monthly_demand}`
                                ];
                            }
                        }
                    }
                }
            }
        });
    }

    // Order Fulfillment Chart
    initializeChart('fulfillmentChart', {
        type: 'doughnut',
        data: {
            labels: dashboardData.online_fulfillment.map(item => item.status),
            datasets: [{
                data: dashboardData.online_fulfillment.map(item => item.order_count),
                backgroundColor: ['#4CAF50', '#FFC107', '#F44336']
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Sales Channel Comparison
    initializeChart('salesChannelChart', {
        type: 'line',
        data: {
            labels: dashboardData.sales_trends.map(item => item.date),
            datasets: [{
                label: 'Online Sales',
                data: dashboardData.sales_trends.map(item => item.online_sales),
                borderColor: '#4CAF50'
            }, {
                label: 'Walk-in Sales',
                data: dashboardData.sales_trends.map(item => 
                    item.daily_sales - item.online_sales),
                borderColor: '#2196F3'
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Online vs Walk-in Sales Trend'
                }
            }
        }
    });

    // Customer Insights Chart
    initializeChart('customerInsightsChart', {
        type: 'bar',
        data: {
            labels: dashboardData.customer_analytics.map(item => item.accounttype),
            datasets: [{
                label: 'Average Order Value',
                data: dashboardData.customer_analytics.map(item => item.avg_order_value),
                backgroundColor: '#4CAF50'
            }, {
                label: 'Customer Lifetime Value',
                data: dashboardData.customer_analytics.map(item => 
                    item.customer_lifetime_value),
                backgroundColor: '#2196F3'
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Customer Value Analysis'
                }
            }
        }
    });
}
