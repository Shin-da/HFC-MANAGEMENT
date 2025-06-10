<?php

require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Update order status
if (isset($_POST['updateStatus'])) {
    $orderId = $_POST['orderId'] ?? '';
    $status = $_POST['status'] ?? '';
    if ($status != '') {
        $stmt = $conn->prepare("UPDATE orderhistory SET status = ?, datecompleted = NOW() WHERE orderId = ?");
        $stmt->bind_param("si", $status, $orderId);
        if ($stmt->execute()) {
            header("Location: orderhistorydetail.php?orderId=$orderId");
            exit;
        } else {
            $error = "Error updating record: " . $conn->error;
        }
    }
}

// Delete order
if (isset($_POST['deleteOrder'])) {
    $orderId = $_POST['orderId'] ?? '';
    // Transfer order to archivedorder table
    $stmt = $conn->prepare("INSERT INTO archivedorder (orderId, oid, customerName, customerAddress, customerPhoneNumber, orderDescription, orderTotal, orderDate, timeOfOrder, salesPerson, status) SELECT orderId, oid, customerName, customerAddress, customerPhoneNumber, orderDescription, orderTotal, orderDate, timeOfOrder, salesPerson, status FROM orderhistory WHERE orderId = ?");
    $stmt->bind_param("i", $orderId);
    if ($stmt->execute()) {
        // Delete order from orderhistory table
        $stmt = $conn->prepare("DELETE FROM orderhistory WHERE orderId = ?");
        $stmt->bind_param("i", $orderId);
        if ($stmt->execute()) {
            header("Location: orderhistory.php");
            exit;
        } else {
            $error = "Error deleting record: " . $conn->error;
        }
    } else {
        $error = "Error transferring record: " . $conn->error;
    }
}

?>

