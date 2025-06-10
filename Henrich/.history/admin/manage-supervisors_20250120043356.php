<?php
require_once 'access_control.php';
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    switch($action) {
        case 'add':
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
            $lname = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
            $username = strtolower($fname[0] . $lname); // Create username from first initial and last name
            $temp_password = bin2hex(random_bytes(8));
            
            $stmt = $conn->prepare("INSERT INTO users (username, useremail, first_name, last_name, department, role, password, status) VALUES (?, ?, ?, ?, ?, 'supervisor', ?, 'active')");
            $stmt->bind_param("ssssss", $username, $email, $fname, $lname, $_POST['department'], $temp_password);
            
            if ($stmt->execute()) {
                // Send email with credentials
                mail($email, "Account Created", "Your account has been created.\nUsername: $username\nTemporary password: $temp_password");
                $_SESSION['success'] = "Supervisor added successfully";
            } else {
                $_SESSION['error'] = "Error adding supervisor";
            }
            break;
            
        case 'update_status':
            $user_id = $_POST['user_id'];
            $status = $_POST['status'];
            $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role = 'supervisor'");
            $stmt->bind_param("si", $status, $user_id);
            $stmt->execute();
            break;
    }
}

// Get all supervisors
<body>
    <div class="container">
        <h2>Manage Supervisors</h2>
        
        <!-- Add Supervisor Form -->
        <form method="POST" class="add-supervisor-form">
            <input type="hidden" name="action" value="add">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="department" placeholder="Department">
            <button type="submit">Add Supervisor</button>
        </form>

        <!-- Supervisors List -->
        <div class="supervisors-list">
            <?php while ($supervisor = $supervisors->fetch_assoc()): ?>
            <div class="supervisor-card">
                <h3><?= htmlspecialchars($supervisor['first_name']) ?> <?= htmlspecialchars($supervisor['last_name']) ?></h3>
                <p>Username: <?= htmlspecialchars($supervisor['username']) ?></p>
                <p>Email: <?= htmlspecialchars($supervisor['useremail']) ?></p>
                <p>Department: <?= htmlspecialchars($supervisor['department']) ?></p>
                <p>Status: <?= htmlspecialchars($supervisor['status']) ?></p>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
