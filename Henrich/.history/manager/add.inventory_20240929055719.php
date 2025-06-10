<?php require '../reusable/redirect404.php'; require '../session/session.php'; require '../database/dbconnect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>INVENTORY</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/form.css">
</head>

<body>
    <?php

    // Sidebar 
    include '../reusable/sidebar.php';
    ?>
    <section class=" panel">
        <?php  include '../reusable/navbarNoSearch.html';// TOP NAVBAR  ?>
        <div class="container-fluid">
            <div class="table-header">
                <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
                <h2>ADD TO INVENTORY (admin only)</h2>
            </div>

            <div class="container-fluid" style="overflow-y: scroll">
                <form action="./process/add.inventory.process.php" method="POST">
                    <table id="inventory">
                        <thead>
                            <tr>
                                <th>Inventory ID</th>
                                <th>Product Code</th>
                                <th>Product Description</th>
                                <th>Category</th>
                                <th>On Hand</th>
                                <th>Date Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <script>
                            // Set the date format to yyyy-mm-dd
                            document.querySelectorAll('input[type="date"]').forEach(el => {
                                el.value = new Date().toISOString().slice(0, 10).replace(/-/g, '-');
                            });
                        </script>
                        <tbody>
                            <?php for ($i = 0; $i < 1; $i++): ?>
                        <tbody>
                            <tr>
                                <td><input type="text" name="inventoryID[]" id="inventoryID<?= $i ?>" placeholder="Inventory ID"></td>
                                <td><input type="text" name="productCode[]" id="productCode<?= $i ?>" placeholder="Product Code"></td>
                                <td><input type="text" name="productDescription[]" id="productDescription<?= $i ?>" placeholder="Product Description"></td>
                                <td><input type="text" name="category[]" id="category<?= $i ?>" placeholder="Category"></td>
                                <td><input type="number" name="onHand[]" id="onHand<?= $i ?>" placeholder="On Hand"></td>
                                <td><input type="date" name="dateUpdated[]" id="dateUpdated<?= $i ?>" placeholder="Date Updated"></td>
                                <td><button type="button" class="btn btn-danger" onclick="deleteRow(this)">X</button></td>
                            </tr>
                        <?php endfor; ?>
                        </tbody>
                    </table>

                    <div class="bottom-form">
                        <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Row</button>
                    </div>

                    <div class="bottom-form">
                        <div class="buttons">
                            <input type="reset" value="Reset" name="reset" class="btn btn-danger" style="flex: 1"></input>
                            <input type="submit" value="Add Inventory" name="addInventory" class="btn btn-primary" style="flex: 1">
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <script>
            function addTableRow() {   // Function for adding new row on the table. 
                var table = document.getElementById("inventory");
                var row = table.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);

                cell1.innerHTML = "<input type='text' name='inventoryID[]' id='inventoryID' placeholder='Inventory ID'>";
                cell2.innerHTML = "<input type='text' name='productCode[]' id='productCode' placeholder='Product Code'>";
                cell3.innerHTML = "<input type='text' name='productDescription[]' id='productDescription' placeholder='Product Description'>";
                cell4.innerHTML = "<input type='text' name='category[]' id='category' placeholder='Category'>";
                cell5.innerHTML = "<input type='number' name='onHand[]' id='onHand' placeholder='On Hand'>";
                cell6.innerHTML = "<input type='date' name='dateUpdated[]' id='dateUpdated' placeholder='Date Updated'>";
                cell7.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteRow(this)'>X</button>";
            }

            function deleteRow(r) {
                var i = r.parentNode.parentNode.rowIndex;
                document.getElementById("inventory").deleteRow(i);
            }
        </script>
    </section>

</body>
<?php  include_once("../reusable/footer.php"); ?>
</html>