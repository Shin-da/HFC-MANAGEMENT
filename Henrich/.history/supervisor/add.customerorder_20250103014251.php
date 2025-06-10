<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php'); ?>
<!DOCTYPE html>
<html>

<head>
    <title>ADD CUSTOMER ORDER</title>
    <?php require '../reusable/header.php';    ?>
    <link type="text/css" href="../resources/css/form.css" rel="stylesheet">
    <style>
        .whiteth th {
            background-color: var(--sidebar-color);
            color: var(--text-color);
            border-color: transparent;
        }
    </style>
</head>

<body>
    <?php require '../reusable/sidebar.php';  // Sidebar 
    ?>
    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR 
        ?>
        <!-- === ADD CUSTOMER ORDER === -->
        <div>

            <div class="container-fluid" style="border-top:solid 5px yellow;">
                <div class="table-header">
                    <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                    <h2>Add Customer Order</h2>
                </div>

                <div class="container-fluid" style="overflow-y: scroll; ">
                    <form action="./process/add.customerorder.process.php" method="POST">
                        <div style="margin:0px; margin-bottom:40px; " >
                            <table>
                                <thead>
                                    <tr class="whiteth">
                                        <th>Order ID</th>
                                        <th>Order Date</th>
                                        <th>Salesperson</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="customerOrderID[]" placeholder="Customer Order ID" required></td>
                                        <td><input type="date" name="orderDate[]" value="<?= date('Y-m-d') ?>" required></td>
                                        <td><input type="text" name="encoder" id="<?php echo $_SESSION['username']; ?>" value="<?= $_SESSION['username'] ?>" readonly required></td>
                                        <td><input type="text" name="status[]" placeholder="Status" value="Pending" readonly required></td>
                                        <?php $username = $_SESSION["username"] ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <table>
                            <thead>CUSTOMER DETAILS</thead>
                            <thead>
                                <tr class="whiteth">
                                    <th>Customer Name <span style="color:red">*</span></th>
                                    <th>Customer Address <span style="color:red">*</span></th>
                                    <th>Customer Phone <span style="color:red">*</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="customerName[]" placeholder="Customer Name" required></td>
                                    <td><input type="text" name="customerAddress[]" placeholder="Customer Address" required></td>
                                    <td><input type="text" name="customerPhone[]" placeholder="Customer Phone" required></td>
                                </tr>
                            </tbody>
                        </table>

                        <table id="customerOrderTable">
                            <thead>
                                <strong>
                                    PRODUCTS TO ORDER
                                </strong>
                            </thead>
                            <thead>
                                <th>Product Code </th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Action</th>
                                </th>
                            </thead>

                            <tbody>
                                <?php for ($i = 0; $i < 1; $i++): ?>
                                    <tr>
                                        <td><input type="text" name="productCode[]" placeholder="Product Code"></td>
                                        <td><input type="text" name="productName[]" placeholder="Product Name"></td>
                                        <td><input type="text" name="quantity[]" placeholder="Quantity"></td>
                                        <td><input type="text" name="price[]" placeholder="Price"></td>
                                        <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>

                        </table>
                        <div class="bottom-form">
                            <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Product</button>
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

                    ['productCode', 'productName', 'quantity', 'price'].forEach((key, i) => {
                        var newCell = newRow.insertCell(i);
                        var newText = document.createElement('input');
                        newText.type = 'text';
                        newText.name = `${key}[]`;
                        newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                        newCell.appendChild(newText);
                    });

                    var actionCell = newRow.insertCell(-1);
                    var deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.className = 'btn btn-danger';
                    deleteButton.onclick = function() {
                        deleteTableRow(this);
                    }
                    deleteButton.innerText = 'X';
                    actionCell.appendChild(deleteButton);
                }

                function deleteTableRow(element) {
                    var row = element.parentNode.parentNode;
                    row.parentNode.removeChild(row);
                }
            </script>
    </section>

</body>
<?php include_once("../reusable/footer.php"); ?>
