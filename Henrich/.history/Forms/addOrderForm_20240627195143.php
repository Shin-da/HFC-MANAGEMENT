<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) { ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Order</title>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    </head>

    <body>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

        <?php

        include '../database/dbconnect.php';

        if (isset($_POST["OrderID"]) && isset($_POST["Datetime"]) && isset($_POST["Lname"]) && isset($_POST["Fname"]) && isset($_POST["ProductCode"]) && isset($_POST["Quantity"]) && isset($_POST["Price"])) {
            $OrderID = $_POST["OrderID"];
            $Datetime = $_POST["Datetime"];
            $Lname = $_POST["Lname"];

            $Fname = $_POST["Fname"];
            $ProductCode = $_POST["ProductCode"];
            $Quantity = $_POST["Quantity"];
            $Price = $_POST["Price"];
        }

        $sql = "SELECT * FROM Inventory";
        $result = $conn->query($sql);
        ?>

        <!-- Orders Form -->
        <div>
            <h1>Add Orders </h1>
            <div>
                <form action="../Process/add_order.php" class="form w-50 p-5 d-flex flex-column" method="post">
                    <div class="form-group w-100">
                        <label for="OrderID" class="d-block">OrderID</label>
                        <input type="text" class="form-control" id="OrderID" name="OrderID">
                    </div>

                    <div class="form-group w-100">
                        <label for="Lname" class="d-block">Lname</label>
                        <input type="text" class="form-control" id="Lname" name="Lname">
                    </div>

                    <div class="form-group w-100">
                        <label for="Fname" class="d-block">Fname</label>
                        <input type="text" class="form-control" id="Fname" name="Fname">
                    </div>

                    <div class="form-group w-100">
                        <label for="ProductCode" class="d-block">Product Code</label>
                        <select class="form-control w-100" id="ProductCode" name="ProductCode">
                            <?php while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["ProductCode"] . "'>" . $row["ProductCode"] . "</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="form-group w-100">
                        <label for="Quantity" class="d-block">Quantity</label>
                        <input type="text" class="form-control" id="Quantity" name="Quantity">
                    </div>

                    <div class="form-group w-100">
                        <label for="Price" class="d-block">Price</label>
                        <input type="text" class="form-control" id="Price" name="Price">
                    </div>

                    <div class="form-group w-100">
                        <label for="datetime" class="d-block">Datetime</label>
                        <input type="text" class="form-control" id="datetime" name="datetime">
                    </div>

                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>

        </div>

        <script>
            $(function() {
                $("#datetime").datepicker({
                    // Specify the date format
                    dateFormat: 'yy-mm-dd'
                });

                // Handle form submission
                $("#submitForm").on("click", function() {
                    // Get the selected date from the datepicker
                    var selectedDate = $("#datetime").datepicker("getDate");

                    // Format the date to 'yyyy-mm-dd'
                    var formattedDate = $.datepicker.formatDate('yy-mm-dd', selectedDate);

                    // Set the formatted date to the hidden input
                    $("#formattedDate").val(formattedDate);

                    // Submit the form
                    $("#orderForm").submit();
                });
            });
        </script>
        <script>
            $('#ProductCode').change(function() {
                var ProductCode = $(this).val();
                console.log('Product Code:', ProductCode);

                $.ajax({
                    url: 'get_price.php',
                    method: 'POST',
                    data: {
                        ProductCode: ProductCode
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        $('#Price').val(response);
                    },
                    error: function() {
                        $('#Price').val('Error fetching price');
                    }
                });
            });
        </script>

    </body>

    </html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>