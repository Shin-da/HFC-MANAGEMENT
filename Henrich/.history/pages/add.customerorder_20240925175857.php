<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>ADD CUSTOMER ORDER</title>
    <?php require '../reusable/header.php'; ?>
/*************  ✨ Codeium Command 🌟  *************/
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                overflow-x: auto;
            }
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            overflow-x: auto;
        }

            thead{
                border-radius: 5px;
                border: 1px solid var(--border-color);
                background-color: var(--grey-active);
            }
            th,
            td {
                text-align: left;
                padding: 2px;
                border-bottom: 1px solid #ddd;
                
            }
        thead{
            border-radius: 5px;
            border: 1px solid var(--border-color);
            background-color: var(--grey-active);
        }
        th,
        td {
            text-align: left;
            padding: 2px;
            border-bottom: 1px solid #ddd;
            
        }

            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

            th {
                color: white;
                padding: 8px;
                text-align: left;
                text-transform: uppercase;
                font-weight: 500;
                font-size: 14px;
                letter-spacing: 1px;
            }
        th {
            color: white;
            padding: 8px;
            text-align: left;
            text-transform: uppercase;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 1px;
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
                border-radius: 5px;
            }
        .btn {
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

            .btn-primary {
                background-color: var(--blue-color);
            }
        .btn-primary {
            background-color: var(--blue-color);
        }

            .btn-secondary {
                background-color: var(--panel-color);
                color: var(--text-color);
            }
        .btn-secondary {
            background-color: var(--panel-color);
            color: var(--text-color);
        }

            .btn-danger {
                background-color: var(--danger-color);
            }
        .btn-danger {
            background-color: var(--danger-color);
        }

            .btn:hover {
                background-color: var(--accent-color-dark);
            }
        .btn:hover {
            background-color: var(--accent-color-dark);
        }

            .bottom-form {
                display: flex;
                justify-content: center;
        .bottom-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;

            .buttons {
                display: flex; 
                justify-content: space-around;
                align-items: center;
                gap: 10px;
                margin-top: 10px;

                .buttons {
                    display: flex; 
                    justify-content: space-around;
                    align-items: center;
                    gap: 10px;
                 
                }
            }

                .btn {
                    width: 40%;
                    border: solid 1px var(--border-color);
                    border-radius: 5px;
                    padding: 10px;
                }
            .btn {
                width: 40%;
                border: solid 1px var(--border-color);
                border-radius: 5px;
                padding: 10px;
            }
        }

        </style>
    </head>
    </style>
</head>

    <body>
<body>
    <?php

    // Sidebar 
    include '../reusable/sidebar.php';
    ?>
    <section class=" panel">
        <?php

        // Sidebar 
        include '../reusable/sidebar.php';
        // TOP NAVBAR
        include '../reusable/navbarNoSearch.html';
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

            <div class="container-fluid">
                <div class="table-header">
                    <h2>Add Customer Order</h2>
                </div>

                <div class="container-fluid">
                    <form action="add.customerorder.process.php" method="POST">
                        <table id="customerOrderTable">
                            <thead>
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
                                <th>Quantity (per box or per piece)</th>
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
                                    <th>Customer Name</th>
                                    <th>Customer Email</th>
                                    <th>Customer Address</th>
                                    <th>Customer Phone</th>
                                    <th>Customer Order Status</th>
                                    <th>Order Name</th>
                                    <th>Quantity (per box or per piece)</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <td><input type="text" name="customerName[]" placeholder=" Name"></td>
                                    <td><input type="text" name="customerEmail[]" placeholder=" Email"></td>
                                    <td><input type="text" name="customerAddress[]" placeholder=" Address"></td>
                                    <td><input type="text" name="customerPhone[]" placeholder=" Phone"></td>
                                    <td><input type="text" name="customerOrderStatus[]" placeholder=" Order Status"></td>
                                    <td><input type="text" name="orderName[]" placeholder="Order Name"></td>
                                    <td>
                                        <input type="number" name="quantity[]" placeholder="Quantity" style="width: 30%">
                                        <select name="quantityType[]" style="width: 40%">
                                            <option value="per box">per box</option>
                                            <option value="per piece">per piece</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="price[]" placeholder="Price"></td>
                                    <td><input type="date" name="date[]" placeholder="Date"></td>
                                    <td><input type="time" name="time[]" placeholder="Time"></td>
                                    <td><input type="text" name="description[]" placeholder="Description"></td>
                                    <td><input type="text" name="status[]" placeholder="Status"></td>
                                    <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
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
                                        <td>
                                            <input type="number" name="quantity[]" placeholder="Quantity" style="width: 40%">
                                            <select name="quantityType[]" style="width: 40%">
                                                <option value="per box">per box</option>
                                                <option value="per piece">per piece</option>
                                            </select>
                                        </td>
                                        <td><input type="number" name="price[]" placeholder="Price"></td>
                                        <td><input type="date" name="date[]" placeholder="Date"></td>
                                        <td><input type="time" name="time[]" placeholder="Time"></td>
                                        <td><input type="text" name="description[]" placeholder="Description"></td>
                                        <td><input type="text" name="status[]" placeholder="Status"></td>
                                        <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                            <?php endfor; ?>
                        </tbody>

                        </table>
                        <div class="bottom-form">
                            <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Row</button>
                    </table>
                    <div class="bottom-form">
                        <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Row</button>
                    </div>
                    <div class="bottom-form">
                        <div class="buttons">
                            <input type="reset" value="Reset" name="reset" class="btn btn-danger" style="flex: 1"></input>
                            <input type="submit" value="Add Customer Order" name="addCustomerOrder" class="btn btn-primary" style="flex: 1">
                        </div>
                        <div class="bottom-form">
                            <div class="buttons">
                                <input type="reset" value="Reset" name="reset" class="btn btn-danger" style="flex: 1"></input>
                                <input type="submit" value="Add Customer Order" name="addCustomerOrder" class="btn btn-primary" style="flex: 1">
                            </div>
                        </div>
                    </form>
                    </div>
                </form>

                </div>
            </div>
        </div>

            <script>
                function addTableRow() {
                    var tableRef = document.getElementById('customerOrderTable');
                    var newRow = tableRef.insertRow();
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
            function deleteTableRow(element) {
                var row = element.parentNode.parentNode;
                row.parentNode.removeChild(row);
            }
        </script>
    </section>
/******  2504137f-296d-47ad-bfc1-a773331c8fe9  *******/

</body>
<script src="../resources/js/script.js"></script>
<script src="../resources/js/chart.js"></script>
<!-- ======= Charts JS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="../resources/js/chartsJS.js"></script>

</html>