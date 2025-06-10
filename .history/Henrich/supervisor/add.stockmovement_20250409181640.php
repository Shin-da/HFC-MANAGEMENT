<?php
require_once '../includes/session.php';
require_once '../includes/config.php';

// Check if session is valid
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login/login.php");
    exit();
}

// Get product codes
$productcodesql = "SELECT productcode FROM products";
$result = $conn->query($productcodesql);
$productcodes = [];
while ($row = $result->fetch_assoc()) {
    $productcodes[] = $row['productcode'];
}

// Get last IDs
$lastIbdId = $conn->query("SELECT MAX(ibdid) AS max_ibdid FROM stockmovement")->fetch_assoc()['max_ibdid'] ?? 0;
$lastBatchId = $conn->query("SELECT MAX(batchid) AS max_batchid FROM stockactivitylog")->fetch_assoc()['max_batchid'] ?? 0;

// Get product codes
$productCodes = $conn->query("SELECT productcode FROM products");

$ibdidSql = "SELECT MAX(ibdid) AS max_ibdid FROM stockmovement";
$result = $conn->query($ibdidSql);
$lastibdid = $result->num_rows > 0 ? (int)$result->fetch_assoc()['max_ibdid'] + 1 : 1;
$ibdid = isset($_POST['ibdid'][0]) ? $_POST['ibdid'][0] : $lastibdid;

$batchidSql = "SELECT MAX(batchid) AS max_batchid FROM stockactivitylog";
$result = $conn->query($batchidSql);
$lastbatchid = $result->num_rows > 0 ? (int)$result->fetch_assoc()['max_batchid'] + 1 : 1;
$batchid = isset($_POST['batchid'][0]) ? $_POST['batchid'][0] : $lastbatchid;

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stock Movement</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <link rel="stylesheet" href="../assets/css/table.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/css/inventory-master.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin-layout.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/theme-toggle.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            width: 95%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .header {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button, input[type="submit"], input[type="reset"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }
        button.btn-danger {
            background-color: #f44336;
        }
        button.btn-secondary {
            background-color: #2196F3;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        .short {
            width: 100px;
        }
        h1, h2, h3 {
            color: #333;
        }
        .subtitle {
            color: #666;
        }
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: white;
            background-color: #666;
            padding: 8px 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="javascript:window.history.back()" class="back-button">Back</a>
            <h1>Add Stock Movement</h1>
            <p class="subtitle">Add new inventory to the system</p>
            <h2>ADD TO INVENTORY</h2>
            <h3>Batch Details</h3>
        </div>

        <form action="./process/add.stockmovement.process.php" method="POST">
            <!-- Batch Details Table -->
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
                        <td><input type="text" name="batchid" id="<?php echo $batchid; ?>" value="<?php echo $batchid; ?>" readonly required></td>
                        <td><input type="date" name="dateofarrival" id="dateofarrival" value="<?php echo date('Y-m-d'); ?>" required></td>
                        <td><input type="date" name="dateencoded" id="dateencoded" value="<?php echo date('Y-m-d'); ?>" required></td>
                        <td><input type="text" name="encoder" id="<?php echo $_SESSION['role']; ?>" value="<?= $_SESSION['role'] ?>" readonly required></td>
                    </tr>
                </tbody>
            </table>

            <!-- Stock Movement Table -->
            <table id="stockmovement">
                <thead>
                    <tr>
                        <th>Inventory Batch ID</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Weight perpiece (Kg)</th>
                        <th>Pieces perbox</th>
                        <th>Number of box</th>
                        <th>Total Pieces</th>
                        <th>Total Weight (Kg)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < 1; $i++): ?>
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
            </table>

            <!-- Totals Table -->
            <table>
                <tr>
                    <td style="text-align: right !important; margin-right: 5px;">
                        <label for="overalltotalpieces">Total Pieces:</label>
                        <input type="number" id="overalltotalpieces" style="width: 100px;" readonly required>
                    </td>
                    <td style="text-align: right !important; margin-right: 5px;">
                        <label for="overalltotalweight">Total Weight:</label>
                        <input type="number" id="overalltotalweight" style="width: 100px;" readonly required>
                    </td>
                </tr>
            </table>

            <!-- Bottom Form Buttons -->
            <div class="btn-container">
                <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Row</button>
                <div class="btn-group">
                    <input type="reset" value="Reset" name="reset" class="btn btn-danger">
                    <input type="submit" value="Add Inventory" name="addInventoryBatch" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Function to compute total weight
        function computeTotalWeight(rowId) {
            // Get the input values
            const numberofbox = parseFloat(document.getElementById(`numberofbox${rowId}`).value) || 0;
            const piecesperbox = parseFloat(document.getElementById(`piecesperbox${rowId}`).value) || 0;
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

        // Function to compute overall totals
        function computeOverallTotals() {
            let overallTotalPieces = 0;
            let overallTotalWeight = 0;
            
            // Get all rows
            const rows = document.querySelectorAll('#stockmovement tbody tr');
            rows.forEach(row => {
                const totalPieces = parseFloat(row.querySelector('input[name="totalpieces[]"]').value) || 0;
                const totalWeight = parseFloat(row.querySelector('input[name="totalweight[]"]').value) || 0;
                
                overallTotalPieces += totalPieces;
                overallTotalWeight += totalWeight;
            });
            
            // Update the overall totals
            document.getElementById('overalltotalpieces').value = overallTotalPieces;
            document.getElementById('overalltotalweight').value = overallTotalWeight.toFixed(2);
        }

        // Function to fetch product information
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

        // Function to add a new table row
        function addTableRow() {
            const tableBody = document.querySelector('#stockmovement tbody');
            const rows = tableBody.querySelectorAll('tr');
            const lastRowIndex = rows.length;
            const lastIbdId = parseInt(document.querySelector('input[name="ibdid[]"]:last-of-type').value) + 1;
            
            const newRow = document.createElement('tr');
            
            // Column 1: Inventory Batch ID
            let cell = document.createElement('td');
            cell.className = 'short';
            let input = document.createElement('input');
            input.type = 'input';
            input.name = 'ibdid[]';
            input.id = 'ibdid' + lastIbdId;
            input.placeholder = 'IBD ID';
            input.value = lastIbdId;
            input.readOnly = true;
            cell.appendChild(input);
            newRow.appendChild(cell);
            
            // Hidden inputs for batch ID and encoder
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'batchid[]';
            input.value = '<?= $batchid ?>';
            input.readOnly = true;
            newRow.appendChild(input);
            
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'encoder[]';
            input.value = '<?= $role ?>';
            input.readOnly = true;
            input.disabled = true;
            newRow.appendChild(input);
            
            // Column 2: Product Code
            cell = document.createElement('td');
            let select = document.createElement('select');
            select.name = 'productcode[]';
            select.id = 'productcode' + lastRowIndex;
            select.setAttribute('row-id', lastRowIndex);
            select.className = 'select-search';
            select.setAttribute('onchange', 'fetchProductInfo(this)');
            select.required = true;
            
            let option = document.createElement('option');
            option.value = '';
            option.disabled = true;
            option.selected = true;
            option.textContent = 'ProductCode';
            select.appendChild(option);
            
            <?php foreach ($productcodes as $productcode): ?>
            option = document.createElement('option');
            option.value = '<?= $productcode ?>';
            option.textContent = '<?= $productcode ?>';
            select.appendChild(option);
            <?php endforeach; ?>
            
            cell.appendChild(select);
            newRow.appendChild(cell);
            
            // Column 3: Product Name
            cell = document.createElement('td');
            input = document.createElement('input');
            input.type = 'text';
            input.name = 'productname[]';
            input.id = 'productname' + lastRowIndex;
            input.placeholder = 'Product Name';
            input.readOnly = true;
            cell.appendChild(input);
            newRow.appendChild(cell);
            
            // Column 4: Weight per piece
            cell = document.createElement('td');
            input = document.createElement('input');
            input.type = 'number';
            input.name = 'productweight[]';
            input.id = 'productweight' + lastRowIndex;
            input.placeholder = 'Product Weight';
            input.readOnly = true;
            input.min = '0';
            cell.appendChild(input);
            newRow.appendChild(cell);
            
            // Column 5: Pieces per box
            cell = document.createElement('td');
            cell.className = 'short';
            input = document.createElement('input');
            input.type = 'number';
            input.name = 'piecesperbox[]';
            input.id = 'piecesperbox' + lastRowIndex;
            input.placeholder = 'Pieces per Box';
            input.value = '25';
            input.readOnly = true;
            input.required = true;
            input.min = '0';
            cell.appendChild(input);
            newRow.appendChild(cell);
            
            // Column 6: Number of box
            cell = document.createElement('td');
            input = document.createElement('input');
            input.type = 'number';
            input.name = 'numberofbox[]';
            input.id = 'numberofbox' + lastRowIndex;
            input.placeholder = 'Number of Box';
            input.required = true;
            input.min = '0';
            input.setAttribute('onchange', 'computeTotalWeight(' + lastRowIndex + ')');
            cell.appendChild(input);
            newRow.appendChild(cell);
            
            // Column 7: Total Pieces
            cell = document.createElement('td');
            input = document.createElement('input');
            input.type = 'number';
            input.name = 'totalpieces[]';
            input.id = 'totalpieces' + lastRowIndex;
            input.placeholder = 'Total Pieces';
            input.readOnly = true;
            input.min = '0';
            cell.appendChild(input);
            newRow.appendChild(cell);
            
            // Column 8: Total Weight
            cell = document.createElement('td');
            input = document.createElement('input');
            input.type = 'number';
            input.name = 'totalweight[]';
            input.id = 'totalweight' + lastRowIndex;
            input.placeholder = 'Total Weight';
            input.readOnly = true;
            input.min = '0';
            cell.appendChild(input);
            newRow.appendChild(cell);
            
            // Hidden Product Category
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'productcategory[]';
            input.id = 'productcategory' + lastRowIndex;
            input.readOnly = true;
            input.disabled = true;
            newRow.appendChild(input);
            
            // Hidden Date Encoded
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'dateencoded[]';
            input.id = 'dateencoded' + lastRowIndex;
            input.placeholder = 'Date Encoded';
            input.value = '<?= date('Y-m-d') ?>';
            input.readOnly = true;
            input.disabled = true;
            input.required = true;
            newRow.appendChild(input);
            
            // Column 9: Action Button
            cell = document.createElement('td');
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-danger';
            button.textContent = 'X';
            button.setAttribute('onclick', 'deleteTableRow(this)');
            cell.appendChild(button);
            newRow.appendChild(cell);
            
            // Add the new row to the table
            tableBody.appendChild(newRow);
            
            // Initialize Select2 for the new row
            $(select).select2({
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
                        return {
                            results: data.results || []
                        };
                    },
                    cache: false
                }
            }).on('select2:select', function(e) {
                const rowId = $(this).attr('row-id');
                const data = e.params.data;
                
                $(`#productname${rowId}`).val(data.productname);
                $(`#productweight${rowId}`).val(data.productweight);
                $(`#piecesperbox${rowId}`).val(data.piecesperbox || data.packsperbox);
                
                computeTotalWeight(rowId);
            });
        }

        // Function to delete a table row
        function deleteTableRow(button) {
            if (confirm('Are you sure you want to delete this row?')) {
                const row = button.closest('tr');
                row.remove();
                computeOverallTotals();
            }
        }

        // Initialize Select2 on document ready
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
                            search: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || []
                        };
                    },
                    cache: false
                }
            }).on('select2:select', function(e) {
                const rowId = $(this).attr('row-id');
                const data = e.params.data;
                
                $(`#productname${rowId}`).val(data.productname);
                $(`#productweight${rowId}`).val(data.productweight);
                $(`#piecesperbox${rowId}`).val(data.piecesperbox || data.packsperbox);
                
                computeTotalWeight(rowId);
            });

            // Add input event handler for number of boxes
            $(document).on('input', 'input[name="numberofbox[]"]', function() {
                const rowId = $(this).attr('id').replace('numberofbox', '');
                computeTotalWeight(rowId);
            });
        });
    </script>
</body>
</html>