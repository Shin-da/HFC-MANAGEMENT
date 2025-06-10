/*************  ✨ Codeium Command ⭐  *************/
    $ordertype = $_POST['ordertype'];

    $sql = "INSERT INTO orderhistory (oid, orderdate, salesperson, ordertype, status)
            VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $oid, $orderdate, $salesperson, $ordertype);
    $stmt->execute();
    $stmt->close();
/******  4512223e-a994-44ee-b27f-575ab32ff668  *******/