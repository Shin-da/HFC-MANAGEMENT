// Update color palette constants to match CSS variables
const CHART_COLORS = {
    primary: 'var(--gold-accent)',     
    secondary: 'var(--rust-light)',   
    accent: 'var(--rust-medium)',      
    warning: 'var(--beige-warm)',    
    danger: 'var(--rust-medium)',     
    success: 'var(--sage-medium)',     
    info: 'var(--sage-medium)',        
    neutral: 'var(--rust-light)'      
};

const CATEGORY_COLORS = [
    'var(--gold-accent)',
    'var(--rust-light)', 
    'var(--sage-medium)',
    'var(--rust-medium)',
    'var(--beige-warm)',
    'var(--rust-medium)',
    'var(--sage-medium)',
    'var(--rust-light)'
];

// Replace the window.addEventListener section with:
document.addEventListener('DOMContentLoaded', () => {
    console.log('Initializing dashboard...', dashboardData);
    try {
        if (typeof dashboardData === 'undefined' || !dashboardData.charts) {
            throw new Error('Dashboard data not available');
        }
        
        // Remove loading class from containers
        document.querySelectorAll('.chart-container').forEach(container => {
            container.classList.remove('loading');
        });
        
        initializeCharts(dashboardData.charts);
    } catch (error) {
        console.error('Chart initialization error:', error);
        showError('Failed to load charts: ' + error.message);
    }
});

function initializeCharts(data) {
    console.log('Chart data:', data); // Debug log
    
    // Sales Trends Chart
    const salesCtx = document.getElementById('salesTrendsChart');
    if (!salesCtx) {
        console.error('Sales chart canvas not found');
        return;
    }

    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: data.sales.labels,
                datasets: [{
                    label: 'Sales (₱)',
                    data: data.sales.data,
                    borderColor: CHART_COLORS.primary,
                    backgroundColor: `${CHART_COLORS.primary}20`,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Orders',
                    data: data.sales.orderCounts,
                    borderColor: CHART_COLORS.secondary,
                    backgroundColor: `${CHART_COLORS.secondary}20`,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'count'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Sales Amount (₱)'
                        }
                    },
                    count: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Number of Orders'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#333',
                        titleFont: { weight: '600' },
                        bodyColor: '#666',
                        bodyFont: { size: 13 },
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-PH', {
                                        style: 'currency',
                                        currency: 'PHP'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 13 }
                        }
                    }
                }
            }
        });
    }

    // Category Performance Chart
    const categoryCtx = document.getElementById('categoryPerformanceChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: data.categories.labels,
                datasets: [{
                    data: data.categories.data,
                    backgroundColor: CATEGORY_COLORS,
                    borderColor: 'white',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                return `₱${value.toLocaleString()}`;
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#333',
                        titleFont: { weight: '600' },
                        bodyColor: '#666',
                        bodyFont: { size: 13 },
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-PH', {
                                        style: 'currency',
                                        currency: 'PHP'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 13 }
                        }
                    }
                }
            }
        });
    }

    // Inventory Health Chart
    const inventoryCtx = document.getElementById('inventoryHealthChart');
    if (inventoryCtx) {
        new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: data.inventory.labels,
                datasets: [{
                    label: 'Available',
                    data: data.inventory.available,
                    backgroundColor: CHART_COLORS.success,
                    borderRadius: 4
                }, {
                    label: 'On Hand',
                    data: data.inventory.onhand,
                    backgroundColor: CHART_COLORS.info,
                    borderRadius: 4
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
                            text: 'Quantity'
                        },
                        grid: {
                            display: true,
                            color: '#E0E0E0',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'start',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12 }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#333',
                        titleFont: { weight: '600' },
                        bodyColor: '#666',
                        bodyFont: { size: 13 },
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-PH', {
                                        style: 'currency',
                                        currency: 'PHP'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 13 }
                        }
                    }
                }
            }
        });
    }
}

// Add smooth transitions for data updates
function updateCharts(newData) {
    charts.forEach(chart => {
        chart.data.datasets.forEach((dataset, i) => {
            const oldData = dataset.data;
            const newValues = newData[chart.id][i];
            
            // Animate each value
            oldData.forEach((value, index) => {
                const start = value;
                const end = newValues[index];
                
                animateValue(start, end, 1000, value => {
                    dataset.data[index] = value;
                    chart.update('none');
                });
            });
        });
    });
}

// Add loading states
function showLoading(chartId) {
    const container = document.querySelector(`#${chartId}`).parentElement;
    container.classList.add('loading');
}

function hideLoading(chartId) {
    const container = document.querySelector(`#${chartId}`).parentElement;
    container.classList.remove('loading');
}

// Add error handling with user feedback
function showError(message) {
    const toast = document.createElement('div');
    toast.className = 'toast error';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }, 100);
}

// Add refresh functionality
function refreshDashboard() {
    window.location.reload();
}
