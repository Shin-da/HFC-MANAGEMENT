<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
$product_codes = $_POST['product_code'];
$stmt = $conn->prepare("SELECT * FROM Products WHERE ProductCode IN (".implode(',', $product_codes).")");
$stmt->execute();
$product_details = $stmt->get_result();