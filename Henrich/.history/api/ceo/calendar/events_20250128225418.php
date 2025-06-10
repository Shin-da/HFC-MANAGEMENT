<?php
require_once '../../../includes/config.php';
require_once '../../../includes/session.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ceo') {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            $events = getEvents($conn);
            echo json_encode(['success' => true, 'events' => $events]);
            break;
            
        case 'add':
            $eventData = json_decode(file_get_contents('php://input'), true);
            $result = addEvent($conn, $eventData);
            echo json_encode(['success' => true, 'id' => $result]);
            break;
            
        case 'update':
            $eventData = json_decode(file_get_contents('php://input'), true);
            updateEvent($conn, $eventData);
            echo json_encode(['success' => true]);
            break;
            
        case 'delete':
            $eventId = $_POST['event_id'] ?? null;
            deleteEvent($conn, $eventId);
            echo json_encode(['success' => true]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getEvents($conn) {
    $query = "SELECT 
        event_id,
        title,
        description,
        start_time,
        end_time,
        event_type,
        location,
        priority
    FROM executive_events
    WHERE event_date >= CURRENT_DATE()
    ORDER BY start_time ASC";
    
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
