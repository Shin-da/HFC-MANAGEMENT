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
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            overflow-x: auto;
        }

        th,
        td {
            text-align: left;
            padding: 2px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        th {
            background-color: var(--grey-active);
            color: white;
        }

        input {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 1px;
            box-sizing: border-box;
        }

        .btn {
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            border-radius: 1px;
        }

        .btn-primary {
            background-color: var(--blue-color);
        }

        .btn-secondary {
            background-color: var(--panel-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn:hover {
            background-color: var(--accent-color-dark);
        }

        .bottom-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;

            input {}
        }
    </style>
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
                <h2>Add Customer Order</h2>
            </div>

            <div class="c">
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 1; $i++): ?>
                                <tr>
                                    <td><input type="text" name="customerName[]" placeholder=" Name"></td>
                                    <td><input type="text" name="customerEmail[]" placeholder=" Email"></td>
                                    <td><input type="text" name="customerAddress[]" placeholder=" Address"></td>
                                    <td><input type="text" name="customerPhone[]" placeholder=" Phone"></td>
                                    <td><input type="text" name="customerOrderStatus[]" placeholder=" Order Status"></td>
                                    <td><input type="text" name="orderName[]" placeholder="Order Name"></td>
                                    <td><input type="number" name="quantity[]" placeholder="Quantity"></td>
                                    <td><input type="number" name="price[]" placeholder="Price"></td>
                                    <td><input type="date" name="date[]" placeholder="Date"></td>
                                    <td><input type="time" name="time[]" placeholder="Time"></td>
                                    <td><input type="text" name="description[]" placeholder="Description"></td>
                                    <td><input type="text" name="status[]" placeholder="Status"></td>
                                    <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>

                    </table>
                    <div class="bottom-form">
                        <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Row</button>
                    </div>
                    <div class="bottom-form">
                        <div style="display: flex; justify-content: space-between; ">
                            <input type="reset" value="Reset" name="reset" class="btn btn-danger" style="flex: 1"></input>
                            <input type="submit" value="Add Customer Order" name="addCustomerOrder" class="btn btn-primary" style="flex: 1">
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <script>
            function addTableRow() {
                var tableRef = document.getElementById('customerOrderTable');
                var newRow = tableRef.insertRow();

                ['customerName', 'customerEmail', 'customerAddress', 'customerPhone', 'customerOrderStatus', 'orderName', 'quantity', 'price', 'date', 'time', 'description', 'status'].forEach((key, i) => {
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