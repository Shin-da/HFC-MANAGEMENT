<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

header('Content-Type: application/json');

try {
    $search = $_GET['query'] ?? '';
    $search = "%{$search}%";

    $stmt = $pdo->prepare("
        SELECT 
            user_id,
            username,
            role,
            is_online,
            last_online
        FROM users 
        WHERE (username LIKE ? OR role LIKE ?)
        AND role IN ('supervisor', 'ceo', 'admin')
        AND user_id != ?
        ORDER BY is_online DESC, username ASC
    ");

    $stmt->execute([$search, $search, $_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Search failed'
    ]);
}
