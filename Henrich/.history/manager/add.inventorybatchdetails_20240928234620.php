<?php require '../reusable/redirect404.php'; require '../session/session.php'; require '../database/dbconnect.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>INVENTORY</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/form.css">
</head>

<body>
    <?php include '../reusable/sidebar.php';    // Sidebar  ?>

    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR  ?>
        <div class="container-fluid">
            <div class="table-header">
                <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                <h2>ADD TO INVENTORY</h2>
                <h3>Batch Details</h3>
            </div>

            <div class="container-fluid" style="overflow-y: scroll">
                <form action="./process/add.inventorybatchdetails.process.php" method="POST">
                    <table id="inventorybatchdetails">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Batch ID</th>
                                <th>Product Code</th>
                                <th>Quantity</th>
                                <th>Weight</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <script>
                            // Set the date format to yyyy-mm-dd
                            document.querySelectorAll('input[type="date"]').forEach(el => {
                                el.value = new Date().toISOString().slice(0, 10).replace(/-/g, '-');
                            });

                            // Function to fetch product info from database when product code is changed
                            function fetchProductInfo() {
                                var productcode = event.target.value;
                                var xhttp = new XMLHttpRequest();
                                xhttp.onreadystatechange = function() {
                                    if (this.readyState == 4 && this.status == 200) {
                                        var response = JSON.parse(this.responseText);
                                        document.getElementById("weight" + event.target.id.split("productcode")[1]).value = response.weight;
                                        document.getElementById("price" + event.target.id.split("productcode")[1]).value = response.price;
                                    }
                                };
                                xhttp.open("GET", "../database/fetchProductInfo.php?productcode=" + productcode, true);
                                xhttp.send();
                            }
                        </script>
                        <tbody>
                            <?php
                            $productcodesql = "SELECT productcode FROM products";
                            $result = $conn->query($productcodesql);
                            $productcodes = array();
                            while ($row = $result->fetch_assoc()) {
                                array_push($productcodes, $row['productcode']);
                            }
                            for ($i = 0; $i < 1; $i++):
                            ?>
                            <tr>
                                <td><input type="input" name="ibdid[]" id="ibdid<?= $i ?>" placeholder="IBD ID"></td>
                                <td><input type="text" name="batchid[]" id="batchid<?= $i ?>" placeholder="Batch ID"></td>
                                <td>
                                    <select name="productcode[]" id="productcode<?= $i ?>" oninput="fetchProductInfo()">
                                        <?php foreach ($productcodes as $productcode): ?>
                                        <option value="<?= $productcode ?>"><?= $productcode ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="quantity[]" id="quantity<?= $i ?>" placeholder="Quantity"></td>
                                <td><input type="number" name="weight[]" id="weight<?= $i ?>" placeholder="Weight"></td>
                                <td><input type="number" name="price[]" id="price<?= $i ?>" placeholder="Price"></td>
                                <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
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
                            <input type="submit" value="Add Inventory" name="addInventoryBatch" class="btn btn-primary" style="flex: 1">
                        </div>
                    </div>
                </form>

            </div>
        </div>


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
                cell3.innerHTML = "<input type='text' name='productCode[]' id='productcode' placeholder='Product Code'>";
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