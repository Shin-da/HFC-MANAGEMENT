<?php
require '../../session/session.php';
require '../../database/dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['uid'];
    $old_password = $_POST['oldpassword'];
    
    // Validate inputs
    if (empty($uid) || empty($old_password)) {
        $_SESSION['error'] = "Please enter your current password";
        header("Location: ../myaccount.php");
        exit();
    }

    // Verify current password
    $sql = "SELECT password FROM user WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);