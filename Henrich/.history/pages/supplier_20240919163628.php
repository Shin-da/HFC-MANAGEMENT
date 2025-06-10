<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>SUPPLIER</title>
    <?php require '../reusable/header.php'; ?>
</head>

<body>
    <?php

    // Sidebar 
    include '../reusable/sidebar.php';
    ?>

    <section class="dashboard panel">

        <?php
        // TOP NAVBAR
        include '../reusable/navbar.html';
        ?>

        <!-- Encoding Inventory -->
        <div class="card">
            <div class="">
                <div class="table-header">
                    <div class="title">
                        <h2>Encode Inventory</h2>
                    </div>
                </div>
                <div class="container">
                    <form action="encodeInventory.php" method="POST">
                        <div class="input-group">
                            <div class="icon">
                                <i class='bx bx-barcode'></i>
                            </div>
                            <input type="text" name="inventoryName" placeholder="Inventory Name">
                        </div>

                        <div class="input-group">
                            <div class="icon">
                                <i class='bx bx-package'></i>
                            </div>
                            <input type="number" name="quantity" placeholder="Quantity">
                        </div>
                </div>
            </div>
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