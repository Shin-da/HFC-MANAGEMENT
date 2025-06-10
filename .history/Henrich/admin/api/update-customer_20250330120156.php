<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['accountId', 'customerName', 'customerAddress', 'customerPhone', 'customerEmail', 'username', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate email format
    if (!filter_var($_POST['customerEmail'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if username or email already exists for other customers
    $stmt = $GLOBALS['pdo']->prepare("SELECT COUNT(*) FROM customeraccount WHERE (username = ? OR useremail = ?) AND accountid != ?");
    $stmt->execute([$_POST['username'], $_POST['customerEmail'], $_POST['accountId']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Username or email already exists');
    }

    // Handle profile picture upload
    $profilePicture = null;
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

    // Start building the update query
    $update_fields = [
        'customername' => $_POST['customerName'],
        'customeraddress' => $_POST['customerAddress'],
        'customerphonenumber' => $_POST['customerPhone'],
        'useremail' => $_POST['customerEmail'],
        'username' => $_POST['username'],
        'status' => $_POST['status']
    ];

    // If password is provided, update it
    if (!empty($_POST['password'])) {
        $update_fields['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // If new profile picture is uploaded, update it
    if ($profilePicture) {
        $update_fields['profilepicture'] = $profilePicture;
    }

    // Build the SQL query dynamically
    $sql = "UPDATE customeraccount SET ";
    $params = [];
    foreach ($update_fields as $field => $value) {
        $sql .= "$field = ?, ";
        $params[] = $value;
    }
    $sql = rtrim($sql, ', ') . " WHERE accountid = ?";
    $params[] = $_POST['accountId'];

    // Execute the update
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        'status' => 'success',
        'message' => 'Customer updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 