<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Pre Order</title>
    <?php require '../reusable/header.php'; ?>

</head>

<body>
    <?php
    include '../reusable/sidebar.php'; // Sidebar 
    ?>

    <section class=" panel">
        <?php include '../reusable/navbar.html'; // TOP NAVBAR ?>

        <div class="overview ">



        </div>

        <a href="./admin/admin"></a>

    </section>

</body>
<script src="../resources/js/script.js"></script>
<script src="../resources/js/chart.js"></script>
<!-- ======= Charts JS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="../resources/js/chartsJS.js"></script>

</html>