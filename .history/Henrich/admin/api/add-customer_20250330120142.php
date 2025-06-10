<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['customerName', 'customerAddress', 'customerPhone', 'customerEmail', 'username', 'password', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate email format
    if (!filter_var($_POST['customerEmail'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if username or email already exists
    $stmt = $GLOBALS['pdo']->prepare("SELECT COUNT(*) FROM customeraccount WHERE username = ? OR useremail = ?");
    $stmt->execute([$_POST['username'], $_POST['customerEmail']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Username or email already exists');
    }

    // Handle profile picture upload
    $profilePicture = 'default.jpg'; // Default profile picture
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/profile_pictures/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['profilePicture']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.');
        }

        $fileName = uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetPath)) {
            $profilePicture = 'uploads/profile_pictures/' . $fileName;
        }
    }

    // Hash password
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert new customer
    $stmt = $GLOBALS['pdo']->prepare("
        INSERT INTO customeraccount (
            customername, customeraddress, customerphonenumber, 
            useremail, username, password, status, profilepicture
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['customerName'],
        $_POST['customerAddress'],
        $_POST['customerPhone'],
        $_POST['customerEmail'],
        $_POST['username'],
        $hashedPassword,
        $_POST['status'],
        $profilePicture
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Customer added successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 