<?php
session_start();
require_once 'includes/config.php';

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) { // Later update to password_verify()
            // Store user_id instead of uid
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect based on role
            switch($user['role']) {
                case 'admin':
                    header("Location: admin/index.php");
                    break;
                case 'supervisor':
                    header("Location: supervisor/index.php");
                    break;
                case 'ceo':
                    header("Location: ceo/index.php");
                    break;
            }
            exit();
        }
    }
    $error = "Invalid username or password";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>
    <div class="login-container" style="background-color: var(--bg-secondary);">
        <div class="card" style="border-color: var(--border-color);">
            <div class="card-header" style="background-color: var(--primary); color: var(--light);">
                <h2>Admin Login</h2>
            </div>
            <div class="card-body">
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" class="btn" style="background-color: var(--primary); color: var(--light);">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
