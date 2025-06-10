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

            <style>
                table {
                    border-collapse: collapse;
                    width: 100%;
                }

                th, td {
                    text-align: left;
                    padding: 8px;
                }

                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }

                th {
                    background-color: #4CAF50;
                    color: white;
                }
            </style>

            <div class="container-fluid">
                <form action="add.customerorder.process.php" method="POST">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer Order Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
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
                    <input type="submit" value="Add Customer Order" name="addCustomerOrder" class="btn btn-primary">
                </form>
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