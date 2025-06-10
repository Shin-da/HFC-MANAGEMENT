<?php

require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>HOME</title>
    <?php require '../reusable/header.php'; ?>

</head>

<body>
    <?php
    // Alert-messages
    // include 'alerts/alert-messages.php';

    // Modals
    // include 'modals/modals.php';

    // Sidebar 
    include '../reusable/sidebar.php';

    ?>
    <!-- === Orders === -->
    <section class=" panel">
        <?php
        include '../reusable/navbar.html'; // TOP NAVBAR
        ?>


        <div class="container">
            <div class="panel-content">
                <div class="content-header">
                    <div class="title ">
                        <i class='bx bx-tachometer'></i>
                        <span class="text">Sales</span>
                    </div>
                </div>

                <div class="container" style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid var(--border-color);">
                    <!-- dito ka maglagay mike -->
                <?php 
                    include 'salesreport.php';
                ?>

                <script>
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
        } else {
            console.warn('Chart container element not found.');
        }
    };
    document.head.appendChild(script);
});



                </script>
                </div>

            </div>

        </div>

    </section>

</body>
<script src="../resources/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    import {
        useState,
        useEffect
    } from 'react'

    export default function Component() {
        const [salesData, setSalesData] = useState(initialSalesData)
        const [productPerformance, setProductPerformance] = useState(initialProductPerformance)
        const [prescriptiveActions, setPrescriptiveActions] = useState(initialPrescriptiveActions)

        useEffect(() => {
            // Simulating data fetch/update every 5 seconds
            const intervalId = setInterval(() => {
                // In a real application, you would fetch new data here
                // For this example, we'll just modify the existing data
                setSalesData(currentData =>
                    currentData.map(item => ({
                        ...item,
                        revenue: item.revenue * (1 + Math.random() * 0.1)
                    }))
                )
                setProductPerformance(currentData =>
                    currentData.map(item => ({
                        ...item,
                        value: item.value * (1 + Math.random() * 0.1)
                    }))
                )
                setPrescriptiveActions(currentData =>
                    currentData.map(item => ({
                        ...item,
                        impact: item.impact * (1 + Math.random() * 0.1)
                    }))
                )
            }, 5000)

            return () => clearInterval(intervalId) // Cleanup on component unmount
        }, [])

        // Rest of the component code using salesData, productPerformance, and prescriptiveActions...
    }
</script>

</html>