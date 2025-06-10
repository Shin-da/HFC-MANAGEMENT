<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Adding to Inventory</title>
    </head>

    <body>
        <?php
        include './database/dbconnect.php';

        if (isset($_POST["InventoryID"]) && isset($_POST["ProductCode"]) && isset($_POST["Product_Name"]) && isset($_POST["Weight"]) && isset($_POST["Available_quantity"])) {
            $InventoryID = $_POST["InventoryID"];
            $ProductCode = $_POST["Datetime"];
            $Product_Name = $_POST["Product_Name"];
            $Weight = $_POST["Weight"];
            $Available_quantity = $_POST["Available_quantity"];
        }  

        $sql = "SELECT * FROM Inventory";
        $result = $conn->query($sql);
        ?>
        
        <div>
            <h1>Adding to Inventory </h1>
            <div>
                <form action="check_inventory.php" class="form w-50 p-5 d-flex flex-column" method="post">
                    <div class="form-group w-100">
                        <label for="InventoryID" class="d-block">InventoryID</label>
                        <input type="text" class="form-control" id="InventoryID" name="InventoryID">
                    </div>

                    <div class="form-group w-100">
                        <label for="ProductCode" class="d-block">ProductCode</label>
                        <input type="text" class="form-control" id="ProductCode" name="ProductCode">
                    </div>

                    <div class="form-group w-100">
                        <label for="Product_Name" class="d-block">Product_Name</label>
                        <input type="text" class="form-control" id="Product_Name" name="Product_Name">
                    </div>

                    <div class="form-group w-100">
                        <label for="Weight" class="d-block">Weight</label>
                        <input type="text" class="form-control" id="Weight" name="Weight">
                    </div>

                    <div class="form-group w-100">
                        <label for="Available_quantity" class="d-block">Available_quantity</label>
                        <input type="text" class="form-control" id="Available_quantity" name="Available_quantity">
                    </div>

                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>

    </body>

    </html>
<?php

} else {
    header("Location: index.php");
    exit();
}
?>