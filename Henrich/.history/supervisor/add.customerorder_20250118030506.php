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
                                        <th>Order Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tr>
                                    <td><input type="text" name="oid" value="<?php echo $oid; ?>" readonly required></td>
                                    <td><input type="date" name="orderdate" value="<?= date('Y-m-d') ?>" required></td>
                                    <td><input type="text" name="salesperson" id="<?php echo $_SESSION['username']; ?>" value="<?= $_SESSION['username'] ?>" readonly required></td>
                                    <td>
                                        <select name="ordertype" id="ordertype" required>
                                            <option value="Walk-in" selected>Walk-in</option>
                                            <option value="Delivery">Delivery</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="status" placeholder="Status" value="Pending" readonly required></td>
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
                                    <td><input type="text" name="customername" placeholder="Customer Name" required></td>
                                    <td><input type="text" name="customeraddress" placeholder="Customer Address" required></td>
                                    <td><input type="text" name="customerphonenumber" placeholder="Customer Phone" required></td>
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
                        <table id="customerOrderTable">
                            <thead>
                                <th>Product Code </th> <!-- productcode -->
                                <th>Product Name</th> <!-- productname -->
                                <th>Product Price</th> <!-- productprice  -->
                                <th>Weight per pack (Kg) </th> <!-- weightperpiece -->
                                <th>Quantity</th> <!-- quantity -->
                                <th>Available Quantity</th> <!-- availablequantity -->
                                <th>Stock Status</th> <!-- stock status -->
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
                                    $productcodes[] = $row['productcode'];
                                }
                                for ($i = 0; $i < 1; $i++):
                                ?>
                                    <tr>
                                        <td>
                                            <select id="productcode<?= $i ?>" name="productcode[]">
                                                <?php
                                                foreach ($productcodes as $productcode) {
                                                ?>
                                                    <option value="<?= $productcode ?>"><?= $productcode ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="productname[]" id="productname<?= $i ?>" placeholder="Product Name" readonly></td>
                                        <td><input type="text" name="productprice[]" id="productprice<?= $i ?>" placeholder="Product Price" readonly></td>
                                        <td><input type="text" name="productweight[]" id="productweight<?= $i ?>" placeholder="Product Weight" readonly></td>
                                        <td>
                                            <input type="number" name="quantity[]" placeholder="Quantity" min="1" onchange="calculateTotal(this)">
                                        </td>
                                        <td>
                                            <input type="number" name="availablequantity[]" id="availablequantity0" class="availablequantity" placeholder="Available Quantity" readonly>

                                        </td>
                                        <td>
                                            <input type="text" name="stockstatus[]" id="stockstatus0" class="stockstatus" placeholder="Stock Status" readonly>
                                        </td>
                                        <td><input type="text" name="totalweight[]" id="totalweight<?= $i ?>" placeholder="Total Weight" readonly></td>

                                        <td><input type="text" name="totalprice[]" id="totalprice<?= $i ?>" placeholder="Total Price" readonly></td>
                                        <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
                                    </tr>
                                    <script>
                                        document.getElementById("productcode<?= $i ?>").addEventListener("change", function() {
                                            var productCode = this.value;
                                            var xhttp = new XMLHttpRequest();
                                            xhttp.onreadystatechange = function() {
                                                if (this.readyState === 4 && this.status === 200) {
                                                    try {
                                                        var response = JSON.parse(this.responseText);
                                                        document.getElementById("productname<?= $i ?>").value = response.productname;
                                                        document.getElementById("productweight<?= $i ?>").value = response.productweight;
                                                        document.getElementById("productprice<?= $i ?>").value = response.productprice;
                                                        document.getElementById("availablequantity<?= $i ?>").value = response.availablequantity;
                                                        document.getElementById("stockstatus<?= $i ?>").value = response.stockstatus;
                                                    } catch (e) {
                                                        console.error(`Error parsing response: ${e.name}: ${e.message}. Response text: ${this.responseText}`);
                                                    }
                                                }
                                            };
                                            xhttp.open("GET", "./process/get_product_info.php?productcode=" + productCode, true);
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
                document.addEventListener("DOMContentLoaded", function() {
                    var productCodeSelect = document.getElementById("productcode0");
                    productCodeSelect.addEventListener("change", function() {
                        var productCode = this.value;
                        var rowIndex = this.id.replace("productcode", "");
                        var xhttp = new XMLHttpRequest();

                        xhttp.onreadystatechange = function() {
                            if (this.readyState === 4 && this.status === 200) {
                                try {
                                    // Remove any whitespace or HTML comments before parsing
                                    var cleanResponse = this.responseText.trim();
                                    if (cleanResponse.includes('{')) {
                                        cleanResponse = cleanResponse.substring(cleanResponse.indexOf('{'));
                                    }
                                    
                                    var response = JSON.parse(cleanResponse);
                                    console.log("Clean response:", response);

                                    // Get the correct input fields using the row index
                                    var availableQuantityInput = document.getElementById("availablequantity" + rowIndex);
                                    var stockStatusInput = document.getElementById("stockstatus" + rowIndex);

                                    if (availableQuantityInput && stockStatusInput) {
                                        // Convert to number before setting
                                        availableQuantityInput.value = Number(response.availablequantity);
                                        stockStatusInput.value = response.stockstatus;
                                    }

                                } catch (e) {
                                    console.error("Error parsing response:", e);
                                    console.log("Raw response text:", this.responseText);
                                }
                            }
                        };

                        xhttp.open("GET", "./process/check_available_quantity.php?productcode=" + encodeURIComponent(productCode), true);
                        xhttp.send();
                    });
                });
            </script>
    </section>



</body>
<?php include_once("../reusable/footer.php"); ?>