<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$oid =  00 + rand(1000000000, 9999999999);

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
    <section class=" panel">
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
                                    <td><input type="text" name="oid" value="<?php echo $oid; ?>" readonly required></td>
                                    <td><input type="date" name="orderdate" value="<?= date('Y-m-d') ?>" required></td>
                                    <td><input type="text" name="salesperson" id="<?php echo $_SESSION['username']; ?>" value="<?= $_SESSION['username'] ?>" readonly required></td>
                                    <td><input type="text" name="status" placeholder="Status" value="Pending" readonly required></td>
                                    <?php $username = $_SESSION["username"] ?>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="table-header ">
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
                                    <td><input type="text" name="customername" placeholder="Customer Name" required></td>
                                    <td><input type="text" name="customeraddress" placeholder="Customer Address" required></td>
                                    <td><input type="text" name="customerphonenumber" placeholder="Customer Phone" required></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="table-header ">
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
                        <table id="customerOrderTable">
                            <thead>
                                <th>Product Code </th> <!-- productcode -->
                                <th>Product Name</th> <!-- productname -->
                                <th>Product Price</th> <!-- productprice  -->
                                <th>Weight perpiece (Kg) </th> <!-- weightperpiece -->
                                <th>Pieces perbox</th> <!-- piecesperbox -->
                                <th>Quantity</th> <!-- quantity -->
                                <th>Type</th> <!-- type -->
                                <th>Total Weight</th> <!-- totalweight -->
                                <th>Total Price</th> <!-- totalprice -->
                                <th>Action</th>
                            </thead>

                            <tbody>
                                <?php
                                $productcodesql = "SELECT productcode FROM productlist";
                                $result = $conn->query($productcodesql);
                                $productcodes = array();
                                while ($row = $result->fetch_assoc()) {
                                    array_push($productcodes, $row['productcode']);
                                }
                                for ($i = 0; $i < 1; $i++):
                                ?>
                                    <tr>
                                        <td>
                                            <select name="productcode[]" id="productcode<?= $i ?>" row-id="<?= $i ?>" class="select-search" onchange="fetchProductInfo(this)" required>
                                                <option value="" disabled selected>ProductCode</option>
                                                <?php foreach ($productcodes as $productcode): ?>
                                                    <option value="<?= $productcode ?>"><?= $productcode ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="productname[]" id="productname<?= $i ?>" placeholder="Product Name" readonly></td>
                                        <td><input type="text" name="productprice[]" id="productprice<?= $i ?>" placeholder="Product Price" readonly></td>
                                        <td><input type="text" name="productweight[]" id="productweight<?= $i ?>" placeholder="Product Weight" readonly></td>
                                        <td><input type="text" name="piecesperbox[]" id="piecesperbox<?= $i ?>" placeholder="Pieces per Box" readonly></td>
                                        <td>
                                            <input type="number" name="quantity[]" placeholder="Quantity" min="1" onchange="calculateTotal(this)">
                                        </td>
                                        <td>
                                            <select name="quantityType[]" id="quantityType<?= $i ?>">
                                                <option value="per_piece">Per Piece</option>
                                                <option value="per_box">Per Box</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="totalweight[]" id="totalweight<?= $i ?>" placeholder="Total Weight" readonly></td>

                                        <td><input type="text" name="ordertotal[]" id="ordertotal<?= $i ?>" placeholder="Total Price" readonly></td>
                                        <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
                                    </tr>
                                    <script>
                                        document.getElementById("productcode<?= $i ?>").addEventListener("change", function() {
                                            var productCode = this.value;
                                            var xhttp = new XMLHttpRequest();
                                            xhttp.onreadystatechange = function() {
                                                if (this.readyState === 4 && this.status === 200) {
                                                    var response = JSON.parse(this.responseText);
                                                    document.getElementById("productname<?= $i ?>").value = response.productname;
                                                    document.getElementById("productweight<?= $i ?>").value = response.productweight;
                                                    document.getElementById("productprice<?= $i ?>").value = response.productprice;
                                                }
                                            };
                                            xhttp.open("GET", "get_product_info.php?productcode=" + productCode, true);
                                            xhttp.send();
                                        });
                                    </script>
                                <?php endfor; ?>
                            </tbody>

                        </table>
                        <div class="bottom-form">

                            <div class="order-summary">
                                <div>
                                    <span>Total Order Weight:</span>
                                    <span id="totalOrderWeight">0.00</span>
                                </div>
                                <div>
                                    <span>Total Order Amount:</span>
                                    <span id="ordertotal">0.00</span>
                                </div>
                            </div>

                        </div>
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
                var rowIndex = 0;

                function addTableRow() {
                    var tableRef = document.getElementById('customerOrderTable');
                    var newRow = tableRef.insertRow();
                    rowIndex++;

                    var columns = ['productcode', 'productname', 'productweight', 'quantity', 'quantityType', 'productprice', 'piecesperbox', 'totalweight', 'ordertotal'];
                    columns.forEach((key, i) => {
                        var newCell = newRow.insertCell(i);
                        var newText;
                        switch (key) {
                            case 'quantityType':
                                newText = document.createElement('select');
                                newText.name = `${key}[]`;
                                newText.innerHTML = "<option value='per_piece'>Per Piece</option><option value='per_box'>Per Box</option>";
                                break;
                            case 'ordertotal':
                                newText = document.createElement('input');
                                newText.type = 'text';
                                newText.name = `${key}[]`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.readOnly = true;
                                break;
                            case 'productcode':
                                newText = document.createElement('select');
                                newText.name = `${key}[]`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.id = `productcode${rowIndex}`;
                                newText.rowId = rowIndex;
                                newText.onchange = function() {
                                    getProductInfo(this);
                                }
                                newText.innerHTML = `<option value="" disabled selected>ProductCode</option>`;
                                <?php foreach ($productcodes as $productcode): ?>
                                    newText.innerHTML += `<option value="<?= $productcode ?>"><?= $productcode ?></option>`;
                                <?php endforeach; ?>
                                break;
                            case 'productname':
                            case 'productweight':   
                            case 'productprice':
                            case 'piecesperbox':
                                newText = document.createElement('input');
                                newText.type = 'text';
                                newText.name = `${key}[]`;
                                newText.id = `${key}${rowIndex}`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.readOnly = true;
                                break;
                            case 'quantity':
                                newText = document.createElement('input');
                                newText.type = 'number';
                                newText.name = `${key}[]`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.id = `quantity${rowIndex}`;
                                newText.oninput = function() {
                                    if (this.value <= 0) {
                                        this.value = 1;
                                    }
                                    calculateTotal(this);
                                }
                                break;
                            default:
                                break;
                        }
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

                function getProductInfo(select) {
                    var rowId = select.rowId;
                    var productCode = select.value;

                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = JSON.parse(this.responseText);
                            var productNameElement = document.getElementById(`productname${rowId}`);
                            var productPriceElement = document.getElementById(`productprice${rowId}`);
                            var productWeightElement = document.getElementById(`productweight${rowId}`);
                            var piecesPerBoxElement = document.getElementById(`piecesperbox${rowId}`);
                            if (productNameElement && productWeightElement && productPriceElement) {
                                productNameElement.value = response.productname;
                                productPriceElement.value = response.productprice;
                                productWeightElement.value = response.productweight;
                                piecesPerBoxElement.value = response.piecesperbox;
                            } else {
                                console.error(`add.customerorder.php:${rowId} HTML elements with IDs 'productname${rowId}', 'productweight${rowId}' and 'productprice${rowId}' not found in the DOM`);
                            }
                        }
                    };

                    xmlhttp.open("GET", "get_product_info.php?productcode=" + productCode, true);
                    xmlhttp.send();
                }

                function runGetProductStock(element, event) {
                    if (event.key === 'Enter') {
                        var productCode = element.value;
                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {
                            if (this.readyState === 4 && this.status === 200) {
                                var response = JSON.parse(this.responseText);
                                element.nextElementSibling.value = response.productName;
                                element.nextElementSibling.nextElementSibling.value = response.stock;
                                element.nextElementSibling.nextElementSibling.nextElementSibling.value = response.productprice;
                                console.log("add.customerorder.php: Product stock retrieved for product code: " + productCode);
                            }
                        };
                        xhttp.open("GET", "check_product_stock.php?productcode=" + productCode, true);
                        xhttp.send();
                    }
                }

                function calculateTotal(inputElement) {
                    var row = inputElement.parentNode.parentNode;
                    var quantityType = row.querySelector('select[name="quantityType[]"]').value;
                    var quantity = parseFloat(inputElement.value) || 0;
                    var priceCell = row.querySelector('input[name="productprice[]"]');
                    var price = parseFloat(priceCell.value) || 0;

                    var totalPieces = 0;
                    if (quantityType === 'per_box') {
                        // Assume a box contains 10 pieces for calculation
                        totalPieces += quantity * 10;
                    } else {
                        totalPieces += quantity;
                    }

                    var total = totalPieces * price;
                    // Update total price somewhere if needed
                    row.querySelector('input[name="totalprice[]"]').value = total.toFixed(2);
                }
            </script>
            <script>
                function calculateTotal(element) {
                    var row = element.closest('tr');
                    var quantity = row.querySelector('input[name="quantity[]"]').value;
                    var productWeight = row.querySelector('input[name="productweight[]"]').value;
                    var productPrice = row.querySelector('input[name="productprice[]"]').value;

                    var totalWeight = parseFloat(quantity) * parseFloat(productWeight);
                    var totalPrice = parseFloat(quantity) * parseFloat(productPrice);

                    row.querySelector('input[name="totalprice[]"]').value = totalPrice.toFixed(2);

                    updateOrderTotalAndWeight();
                }

                function updateOrderTotalAndWeight() {
                    var table = document.getElementById('customerOrderTable');
                    var totalOrderWeight = 0;
                    var totalOrderAmount = 0;

                    Array.from(table.rows).forEach(row => {
                        var totalWeightCell = row.querySelector('input[name="productweight[]"]');
                        var totalPriceCell = row.querySelector('input[name="totalprice[]"]');
                        if (totalWeightCell && totalPriceCell) {
                            totalOrderWeight += parseFloat(totalWeightCell.value || 0);
                            totalOrderAmount += parseFloat(totalPriceCell.value || 0);
                        }
                    });

                    document.getElementById('totalOrderWeight').textContent = totalOrderWeight.toFixed(2);
                    document.getElementById('totalOrderAmount').textContent = totalOrderAmount.toFixed(2);
                }
            </script>

    </section>

</body>
<?php include_once("../reusable/footer.php"); ?>