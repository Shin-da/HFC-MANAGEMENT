/*************  ✨ Codeium Command 🌟  *************/
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// inventory_functions.php
function checkAvailableQuantity($productCode) {
    $conn = $GLOBALS['conn']; // your database connection
    $sql = "SELECT availablequantity FROM inventory WHERE productcode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['availablequantity'];
    } else {
        return 0;
    }
}

?>
<script>
document.getElementById("productcode<?= $i ?>").addEventListener("change", function() {
    var productCode = this.value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            try {
                var response = JSON.parse(this.responseText);
                console.log(`Checking available quantity for product code: ${productCode}`);
                console.log(`Query executed. Number of rows returned: ${response.rowsreturned}`);
                console.log(`Available quantity: ${response.availablequantity}`);
                console.log(`Stock status: ${response.stockstatus}`);
                var availableQuantityInput = document.getElementById("availablequantity<?= $i ?>");
                var stockStatusInput = document.getElementById("stockstatus<?= $i ?>");
                availableQuantityInput.value = response.availablequantity;
                stockStatusInput.value = response.stockstatus;
            } catch (e) {
                console.error(`Error parsing response: ${e.name}: ${e.message}. Response text: ${xhttp.responseText}`);
            }
        } else if (this.readyState === 4) {
            console.error(`Error occurred while checking available quantity: Status code: ${this.status}`);
        }
    };
    xhttp.open("GET", "./process/check_available_quantity.php?productcode=" + productCode, true);
    xhttp.send();
});
</script>


/******  1f7e86a8-24e2-46e8-b807-e7e845bac2c4  *******/