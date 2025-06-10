<?php require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>HOME</title>
    <?php require '../reusable/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>

<body>
    <?php include '../reusable/sidebar.php'; // Sidebar     ?>

    <!-- === Orders === -->
    <section class=" panel">
        <?php include '../reusable/navbar.html'; // TOP NAVBAR        ?>

        <div class="container">
            <div class="panel-content">
                <div class="content-header">
                    <div class="title ">
                        <i class='bx bx-tachometer'></i>
                        <span class="text">Sales</span>
                    </div>
                </div>

                <!-- === Sales Report === -->
                <div class="container" style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid var(--border-color);">
                    <!-- dito ka maglagay mike -->
                    <canvas id="sales-chart" style="width: 100%; height: 300px; max-height: 300px;"></canvas>
                    <?php
                    include 'salesreport.php';
                    ?>
                    <script src="../resources/js/chart.js"></script>
                    <script>
                        var ctx = document.getElementById("sales-chart").getContext("2d");
                        var salesChart = new Chart(ctx, <?php echo json_encode($config); ?>);
                    </script>
                </div>
            </div>
        </div>
    </section>

</body>

</html>