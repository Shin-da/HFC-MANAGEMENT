// Initialize charts when the page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    renderCharts(dashboardData);
    setupEventListeners();
}
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
});
