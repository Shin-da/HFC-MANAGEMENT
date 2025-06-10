const CHART_COLORS = {
    primary: 'rgba(47, 82, 51, 0.8)',     // forest-primary with transparency
    secondary: 'rgba(147, 61, 36, 0.8)',   // rust-medium with transparency
    accent: 'rgba(212, 185, 94, 0.8)',     // gold-accent with transparency
    warning: 'rgba(223, 92, 54, 0.8)',     // rust-light with transparency
    success: '#4F7942',     // forest-light
    info: '#598777'         // sage-dark
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
