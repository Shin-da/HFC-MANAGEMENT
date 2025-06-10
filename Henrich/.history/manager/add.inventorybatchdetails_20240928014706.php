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
                                <td><input type="text" name="batchid[]" id="batchid<?= $i ?>" value="" /></td>
                                <td><input type="date" name="dateofarrival[]" id="dateofarrival<?= $i ?>" value="" /></td>
                                <td><input type="text" name="productcode[]" id="productcode<?= $i ?>" value="" /></td>
                                <td><input type="text" name="encoder[]" id="encoder<?= $i ?>" value="" /></td>
                                <td><input type="date" name="dateencoded[]" id="dateencoded<?= $i ?>" value="" /></td>
                                <td><input type="text" name="description[]" id="description<?= $i ?>" value="" /></td>
                                <td><input type="date" name="datestockin[]" id="datestockin<?= $i ?>" value="" /></td>
                                <td><input type="date" name="datestockout[]" id="datestockout<?= $i ?>" value="" /></td>
                                <td><input type="text" name="totalboxes[]" id="totalboxes<?= $i ?>" value="" /></td>
                                <td><input type="text" name="totalweight[]" id="totalweight<?= $i ?>" value="" /></td>
                                <td><input type="text" name="totalcost[]" id="totalcost<?= $i ?>" value="" /></td>
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
                var table = document.getElementById("inventoryhistory");
                var row = table.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);
                var cell9 = row.insertCell(8);
                var cell10 = row.insertCell(9);
                

                cell1.innerHTML = "<input type='text' name='batchid[]' id='batchid' placeholder='Batch Id'>";
                cell2.innerHTML = "<input type='date' name='dateofarrival[]' id='dateofarrival' placeholder='Date of Arrival'>";
                cell3.innerHTML = "<input type='text' name='encoder[]' id='encoder' placeholder='Encoder'>";
                cell4.innerHTML = "<input type='date' name='dateencoded[]' id='dateencoded' placeholder='Date Encoded'>";
                cell5.innerHTML = "<input type='text' name='description[]' id='description' placeholder='Description'>";
                cell6.innerHTML = "<input type='date' name='datestockin[]' id='datestockin' placeholder='Date Stock In'>";
                cell7.innerHTML = "<input type='date' name='datestockout[]' id='datestockout' placeholder='Date Stock Out'>";
                cell8.innerHTML = "<input type='number' name='totalboxes[]' id='totalboxes' placeholder='Total Boxes'>";
                cell9.innerHTML = "<input type='number' name='totalweight[]' id='totalweight' placeholder='Total Weight'>";
                cell10.innerHTML = "<input type='number' name='totalcost[]' id='totalcost' placeholder='Total Cost'>";
                cell11.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteTableRow(this)'>X</button>";
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