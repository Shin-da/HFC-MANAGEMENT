<?php
require_once '../../includes/session.php';
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASSWORD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        $stmt = $pdo->prepare("
            UPDATE users 
            SET first_name = :first_name,
                last_name = :last_name,
                useremail = :email,
        $conn->begin_transaction();

        // Check email uniqueness
        $stmt = $conn->prepare("SELECT uid FROM user WHERE useremail = ? AND uid != ?");
        $stmt->bind_param("si", $email, $uid);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Email already exists");
        }

        // Update user
        $stmt = $conn->prepare("UPDATE user SET useremail = ?, username = ? WHERE uid = ?");
        $stmt->bind_param("ssi", $email, $username, $uid);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating account");
        }

        $conn->commit();
        $_SESSION['success'] = "Account updated successfully";
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: ../myaccount.php");
    exit();
}

header("Location: ../myaccount.php");
exit();
?>
