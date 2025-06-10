<?php
function logActivity($userId, $activity, $activityType) {
    global $conn;
    
    $userId = mysqli_real_escape_string($conn, $userId);
    $activity = mysqli_real_escape_string($conn, $activity);
    $activityType = mysqli_real_escape_string($conn, $activityType);
    
    $query = "INSERT INTO activity_log (uid, activity, activity_type, timestamp) 
              VALUES ('$userId', '$activity', '$activityType', NOW())";
    
    return mysqli_query($conn, $query);
}

// Example usage:
// logActivity($userId, "User logged in", "login");
// logActivity($userId, "Created new record #123", "create");
// logActivity($userId, "Updated record #123", "update");
// logActivity($userId, "Deleted record #123", "delete");
?>
