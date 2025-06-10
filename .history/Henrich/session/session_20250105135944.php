<?php

session_start();

// Set session timeout to 30 minutes
if (isset($_SESSION['timeout'])) {
    if (time() - $_SESSION['timeout'] > 1800) {
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

// Check if user is logged in
if (!isset($_SESSION['uid']) || !isset($_SESSION['role'])) {
    header("Location:../index.php?error=You've been logged out");
    header("Refresh:0");
    exit();
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
    } else {
        header("Location: /404.php");
        exit();
    }
} else {
    header("Location: /404.php");
    exit();
}

// Check if the login success flag is set in the session
$login_success = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    $login_success = true;
    // Unset the login success flag to prevent showing the toast on page refresh
    unset($_SESSION['login_success']);
}

