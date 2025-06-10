/*************  ✨ Codeium Command 🌟  *************/
<?php

// indicator that add.stockmovement.process.php is running
echo "Running add.stockmovement.process.php";

// Check if the form data is being submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data from the form
    $ibdids = $_POST['ibdid'] ?? [];
    $batchids = $_POST['batchid'] ?? [];
    $encoders = $_POST['encoder'] ?? [];
    $productcodes = $_POST['productcode'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $weights = $_POST['weight'] ?? [];
    $prices = $_POST['price'] ?? [];
    $dateencoded = $_POST['dateencoded'] ?? [];





// Check if any of the form fields are empty
$hasEmptyValue = false;
foreach ($ibdids as $key => $ibdid) {
    if (empty($ibdid) || empty($batchids[$key]) || empty($encoders[$key]) || empty($productcodes[$key]) || empty($quantities[$key]) || empty($weights[$key]) || empty($prices[$key])) {
        $hasEmptyValue = true;
        break;
    }
}

if ($hasEmptyValue) {
    echo "Error: Invalid form data";
    exit;
}
    // Connect to the database
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    // Insert the data into the database
    foreach ($ibdids as $key => $ibdid) {
        $batchid = $batchids[$key] ?? '';
        $encoder = $encoders[$key] ?? '';
        $productcode = $productcodes[$key] ?? '';
        $quantity = $quantities[$key] ?? 0;
        $weight = $weights[$key] ?? 0;
        $price = $prices[$key] ?? 0;

        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, price) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Bind the parameters
        $stmt->bind_param("sssssis", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $price);

        // Execute the insert statement
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
    }

    echo "Data inserted successfully into stock movement table!";
} else {
    echo "Error: Invalid request method";
}
echo "Data inserted successfully into stock movement table!";

$description = "Encoded " . implode(", ", array_map(function($productcode, $quantity) {
    return "$productcode ($quantity)";
}, $productcodes, $quantities));


require 'add.stockactivitylog.process.php';
?>


/******  bd4b021f-9fe7-41c5-bde3-b5a369c981c4  *******/