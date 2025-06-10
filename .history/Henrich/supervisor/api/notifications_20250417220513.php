<?php
/**
 * Notifications API
 * 
 * Handles getting and managing notifications
 */
require_once '../../includes/config.php';
require_once '../../includes/classes/NotificationManager.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$conn = $GLOBALS['conn'];
$notificationManager = new NotificationManager($conn);

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different request types
switch ($method) {
    case 'GET':
        handleGetRequest($notificationManager, $userId);
        break;
        
    case 'POST':
        handlePostRequest($notificationManager, $userId);
        break;
        
    case 'PUT':
        handlePutRequest($notificationManager, $userId);
        break;
        
    case 'DELETE':
        handleDeleteRequest($notificationManager, $userId);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}

/**
 * Handle GET requests
 * 
 * @param NotificationManager $notificationManager
 * @param int $userId
 */
function handleGetRequest($notificationManager, $userId) {
    // Check for action parameter
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    switch ($action) {
        case 'list':
            // Get parameters with defaults
            $includeRead = isset($_GET['include_read']) ? (bool)$_GET['include_read'] : false;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            
            // Get notifications
            $notifications = $notificationManager->getNotifications($userId, $includeRead, $limit);
            echo json_encode(['notifications' => $notifications]);
            break;
            
        case 'count':
            $count = $notificationManager->getUnreadCount($userId);
            echo json_encode(['count' => $count]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

/**
 * Handle POST requests (create new notification)
 * 
 * @param NotificationManager $notificationManager
 * @param int $userId
 */
function handlePostRequest($notificationManager, $userId) {
    // Check if user is admin or has permission
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        return;
    }
    
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        return;
    }
    
    // Validate required fields
    if (!isset($data['type']) || !isset($data['title']) || !isset($data['message'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }
    
    // Set defaults for optional fields
    $severity = isset($data['severity']) ? $data['severity'] : 'info';
    $targetUserId = isset($data['user_id']) ? $data['user_id'] : 0;
    $metadata = isset($data['metadata']) ? $data['metadata'] : null;
    
    // Create notification
    $notificationId = $notificationManager->createNotification(
        $data['type'],
        $data['title'],
        $data['message'],
        $severity,
        $targetUserId,
        $metadata
    );
    
    if ($notificationId) {
        echo json_encode(['success' => true, 'notification_id' => $notificationId]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create notification']);
    }
}

/**
 * Handle PUT requests (mark as read)
 * 
 * @param NotificationManager $notificationManager
 * @param int $userId
 */
function handlePutRequest($notificationManager, $userId) {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        return;
    }
    
    // Check for action
    if (!isset($data['action'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing action parameter']);
        return;
    }
    
    switch ($data['action']) {
        case 'mark_read':
            if (!isset($data['notification_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing notification_id']);
                return;
            }
            
            $success = $notificationManager->markAsRead($data['notification_id'], $userId);
            echo json_encode(['success' => $success]);
            break;
            
        case 'mark_all_read':
            $success = $notificationManager->markAllAsRead($userId);
            echo json_encode(['success' => $success]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

/**
 * Handle DELETE requests (delete notification)
 * 
 * @param NotificationManager $notificationManager
 * @param int $userId
 */
function handleDeleteRequest($notificationManager, $userId) {
    // Get notification ID from URL parameter
    $notificationId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if (!$notificationId) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing notification ID']);
        return;
    }
    
    $success = $notificationManager->deleteNotification($notificationId);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete notification']);
    }
}

/**
 * Check if current user is an admin
 * 
 * @return bool
 */
function isAdmin() {
    // Check if user is admin based on session
    return isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor');
} 