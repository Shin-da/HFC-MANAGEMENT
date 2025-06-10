<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$orderId = rand(1000000000, 9999999999);

error_log("add.customerorder.php: Page accessed by user " . $_SESSION["username"]);

?>
<!DOCTYPE html>
<html>

<head>
    <title>ADD CUSTOMER ORDER</title>
    <?php require '../reusable/header.php'; ?>
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
    <?php require '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <!-- === ADD CUSTOMER ORDER === -->
        <div>
            <div class="container-fluid" style="overflow-y: scroll;">
                <div class="table-header">
                    <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                    <h2>Add Customer Order</h2>
                </div>

                <div class="container-fluid" style="overflow-y: scroll; border-top:solid 5px #fa1;">
                    <form action="./process/add.customerorder.process.php" method="POST">
                        <div style="margin:0px; margin-bottom:40px;">
                            <table>
                                <thead>
                                    <tr class="whiteth">
                                        <th>Order ID</th>
                                        <th>Order Date</th>
                                        <th>Salesperson</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tr>
                                    <td><input type="text" name="customerOrderID[]" value="<?php echo $orderId; ?>" readonly required></td>
                                    <td><input type="date" name="orderDate[]" value="<?= date('Y-m-d') ?>" required></td>
                                    <td><input type="text" name="encoder" id="<?php echo $_SESSION['username']; ?>" value="<?= $_SESSION['username'] ?>" readonly required></td>
                                    <td><input type="text" name="status[]" placeholder="Status" value="Pending" readonly required></td>
                                    <?php $username = $_SESSION["username"] ?>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="table-header">
                            <strong>
                                <thead>CUSTOMER DETAILS</thead>
                            </strong>
                        </div>
                        <table>
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

                        <div class="table-header">
                            <div>
                                <h3>
                                    <strong>
                                        PRODUCTS TO ORDER
                                    </strong>
                                </h3>
                            </div>
                            <div class="search-box">
                                <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
                                <input type="text" id="myInput" onkeyup="searchProduct()" placeholder="Search product...">
                            </div>
                        </div>
                        <div>
                            <!-- searched products will show up here  -->
                        </div>
/*************  ✨ Codeium Command 🌟  *************/
                        <table id="customerOrderTable">
                            <thead>
                                <th>Product Code </th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Action</th>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <select name="productcode[]" id="productcode0" class="select-search" onchange="fetchProductInfo(this)" required>
                                            <option value="" disabled selected>ProductCode</option>
                                            <?php foreach ($productcodes as $productcode): ?>
                                                <option value="<?= $productcode ?>"><?= $productcode ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="productname[]" id="productname0" placeholder="Product Name" readonly></td>
                                    <td><input type="text" name="quantity[]" placeholder="Quantity"></td>
                                    <td><input type="text" name="price[]" id="price0" placeholder="Price" readonly></td>
                                    <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
                                </tr>
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

                    ['productcode', 'productname', 'quantity', 'price'].forEach((key, i) => {
                        var newCell = newRow.insertCell(i);
                        if(key === 'productcode') {
                            var select = document.createElement('select');
                            select.name = `${key}[]`;
                            select.className = 'select-search';
                            select.onchange = function() {
                                fetchProductInfo(this);
                            }
                            <?php foreach ($productcodes as $productcode): ?>
                                var option = document.createElement('option');
                                option.value = '<?= $productcode ?>';
                                option.text = '<?= $productcode ?>';
                                select.appendChild(option);
                            <?php endforeach; ?>
                            newCell.appendChild(select);
                        } else {
                            var newText = document.createElement('input');
                            newText.type = 'text';
                            newText.name = `${key}[]`;
                            newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                            if(key !== 'quantity') newText.readOnly = true;
                            newCell.appendChild(newText);
                        }
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

                function fetchProductInfo(element) {
                    var productCode = element.value;
                    var rowId = element.id.match(/\d+/)[0];
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState === 4 && this.status === 200) {
                            var response = JSON.parse(this.responseText);
                            document.getElementById("productname" + rowId).value = response.productname;
                            document.getElementById("price" + rowId).value = response.price;
                        }
                    };
                    xhttp.open("GET", "get_product_info.php?productcode=" + productCode, true);
                    xhttp.open("GET", "check_product_stock.php?productcode=" + productCode, true);
                    xhttp.send();
                }
            </script>
        </div>
    </section>
<?php include_once("../reusable/footer.php"); ?>



/******  c9fdc1bf-7fec-439e-aac9-0f9dd19e261d  *******/