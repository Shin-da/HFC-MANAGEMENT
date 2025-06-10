<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/Page.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
Page::setCurrentPage($current_page);

// Set page title and styles
Page::setTitle('Add Inventory');
Page::addStyle('./assets/css/variables.css');
Page::addStyle('../assets/css/forms.css');
Page::addStyle('./assets/css/table.css');

ob_start();
?>

<div class="container-fluid">
    <div class="table-header">
        <a class="btn btn-secondary" href="javascript:window.history.back()">Back</a>
        <h2>Add to Inventory</h2>
    </div>

    <div class="container-fluid order-form-container">
        <form action="./process/add.inventory.process.php" method="POST" id="inventoryForm">
            <div class="inventory-section">
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
                    <tbody>
                        <tr>
                            <td><input type="text" name="inventoryID[]" placeholder="Inventory ID" required></td>
                            <td><input type="text" name="productCode[]" placeholder="Product Code" required></td>
                            <td><input type="text" name="productDescription[]" placeholder="Product Description" required></td>
                            <td><input type="text" name="category[]" placeholder="Category" required></td>
                            <td><input type="number" name="onHand[]" placeholder="On Hand" required></td>
                            <td><input type="date" name="dateUpdated[]" value="<?= date('Y-m-d') ?>" required></td>
                            <td><button type="button" class="btn btn-danger" onclick="deleteRow(this)">X</button></td>
                        </tr>
                    </tbody>
                </table>

                <div class="action-buttons">
                    <button type="button" class="btn btn-secondary" onclick="addTableRow()">Add Product</button>
                </div>
            </div>

            <div class="form-actions">
                <div class="submit-buttons">
                    <input type="reset" value="Reset" class="btn btn-danger">
                    <input type="submit" value="Add to Inventory" name="addInventory" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function addTableRow() {
        var table = document.getElementById("inventory");
        var row = table.insertRow(-1);
        var cells = row.insertCells(7);
        
        var inputs = [
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