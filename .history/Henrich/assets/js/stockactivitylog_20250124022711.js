// Chart configurations and data
const chartColors = {
    primary: getComputedStyle(document.documentElement).getPropertyValue('--forest-primary').trim(),
    secondary: getComputedStyle(document.documentElement).getPropertyValue('--forest-light').trim(),
    accent: getComputedStyle(document.documentElement).getPropertyValue('--accent-warning').trim(),
    background: getComputedStyle(document.documentElement).getPropertyValue('--bg-white').trim(),
    gridLines: getComputedStyle(document.documentElement).getPropertyValue('--sage-200').trim()
};

// Monthly Trends Chart
function initMonthlyTrendsChart(data) {
    const ctx = document.getElementById('monthlyTrends').getContext('2d');
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Activity Volume',
                data: data.values,
                backgroundColor: chartColors.secondary + '40', // 40 = 25% opacity
                borderColor: chartColors.primary,
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Activity Trends',
                    color: chartColors.primary,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: chartColors.gridLines
                    },
                    ticks: {
                        color: chartColors.primary
                    }
                },
                x: {
                    grid: {
                        color: chartColors.gridLines
                    },
                    ticks: {
                        color: chartColors.primary
                    }
                }
            }
        }
    });
}

// Product Distribution Chart
function initProductDistributionChart(data) {
    const ctx = document.getElementById('productDistribution').getContext('2d');
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: [
                    chartColors.primary,
                    chartColors.secondary,
                    chartColors.accent
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Activity Distribution by Type',
                    color: chartColors.primary,
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            }
        }
    });
}

// Table filtering functionality
function filterTable(tbody, value, columnIndex) {
    const rows = tbody.getElementsByTagName('tr');
    const filterValue = value.toLowerCase();

    for (let row of rows) {
        const cell = row.getElementsByTagName('td')[columnIndex];
        if (cell) {
            const text = cell.textContent || cell.innerText;
            row.style.display = text.toLowerCase().includes(filterValue) ? '' : 'none';
        }
    }
}

// Date range filter
function handleDateFilter() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        const rows = document.querySelectorAll('#myTable tbody tr');
        rows.forEach(row => {
            const dateCell = row.getElementsByTagName('td')[2]; // Date encoded column
            const rowDate = new Date(dateCell.textContent);
            const isInRange = rowDate >= new Date(startDate) && rowDate <= new Date(endDate);
            row.style.display = isInRange ? '' : 'none';
        });
    }
}

// Activity type filter
function handleActivityFilter() {
    const selectedType = document.getElementById('activityType').value;
    const rows = document.querySelectorAll('#myTable tbody tr');
    
    rows.forEach(row => {
        if (!selectedType) {
            row.style.display = '';
            return;
        }
        const descriptionCell = row.getElementsByTagName('td')[4]; // Description column
        row.style.display = descriptionCell.textContent.includes(selectedType) ? '' : 'none';
    });
}

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get theme colors
    const colors = {
        primary: getComputedStyle(document.documentElement).getPropertyValue('--forest-primary').trim(),
        secondary: getComputedStyle(document.documentElement).getPropertyValue('--forest-light').trim(),
        warning: getComputedStyle(document.documentElement).getPropertyValue('--accent-warning').trim(),
        bgWhite: getComputedStyle(document.documentElement).getPropertyValue('--bg-white').trim(),
        gridLines: getComputedStyle(document.documentElement).getPropertyValue('--sage-200').trim(),
        text: getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim()
    };

    // Initialize charts
    function initCharts() {
        const monthlyTrends = document.getElementById('monthlyTrends');
        const productDistribution = document.getElementById('productDistribution');

        if (!monthlyTrends || !productDistribution) return;

        // Show loading state
        monthlyTrends.innerHTML = '<div class="chart-loading">Loading...</div>';
        productDistribution.innerHTML = '<div class="chart-loading">Loading...</div>';

        fetch('../api/stock-activity-stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Monthly Trends Chart
                    new Chart(monthlyTrends.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: data.monthly.labels,
                            datasets: [{
                                label: 'Activity Volume',
                                data: data.monthly.values,
                                backgroundColor: `${colors.secondary}40`,
                                borderColor: colors.primary,
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Monthly Activity Trends',
                                    color: colors.text,
                                    font: { size: 16, weight: 600 }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: colors.gridLines },
                                    ticks: { color: colors.text }
                                },
                                x: {
                                    grid: { color: colors.gridLines },
                                    ticks: { color: colors.text }
                                }
                            }
                        }
                    });

                    // Distribution Chart
                    new Chart(productDistribution.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: data.distribution.labels,
                            datasets: [{
                                data: data.distribution.values,
                                backgroundColor: [
                                    colors.primary,
                                    colors.secondary,
                                    colors.warning
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Activity Distribution',
                                    color: colors.text,
                                    font: { size: 16, weight: 600 }
                                },
                                legend: {
                                    position: 'bottom',
                                    labels: { color: colors.text }
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading charts:', error);
                monthlyTrends.innerHTML = '<div class="chart-error">Failed to load chart data</div>';
                productDistribution.innerHTML = '<div class="chart-error">Failed to load chart data</div>';
            });
    }

    // Initialize charts
    initCharts();

    // Initialize existing table filters
    document.getElementById('startDate')?.addEventListener('change', handleDateFilter);
    document.getElementById('endDate')?.addEventListener('change', handleDateFilter);
    document.getElementById('activityType')?.addEventListener('change', handleActivityFilter);
});
