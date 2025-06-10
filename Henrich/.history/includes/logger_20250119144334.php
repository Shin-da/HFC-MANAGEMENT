<?php
function logActivity($userId, $activity, $activityType) {
    global $conn;
    
    $userId = mysqli_real_escape_string($conn, $userId);
    $activity = mysqli_real_escape_string($conn, $activity);
    $activityType = mysqli_real_escape_string($conn, $activityType);
    
    $query = "INSERT INTO activity_log (uid, activity, activity_type, timestamp) 
              VALUES ('$userId', '$activity', '$activityType', NOW())";
    
    if ($result = mysqli_query($conn, $query)) {
        $activityId = mysqli_insert_id($conn);
        
        // Create notification based on activity type
        switch($activityType) {
            case 'login':
                createNotification($userId, "New login detected", $activityId);
                break;
            case 'create':
                createNotification($userId, "New item created: " . $activity, $activityId);
                break;
            case 'update':
                createNotification($userId, "Item updated: " . $activity, $activityId);
                break;
            case 'delete':
                createNotification($userId, "Item deleted: " . $activity, $activityId);
                break;
        }
        
        return true;
    }
    return false;
}

function createNotification($userId, $message, $activityId) {
    global $conn;
    
    $userId = mysqli_real_escape_string($conn, $userId);
    $message = mysqli_real_escape_string($conn, $message);
    $activityId = mysqli_real_escape_string($conn, $activityId);
    
    $query = "INSERT INTO notifications (user_id, message, activity_id) 
              VALUES ('$userId', '$message', '$activityId')";
    
    return mysqli_query($conn, $query);
}

// Example usage:
// logActivity($userId, "User logged in", "login");
// logActivity($userId, "Created new record #123", "create");
// logActivity($userId, "Updated record #123", "update");
// logActivity($userId, "Deleted record #123", "delete");
?>
