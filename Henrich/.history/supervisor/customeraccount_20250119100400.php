<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

try {
    // Get total accounts count
    $total_records_query = "SELECT COUNT(*) as count FROM customeraccount WHERE accountstatus = 'Active'";
    $total_records = $conn->query($total_records_query)->fetch_assoc()['count'];

    // Get today's registered accounts
    $today_registered_query = "SELECT COUNT(*) as count FROM customeraccount WHERE DATE(created_at) = CURDATE()";
    $today_registered = $conn->query($today_registered_query)->fetch_assoc()['count'];

    // Update the table display query
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM customeraccount WHERE accountstatus = 'Active' ORDER BY accountid DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Calculate total pages
    $total_pages = ceil($total_records / $limit);

} catch (mysqli_sql_exception $e) {
    error_log("Error in customeraccount.php: " . $e->getMessage());
    $total_records = 0;
    $today_registered = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Accounts</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .dashboard-header {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin-bottom: 20px;