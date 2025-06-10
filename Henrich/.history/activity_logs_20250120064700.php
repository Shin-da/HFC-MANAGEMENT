<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// Filtering options
$filter_user = isset($_GET['user']) ? $_GET['user'] : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

// Build query with filters
$query = "SELECT al.*, u.username 
          FROM activity_log al 
          LEFT JOIN users u ON al.uid = u.id 
          WHERE 1=1";

if ($filter_user) $query .= " AND al.uid = '$filter_user'";
if ($filter_type) $query .= " AND al.activity_type = '$filter_type'";
if ($filter_date) $query .= " AND DATE(al.timestamp) = '$filter_date'";

$query .= " ORDER BY al.timestamp DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Activity Logs</h2>
        
        <!-- Filter Form -->
        <form method="GET" class="filter-form">
            <select name="user">
                <option value="">All Users</option>
                <?php
                $users = mysqli_query($conn, "SELECT id, username FROM users");
                while ($user = mysqli_fetch_assoc($users)) {
                    echo "<option value='{$user['id']}'>{$user['username']}</option>";
                }
                ?>
            </select>
            
            <select name="type">
                <option value="">All Types</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="create">Create</option>
                <option value="update">Update</option>
                <option value="delete">Delete</option>
            </select>
            
            <input type="date" name="date">
            <button type="submit">Filter</button>
        </form>

        <!-- Logs Table -->
        <table class="logs-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Activity</th>
                    <th>Type</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['activity']); ?></td>
                        <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($row['timestamp'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
