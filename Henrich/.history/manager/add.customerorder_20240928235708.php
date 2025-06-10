
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
    <link href="../resources/css/form.css" rel="stylesheet" >
</head>

<body>
    <?php require '../reusable/sidebar.php';  // Sidebar ?> 
    <section class=" panel">
        <?php  include '../reusable/navbarNoSearch.html';// TOP NAVBAR ?>
        
        <div class="container-fluid">
            <div class="table-header">
                <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                <h2>Add Customer Order</h2>
            </div>

            <div class="container-fluid" style="overflow-y: scroll;">
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
                                    <td><input type="text" name="customerOrderStatus[]" placeholder=" Order Status"></td>
                                    <td><input type="text" name="orderName[]" placeholder="Order Name"></td>
                                    <td>
                                        <select name="quantityType[]" style="width: 60%">
                                            <option value="box"> box</option>
                                            <option value="pack"> pack</option>
                                            <input type="number" name="quantity[]" value="1" style="width: 40%">
                                        </select>
                                    </td>
                                    <td><input type="number" name="price[]" placeholder="Price"></td>
                                    <td>
                                        <input type="date" name="date[]" placeholder="Date" value="<?php echo date('Y-m-d'); ?>">
                                    </td>
                                    <td><input type="time" name="time[]" placeholder="Time"></td>
                                    <td><input type="text" name="description[]" placeholder="Description"></td>
                                    <td>
                                        <input type="input" name="status[]" value="pending">
                                    </td>
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