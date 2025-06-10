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
            {type: 'text', name: 'inventoryID[]', placeholder: 'Inventory ID'},
            {type: 'text', name: 'productCode[]', placeholder: 'Product Code'},
            {type: 'text', name: 'productDescription[]', placeholder: 'Product Description'},
            {type: 'text', name: 'category[]', placeholder: 'Category'},
            {type: 'number', name: 'onHand[]', placeholder: 'On Hand'},
            {type: 'date', name: 'dateUpdated[]', value: new Date().toISOString().split('T')[0]},
        ];

        inputs.forEach((input, index) => {
            var cell = row.insertCell(index);
            var element = document.createElement('input');
            element.type = input.type;
            element.name = input.name;
            element.placeholder = input.placeholder;
            if (input.value) element.value = input.value;
            element.required = true;
            cell.appendChild(element);
        });

        var actionCell = row.insertCell(6);
        var deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-danger';
        deleteBtn.onclick = function() { deleteRow(this); };
        deleteBtn.innerText = 'X';
        actionCell.appendChild(deleteBtn);
    }

    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>