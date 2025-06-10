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
        
/*************  ✨ Codeium Command 🌟  *************/
        <div class="container">
            <div class="title">
                <h2>Add Customer Order</h2>
            </div>

            <style>
                table {
                    border-collapse: collapse;
                .input-group {
                    display: flex;
                    align-items: center;
                    margin-bottom: 20px;
                }

                .input-group > div {
                    margin-right: 10px;
                }

                .input-group > div:last-child {
                    flex: 1;
                }

                .input-group > div > input {
                    width: 100%;
                }

                th, td {
                    text-align: left;
                    padding: 8px;
                .input-group > div > input[type="date"],
                .input-group > div > input[type="time"] {
                    padding: 10px;
                    border: 1px solid var(--border-color);
                    border-radius: 4px;
                }

                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }

                th {
                    background-color: #4CAF50;
                .input-group > div > input[type="submit"] {
                    background-color: var(--success-color);
                    color: white;
                    border: none;
                    padding: 10px;
                    border-radius: 4px;
                    cursor: pointer;
                }

                .input-group > div > input[type="submit"]:hover {
                    background-color: var(--border-color);
                }
            </style>

            <div class="container">
                <form action="add.customerorder.process.php" method="POST">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer Order Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Supplier Name</th>
                                <th>Supplier Email</th>
                                <th>Supplier Address</th>
                                <th>Supplier Phone</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Customer Name</th>
                                <th>Customer Email</th>
                                <th>Customer Address</th>
                                <th>Customer Phone</th>
                                <th>Customer Order Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 10; $i++): ?>
                            <tr>
                                <td><input type="text" name="customerOrderName[]" placeholder="Customer Order Name"></td>
                                <td><input type="number" name="quantity[]" placeholder="Quantity"></td>
                                <td><input type="number" name="price[]" placeholder="Price"></td>
                                <td><input type="text" name="supplierName[]" placeholder="Supplier Name"></td>
                                <td><input type="text" name="supplierEmail[]" placeholder="Supplier Email"></td>
                                <td><input type="text" name="supplierAddress[]" placeholder="Supplier Address"></td>
                                <td><input type="text" name="supplierPhone[]" placeholder="Supplier Phone"></td>
                                <td><input type="date" name="date[]" placeholder="Date"></td>
                                <td><input type="time" name="time[]" placeholder="Time"></td>
                                <td><input type="text" name="description[]" placeholder="Description"></td>
                                <td><input type="text" name="status[]" placeholder="Status"></td>
                                <td><input type="text" name="customerName[]" placeholder="Customer Name"></td>
                                <td><input type="text" name="customerEmail[]" placeholder="Customer Email"></td>
                                <td><input type="text" name="customerAddress[]" placeholder="Customer Address"></td>
                                <td><input type="text" name="customerPhone[]" placeholder="Customer Phone"></td>
                                <td><input type="text" name="customerOrderStatus[]" placeholder="Customer Order Status"></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
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
                        <input type="text" name="customerName" placeholder="Customer Name">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="customerEmail" placeholder="Customer Email">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="customerAddress" placeholder="Customer Address">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="customerPhone" placeholder="Customer Phone">
                    </div>
                    <div class="input-group">
                        <div class="icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <input type="text" name="customerOrderStatus" placeholder="Customer Order Status">
                    </div>

                    <input type="submit" value="Add Customer Order" name="addCustomerOrder" class="btn btn-primary">
                </form>
            </div>
        </div>
/******  4c5a4d4a-2855-45f8-9a1c-1f72654cfe04  *******/

    </section>

</body>
<script src="../resources/js/script.js"></script>
<script src="../resources/js/chart.js"></script>
<!-- ======= Charts JS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="../resources/js/chartsJS.js"></script>

</html>