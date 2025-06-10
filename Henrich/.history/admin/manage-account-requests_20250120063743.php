<?php
require_once 'access_control.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $temp_password = bin2hex(random_bytes(8)); // Generate temporary password
    
    if ($action === 'approve') {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Get request details
            $stmt = $conn->prepare("SELECT * FROM account_requests WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $request = $stmt->get_result()->fetch_assoc();
            
            // Create user account
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, useremail, role, password) VALUES (?, ?, 'employee', ?)");
            $stmt->bind_param("sss", $request['username'], $request['email'], $hashed_password);
            $stmt->execute();
            
            // Update request status
            $stmt = $conn->prepare("UPDATE account_requests SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            
            // Send email with temporary password
            mail($request['email'], 
                 "Account Approved", 
                 "Your account has been approved. Temporary password: $temp_password");
            
            $conn->commit();
            $success = "Account approved and created successfully";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error processing request";
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE account_requests SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }
}

// Get pending requests
$result = $conn->query("SELECT * FROM account_requests WHERE status = 'pending'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Requests - HFC Management</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/requests.css">
</head>
<body class="admin-body">
    <?php include '../includes/admin_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h2><i class="fas fa-user-plus"></i> Account Requests</h2>
            <div class="filter-controls">
                <select id="statusFilter" onchange="filterRequests(this.value)">
                    <option value="all">All Requests</option>
                    <option value="pending" selected>Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="requests-grid" id="requestsContainer">
            <?php while ($request = $result->fetch_assoc()): ?>
            <div class="request-card" data-status="<?= $request['status'] ?>">
                <div class="request-header">
                    <span class="request-id">#<?= $request['id'] ?></span>
                    <span class="request-date"><?= date('M d, Y', strtotime($request['created_at'])) ?></span>
                </div>
                <div class="request-body">
                    <div class="request-info">
                        <p><i class="fas fa-user"></i> <?= htmlspecialchars($request['username']) ?></p>
                        <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($request['email']) ?></p>
                        <p><i class="fas fa-id-card"></i> <?= htmlspecialchars($request['employee_id']) ?></p>
                    </div>
                    <div class="request-actions">
                        <button class="btn btn-success" onclick="processRequest(<?= $request['id'] ?>, 'approve')">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn btn-danger" onclick="processRequest(<?= $request['id'] ?>, 'reject')">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>