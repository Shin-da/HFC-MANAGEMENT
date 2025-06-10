// Reference the Chart.js script
document.addEventListener("DOMContentLoaded", function() {
    var script = document.createElement("script");
    script.src = "https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js";
    script.onload = function() {
        var ctx = document.getElementById('salesChart');

        if (ctx) {
            var config = {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    datasets: [{
                        label: 'Monthly Sales',
                        data: [12, 19, 3, 5, 2, 3, 15, 10, 11, 14, 9, 15],
                        borderColor: [
                            
                        ],
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }       
                    }
                }
            };

            var salesChart = new Chart(ctx, config);
        } else {
            console.warn('Chart container element not found.');
        }
    };
    document.head.appendChild(script);
});


