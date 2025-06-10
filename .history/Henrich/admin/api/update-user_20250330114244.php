<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['userId', 'username', 'useremail', 'firstName', 'lastName', 'role'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate email format
    if (!filter_var($_POST['useremail'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if username or email already exists for other users
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR useremail = ?) AND user_id != ?");
    $stmt->execute([$_POST['username'], $_POST['useremail'], $_POST['userId']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Username or email already exists');
    }

    // Start building the update query
    $update_fields = [
        'username' => $_POST['username'],
        'useremail' => $_POST['useremail'],
        'first_name' => $_POST['firstName'],
        'last_name' => $_POST['lastName'],
        'role' => $_POST['role'],
        'department' => $_POST['department'] ?? null,
        'status' => $_POST['status'] ?? 'active'
    ];

    // If password is provided, update it
    if (!empty($_POST['password'])) {
        $update_fields['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Build the SQL query dynamically
    $sql = "UPDATE users SET ";
    $params = [];
    foreach ($update_fields as $field => $value) {
        $sql .= "$field = ?, ";
        $params[] = $value;
    }
    $sql = rtrim($sql, ', ') . " WHERE user_id = ?";
    $params[] = $_POST['userId'];

    // Execute the update
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        'status' => 'success',
        'message' => 'User updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 