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
            </div>

/*************  ✨ Codeium Command 🌟  *************/
            <div class="container-fluid" style="overflow-y: scroll">
                <form action="./process/add.inventory.process.php" method="POST">
                    <table id="customerOrderTable">
                        <thead>
                            <tr>
                                <th>Inventory ID</th>
                                <th>Product Code</th>
                                <th>Product Description</th>
                                <th>Category</th>
                                <th>On Hand</th>
                                <th>Date Updated</th>
                                <th>Customer Name</th>
                                <th>Customer Email</th>
                                <th>Customer Address</th>
                                <th>Customer Phone</th>
                                <th>Order Name</th>
                                <th>Quantity (per box or per piece)</th>
                                <th>Price</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
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
                                <tr>
                                    <td><input type="text" name="customerName[]" placeholder=" Name"></td>
                                    <td><input type="text" name="customerEmail[]" placeholder=" Email"></td>
                                    <td><input type="text" name="customerAddress[]" placeholder=" Address"></td>
                                    <td><input type="text" name="customerPhone[]" placeholder=" Phone"></td>
                                    <td><input type="text" name="orderName[]" placeholder="Order Name"></td>
                                    <td style="">
                                        <select name="quantityType[]" style="">
                                            <option value="per box">per box</option>
                                            <option value="per piece">per piece</option>
                                        </select>
                                        <input type="number" name="quantity[]" placeholder="Quantity" >
                                    </td>
                                    <td><input type="number" name="price[]" placeholder="Price"></td>
                                    <td>
                                        <input type="date" name="date[]" placeholder="Date" value="<?php echo date('Y-m-d'); ?>">
                                    </td>
                                    <td><input type="time" name="time[]" placeholder="Time"></td>
                                    <td><input type="text" name="description[]" placeholder="Description"></td>
                                    <td><input type="input" name="status[]" value="pending"></td>
                                    <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
/******  ac0c32c6-2eb6-4167-b803-066ae6596685  *******/
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
                            <input type="submit" value="Add Customer Order" name="addCustomerOrder" class="btn btn-primary" style="flex: 1">
                        </div>
                    </div>
                </form>

            </div>
        </div>


        <script> 
        // Function for adding new row on the table. 
            function addTableRow() {
                var tableRef = document.getElementById('customerOrderTable');
                var newRow = tableRef.insertRow();

                ['Name', 'Email', 'Address', 'Phone', 'Name', 'quantity', 'price', 'date', 'time', 'description', 'status' ].forEach((key, i) => {
                    var newCell = newRow.insertCell(i);
                    var newText = document.createElement('input');
                    newText.type = key === 'date' ? 'date' : key === 'time' ? 'time' : 'text';
                    newText.name = `${key}[]`;
                    newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                    newCell.appendChild(newText);
                });

                var newCell = newRow.insertCell(12);
                var deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'btn btn-danger';
                deleteButton.onclick = function() {
                    deleteTableRow(this);
                }
                deleteButton.innerText = 'X';
                newCell.appendChild(deleteButton);
            }

            function deleteTableRow(element) {
                var row = element.parentNode.parentNode;
                row.parentNode.removeChild(row);
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