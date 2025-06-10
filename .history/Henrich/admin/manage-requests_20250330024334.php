<?php
session_start();
require '../includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['uid']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Handle request actions (approve/reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? null;
    $action = $_POST['action'] ?? null;
    $notes = $_POST['notes'] ?? '';

    if ($request_id && $action) {
        try {
            if ($action === 'approve') {
                // Get request details
                $stmt = $pdo->prepare("SELECT * FROM account_requests WHERE request_id = ?");
                $stmt->execute([$request_id]);
                $request = $stmt->fetch();

                // Create user account
                $stmt = $pdo->prepare("
                    INSERT INTO users (firstname, lastname, useremail, password, role, department, position) 
                    VALUES (?, ?, ?, ?, 'user', ?, ?)
                ");
                
                // Generate temporary password
                $temp_password = bin2hex(random_bytes(8));
                $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
                
                $stmt->execute([
                    $request['firstname'],
                    $request['lastname'],
                    $request['email'],
                    $hashed_password,
                    $request['department'],
                    $request['position']
                ]);

                // Update request status
                $stmt = $pdo->prepare("
                    UPDATE account_requests 
                    SET status = 'approved', 
                        processed_date = CURRENT_TIMESTAMP, 
                        processed_by = ?, 
                        notes = ? 
                    WHERE request_id = ?
                ");
                $stmt->execute([$_SESSION['uid'], $notes, $request_id]);

                // TODO: Send email to user with their temporary password
                $success = "Account approved and created successfully. Temporary password: " . $temp_password;
            } else if ($action === 'reject') {
                $stmt = $pdo->prepare("
                    UPDATE account_requests 
                    SET status = 'rejected', 
                        processed_date = CURRENT_TIMESTAMP, 
                        processed_by = ?, 
                        notes = ? 
                    WHERE request_id = ?
                ");
                $stmt->execute([$_SESSION['uid'], $notes, $request_id]);
                $success = "Request rejected successfully";
            }
        } catch (PDOException $e) {
            error_log("Error processing request: " . $e->getMessage());
            $error = "An error occurred while processing the request";
        }
    }
}

// Get all requests
try {
    $stmt = $pdo->query("
        SELECT ar.*, u.firstname as processed_by_name 
        FROM account_requests ar 
        LEFT JOIN users u ON ar.processed_by = u.user_id 
        ORDER BY 
            CASE 
                WHEN ar.status = 'pending' THEN 1 
                WHEN ar.status = 'approved' THEN 2 
                ELSE 3 
            END,
            ar.request_date DESC
    ");
    $requests = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching requests: " . $e->getMessage());
    $error = "An error occurred while fetching requests";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account Requests - HFC Management System</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .requests-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .requests-table th,
        .requests-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .requests-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .requests-table tr:hover {
            background: #f8f9fa;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close {
            cursor: pointer;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
        }

        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 100px;
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php require '../reusable/admin-nav.php'; ?>
    
    <div class="container">
        <div class="header">
            <h1>Manage Account Requests</h1>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (empty($requests)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox fa-3x"></i>
                <p>No account requests found</p>
            </div>
        <?php else: ?>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['firstname'] . ' ' . $request['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($request['email']); ?></td>
                            <td><?php echo htmlspecialchars($request['department']); ?></td>
                            <td><?php echo htmlspecialchars($request['position']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($request['request_date'])); ?></td>
                            <td>
                                <span class="status status-<?php echo strtolower($request['status']); ?>">
                                    <?php echo ucfirst($request['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($request['status'] === 'pending'): ?>
                                    <div class="action-buttons">
                                        <button class="btn btn-approve" 
                                                onclick="showModal('approve', <?php echo $request['request_id']; ?>)">
                                            Approve
                                        </button>
                                        <button class="btn btn-reject" 
                                                onclick="showModal('reject', <?php echo $request['request_id']; ?>)">
                                            Reject
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <small>
                                        Processed by: <?php echo htmlspecialchars($request['processed_by_name']); ?><br>
                                        on <?php echo date('M d, Y H:i', strtotime($request['processed_date'])); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="actionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle"></h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="actionForm" method="POST">
                <input type="hidden" name="request_id" id="requestId">
                <input type="hidden" name="action" id="actionType">
                
                <div class="form-group">
                    <label for="notes">Notes:</label>
                    <textarea name="notes" id="notes" required></textarea>
                </div>

                <button type="submit" class="btn" id="submitBtn"></button>
            </form>
        </div>
    </div>

    <script>
        function showModal(action, requestId) {
            const modal = document.getElementById('actionModal');
            const modalTitle = document.getElementById('modalTitle');
            const actionType = document.getElementById('actionType');
            const requestIdInput = document.getElementById('requestId');
            const submitBtn = document.getElementById('submitBtn');
            
            modalTitle.textContent = action === 'approve' ? 'Approve Request' : 'Reject Request';
            actionType.value = action;
            requestIdInput.value = requestId;
            submitBtn.textContent = action === 'approve' ? 'Approve' : 'Reject';
            submitBtn.className = action === 'approve' ? 'btn btn-approve' : 'btn btn-reject';
            
            modal.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('actionModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('actionModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html> 