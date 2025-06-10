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
<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
+                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
-                    labels: [],
<<<<<<<  c1ca7648-c802-4c19-a58a-e4f29959e4c1  >>>>>>>
                    datasets: [{
                        label: 'Monthly Sales',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'    
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


