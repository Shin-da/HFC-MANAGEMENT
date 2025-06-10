<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['username', 'useremail', 'password', 'firstName', 'lastName', 'role'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate email format
    if (!filter_var($_POST['useremail'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR useremail = ?");
    $stmt->execute([$_POST['username'], $_POST['useremail']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Username or email already exists');
    }

    // Hash password
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("
        INSERT INTO users (
            username, useremail, password, first_name, last_name, 
            role, department, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['username'],
        $_POST['useremail'],
        $hashed_password,
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['role'],
        $_POST['department'] ?? null,
        $_POST['status'] ?? 'active'
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'User added successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 