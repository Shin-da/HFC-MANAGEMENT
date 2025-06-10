<?php
session_start();
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Modified SQL to use existing table structure
    $sql = "SELECT * FROM users WHERE username = ? AND role IN ('admin')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // If you're still using plain passwords, you'll need this temporary check
        // TODO: Update to password_verify once passwords are hashed
        if ($password === $user['password']) {
            $_SESSION['admin_id'] = $user['uid'];
            $_SESSION['role'] = $user['role'];
            header("Location: admin/index.php");
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
