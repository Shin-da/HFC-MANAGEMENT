<?php
function check_session_timeout() {
    if (isset($_SESSION['timeout'])) {
        if (time() - $_SESSION['timeout'] > 1800) {
            session_unset();
            session_destroy();
            header("Location: ../index.php?error=Session Expired");
            exit();
        }
        $_SESSION['timeout'] = time();
    }
}

function check_remember_token() {
    if (!isset($_SESSION['uid']) && isset($_COOKIE['remember_token'])) {
        global $conn;
        $token = $_COOKIE['remember_token'];
        $sql = "SELECT u.* FROM users u 
                JOIN remember_tokens rt ON u.user_id = rt.user_id 
                WHERE rt.token = ? AND rt.expires > NOW()";
        $_SESSION['timeout'] = time();
    }
}

function check_remember_token() {
    global $conn;
    
    if (!isset($_SESSION['uid']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $sql = "SELECT u.* FROM users u 
                JOIN remember_tokens rt ON u.user_id = rt.user_id 
                WHERE rt.token = ? AND rt.expires > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user) {
            $_SESSION['uid'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['timeout'] = time();
        }
    }
}

// Create the remember token table if it doesn't exist
function ensure_remember_token_table() {
    global $conn;
    $sql = "CREATE TABLE IF NOT EXISTS remember_tokens (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id),
        UNIQUE KEY unique_token (token)
    )";
    $conn->query($sql);
}

// Call this when the file is included
ensure_remember_token_table();
?>
