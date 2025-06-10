<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>INVENTORY</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/form.css">
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
        <div class="container-fluid">
            <div class="table-header">
                <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                <h2>ADD TO INVENTORY</h2>
                <h3>Batch Details</h3>
            </div>

            <div class="container-fluid" style="overflow-y: scroll">
                <form action="./process/add.inventoryhistory.process.php" method="POST">
                    <table id="inventoryhistory">
                        <thead>
                            <tr>
                                <th>Batch Detail ID</th>
                                <th>Batch ID</th>
                                <th>Product Code</th>
                                <th>Quantity</th>
                                <th>Weight</th>
                                <th>Price</th>
                                
                            </tr>
                        </thead>
                        <script>
                            // Set the date format to yyyy-mm-dd
                            document.querySelectorAll('input[type="date"]').forEach(el => {
                                el.value = new Date().toISOString().slice(0, 10).replace(/-/g, '-');
                            });
                        </script>
                        <tbody>
                            <?php for ($i = 0; $i < 1; $i++): ?>
                        <tbody>
                            <tr>
                                <td><input type="text" name="batchDetailID[]" id="batchDetailID<?= $i ?>" placeholder="Batch Detail ID"></td>
                                <td><input type="text" name="batchID[]" id="batchID<?= $i ?>" placeholder="Batch ID"></td>
                                <td><input type="text" name="productCode[]" id="productCode<?= $i ?>" placeholder="Product Code"></td>
                                <td><input type="number" name="quantity[]" id="quantity<?= $i ?>" placeholder="Quantity"></td>
                                <td><input type="number" name="weight[]" id="weight<?= $i ?>" placeholder="Weight"></td>
                                <td><input type="number" name="price[]" id="price<?= $i ?>" placeholder="Price"></td>
                            </tr>
                        <?php endfor; ?>
                        </tbody>

                    </table>
                    <div class="bottom-form">
                        <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Row</button>
                    </div>
                    <div class="bottom-form">
                        <div class="buttons">
                            <input type="reset" value="Reset" name="reset" class="btn btn-danger" style="flex: 1"></input>
                            <input type="submit" value="Add Inventory" name="addInventory" class="btn btn-primary" style="flex: 1">
                        </div>
                    </div>
                </form>

            </div>
        </div>


        <script>
            // Function for adding new row on the table. 
            function addTableRow() {
              
            }
        </script>
    </section>

</body>
<script src="../resources/js/script.js"></script>
<script src="../resources/js/chart.js"></script>
<!-- ======= Charts JS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="../resources/js/chartsJS.js"></script>

</html>