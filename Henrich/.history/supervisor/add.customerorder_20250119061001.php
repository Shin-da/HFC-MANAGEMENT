<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

error_log("add.customerorder.php: Page accessed by user " . $_SESSION["username"]);

// Generate a unique order ID with timestamp and random number
// Format: SO-YYYYMMDD-XXXX (SO = Sales Order)
$orderid = 'SO-' . date('Ymd') . '-' . sprintf("%04d", mt_rand(1, 9999));

?>
<!DOCTYPE html>
<html>

<head>
    <title>ADD CUSTOMER ORDER</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/form.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/customer-order.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <?php require '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <div class="order-container">
            <div class="order-header">
                <h2>Add Customer Order</h2>
                <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
            </div>

            <form action="./process/add.customerorder.process.php" method="POST">
                <!-- Order Details Section -->
                <div class="order-section">
                    <h3>Order Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Order ID</label>
                            <input type="text" class="form-control" name="orderid" value="<?php echo $orderid; ?>" readonly required>
                        </div>
                        <div class="form-group">
                            <label>Order Date</label>
                            <input type="date" class="form-control" name="orderdate" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Salesperson</label>
                            <input type="text" class="form-control" name="salesperson" value="<?= $_SESSION['username'] ?>" readonly required>
                        </div>
                        <div class="form-group">
                            <label>Order Type</label>
                            <select name="ordertype" class="form-control" required>
                                <option value="Walk-in" selected>Walk-in</option>
                                <option value="Delivery">Delivery</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Customer Details Section -->
                <div class="order-section">
                    <h3>Customer Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Customer Name <span class="required">*</span></label>
                            <input type="text" class="form-control" name="customername" required>
                        </div>
                        <div class="form-group">
                            <label>Customer Address <span class="required">*</span></label>
                            <input type="text" class="form-control" name="customeraddress" required>
                        </div>
                        <div class="form-group">
                            <label>Customer Phone <span class="required">*</span></label>
                            <input type="text" class="form-control" name="customerphonenumber" required>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="order-section">
                    <h3>Products</h3>
                    <div class="table-responsive">
                        <table id="customerOrderTable" class="product-table">
                            <thead>
                                <th>Product Code </th> <!-- productcode -->
                                <th>Product Name</th> <!-- productname -->
                                <th>Product Price</th> <!-- productprice  -->
                                <th>Weight per pack (Kg) </th> <!-- weightperpiece -->
                                <th>Quantity</th> <!-- quantity -->
                                <th>Available Quantity</th> <!-- availablequantity -->
                                <th>Stock Status</th> <!-- stock status -->
                                <th>Remaining Quantity</th> <!-- Add this new column -->
                                <th>Total Weight</th> <!-- totalweight -->
                                <th>Total Price</th> <!-- totalprice -->
                                <th>Action</th>
                            </thead>

                            <tbody>
                                <?php
                                $productcodesql = "SELECT productcode FROM products";
                                $result = $conn->query($productcodesql);
                                $productcodes = array();
                                while ($row = $result->fetch_assoc()) {
                                    $productcodes[] = $row['productcode'];
                                }
                                for ($i = 0; $i < 1; $i++):
                                ?>
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
                    </div>
                    
                    <div class="action-buttons">
                        <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Product</button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="summary-row">
                        <span>Total Order Weight:</span>
                        <span><span id="totalOrderWeight">0.00</span> Kg</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Order Amount:</span>
                        <span>₱<span id="ordertotal">0.00</span></span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="action-buttons">
                    <button type="reset" class="btn btn-danger">Reset</button>
                    <button type="submit" name="addCustomerOrder" class="btn btn-primary">Add Customer Order</button>
                </div>
            </form>
        </div>
        
<?php include_once("../reusable/footer.php"); ?>
    </section>

    <script>
        var rowIndex = 0;

        function addTableRow() {
            var tableRef = document.getElementById('customerOrderTable');
            var newRow = tableRef.insertRow();
            rowIndex++;

            var columns = ['productcode', 'productname', 'productprice', 'productweight', 'quantity', 'availablequantity', 'stockstatus', 'remainingquantity', 'totalweight', 'totalprice'];
            columns.forEach((key, i) => {
                var newCell = newRow.insertCell(i);
                var newText;
                switch (key) {

                    case 'totalprice':
                        newText = document.createElement('input');
                        newText.type = 'text';
                        newText.name = `${key}[]`;
                        newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                        newText.readOnly = true;
                        break;
                    case 'totalweight':
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
                        newText = document.createElement('input');
                        newText.type = 'text';
                        newText.name = 'productname[]';
                        newText.id = `productname${rowIndex}`;
                        newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                        newText.readOnly = true;
                        break;
                    case 'productweight':
                        newText = document.createElement('input');
                        newText.type = 'text';
                        newText.name = 'productweight[]';
                        newText.id = `productweight${rowIndex}`;
                        newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                        newText.readOnly = true;
                        break;
                    case 'productprice':
                        newText = document.createElement('input');
                        newText.type = 'text';
                        newText.name = 'productprice[]';
                        newText.id = `productprice${rowIndex}`;
                        newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                        newText.readOnly = true;
                        break;
                    case 'quantity':
                        newText = document.createElement('input');
                        newText.type = 'number';
                        newText.name = 'quantity[]';
                        newText.id = `quantity${rowIndex}`;
                        newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                        newText.oninput = function() {
                            if (this.value <= 0) {
                                this.value = 1;
                            }
                            calculateTotal(this);
                        }
                        break;
                    case 'availablequantity':
                        newText = document.createElement('input');
                        newText.type = 'number';
                        newText.name = 'availablequantity[]';
                        newText.id = `availablequantity${rowIndex}`;
                        newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                        newText.readOnly = true;
                        break;
                    case 'stockstatus':
                        newText = document.createElement('input');
                        newText.type = 'text';
                        newText.name = 'stockstatus[]';
                        newText.id = `stockstatus${rowIndex}`;
                        newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                        newText.readOnly = true;
                        break;
                        // Add remaining quantity field
                    case 'remainingquantity':
                        newText = document.createElement('input');
                        newText.type = 'text';
                        newText.name = 'remainingquantity[]';
                        newText.id = `remainingquantity${rowIndex}`;
                        newText.placeholder = 'Remaining';
                        newText.className = 'remaining-quantity';
                        newText.readOnly = true;
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

            var newProductCodeSelect = document.getElementById(`productcode${rowIndex}`);
            if (newProductCodeSelect) {
                newProductCodeSelect.addEventListener('change', function() {
                    getProductInfo(this);
                });
            }
            initializeSelect2('#productcode' + (rowIndex));
        }
        // Trigger the change event on the newly added product code select
        var newProductCodeSelect = document.getElementById(`productcode${rowIndex}`);
        if (newProductCodeSelect) {
            var event = new Event('change');
            newProductCodeSelect.dispatchEvent(event);
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
                    if (productNameElement && productWeightElement && productPriceElement) {
                        productNameElement.value = response.productname;
                        productPriceElement.value = response.productprice;
                        productWeightElement.value = response.productweight;
                    }

                    var quantityXhttp = new XMLHttpRequest();
                    quantityXhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            try {
                                var cleanResponse = this.responseText.trim();
                                if (cleanResponse.includes('{')) {
                                    cleanResponse = cleanResponse.substring(cleanResponse.indexOf('{'));
                                }
                                var quantityResponse = JSON.parse(cleanResponse);

                                var availableQuantityElement = document.getElementById(`availablequantity${rowId}`);
                                var stockStatusElement = document.getElementById(`stockstatus${rowId}`);

                                if (availableQuantityElement && stockStatusElement) {
                                    availableQuantityElement.value = Number(quantityResponse.availablequantity);
                                    stockStatusElement.value = quantityResponse.stockstatus;
                                }
                            } catch (e) {
                                console.error("Error parsing quantity response:", e);
                            }
                        }
                    };
                    quantityXhttp.open("GET", "./process/check_available_quantity.php?productcode=" + encodeURIComponent(productCode), true);
                    quantityXhttp.send();
                }
            };
            xmlhttp.open("GET", "./process/get_product_info.php?productcode=" + productCode, true);
            xmlhttp.send();
        }

        function calculateTotal(inputElement) {
            var row = inputElement.closest('tr');
            var quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
            var availableQuantity = parseFloat(row.querySelector('input[name="availablequantity[]"]').value) || 0;
            
            // Check if quantity exceeds available stock
            if (quantity > availableQuantity) {
                Swal.fire({
                    icon: 'error',
                    title: 'Insufficient Stock',
                    text: `Only ${availableQuantity} items available in stock!`,
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    inputElement.value = availableQuantity; // Reset to maximum available
                    calculateTotal(inputElement); // Recalculate with corrected quantity
                });
                return;
            }
        
            var productWeight = parseFloat(row.querySelector('input[name="productweight[]"]').value) || 0;
            var productPrice = parseFloat(row.querySelector('input[name="productprice[]"]').value) || 0;
        
            // Calculate remaining quantity
            var remainingQuantity = availableQuantity - quantity;
            var remainingElement = row.querySelector('input[name="remainingquantity[]"]');
            if (remainingElement) {
                remainingElement.value = remainingQuantity;
                if (remainingQuantity < 5) {
                    remainingElement.style.color = 'red';
                    remainingElement.style.fontWeight = 'bold';
                    if (remainingQuantity <= 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Warning',
                            text: 'This order will deplete the stock completely!',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                } else {
                    remainingElement.style.color = 'green';
                    remainingElement.style.fontWeight = 'normal';
                }
            }
        
            var totalWeight = quantity * productWeight;
            var totalPrice = quantity * productPrice;
        
            row.querySelector('input[name="totalweight[]"]').value = totalWeight.toFixed(2);
            row.querySelector('input[name="totalprice[]"]').value = totalPrice.toFixed(2);
        
            updateOrderTotalAndWeight();
        }
        
        // Add form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            var rows = document.querySelectorAll('#customerOrderTable tbody tr');
            var hasInsufficientStock = false;
            
            rows.forEach(row => {
                var quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
                var availableQuantity = parseFloat(row.querySelector('input[name="availablequantity[]"]').value) || 0;
                
                if (quantity > availableQuantity) {
                    hasInsufficientStock = true;
                }
            });
        
            if (hasInsufficientStock) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Order Cannot Be Processed',
                    text: 'One or more items have insufficient stock!',
                    confirmButtonColor: '#3085d6'
                });
            }
        });

        function updateOrderTotalAndWeight() {
            var table = document.getElementById('customerOrderTable');
            var totalOrderWeight = 0;
            var ordertotal = 0;

            Array.from(table.rows).forEach(row => {
                var totalWeightCell = row.querySelector('input[name="totalweight[]"]');
                var totalPriceCell = row.querySelector('input[name="totalprice[]"]');
                if (totalWeightCell && totalPriceCell) {
                    totalOrderWeight += parseFloat(totalWeightCell.value || 0);
                    ordertotal += parseFloat(totalPriceCell.value || 0);
                }
            });

            document.getElementById('totalOrderWeight').textContent = totalOrderWeight.toFixed(2);
            document.getElementById('ordertotal').textContent = ordertotal.toFixed(2);
        }

        document.getElementById("productcode<?= $i ?>").addEventListener("change", function() {
            var productCode = this.value;
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.availablequantity <= 0) {
                        alert("Product is out of stock!");
                    }
                }
            };
            xhttp.open("GET", "./process/check_available_quantity.php?productcode=" + productCode, true);
            xhttp.send();
        });

        function initializeSelect2(element) {
            $(element).select2({
                placeholder: 'Search for a product...',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: 'get_products.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        if (data.error) {
                            console.error('Error:', data.message);
                            return { results: [] };
                        }

                        return {
                            results: data.results.map(item => ({
                                id: item.id,
                            console.error('Invalid data format:', data);
                            return { results: [] };
                        }

                        return {
                            results: data.results.map(item => ({
                                id: item.id,
                                text: item.text,
                                productname: item.productname,
                                productprice: item.productprice,
                                productweight: item.productweight,
                                availablequantity: item.availablequantity || 0,
                                stockstatus: getStockStatus(item.availablequantity || 0)
                            }))
                        };
                    }
                },
                templateResult: formatProduct,
                templateSelection: formatProductSelection
            }).on('select2:select', function(e) {
                const rowId = $(this).attr('row-id');
                const data = e.params.data;
                
                // Safely update fields with null checks
                if (data && rowId) {
                    $(`#productname${rowId}`).val(data.productname || '');
                    $(`#productprice${rowId}`).val(data.productprice || '0');
                    $(`#productweight${rowId}`).val(data.productweight || '0');
                    $(`#availablequantity${rowId}`).val(data.availablequantity || '0');
                    $(`#stockstatus${rowId}`).val(data.stockstatus || 'Unknown');
                }
            });
        }

        function formatProduct(product) {
            if (product.loading) return 'Searching...';
            if (!product.id) return product.text;
            
            const qty = product.availablequantity || 0;
            const status = getStockStatus(qty);
            const color = getStockColor(qty);
            
            return $(`
                <div class="select2-result-product">
                    <div class="product-info">
                        <strong>${product.id}</strong>
                        <div>${product.productname || ''}</div>
                    </div>
                    <div class="stock-info" style="color: ${color}">
                        ${qty} pcs (${status})
                    </div>
                </div>
            `);
        }

        function formatProductSelection(product) {
            if (!product.id) return product.text;
            return product.text || `${product.id}`;
        }

        // Initialize on document ready with error handling
        $(document).ready(function() {
            try {
                $('.select-search').each(function() {
                    initializeSelect2(this);
                });
            } catch (error) {
                console.error('Select2 initialization error:', error);
            }
        });
    </script>
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
    <style>
        .select2-container {
            width: 100% !important;
        }
        .select2-selection {
            height: 38px !important;
            padding: 5px;
        }
        .select2-result-product {
            padding: 5px;
        }
        .select2-result-product strong {
            color: #333;
            font-size: 14px;
        }
        .select2-result-product small {
            color: #666;
            display: block;
            margin-top: 3px;
        }
        .select2-result-product {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .product-info {
            flex: 1;
        }

        .stock-info {
            text-align: right;
            font-size: 0.85em;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .select2-selection__rendered .stock-dot {
            margin-bottom: 2px;
        }

        /* Stock status colors */
        .stock-status-low { color: orange; }
        .stock-status-out { color: red; }
        .stock-status-good { color: green; }
    </style>
</body>
</html>