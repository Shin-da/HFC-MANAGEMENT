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
                username = :username,
                department = :department,
                updated_at = NOW()
            WHERE user_id = :user_id
        ");

        $result = $stmt->execute([
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'username' => $_POST['username'],
            'department' => $_POST['department'],
            'user_id' => $_POST['user_id']
        ]);

        if ($result) {
            $_SESSION['success'] = "Account updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update account.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    header("Location: ../myaccount.php");
    exit();
}

header("Location: ../myaccount.php");
exit();
?>
