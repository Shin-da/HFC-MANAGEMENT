<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/Page.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
Page::setCurrentPage($current_page);

// Set page title and styles
Page::setTitle('Add Stock Movement');
Page::addStyle('./assets/css/variables.css');
Page::addStyle('../assets/css/forms.css');
Page::addStyle('./assets/css/table.css');

// Get product codes
$productcodesql = "SELECT productcode FROM products";
$result = $conn->query($productcodesql);
$productcodes = [];
while ($row = $result->fetch_assoc()) {
    $productcodes[] = $row['productcode'];
}

try {
    // Get last IDs
    $lastIbdId = $conn->query("SELECT MAX(ibdid) AS max_ibdid FROM stockmovement")->fetch_assoc()['max_ibdid'] ?? 0;
    $lastBatchId = $conn->query("SELECT MAX(batchid) AS max_batchid FROM stockactivitylog")->fetch_assoc()['max_batchid'] ?? 0;

    // Get product codes
    $productCodes = $conn->query("SELECT productcode FROM products");

    ob_start();
?>

<div class="container-fluid">
    <div class="table-header">
        <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
        <h2>Add Stock Movement</h2>
    </div>

    <div class="container-fluid order-form-container">
        <form action="./process/add.stockmovement.process.php" method="POST">
            <!-- Batch Details Section -->
            <div class="order-details-section">
                <table>
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>Date of Arrival</th>
                            <th>Date Encoded</th>
                            <th>Encoder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="batchid" value="<?= isset($batchid) ? $batchid : '' ?>" readonly required></td>
                            <td><input type="date" name="dateofarrival" value="<?= date('Y-m-d') ?>" required></td>
                            <td><input type="date" name="dateencoded" value="<?= date('Y-m-d') ?>" required></td>
                            <td><input type="text" name="encoder" value="<?= $_SESSION['role'] ?>" readonly required></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Stock Movement Details -->
            <div class="order-section">
                <h3>Product Details</h3>
                <div class="table-responsive">
                    <table id="stockmovement" class="product-table">
                        <thead>
                            <tr>
                                <th>Inventory Batch ID</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Weight per piece (Kg)</th>
                                <th>packs per box</th>
                                <th>Number of box</th>
                                <th>Total packs</th>
                                <th>Total Weight (Kg)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ... existing tbody content ... -->
                        </tbody>
                    </table>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="total-info">
                        <div>
                            <span>Total packs:</span>
                            <span id="overalltotalpacks">0</span>
                        </div>
                        <div>
                            <span>Total Weight:</span>
                            <span id="overalltotalweight">0.00</span> Kg
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Product</button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="submit-buttons">
                    <input type="reset" value="Reset" class="btn btn-danger">
                    <input type="submit" value="Add Stock Movement" name="addInventoryBatch" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
        const formData = {
            lastIbdId: <?= $lastIbdId + 1 ?>,
            lastBatchId: <?= $lastBatchId + 1 ?>,
            currentUser: '<?= $_SESSION['role'] ?>',
            currentDate: '<?= date('Y-m-d') ?>'
        };

        function addTableRow() {
            var table = document.getElementById('stockmovement');
            var tbody = table.getElementsByTagName('tbody')[0];
            var rowCount = tbody.rows.length;
            var nextIbdId = parseInt(document.querySelector('input[name="ibdid[]"]:last-of-type')?.value || formData.lastIbdId) + 1;

            var newRow = tbody.insertRow();
            var rowHtml = `
                <td>
                    <input type="text" name="ibdid[]" id="ibdid${rowCount}" value="${nextIbdId}" readonly>
                </td>
                <td>
                    <select name="productcode[]" id="productcode${rowCount}" row-id="${rowCount}" class="select-search" required>
                        <option value="" disabled selected>Select Product</option>
                    </select>
                </td>
                <td><input type="text" name="productname[]" id="productname${rowCount}" readonly></td>
                <td><input type="number" name="productweight[]" id="productweight${rowCount}" readonly></td>
                <td class="short">
                    <input type="number" name="packsperbox[]" id="packsperbox${rowCount}" value="25" readonly required>
                </td>
                <td>
                    <input type="number" name="numberofbox[]" id="numberofbox${rowCount}" required onchange="computeTotalWeight(${rowCount})">
                </td>
                <td><input type="number" name="totalpacks[]" id="totalpacks${rowCount}" readonly></td>
                <td><input type="number" name="totalweight[]" id="totalweight${rowCount}" readonly></td>
                <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
            `;

            newRow.innerHTML = rowHtml;

            // Focus on the new product code field
            document.getElementById(`productcode${rowCount}`).focus();

            // Initialize Select2 for the new row
            initializeSelect2();
        }

        function deleteTableRow(row) {
            Alerts.confirm(
                'Delete Row',
                'Are you sure you want to remove this item?',
                function() {
                    var table = document.getElementById("stockmovement");
                    table.deleteRow(row.parentNode.parentNode.rowIndex);
                    computeTotalpacksOfAllRows();
                    computeTotalWeightOfAllRows();
                    Alerts.toast('Row deleted successfully');
                }
            );
        }
        // Add this function after your existing JavaScript code
        function computeTotalWeight(rowId) {
            const numberofbox = parseInt(document.getElementById(`numberofbox${rowId}`).value) || 0;
            const packsperbox = parseInt(document.getElementById(`packsperbox${rowId}`).value) || 0;
            const productweight = parseFloat(document.getElementById(`productweight${rowId}`).value) || 0;

            // Calculate total packs
            const totalpacks = numberofbox * packsperbox;
            document.getElementById(`totalpacks${rowId}`).value = totalpacks;

            // Calculate total weight
            const totalweight = totalpacks * productweight;
            document.getElementById(`totalweight${rowId}`).value = totalweight.toFixed(2);

            // Update overall totals
            computeOverallTotals();
        }

        function computeOverallTotals() {
            let overallTotalpacks = 0;
            let overallTotalWeight = 0;

            // Get all rows
            const rows = document.querySelectorAll('#stockmovement tbody tr');
            rows.forEach(row => {
                const totalpacks = parseInt(row.querySelector('input[name="totalpacks[]"]').value) || 0;
                const totalWeight = parseFloat(row.querySelector('input[name="totalweight[]"]').value) || 0;

                overallTotalpacks += totalpacks;
                overallTotalWeight += totalWeight;
            });

            // Update the overall totals
            document.getElementById('overalltotalpacks').value = overallTotalpacks;
            document.getElementById('overalltotalweight').value = overallTotalWeight.toFixed(2);
        }

        // Update the Select2 success handler to trigger calculations
        $('.select-search').on('select2:select', function(e) {
            // ... existing select2:select code ...

            // Add this line at the end of the success callback
            computeTotalWeight(rowId);
        });

        // Update the number of boxes input event listener
        $(document).on('input', 'input[name="numberofbox[]"]', function() {
            const row = $(this).closest('tr');
            const rowId = row.find('select[name="productcode[]"]').attr('row-id');
            computeTotalWeight(rowId);
        });

        // Replace the form submit handler with this updated version
        document.querySelector('form').addEventListener('submit', async function(e) {
            e.preventDefault();

            try {
                // Show loading
                await Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your request',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send request
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this)
                });

                // Parse response
                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (err) {
                    throw new Error(`Invalid server response: ${text}`);
                }

                // Close loading
                await Swal.close();

                // Handle error
                if (data.status === 'error') {
                    throw new Error(data.message);
                }

                // Show process messages
                if (data.messages && Array.isArray(data.messages)) {
                    let delay = 0;
                    for (const msg of data.messages) {
                        await new Promise(resolve => {
                            setTimeout(() => {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                                Toast.fire({
                                    icon: msg.icon,
                                    title: msg.title,
                                    text: msg.text
                                });
                                resolve();
                            }, delay);
                        });
                        delay += 1000;
                    }
                }

                // Redirect after delay
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, delay + 1000);
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for existing rows
            initializeSelect2();

            // Function to initialize Select2
            function initializeSelect2() {
                $('.select-search').each(function() {
                    $(this).select2({
                        placeholder: 'Search for a product...',
                        allowClear: true,
                        minimumInputLength: 1,
                        ajax: {
                            url: './process/get_inventory_products.php', // Changed from get_products.php
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.results || []
                                };
                            },
                            cache: true
                        }
                    });

                    // Move the select2:select event handler inside the initialization
                    $(this).on('select2:select', function(e) {
                        var rowId = $(this).attr('row-id');
                        if (!rowId) {
                            console.error('No row-id found for select element');
                            return;
                        }

                        var productCode = e.params.data.id;
                        $.ajax({
                            url: './process/get_inventory_product_info.php',
                            data: {
                                productcode: productCode
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    $(`#productname${rowId}`).val(response.productname);
                                    $(`#productweight${rowId}`).val(response.productweight);
                                    $(`#packsperbox${rowId}`).val(response.packsperbox);
                                    computeTotalWeight(rowId);
                                }
                            }
                        });
                    });
                });
            }

            // Make initializeSelect2 available globally
            window.initializeSelect2 = initializeSelect2;
        });

        // Update addTableRow function
        function addTableRow() {
            var table = document.getElementById('stockmovement');
            var tbody = table.getElementsByTagName('tbody')[0];
            var rowCount = tbody.rows.length;
            var nextIbdId = parseInt(document.querySelector('input[name="ibdid[]"]:last-of-type')?.value || formData.lastIbdId) + 1;

            var newRow = tbody.insertRow();
            var rowHtml = `
        <td class="short">
            <input type="input" name="ibdid[]" id="ibdid${rowCount}" value="${nextIbdId}" readonly>
        </td>
        <td>
            <select name="productcode[]" id="productcode${rowCount}" row-id="${rowCount}" class="select-search" required>
                <option value="" disabled selected>Select Product</option>
            </select>
        </td>
        <td><input type="text" name="productname[]" id="productname${rowCount}" readonly></td>
        <td><input type="number" name="productweight[]" id="productweight${rowCount}" readonly></td>
        <td class="short">
            <input type="number" name="packsperbox[]" id="packsperbox${rowCount}" value="25" readonly required>
        </td>
        <td>
            <input type="number" name="numberofbox[]" id="numberofbox${rowCount}" required onchange="computeTotalWeight(${rowCount})">
        </td>
        <td><input type="number" name="totalpacks[]" id="totalpacks${rowCount}" readonly></td>
        <td><input type="number" name="totalweight[]" id="totalweight${rowCount}" readonly></td>
        <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
    `;

            newRow.innerHTML = rowHtml;
            initializeSelect2();
        }

        // Fix the Select2 event handler
        $('.select-search').on('select2:select', function(e) {
            const $select = $(this);
            const rowId = $select.attr('row-id');

            if (!rowId) {
                console.error('No row-id found for select element');
                return;
            }

            const productCode = e.params.data.id;

            // Fetch and populate product info
            $.ajax({
                url: './process/get_inventory_product_info.php',
                data: {
                    productcode: productCode
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $(`#productname${rowId}`).val(response.productname);
                        $(`#productweight${rowId}`).val(response.productweight);
                        $(`#packsperbox${rowId}`).val(response.packsperbox);
                        computeTotalWeight(rowId);
                    }
                }
            });
        });

        // ... rest of existing code ...
    </script>

    <script>
        $(document).ready(function() {
            function initializeSelect2() {
                $('.select-search').each(function() {
                    $(this).select2({
                        placeholder: 'Search for a product...',
                        allowClear: true,
                        minimumInputLength: 1,
                        ajax: {
                            url: './process/get_inventory_products.php',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.results || []
                                };
                            }
                        }
                    });
                });

                // Attach event handler separately
                $(document).on('select2:select', '.select-search', function(e) {
                    var $select = $(this);
                    var rowId = $select.attr('row-id');
                    var productCode = e.params.data.id;

                    if (!rowId) {
                        console.error('No row-id found for select element');
                        return;
                    }

                    $.ajax({
                        url: './process/get_inventory_product_info.php',
                        data: {
                            productcode: productCode
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $(`#productname${rowId}`).val(response.productname);
                                $(`#productweight${rowId}`).val(response.productweight);
                                $(`#packsperbox${rowId}`).val(response.packsperbox);
                                computeTotalWeight(rowId);
                            }
                        }
                    });
                });
            }

            // Initialize Select2 and make it globally available
            window.initializeSelect2 = initializeSelect2;
            initializeSelect2();
        });
    </script>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.status === 'error') {
                        throw new Error(data.message);
                    }

                    // Show sequential toasts for each process message
                    if (data.messages && Array.isArray(data.messages)) {
                        let delay = 0;
                        data.messages.forEach(msg => {
                            setTimeout(() => {
                                Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                }).fire({
                                    icon: msg.icon,
                                    title: msg.title,
                                    text: msg.text
                                });
                            }, delay);
                            delay += 1000;
                        });
                    }

                    // Redirect after all toasts
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }, delay + 1000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message
                    });
                });
        });
    </script>

<?php
    $content = ob_get_clean();
    Page::render($content);
} catch (Exception $e) {
    error_log("Stock movement error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>