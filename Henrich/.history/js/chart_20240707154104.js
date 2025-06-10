// Sales Chart
document.addEventListener("DOMContentLoaded", function() {
    var script = document.createElement("script");
    script.src = "https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js";
    script.onload = function() {
        var ctx = document.getElementById('salesChart');

        if (ctx) {
            var config = {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Monthly Sales',
                        data: [],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',    
                        ],
                        borderWidth: 2,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                        ],
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }   
                    }
                }
            };

            var fetchSalesData = function() {
                $.ajax({
                    url: 'database/dbconnect.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { sql: "SELECT MONTH(DateTime) as Month, SUM(Quantity) as Sales FROM tblorders GROUP BY MONTH(Order_DateTime) ORDER BY Month ASC" },
                    success: function(salesData) {
                        config.data.labels = salesData.map(item => item.Month);
                        config.data.datasets[0].data = salesData.map(item => item.Sales);
                        var salesChart = new Chart(ctx, config);
                    }
                }).fail(function() {
                    console.warn('Failed to fetch sales data.');
                });
            };

            fetchSalesData();

            setInterval(function() {
                fetchSalesData();