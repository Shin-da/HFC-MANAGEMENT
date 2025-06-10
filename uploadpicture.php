<?php
require_once './database/dbconnect.php';

if (isset($_POST['accountId']) && isset($_FILES['profile-picture'])) {
    $accountId = $_POST['accountId'];
    $uploadDir = "uploads/$accountId/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $uploadFile = $uploadDir . basename($_FILES['profile-picture']['name']);
    if (move_uploaded_file($_FILES['profile-picture']['tmp_name'], $uploadFile)) {
        $stmt = $conn->prepare("UPDATE customeraccount SET profilepicture = ? WHERE accountid = ?");
        $stmt->bind_param("si", $uploadFile, $accountId);
        if ($stmt->execute()) {
            header("Location: profile.php?success=Profile picture updated successfully");
            exit;
        } else {
            echo "Error updating profile picture.";
            header("Location: profile.php?error=Error updating profile picture: " . $stmt->error);
        }
    } else {
        echo "Error uploading file.";
        header("Location: profile.php?error=Error uploading file: " . $_FILES['profile-picture']['error']);
    }
} else {
    echo "No file uploaded.";
    header("Location: profile.php?error=No file uploaded");
}

