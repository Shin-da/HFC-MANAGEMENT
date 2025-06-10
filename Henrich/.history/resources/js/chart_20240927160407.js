document.addEventListener("DOMContentLoaded", function () {
    var script = document.createElement("script");
    script.src = "https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js";
    document.head.appendChild(script);

    var ctx = document.getElementById('sales-chart').getContext('2d');

    var fetchSalesData = function () {
        $.ajax({
            url: 'database/dbconnect.php',
            type: 'POST',
            dataType: 'json',
            data: { sql: "SELECT MONTH(DateTime) as Month, SUM(Quantity) as Sales FROM tblorders GROUP BY MONTH(Order_DateTime) ORDER BY Month ASC" },
            success: function (salesData) {
                console.log('Sales data fetched successfully:', salesData);

                var config = {
                    type: 'line',
                    data: {
                        labels: salesData.map(item => item.Month),
                        datasets: [{
                            label: 'Monthly Sales',
                            data: salesData.map(item => item.Sales),
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

                var chart = new Chart(ctx, config);
                console.log('Chart rendered successfully!');
            },
            error: function (xhr, status, error) {
                console.error('Error fetching sales data:', error);
                console.error('XHR:', xhr);
                console.error('Status:', status);
            }
        });

    };

    fetchSalesData();
});
