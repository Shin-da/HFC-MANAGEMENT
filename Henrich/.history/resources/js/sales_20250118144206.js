function initializeCharts(chartData, chartDates, chartLabels, chartValues) {
    // Bar Chart
    const ctx = document.getElementById('myChart');
    const chartBackgroundColor = chartData.map(data => data > 1000 ? 'rgba(54, 162, 235, 0.7)' : 'rgba(255, 99, 132, 0.8)');
    const chartBorderColor = chartData.map(data => data > 1000 ? 'rgba(54, 162, 235, 1)' : 'rgba(255, 99, 132, 1)');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartDates,
            datasets: [{
                label: 'Total Sales',
                data: chartData,
                backgroundColor: chartBackgroundColor,
                borderColor: chartBorderColor,
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: chartLabel || "All Orders",
                    color: 'black'
                },
                legend: {
                    labels: {
                        color: 'black'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'black'
                    }
                },
                x: {
                    ticks: {
                        color: 'black'
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Pie Chart
    const ctxPolar = document.getElementById('polarAreaChart').getContext('2d');
    const polarChart = new Chart(ctxPolar, {
        type: 'pie',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Product Sales',
                data: chartValues,
                backgroundColor: [
                    'rgba(34, 49, 63, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(199, 199, 199, 0.8)',
                    'rgba(83, 102, 255, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgba(34, 49, 63, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 199, 199, 1)',
                    'rgba(83, 102, 255, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: 'black'
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    return polarChart;
}

function updateChartData(year, month, day) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'supervisor/sales.php?updateChartData=true&year=' + year + '&month=' + month + '&day=' + day, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
            polarChart.data.labels = data.labels.slice(0, 10);
            polarChart.data.datasets[0].data = data.data.slice(0, 10);
            polarChart.update();
        }
    };
    xhr.send();
}
