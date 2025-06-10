<?php require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php'; 
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>INVENTORY</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/form.css">
</head>

<body>
    <?php include '../reusable/sidebar.php';    // Sidebar  
    ?>

    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR  
        ?>

        <div class="container-fluid">
            <div class="table-header" style="border-left: 10px solid var(--accent-color);">
                <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                <h2>ADD TO INVENTORY</h2>
                <h3>Batch Details</h3>
            </div>
            <div class="container-fluid" style="overflow-y: scroll; ">
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
                            $productcodesql = "SELECT  productcode FROM productlist";
                            $result = $conn->query($productcodesql);
                            $productcodes = array();
                            while ($row = $result->fetch_assoc()) {
                                array_push($productcodes, $row['productcode']);
                            }
                            for ($i = 0; $i < 1; $i++):
                            ?>
                                <tr>
                                    <td class="short"><input type="input" name="ibdid[]" id="<?= $lastibdid + $i ?>" placeholder="IBD ID" value="<?= $lastibdid + $i ?>" readonly disabled></td>
                                        <input type="hidden" name="batchid[]" id="<?= $batchid ?>" placeholder="Batch ID" value="<?= $batchid ?>" readonly >
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
                                        document.addEventListener('DOMContentLoaded', function() {
                                            $('.select-search').select2({
                                                placeholder: 'Select a Product Code',
                                                allowClear: true
                                            });
                                        });
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
                                    <input type="hidden" name="dateencoded[]" id="dateencoded<?= $i ?>" placeholder="Date Encoded" required value="<?= date('Y-m-d') ?>" readonly disabled > 
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

        <script>
            // Function for adding new row on the table. 
            var lastibdid = <?= $lastibdid ?> + 1;

            function addTableRow() {
                var table = document.getElementById("stockmovement");
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
                cell8.innerHTML = "<input type='number' name='numberofbox[]' id='numberofbox" + lastibdid + "' placeholder='Number of Box' required onchange='computeTotalWeight(" + lastibdid + ")'>";
                cell9.innerHTML = "<input type='number' name='totalpieces[]' id='totalpieces" + lastibdid + "' placeholder='Total Pieces' readonly>";
                cell10.innerHTML = "<input type='number' name='totalweight[]' id='totalweight" + lastibdid + "' placeholder='Total Weight' readonly>";
                cell11.innerHTML = "<input type='hidden' name='productcategory[]' id='productcategory" + lastibdid + "' placeholder='Product Category' value=''>";
                cell11.style.display = "none";

                cell12.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteTableRow(this)'>X</button>";

                lastibdid++;
                document.getElementById("productcode" + (lastibdid - 1)).focus();
            }

            function deleteTableRow(row) {
                var table = document.getElementById("stockmovement");
                table.deleteRow(row.parentNode.parentNode.rowIndex);
            }
        </script>
        <script>
            // Assuming you have a function called fetchProductInfo() that is called when the product code is changed
            function fetchProductInfo(select) {
                console.log("fetchProductInfo called");
                var rowId = select.getAttribute("row-id");
                var productCode = select.value;

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var responseText = this.responseText;
                        var response = JSON.parse(responseText);
                        var productNameElement = document.getElementById("productname" + rowId);
                        var productWeightElement = document.getElementById("productweight" + rowId);
                        if (productNameElement && productWeightElement) {
                            productNameElement.value = response.productname;
                            productWeightElement.value = response.productweight;
                            computeTotalWeight(rowId);
                            computeTotalWeightOfAllRows(); // Add this line

                        } else {
                            console.error("HTML elements with IDs 'productname" + rowId + "' and 'productweight" + rowId + "' not found in the DOM");
                        }
                    }
                };

                xmlhttp.open("GET", "get_product_info.php?productcode=" + productCode, true);
                xmlhttp.send();
            }
            // Function to compute total weight
            function computeTotalWeight(rowId) {
                var numberOfBoxes = document.getElementById("numberofbox" + rowId).value;
                var piecesPerBox = document.getElementById("piecesperbox" + rowId).value;
                var weightPerPiece = document.getElementById("productweight" + rowId).value;

                var totalPieces = numberOfBoxes * piecesPerBox;
                var totalWeight = totalPieces * weightPerPiece;

                document.getElementById("totalpieces" + rowId).value = totalPieces;
                document.getElementById("totalweight" + rowId).value = totalWeight;

                computeTotalPiecesOfAllRows();
                computeTotalWeightOfAllRows();
            }

            // Function to compute pieces
            function computePieces(rowId) {
                var numberOfBoxes = document.getElementById("quantityperbox" + rowId).value;
                var quantityPerPiece = document.getElementById("quantityperpiece" + rowId).value;
                var pieces = numberOfBoxes / quantityPerPiece;
                document.getElementById("pieces" + rowId).value = pieces;
            }


            // Function to compute total pieces
            function computeTotalPiecesOfAllRows() {
                var totalPieces = 0;
                var totalPiecesInputs = document.getElementsByName("totalpieces[]");
                for (var i = 0; i < totalPiecesInputs.length; i++) {
                    totalPieces += parseFloat(totalPiecesInputs[i].value);
                }

                console.log("Total pieces:", totalPieces);
                document.getElementById("overalltotalpieces").value = totalPieces.toFixed(2);
                console.log("Done computing total pieces of all rows.");
            }

            // Function to compute total weight
            function computeTotalWeightOfAllRows() {
                var totalWeight = 0;
                var totalWeightInputs = document.getElementsByName("totalweight[]");
                for (var i = 0; i < totalWeightInputs.length; i++) {
                    totalWeight += parseFloat(totalWeightInputs[i].value);
                }

                console.log("Total weight:", totalWeight);
                document.getElementById("overalltotalweight").value = totalWeight.toFixed(2);
                console.log("Done computing total weight of all rows.");
            }
        </script>


    </section>
</body>
<?php include_once("../reusable/footer.php"); ?>

</html>


