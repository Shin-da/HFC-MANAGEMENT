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
    <section class=" panel">
        <?php
        // TOP NAVBAR
        include '../reusable/navbarNoSearch.html';
        ?>
        
        <div class="container">
            <div class="title">
                <h2>Add Customer Order</h2>
            </div>


            <div class="container">
                <form action="add.customerorder.process.php" method="POST">
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="customerOrderName" placeholder="Customer Order Name">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="number" name="quantity" placeholder="Quantity">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="number" name="price" placeholder="Price">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="supplierName" placeholder="Supplier Name">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="supplierEmail" placeholder="Supplier Email">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="supplierAddress" placeholder="Supplier Address">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="supplierPhone" placeholder="Supplier Phone">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="date" name="date" placeholder="Date">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="time" name="time" placeholder="Time">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="description" placeholder="Description">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="status" placeholder="Status">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        
        </div>

    </section>

</body>
<script src="../resources/js/script.js"></script>
<script src="../resources/js/chart.js"></script>
<!-- ======= Charts JS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="../resources/js/chartsJS.js"></script>

</html>