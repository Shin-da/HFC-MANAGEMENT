/*************  ✨ Codeium Command ⭐  *************/
    $ordertype = $_POST['ordertype'];

    $stmt = $conn->prepare("INSERT INTO orderhistory (oid, orderdate, salesperson, ordertype, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $oid, $orderdate, $salesperson, $ordertype, $status);
    if (!$stmt->execute()) {
        echo "<div class='alert alert-danger'>Error inserting data into orderhistory table: " . $stmt->error . "</div>";
        exit;
    }
/******  0c9ea6a5-c74c-4b90-8aac-e198093b625c  *******/