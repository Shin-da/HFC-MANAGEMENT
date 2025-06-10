<script src="../resources/js/script.js"></script>
<script src="../resources/js/chart.js"></script>
<!-- ======= Charts JS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="js/chartsJS.js"></script>

<!-- ======= Sweet Alert JS ====== -->
<script src="sweetalert2.min.js"></script>
<link rel="stylesheet" href="sweetalert2.min.css">

<script>
                var rowIndex = 0;

                function addTableRow() {
                    var tableRef = document.getElementById('customerOrderTable');
                    var newRow = tableRef.insertRow();
                    rowIndex++;

                    var columns = ['productcode', 'productname', 'productprice', 'productweight', 'quantity', 'totalweight', 'totalprice'];
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
                            if (productNameElement && productWeightElement && productPriceElement) {
                                productNameElement.value = response.productname;
                                productPriceElement.value = response.productprice;
                                productWeightElement.value = response.productweight;
                            } else {
                                console.error(`add.customerorder.php:${rowId} HTML elements with IDs 'productname${rowId}', 'productweight${rowId}' and 'productprice${rowId}' not found in the DOM`);
                            }
                        }
                    };

                    xmlhttp.open("GET", "./process/get_product_info.php?productcode=" + productCode, true);
                    xmlhttp.send();
                }

                function calculateTotal(inputElement) {
                    var row = inputElement.closest('tr');
                    var quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
                    var productWeight = parseFloat(row.querySelector('input[name="productweight[]"]').value) || 0;
                    var productPrice = parseFloat(row.querySelector('input[name="productprice[]"]').value) || 0;

                    var totalWeight = quantity * productWeight;
                    var totalPrice = quantity * productPrice;

                    row.querySelector('input[name="totalweight[]"]').value = totalWeight.toFixed(2);
                    row.querySelector('input[name="totalprice[]"]').value = totalPrice.toFixed(2);

                    updateOrderTotalAndWeight();
                }

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
            </script>





