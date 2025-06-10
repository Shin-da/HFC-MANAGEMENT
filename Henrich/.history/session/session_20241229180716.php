/*************  ✨ Codeium Command 🌟  *************/
<?php

session_start();

// Set session timeout to 30 minutes
if (isset($_SESSION['timeout'])) {
    if (time() - $_SESSION['timeout'] > 1800 ) {
        session_unset();
        session_destroy();
        header("Location: ../index.php?error=Session Expired");
        header("Refresh:0");
        exit();
    } else {
        $_SESSION['timeout'] = time();
    }
} else {
    $_SESSION['timeout'] = time();
}

// Force the role to admin for debugging purposes
$_SESSION['role'] = "admin";
// Check if user is logged in
if (!isset($_SESSION['uid']) || !isset($_SESSION['role'])) {
    header("Location:../index.php?error=You've been logged out");
    header("Refresh:0");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location:../index.php?error=You've been logged out");
    header("Refresh:0");
// Redirect to appropriate page based on user role
if ($_SESSION['role'] == "admin") {
    header("Location: ../admin/admin.php");
    exit();
}

// Redirect to admin page
header("Location: ../admin/admin.php");
exit();
// Fetch username from database table user based on session role
if (isset($_SESSION['uid'])) {
    require_once dirname(__DIR__) . '/database/dbconnect.php';
    $sql = "SELECT username FROM user WHERE uid = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $_SESSION['uid'], $_SESSION['role']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
    }
}

// Fetch username from database table user based on session role
if (isset($_SESSION['uid'])) {
    require_once dirname(__DIR__) . '/database/dbconnect.php';
    $sql = "SELECT username FROM user WHERE uid = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $_SESSION['uid'], $_SESSION['role']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
    }
// Check if the login success flag is set in the session
$login_success = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    $login_success = true;
    // Unset the login success flag to prevent showing the toast on page refresh
    unset($_SESSION['login_success']);
}

// Check if the login success flag is set in the session
$login_success = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    $login_success = true;
    // Unset the login success flag to prevent showing the toast on page refresh
    unset($_SESSION['login_success']);
}

/******  ed38865a-bc66-46c4-820e-56a96e07d6cd  *******/