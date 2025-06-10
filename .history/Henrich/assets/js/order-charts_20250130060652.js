const CHART_COLORS = {
    primary: '#5c7a65',
    secondary: '#ef4444',
    accent: '#b91c1c',
    warning: '#FFC107',
    success: '#4a624f',
    info: '#9fb3a6'
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('Charts initializing...');
    try {
        if (typeof orderData === 'undefined') {
            throw new Error('orderData is not defined');
        }
        console.log('Order data available:', orderData);
        
        if (!orderData.charts) {
            throw new Error('Chart data not found in orderData');
        }
        
        initializeOrderCharts(orderData.charts);
    } catch (error) {
        console.error('Chart initialization error:', error);
        // Show error in UI
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.textContent = 'Failed to load charts: ' + error.message;
        document.querySelector('.charts-container').prepend(errorDiv);
    }
});

function initializeOrderCharts(data) {
    // Order Trends Chart
    const trendsCtx = document.getElementById('orderTrendsChart');
    if (!trendsCtx) {
        console.error('Trends chart canvas not found');
        return;
    }

    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: data.trends.labels,
            datasets: [{
                label: 'Orders',
                data: data.trends.orders,
                borderColor: CHART_COLORS.primary,
                backgroundColor: `${CHART_COLORS.primary}20`,
                fill: true
            }, {
                label: 'Revenue (₱)',
                data: data.trends.revenue,
                borderColor: CHART_COLORS.success,
                backgroundColor: `${CHART_COLORS.success}20`,
                fill: true,
                yAxisID: 'revenue'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Orders'
                    }
                },
                revenue: {
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenue (₱)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Order Types Chart
    const typesCtx = document.getElementById('orderTypesChart');
    if (!typesCtx) {
        console.error('Types chart canvas not found');
        return;
    }

    new Chart(typesCtx, {
        type: 'doughnut',
        data: {
            labels: data.types.labels,
            datasets: [{
                data: data.types.counts,
                backgroundColor: Object.values(CHART_COLORS)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
