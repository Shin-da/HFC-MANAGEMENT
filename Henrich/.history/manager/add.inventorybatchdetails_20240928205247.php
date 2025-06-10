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
     make the form fetch the productcode from table products, to display the weight and price realtime


        <script>
            // Function for adding new row on the table. 
            function addTableRow() {
                var table = document.getElementById("inventorybatchdetails");
                var row = table.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);

                cell1.innerHTML = "<input type='input' name='ibdid[]' id='ibdid' placeholder='IBD ID'>";
                cell2.innerHTML = "<input type='text' name='batchid[]' id='batchid' placeholder='Batch ID'>";
                cell3.innerHTML = "<input type='text' name='productCode[]' id='productCode' placeholder='Product Code'>";
                cell4.innerHTML = "<input type='number' name='quantity[]' id='quantity' placeholder='Quantity'>";
                cell5.innerHTML = "<input type='number' name='weight[]' id='weight' placeholder='Weight'>";
                cell6.innerHTML = "<input type='number' name='price[]' id='price' placeholder='Price'>";
                cell7.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteRow(this)'>X</button>";
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