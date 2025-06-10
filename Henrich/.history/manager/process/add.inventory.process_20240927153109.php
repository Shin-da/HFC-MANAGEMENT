<!-- Add Customer Order -->
<?php 
    require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    if (isset($_POST['addCustomerOrder'])) {
        $customerName = $_POST['customerName'];
        $customerEmail = $_POST['customerEmail'];
        $customerAddress = $_POST['customerAddress'];
        $customerPhone = $_POST['customerPhone'];
        $orderName = $_POST['orderName'];
        $quantityType = $_POST['quantityType'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $description = $_POST['description'];
        $status = $_POST['status'];
    }

    if (isset($customerName) && isset($customerEmail) && isset($customerAddress) && isset($customerPhone) && isset($orderName) && isset($quantityType) && isset($quantity) && isset($price) && isset($date) && isset($time) && isset($description) && isset($status)) {

        $sql = "INSERT INTO customerorders (customerName, customerEmail, customerAddress, customerPhone, orderName, quantityType, quantity, price, date, time, description, status) VALUES ('$customerName', '$customerEmail', '$customerAddress', '$customerPhone', '$orderName', '$quantityType', '$quantity', '$price', '$date', '$time', '$description', '$status')";
        if ($conn->query($sql)) {
            header("Location: ../manager/add.inventory.php?success");
            exit;
        }
        else {
            echo "Error: 1" . $conn->error;
        }
    }
    else {
        echo "Error:2" . $conn->error;
    }



