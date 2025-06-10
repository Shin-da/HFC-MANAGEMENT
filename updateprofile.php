<?php
include "./database/dbconnect.php";
include "./session/session.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['customername']) && isset($_POST['customeraddress']) &&
        isset($_POST['customerphonenumber']) && isset($_SESSION['accountid']) &&
        !empty($_POST['username']) && !empty($_POST['customername']) && !empty($_POST['customeraddress']) &&
        !empty($_POST['customerphonenumber'])) {

        $accountId = $_SESSION['accountid'];
        $username = $_POST['username'];
        $customerName = $_POST['customername'];
        $customerAddress = $_POST['customeraddress'];
        $customerPhoneNumber = $_POST['customerphonenumber'];

        $stmt = $conn->prepare("UPDATE customeraccount SET username = ?, customername = ?, customeraddress = ?, customerphonenumber = ? WHERE accountid = ?");
        $stmt->bind_param("ssssi", $username, $customerName, $customerAddress, $customerPhoneNumber, $accountId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header("Location: profile.php?success=Profile updated successfully");
            } else {
                header("Location: profile.php?error=No changes were made");
            }
        } else {
            header("Location: profile.php?error=Error updating profile: " . $stmt->error);
        }
    } else {
        header("Location: profile.php?error=All fields are required");
    }
} else {
    header("Location: profile.php");
}
?>

