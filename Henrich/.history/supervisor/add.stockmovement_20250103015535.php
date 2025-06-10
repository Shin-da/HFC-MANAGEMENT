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

                    <div class="search-bar">
                        <input type="text" id="search-input" placeholder="Search by Product Code or Name..." onkeyup="searchProduct()">
                        <div id="search-results" class="search-results"></div>
                    </div>

                    <table id="stockmovement">
                        <thead>
                            <tr>
                                <th>Inventory Batch ID</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Weight per Piece (Kg)</th> 
                                <th>Pieces per Box</th>
                                <th>Number of Box</th>
                                <th>Total Pieces</th>
                                <th>Total Weight (Kg)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="bottom-form">
                        <button type="reset" class="btn btn-danger">Reset</button>
                        <input type="submit" value="Add Inventory" name="addInventoryBatch" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>

        <script>
            var lastibdid = <?= $lastibdid ?> + 1;

            function searchProduct() {
                var input = document.getElementById("search-input").value;
                if (input.length < 3) {
                    document.getElementById("search-results").innerHTML = '';
                    return;
                }

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var results = JSON.parse(this.responseText);
                        var output = '<ul>';
                        results.forEach(function(item) {
                            output += '<li onclick="selectProduct(\'' + item.productcode + '\', \'' + item.productname + '\', ' + item.productweight + ')">' + item.productcode + ' - ' + item.productname + '</li>';
                        });
                        output += '</ul>';
                        document.getElementById("search-results").innerHTML = output;
                    }
                };

                xmlhttp.open("GET", "search_products.php?query=" + input, true);
                xmlhttp.send();
            }

            function selectProduct(productCode, productName, productWeight) {
                var table = document.getElementById("stockmovement").getElementsByTagName("tbody")[0];
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
                cell6.innerHTML = "<input type='number' name='numberofbox[]' onchange='computeTotalWeight(" + lastibdid + ")'>";
                cell7.innerHTML = "<input type='number' name='totalpieces[]' readonly>";
                cell8.innerHTML = "<input type='number' name='totalweight[]' readonly>";
                cell9.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteTableRow(this)'>X</button>";

                document.getElementById("search-results").innerHTML = '';
                lastibdid++;
            }

            function deleteTableRow(button) {
                var row = button.parentNode.parentNode;
                row.parentNode.removeChild(row);
            }

            function computeTotalWeight(rowId) {
                var row = document.querySelector("input[name='numberofbox[]']").parentNode.parentNode;
                var numberOfBoxes = row.querySelector("input[name='numberofbox[]']").value;
                var piecesPerBox = row.querySelector("input[name='piecesperbox[]']").value;
                var weightPerPiece = row.querySelector("input[name='productweight[]']").value;

                var totalPieces = numberOfBoxes * piecesPerBox;
                var totalWeight = totalPieces * weightPerPiece;

                row.querySelector("input[name='totalpieces[]']").value = totalPieces;
                row.querySelector("input[name='totalweight[]']").value = totalWeight;
            }
        </script>
    </section>
</body>
<?php include_once("../reusable/footer.php"); ?>

</html>

