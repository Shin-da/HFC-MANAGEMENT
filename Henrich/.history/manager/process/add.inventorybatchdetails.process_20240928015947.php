/*************  ✨ Codeium Command 🌟  *************/
<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Prepare and bind
if (isset($_POST['ibdid'])) {
    $ibdids = $_POST['ibdid'];
    $batchids = $_POST['batchid'] ?? [];
    $productCodes = $_POST['productCode'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $weights = $_POST['weight'] ?? [];
    $prices = $_POST['price'] ?? [];

    $stmt = $conn->prepare("INSERT INTO inventorybatchdetails (ibdid, batchid, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($batchids as $key => $batchid) {
        $productCode = $productCodes[$key] ?? '';
        $quantity = $quantities[$key] ?? 0;
        $weight = $weights[$key] ?? 0;
        $price = $prices[$key] ?? 0;

        $stmt->bind_param("isssss", $ibdids[$key], $batchid, $productCode, $quantity, $weight, $price);
        if (!$stmt->execute()) {
            // Handle error here, for example: redirect to an error page.
            header('Location: /error_page.php'); // Specify the error page
            exit();
        }
    }
}

$stmt->close();
$conn->close();


/******  dd470025-7987-4f41-94b7-a7e143a3e073  *******/