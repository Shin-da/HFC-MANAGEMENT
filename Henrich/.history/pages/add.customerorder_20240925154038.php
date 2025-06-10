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
        <div class="container-fluid">
            <div class="title">
                <h2>Add Customer Order</h2>
            </div>

            <style>
                table {
                    border-collapse: collapse;
                    width: 100%;
                    overflow-x: auto;
                }

                th, td {
                    text-align: left;
                    padding: 8px;
                }

                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }

                th {
                    background-color: var(--accent-color);
                    color: white;
                }
            </style>

            <div class="container-fluid">
                <form action="add.customerorder.process.php" method="POST">
                    <table id="customerOrderTable">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Customer Email</th>
                                <th>Customer Address</th>
                                <th>Customer Phone</th>
                                <th>Customer Order Status</th>
                                <th>Order Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 10; $i++): ?>
                            <tr>
                                <td><input type="text" name="customerName[]" placeholder="Customer Name"></td>
                                <td><input type="text" name="customerEmail[]" placeholder="Customer Email"></td>
                                <td><input type="text" name="customerAddress[]" placeholder="Customer Address"></td>
                                <td><input type="text" name="customerPhone[]" placeholder="Customer Phone"></td>
                                <td><input type="text" name="customerOrderStatus[]" placeholder="Customer Order Status"></td>
                                <td><input type="text" name="orderName[]" placeholder="Order Name"></td>
                                <td><input type="number" name="quantity[]" placeholder="Quantity"></td>
                                <td><input type="number" name="price[]" placeholder="Price"></td>
                                <td><input type="date" name="date[]" placeholder="Date"></td>
                                <td><input type="time" name="time[]" placeholder="Time"></td>
                                <td><input type="text" name="description[]" placeholder="Description"></td>
                                <td><input type="text" name="status[]" placeholder="Status"></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <input type="submit" value="Add Customer Order" name="addCustomerOrder" class="btn btn-primary">
                    <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Row</button>
                </form>
            </div>
        </div>

        <script>
            function addTableRow() {
                var tableRef = document.getElementById('customerOrderTable');
                var newRow = tableRef.insertRow();

                var newCell = newRow.insertCell(0);
                var newText = document.createElement('input');
                newText.type = "text";
                newText.name = "customerName[]";
                newText.placeholder = "Customer Name";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(1);
                var newText = document.createElement('input');
                newText.type = "text";
                newText.name = "customerEmail[]";
                newText.placeholder = "Customer Email";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(2);
                var newText = document.createElement('input');
                newText.type = "text";
                newText.name = "customerAddress[]";
                newText.placeholder = "Customer Address";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(3);
                var newText = document.createElement('input');
                newText.type = "text";
                newText.name = "customerPhone[]";
                newText.placeholder = "Customer Phone";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(4);
                var newText = document.createElement('input');
                newText.type = "text";
                newText.name = "customerOrderStatus[]";
                newText.placeholder = "Customer Order Status";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(5);
                var newText = document.createElement('input');
                newText.type = "text";
                newText.name = "orderName[]";
                newText.placeholder = "Order Name";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(6);
                var newText = document.createElement('input');
                newText.type = "number";
                newText.name = "quantity[]";
                newText.placeholder = "Quantity";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(7);
                var newText = document.createElement('input');
                newText.type = "number";
                newText.name = "price[]";
                newText.placeholder = "Price";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(8);
                var newText = document.createElement('input');
                newText.type = "date";
                newText.name = "date[]";
                newText.placeholder = "Date";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(9);
                var newText = document.createElement('input');
                newText.type = "time";
                newText.name = "time[]";
                newText.placeholder = "Time";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(10);
                var newText = document.createElement('input');
                newText.type = "text";
                newText.name = "description[]";
                newText.placeholder = "Description";
                newCell.appendChild(newText);

                var newCell = newRow.insertCell(11);
                var newText = document.createElement('input');
                newText.type = "text";
                newText.name = "status[]";
                newText.placeholder = "Status";
                newCell.appendChild(newText);
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