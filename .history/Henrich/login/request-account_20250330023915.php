<?php
session_start();
require '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $department = trim($_POST['department']);
    $position = trim($_POST['position']);
    $reason = trim($_POST['reason']);

    // Validate email domain
    if (!preg_match('/@henrich\.com$/', $email)) {
        $error = "Please use your Henrich company email (@henrich.com)";
    } else {
        try {
            // Check if email already exists in users or account_requests
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE useremail = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "An account with this email already exists";
            } else {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM account_requests WHERE email = ? AND status = 'pending'");
                $stmt->execute([$email]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "You already have a pending account request";
                } else {
                    // Insert account request
                    $stmt = $pdo->prepare("
                        INSERT INTO account_requests (firstname, lastname, email, department, position, reason, status, request_date) 
                        VALUES (?, ?, ?, ?, ?, ?, 'pending', CURRENT_TIMESTAMP)
                    ");
                    $stmt->execute([$firstname, $lastname, $email, $department, $position, $reason]);
                    
                    // Notify admin (you can implement email notification here)
                    
                    $success = "Your account request has been submitted. Please wait for admin approval.";
                }
            }
        } catch (PDOException $e) {
            error_log("Account request error: " . $e->getMessage());
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Account Access - HFC Management System</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../assets/css/variables.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 500px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .description {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #1a1a1a;
            font-size: 14px;
            font-weight: 500;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            color: #1a1a1a;
            background: #f8f9fc;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #4a6ee0;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(74, 110, 224, 0.1);
            outline: none;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .name-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4a6ee0;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #3d5cba;
        }

        .error-message {
            background: #fef2f2;
            color: #dc2626;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .success-message {
            background: #f0fdf4;
            color: #16a34a;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4a6ee0;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            .name-group {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="../resources/images/hfclogo.png" alt="HFC Logo" class="logo">
            <h1>Request Account Access</h1>
            <p class="description">Please fill out this form to request access to the HFC Management System</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
                <a href="../index.php" class="back-link">Back to Login</a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="name-group">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Company Email</label>
                    <input type="email" id="email" name="email" 
                           pattern=".+@henrich\.com" 
                           title="Please use your Henrich company email"
                           placeholder="username@henrich.com" required>
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="Warehouse">Warehouse</option>
                        <option value="Logistics">Logistics</option>
                        <option value="Inventory">Inventory</option>
                        <option value="Quality Control">Quality Control</option>
                        <option value="Administration">Administration</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="position">Position</label>
                    <input type="text" id="position" name="position" required>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Access</label>
                    <textarea id="reason" name="reason" 
                              placeholder="Please explain why you need access to the system"
                              required></textarea>
                </div>

                <button type="submit">Submit Request</button>
            </form>

            <a href="../index.php" class="back-link">Back to Login</a>
        <?php endif; ?>
    </div>
</body>
</html> 