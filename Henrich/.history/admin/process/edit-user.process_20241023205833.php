<?php
// Include the database connection file
include '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Get the form data
$uid = $_POST['uid'];
$useremail = $_POST['useremail'];
$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

// Validate the form data
$useremail = validate($useremail);
$username = validate($username);
$password = validate($password);
$role = validate($role);

// Update the user data in the database
$sql = "UPDATE user SET useremail = '$useremail', username = '$username', password = '$password', role = '$role' WHERE uid = '$uid'";
if ($conn->query($sql) === TRUE) {
    echo "User updated successfully";
} else {
    echo "Error updating user: " . $conn->error;
}

// Close the database connection
$conn->close();

// Validate function (same as in login/login.php:validate)
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}