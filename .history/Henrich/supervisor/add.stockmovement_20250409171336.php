<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

// Get product codes
$productcodesql = "SELECT productcode FROM products";
$result = $conn->query($productcodesql);
$productcodes = [];
while ($row = $result->fetch_assoc()) {
    $productcodes[] = $row['productcode'];
}

// Initialize page
Page::setTitle('Add Stock Movement');
Page::setBodyClass('supervisor-body');
Page::setCurrentPage('add-stockmovement');

// Add required styles and scripts
Page::addStyle('../assets/css/variables.css');
Page::addStyle('../assets/css/forms.css');
Page::addStyle('../assets/css/table.css');
Page::addStyle('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
Page::addStyle('../assets/css/stock-movement.css');
Page::addStyle('../assets/css/inventory-master.css');

// Add jQuery before other scripts
Page::addScript('https://code.jquery.com/jquery-3.7.0.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
Page::addScript('../assets/js/stock-movement.js');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');

// Add theme-related styles and scripts
Page::addStyle('/assets/css/themes.css');
Page::addStyle('/assets/css/theme-toggle.css');
Page::addScript('/assets/js/theme.js');

try {
    // Get last IDs
    $lastIbdId = $conn->query("SELECT MAX(ibdid) AS max_ibdid FROM stockmovement")->fetch_assoc()['max_ibdid'] ?? 0;
    $lastBatchId = $conn->query("SELECT MAX(batchid) AS max_batchid FROM stockactivitylog")->fetch_assoc()['max_batchid'] ?? 0;

    // Get product codes
    $productCodes = $conn->query("SELECT productcode FROM products");

    ob_start();
?>

<div class="stock-management-wrapper theme-aware">
    <div class="dashboard-header">
        <div class="welcome-section">
            <div class="title-section">
                <h1>Add Stock Movement</h1>
                <p class="subtitle">Add new inventory to the system</p>
            </div>
        </div>
    </div>

    <div class="content-container">
        <div class="stock-form theme-container">
            <!-- Form Header -->
            <div class="form-header">
                <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                <h2>ADD TO INVENTORY</h2>
                <h3>Batch Details</h3>
            </div>

            <!-- Form Content -->
            <form action="./process/add.stockmovement.process.php" method="POST">

                <?php
                $ibdidSql = "SELECT MAX(ibdid) AS max_ibdid FROM stockmovement";
                $result = $conn->query($ibdidSql);
                $lastibdid = $result->num_rows > 0 ? (int)$result->fetch_assoc()['max_ibdid'] + 1 : 1;
                $ibdid = isset($_POST['ibdid'][0]) ? $_POST['ibdid'][0] : $lastibdid;
                ?>

                <?php
                $batchidSql = "SELECT MAX(batchid) AS max_batchid FROM stockactivitylog";
                $result = $conn->query($batchidSql);
                $lastbatchid = $result->num_rows > 0 ? (int)$result->fetch_assoc()['max_batchid'] + 1 : 1;
                $batchid = isset($_POST['batchid'][0]) ? $_POST['batchid'][0] : $lastbatchid;
                ?>

                <table>
                    <style>
                        .whiteth th {
                            background-color: var(--sidebar-color);
                            color: var(--text-color);
                            border-color: transparent;
                        }
                    </style>
                    <thead>
                        <tr class="whiteth">
                            <th>Batch ID</th>
                            <th>Date of Arrival</th>
                            <th>Date Encoded</th>
                            <th>Encoder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="batchid" id="<?php echo $batchid; ?>" value="<?php echo $batchid; ?>" readonly required></td>
                            <td><input type="date" name="dateofarrival" id="dateofarrival" value="<?php echo date('Y-m-d'); ?>" required></td>
                            <td><input type="date" name="dateencoded" id="dateencoded" value="<?php echo date('Y-m-d'); ?>" required></td>
                            <td><input type="text" name="encoder" id="<?php echo $_SESSION['role']; ?>" value="<?= $_SESSION['role'] ?>" readonly required></td>
                            <?php $role = $_SESSION['role'] ?>
                        </tr>
                    </tbody>
                </table>

                <table id="stockmovement">
                    <thead>
                        <tr>
                            <th>Inventory Batch ID</th> <!-- ibdid / -->
                            <th>Product Code</th> <!-- productcode / -->
                            <th>Product Name</th> <!-- productname / -->
                            <th>Weight perpiece (Kg) </th> <!-- weightperpiece -->
                            <th>Pieces perbox</th> <!-- piecesperbox -->
                            <th>Number of box</th> <!-- numberofbox / -->
                            <th>Total Pieces</th> <!-- totalpieces / -->
                            <th>Total Weight (Kg)</th> <!-- totalweight / -->
                            <th> Action</th>

                        </tr>

                        <style>
                            .short {
                                width: 100px;
                            }
                        </style>
                    <tbody>
                        <?php
                        $productcodesql = "SELECT  productcode FROM products";
                        $result = $conn->query($productcodesql);
                        $productcodes = array();
                        while ($row = $result->fetch_assoc()) {
                            array_push($productcodes, $row['productcode']);
                        }
                        for ($i = 0; $i < 1; $i++):
                        ?>
                            <tr>
                                <td class="short"><input type="input" name="ibdid[]" id="<?= $lastibdid + $i ?>" placeholder="IBD ID" value="<?= $lastibdid + $i ?>" readonly></td>
                                <input type="hidden" name="batchid[]" id="<?= $batchid ?>" placeholder="Batch ID" value="<?= $batchid ?>" readonly>
                                <input type="hidden" name="encoder[]" id="<?= $role ?>" placeholder="Encoder" value="<?= $role ?>" readonly disabled>
                                <td>
                                    <select name="productcode[]" id="productcode<?= $i ?>" row-id="<?= $i ?>" class="select-search" onchange="fetchProductInfo(this)" required>
                                        <option value="" disabled selected>ProductCode</option>
                                        <?php foreach ($productcodes as $productcode): ?>
                                            <option value="<?= $productcode ?>"><?= $productcode ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <script>
                                    $(document).ready(function() {
                                        $('.select-search').select2({
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
                                                },
                                                cache: true
                                            }
                                        }).on('select2:select', function(e) {
                                            const rowId = $(this).attr('row-id');
                                            const data = e.params.data;
                                            
                                            // Directly populate fields from the Select2 response
                                            $(`#productname${rowId}`).val(data.productname);
                                            $(`#productweight${rowId}`).val(data.productweight);
                                            $(`#piecesperbox${rowId}`).val(data.piecesperbox);
                                            
                                            // Trigger calculation
                                            computeTotalWeight(rowId);
                                        });
                                    });

                                    // Update the fetchProductInfo function
                                    function fetchProductInfo(element) {
                                        var rowId = $(element).attr('row-id');
                                        var productCode = element.value;

                                        $.ajax({
                                            url: './process/get_inventory_product_info.php',
                                            data: { productcode: productCode },
                                            dataType: 'json',
                                            success: function(response) {
                                                if (response.success) {
                                                    $(`#productname${rowId}`).val(response.productname);
                                                    $(`#productweight${rowId}`).val(response.productweight);
                                                    $(`#piecesperbox${rowId}`).val(response.piecesperbox);
                                                    computeTotalWeight(rowId);
                                                }
                                            }
                                        });
                                    }

                                    // Update addTableRow to include proper row-id attribute
                                    function addTableRow() {
                                        // ... existing code ...
                                        if (key === 'productcode') {
                                            newText = document.createElement('select');
                                            newText.name = `${key}[]`;
                                            newText.id = `productcode${rowIndex}`;
                                            newText.setAttribute('row-id', rowIndex); // Add this line
                                            newText.className = 'select-search';
                                            // ... rest of the productcode case ...
                                        }
                                        // ... rest of existing code ...
                                    }
                                </script>
                                <td><input type="text" name="productname[]" id="productname<?= $i ?>" placeholder="Product Name" readonly></td>
                                <td><input type="number" name="productweight[]" id="productweight<?= $i ?>" placeholder="Product Weight" readonly min="0"></td>
                                <td class="short">
                                    <input type="number" name="piecesperbox[]" id="piecesperbox<?= $i ?>" placeholder="Pieces per Box" value="25" readonly required min="0">
                                </td>
                                <td>
                                    <input type="number" name="numberofbox[]" id="numberofbox<?= $i ?>" placeholder="Number of Box" min="0" required onchange="computeTotalWeight(<?= $i ?>)">
                                </td>
                                <td><input type="number" name="totalpieces[]" id="totalpieces<?= $i ?>" placeholder="Total Pieces" readonly min="0"></td>
                                <td><input type="number" name="totalweight[]" id="totalweight<?= $i ?>" placeholder="Total Weight" readonly min="0"></td>
                                <td style="display: none;"><input type="hidden" name="productcategory[]" id="productcategory<?= $i ?>" readonly disabled></td>
                                <input type="hidden" name="dateencoded[]" id="dateencoded<?= $i ?>" placeholder="Date Encoded" required value="<?= date('Y-m-d') ?>" readonly disabled>
                                <td><button type="button" class="btn btn-danger" onclick="deleteTableRow(this)">X</button></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                    <table>
                        <tr>
                            <td style="text-align: right !important; margin-right: 5px;">
                                <label for="overalltotalpieces"> Total Pieces: </label>
                                <input type="number" id="overalltotalpieces" style="width: 100px;" readonly required onchange="computeOverallTotalPieces()">
                            </td>
                            <td style="text-align: right !important; margin-right: 5px;">
                                <label for="overalltotalweight">Total Weight: </label>
                                <input type="number" id="overalltotalweight" style="width: 100px;" readonly required onchange="computeOverallTotalWeight()">
                            </td>
                        </tr>
                    </table>
                </table>


                <div class="bottom-form">

                    <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Row</button>
                </div>
                <div class="bottom-form">
                    <div class="buttons">
                        <input type="reset" value="Reset" name="reset" class="btn btn-danger" style="flex: 1"></input>
                        <input type="submit" value="Add Inventory" name="addInventoryBatch" class="btn btn-primary" style="flex: 1">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .stock-form {
        padding: 20px;
        background: var(--background-color);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .select2-container {
        width: 100% !important;
    }

    table input, table select {
        width: 100%;
        padding: 8px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
    }

    .bottom-form {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    .buttons {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        border: none;
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        color: white;
        border: none;
    }

    .btn-danger {
        background-color: var(--danger-color);
        color: white;
        border: none;
    }
</style>

<script>
    const formData = {
        lastIbdId: <?= $lastIbdId + 1 ?>,
        lastBatchId: <?= $lastBatchId + 1 ?>,
        currentUser: '<?= $_SESSION['role'] ?>',
        currentDate: '<?= date('Y-m-d') ?>'
    };
    function addTableRow() {
        var tableRef = document.getElementById('stockmovement');
        var tbody = tableRef.querySelector('tbody');
        var lastIbdId = parseInt(document.querySelector('input[name="ibdid[]"]:last-of-type')?.value || formData.lastIbdId);
        var newRow = tbody.insertRow();
            var row = table.insertRow();
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
            var cell9 = row.insertCell(8);
            var cell10 = row.insertCell(9);
            var cell11 = row.insertCell(10);
            var cell12 = row.insertCell(11);
            var cell13 = row.insertCell(12);
            var cell14 = row.insertCell(13);


            cell1.innerHTML = "<input type='input' name='ibdid[]' id='ibdid" + lastibdid + "' placeholder='IBD ID' value='" + lastibdid + "' readonly disabled>";
            cell2.style.display = "none";
            cell3.style.display = "none";
            cell2.innerHTML = "<input type='text' name='batchid[]' id='batchid" + lastibdid + "' placeholder='Batch ID' value='" + <?= $batchid ?> + "' readonly disabled>";
            cell3.innerHTML = "<input type='text' name='encoder[]' id='<?= $role ?>' placeholder='Encoder' value='<?= $role ?>' readonly disabled>";
            cell4.innerHTML = "<select name='productcode[]' id='productcode" + lastibdid + "' row-id='" + lastibdid + "' onchange='fetchProductInfo(this)' required><option value='' disabled selected>ProductCode<option><?php foreach ($productcodes as $productcode): ?><option value='<?= $productcode ?>'><?= $productcode ?></option><?php endforeach; ?></select>";
            cell5.innerHTML = "<input type='text' name='productname[]' id='productname" + lastibdid + "' placeholder='Product Name' readonly>";
            cell6.innerHTML = "<input type='number' name='productweight[]' id='productweight" + lastibdid + "' placeholder='Product Weight' readonly>";
            cell7.innerHTML = "<input type='number' name='piecesperbox[]' id='piecesperbox" + lastibdid + "' placeholder='Pieces per Box' value='25' readonly required>";
            cell8.innerHTML = "<input type='number' name='numberofbox[]' id='numberofbox" + lastibdid + "' placeholder='Number of Box' required onchange='computeTotalWeight(" + lastibdid + ")'";
            cell9.innerHTML = "<input type='number' name='totalpieces[]' id='totalpieces" + lastibdid + "' placeholder='Total Pieces' readonly>";
            cell10.innerHTML = "<input type='number' name='totalweight[]' id='totalweight" + lastibdid + "' placeholder='Total Weight' readonly>";
            cell11.innerHTML = "<input type='hidden' name='productcategory[]' id='productcategory" + lastibdid + "' placeholder='Product Category' value=''>";
            cell11.style.display = "none";

            cell12.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteTableRow(this)'>X</button>";

            lastibdid++;
            document.getElementById("productcode" + (lastibdid - 1)).focus();

            // Initialize Select2 for the new row
            initializeSelect2('#productcode' + (lastibdid - 1));
        }

        function deleteTableRow(row) {
            Alerts.confirm(
                'Delete Row',
                'Are you sure you want to remove this item?',
                function() {
                    var table = document.getElementById("stockmovement");
                    table.deleteRow(row.parentNode.parentNode.rowIndex);
                    computeTotalPiecesOfAllRows();
                    computeTotalWeightOfAllRows();
                    Alerts.toast('Row deleted successfully');
                }
            );
        }
    // Add this function after your existing JavaScript code
    function computeTotalWeight(rowId) {
        const numberofbox = parseInt(document.getElementById(`numberofbox${rowId}`).value) || 0;
        const piecesperbox = parseInt(document.getElementById(`piecesperbox${rowId}`).value) || 0;
        const productweight = parseFloat(document.getElementById(`productweight${rowId}`).value) || 0;
        
        // Calculate total pieces
        const totalpieces = numberofbox * piecesperbox;
        document.getElementById(`totalpieces${rowId}`).value = totalpieces;
        
        // Calculate total weight
        const totalweight = totalpieces * productweight;
        document.getElementById(`totalweight${rowId}`).value = totalweight.toFixed(2);
        
        // Update overall totals
        computeOverallTotals();
    }

    function computeOverallTotals() {
        let overallTotalPieces = 0;
        let overallTotalWeight = 0;
        
        // Get all rows
        const rows = document.querySelectorAll('#stockmovement tbody tr');
        rows.forEach(row => {
            const totalPieces = parseInt(row.querySelector('input[name="totalpieces[]"]').value) || 0;
            const totalWeight = parseFloat(row.querySelector('input[name="totalweight[]"]').value) || 0;
            
            overallTotalPieces += totalPieces;
            overallTotalWeight += totalWeight;
        });
        
        // Update the overall totals
        document.getElementById('overalltotalpieces').value = overallTotalPieces;
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

    // Add this new form submit handler
    document.querySelector('form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            // Close loading state
            Swal.close();

            if (data.status === 'error') {
                throw new Error(data.message);
            }

            // Show success message
            await Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Stock movement added successfully',
                confirmButtonText: 'OK'
            });

            // Redirect after success
            if (data.redirect) {
                window.location.href = data.redirect;
            }

        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'An unexpected error occurred'
            });
        }
    });
</script>

<script>
$(document).ready(function() {
    function initializeSelect2() {
        $('.select-search').select2({
            placeholder: 'Search for a product...',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: './process/get_inventory_products.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || ''
                    };
                },
                processResults: function(data) {
                    console.log('Received data:', data); // Debug log
                    return {
                        results: data.results || []
                    };
                },
                cache: false // Disable cache for testing
            }
        }).on('select2:select', function(e) {
            const rowId = $(this).attr('row-id');
            const data = e.params.data;
            console.log('Selected data:', data); // Debug log
            
            $(`#productname${rowId}`).val(data.productname);
            $(`#productweight${rowId}`).val(data.productweight);
            $(`#piecesperbox${rowId}`).val(data.packsperbox);
            
            computeTotalWeight(rowId);
        });
    }

    // Initialize Select2 on page load
    initializeSelect2();
    
    // Make function globally available
    window.initializeSelect2 = initializeSelect2;
});
</script>

<?php
    $content = ob_get_clean();
    Page::render($content);
} catch (Exception $e) {
    error_log("Stock movement error: " . $e->getMessage());
    echo "An error occurred while loading the stock movement form.";
}
?>