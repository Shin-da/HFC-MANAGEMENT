<?php
session_start();
include "../database/dbconnect.php";

if (isset($_POST['useremail']) && isset($_POST['password'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $useremail = validate($_POST['useremail']);
    $password = validate($_POST['password']);

    if (empty($useremail)) {
        header("Location: ../index.php?error=useremail is required");
        exit();
    } else if (empty($password)) {
        header("Location: ../index.php?error=Password is required");
        exit();
    } else {
        // hashing the password
        // $password = md5($password);

        $sql = "SELECT * FROM user WHERE useremail='$useremail' AND password='$password'";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['useremail'] === $useremail && $row['password'] === $password) {
                $_SESSION['useremail'] = $row['useremail'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['uid'] = $row['uid'];
                if ($row['role'] === 'admin') {
                    header("Location: ../admin/index.php?success=You have been logged in successfully");
                } else if ($row['role'] === 'supervisor') {
                    header("Location: ../supervisor/index.php?success=You have been logged in successfully");
                } else if ($row['role'] === 'ceo') {
                    header("Location: ../ceo/index.php?success=You have been logged in successfully");
                }
                exit();
            } else {
                header("Location: ../index.php?error=User does not exist");
                exit();
            }
        } else {
            header("Location: ../index.php?error=Incorrect useremail or password");
            exit();
        }
    }
} else {
    header("Location: index.php?error=You've been logged out");
    exit();
}

