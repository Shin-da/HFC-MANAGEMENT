
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initializeHourlyPatternChart();
    initializeCustomerSegmentsChart();
    initializeSalesForecastChart();
    initializeStockLevelsChart();
});

function initializeHourlyPatternChart() {
    const ctx = document.getElementById('hourlyPatternChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: analyticsData.descriptive.hourly_patterns.map(p => p.hour_of_day + ':00'),
            datasets: [{
                label: 'Orders',
                data: analyticsData.descriptive.hourly_patterns.map(p => p.order_count),
                borderColor: '#4CAF50',
                fill: true
            }]
        },
        plugins: {
            title: {
                display: true,
                text: 'Hourly Order Pattern'
            }
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// ... Initialize other charts similarly ...