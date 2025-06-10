<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['action'])) {
        throw new Exception('Action is required');
    }

    $action = $_POST['action'];
    $pdo = $GLOBALS['pdo'];
    $pdo->beginTransaction();

    switch($action) {
        case 'add':
            // Validate required fields
            $required = ['email', 'first_name', 'last_name', 'department'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("$field is required");
                }
            }

            // Sanitize inputs
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
            $lname = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
            $department = filter_var($_POST['department'], FILTER_SANITIZE_STRING);
            
            // Create username from first initial and last name
            $username = strtolower($fname[0] . $lname);
            
            // Generate random password if not provided
            $password = $_POST['password'] ?? bin2hex(random_bytes(8));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Handle profile picture upload
            $profile_picture = null;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $profile_picture = handleProfilePictureUpload($_FILES['profile_picture']);
            }

            $stmt = $pdo->prepare("
                INSERT INTO users (username, useremail, first_name, last_name, role, password, department, status, profile_picture) 
                VALUES (:username, :email, :fname, :lname, 'supervisor', :password, :department, 'active', :profile_picture)
            ");
            
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':fname' => $fname,
                ':lname' => $lname,
                ':password' => $hashed_password,
                ':department' => $department,
                ':profile_picture' => $profile_picture
            ]);

            // Log the action
            logAdminAction("Added new supervisor: $username");

            // Send email with credentials
            sendWelcomeEmail($email, $username, $password);

            $message = "Supervisor added successfully";
            break;

        case 'update':
            if (empty($_POST['user_id'])) {
                throw new Exception('User ID is required for update');
            }

            $params = [
                ':email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                ':fname' => filter_var($_POST['first_name'], FILTER_SANITIZE_STRING),
                ':lname' => filter_var($_POST['last_name'], FILTER_SANITIZE_STRING),
                ':department' => filter_var($_POST['department'], FILTER_SANITIZE_STRING),
                ':status' => $_POST['status'],
                ':user_id' => $_POST['user_id']
            ];

            $sql = "UPDATE users SET 
                    useremail = :email,
                    first_name = :fname,
                    last_name = :lname,
                    department = :department,
                    status = :status";

            // Handle password update
            if (!empty($_POST['password'])) {
                $sql .= ", password = :password";
                $params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            // Handle profile picture update
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $profile_picture = handleProfilePictureUpload($_FILES['profile_picture']);
                $sql .= ", profile_picture = :profile_picture";
                $params[':profile_picture'] = $profile_picture;
            }

            $sql .= " WHERE user_id = :user_id AND role = 'supervisor'";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // Log the action
            logAdminAction("Updated supervisor ID: {$_POST['user_id']}");

            $message = "Supervisor updated successfully";
            break;

        default:
            throw new Exception('Invalid action');
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log("Error in save-supervisor.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function handleProfilePictureUpload($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Validate file type
    if (!in_array($filetype, $allowed)) {
        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowed));
    }

    // Generate unique filename
    $newFilename = uniqid() . '.' . $filetype;
    $uploadPath = '../../uploads/profile_pictures/' . $newFilename;

    // Create directory if it doesn't exist
    if (!file_exists('../../uploads/profile_pictures')) {
        mkdir('../../uploads/profile_pictures', 0777, true);
    }

    // Move file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to upload file');
    }

    return 'uploads/profile_pictures/' . $newFilename;
}

function logAdminAction($action) {
    $stmt = $GLOBALS['pdo']->prepare("
        INSERT INTO admin_logs (admin_id, action, ip_address)
        VALUES (:admin_id, :action, :ip)
    ");

    $stmt->execute([
        ':admin_id' => $_SESSION['user_id'],
        ':action' => $action,
        ':ip' => $_SERVER['REMOTE_ADDR']
    ]);
}

function sendWelcomeEmail($email, $username, $password) {
    $subject = "Welcome to HFC Management System";
    $message = "Hello,\n\n"
             . "Your supervisor account has been created.\n"
             . "Username: $username\n"
             . "Password: $password\n\n"
             . "Please change your password after first login.\n\n"
             . "Best regards,\nHFC Admin Team";

    mail($email, $subject, $message);
} 