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
            <div class="table-header">
                <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                <h2>ADD TO INVENTORY</h2>
                <h3>Batch Details</h3>
            </div>

            <div class="container-fluid" style="overflow-y: scroll">
                <form action="./process/add.stockmovement.process.php" method="POST">
                    
                    <!-- Search Bar -->
                    <div class="search-bar">
                        <input type="text" id="productSearch" placeholder="Search Product Code or Name" oninput="searchProduct(this.value)">
                        <div id="productSearchResults" style="position: absolute; background: white; z-index: 1000;"></div>
                    </div>

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
                            </tr>
                        </tbody>
                    </table>

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
                        </tbody>
                    </table>

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
            // Function to search product by code or name
            function searchProduct(query) {
                if (query.length < 3) {
                    document.getElementById("productSearchResults").innerHTML = "";
                    return;
                }
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("productSearchResults").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "search_product.php?q=" + query, true);
                xmlhttp.send();
            }

            // Function to select product from search results and add to table
            function selectProduct(productCode, productName, productWeight) {
                var table = document.getElementById("stockmovement").getElementsByTagName('tbody')[0];
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

                cell1.innerHTML = "<input type='input' name='ibdid[]' value='" + lastibdid + "' readonly disabled>";
                cell2.innerHTML = "<input type='text' name='productcode[]' value='" + productCode + "' readonly>";
                cell3.innerHTML = "<input type='text' name='productname[]' value='" + productName + "' readonly>";
                cell4.innerHTML = "<input type='number' name='productweight[]' value='" + productWeight + "' readonly>";
                cell5.innerHTML = "<input type='number' name='piecesperbox[]' value='25' readonly required>";
                cell6.innerHTML = "<input type='number' name='numberofbox[]' min='0' required onchange='computeTotalWeight(" + lastibdid + ")'>";
                cell7.innerHTML = "<input type='number' name='totalpieces[]' readonly>";
                cell8.innerHTML = "<input type='number' name='totalweight[]' readonly>";
                cell9.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteTableRow(this)'>X</button>";

                lastibdid++;
                document.getElementById("productSearchResults").innerHTML = ""; // Clear search results
            }

            function deleteTableRow(row) {
                var table = document.getElementById("stockmovement");
                table.deleteRow(row.parentNode.parentNode.rowIndex);
            }

            // Function to compute total weight
            function computeTotalWeight(rowId) {
                var numberOfBoxes = document.querySelector("[name='numberofbox[]']").value;
                var piecesPerBox = document.querySelector("[name='piecesperbox[]']").value;
                var weightPerPiece = document.querySelector("[name='productweight[]']").value;

                var totalPieces = numberOfBoxes * piecesPerBox;
                var totalWeight = totalPieces * weightPerPiece;

                document.querySelector("[name='totalpieces[]']").value = totalPieces;
                document.querySelector("[name='totalweight[]']").value = totalWeight;

                computeTotalPiecesOfAllRows();
                computeTotalWeightOfAllRows();
            }

            function computeTotalPiecesOfAllRows() {
                var totalPieces = 0;
                var totalPiecesInputs = document.getElementsByName("totalpieces[]");
                for (var i = 0; i < totalPiecesInputs.length; i++) {
                    totalPieces += parseFloat(totalPiecesInputs[i].value);
                }
                document.getElementById("overalltotalpieces").value = totalPieces.toFixed(2);
            }

            function computeTotalWeightOfAllRows() {
                var totalWeight = 0;
                var totalWeightInputs = document.getElementsByName("totalweight[]");
                for (var i = 0; i < totalWeightInputs.length; i++) {
                    totalWeight += parseFloat(totalWeightInputs[i].value);
                }
                document.getElementById("overalltotalweight").value = totalWeight.toFixed(2);
            }
        </script>

    </section>
</body>
<?php include_once("../reusable/footer.php"); ?>

</html>

