<?php
include '../database/dbconnect.php';

$InventoryID = $_POST['InventoryID'];
$ProductCode = $_POST['ProductCode'];
$Product_Name = $_POST['Product_Name'];
$Weight = $_POST['Weight'];
$Available_quantity = $_POST['Available_quantity'];

if (empty($InventoryID) || empty($ProductCode) || empty($Product_Name) || empty($Weight) || empty($Available_quantity)) {
    echo "<script>alert('One or more fields are empty')</script>";
} elseif ($Available_quantity < 0) {
    echo "<script>alert('Quantity cannot be a negative number')</script>";
} else {

    $sql = "SELECT * FROM inventory WHERE InventoryID = '$InventoryID'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        // InventoryID is not in the database
        $updateSql = "INSERT INTO inventory (InventoryID,  ProductCode, Product_Name, Weight , Available_quantity) VALUES ('$InventoryID', '$ProductCode', '$Product_Name', '$Weight', '$Available_quantity')";
        if ($conn->query($updateSql) === TRUE) {
            echo "Inventory updated successfully";
        } else {
            echo "Error updating quantity: " . $conn->error;
        }
    
    } elseif ($result->num_rows > 0 || $InventoryID == $row['InventoryID']) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            echo '<script>
            var InventoryID = "' . $InventoryID . '";
            var ProductCode = "' . $ProductCode . '";
            var Product_Name = "' . $Product_Name . '";
            var Weight = "' . $Weight . '";
            var Available_quantity = "' . $Available_quantity . '";
            
            function openModal() {
                document.getElementById("myModal").style.display = "block";
            }
            
            function closeModal() {
                document.getElementById("myModal").style.display = "none";
            }

            function updateInventory() {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("demo").innerHTML = this.responseText;
                    }
                };
                xhttp.open("GET", "update_inventory_existing.php?InventoryID=" + InventoryID + "&ProductCode=" + ProductCode + "&Product_Name=" + Product_Name + "&Weight=" + Weight + "&Available_quantity=" + Available_quantity, true);
                xhttp.send();
                closeModal();
            }
            
            openModal();
            </script>';

            echo '<div id="myModal" class="modal">

            <!-- Modal content -->
            
            <p>Inventory ID: ' . $InventoryID . '</p>
            <p>Product Code: ' . $ProductCode . '</p>
            <p>Product Name: ' . $Product_Name . '</p>
            <p>Weight: ' . $Weight . '</p>
            <p>Available Quantity: ' . $row['Available_quantity'] . '</p>
            <p>Additional Quantity: ' . $Available_quantity . '</p>
            <p>Do you still want to add to the existing values?</p>
            <form action="update_inventory.php" method="post">
            <input type="hidden" name="InventoryID" value="' . $InventoryID . '">
            <input type="hidden" name="ProductCode" value="' . $ProductCode . '">
            <input type="hidden" name="Product_Name" value="' . $Product_Name . '">
            <input type="hidden" name="Weight" value="' . $Weight . '">
            <input type="hidden" name="Available_quantity" value="' . $Available_quantity . '">
            <button class="btn btn-primary" type="submit">Yes</button>
            </form>

            <button onclick="closeModal()">Cancel</button>
            </div>
            </div>';
        }
    } else {
        // InventoryID is not in the database
        echo "Inventory ID not found";
    }
}

?>



