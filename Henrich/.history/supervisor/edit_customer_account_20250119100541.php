<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

if (!isset($_GET['id'])) {
    header('Location: customeraccount.php');
    exit();
}

$accountId = $_GET['id'];
$sql = "SELECT * FROM customeraccount WHERE accountid = ? AND accountstatus != 'Deleted'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $accountId);
$stmt->execute();
$result = $stmt->get_result();
$account = $result->fetch_assoc();

if (!$account) {
    header('Location: customeraccount.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer Account</title>
    <?php require '../reusable/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .edit-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-secondary);
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: var(--surface);
            color: var(--text-primary);
        }

        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--light);
        }

        .btn-secondary {
            background: var(--secondary);
            color: var(--light);
        }
    </style>
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="dashboard panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <div class="edit-form">
            <h2>Edit Customer Account</h2>
            <form id="editAccountForm">
                <input type="hidden" name="accountid" value="<?php echo $account['accountid']; ?>">
                
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" name="customername" value="<?php echo htmlspecialchars($account['customername']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="useremail" value="<?php echo htmlspecialchars($account['useremail']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($account['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Account Status</label>
                    <select name="accountstatus">
                        <option value="Active" <?php if($account['accountstatus'] == 'Active') echo 'selected'; ?>>Active</option>
                        <option value="Inactive" <?php if($account['accountstatus'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                        <option value="Suspended" <?php if($account['accountstatus'] == 'Suspended') echo 'selected'; ?>>Suspended</option>
                    </select>
                </div>
                
                <div class="btn-container">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='customeraccount.php'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.getElementById('editAccountForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(this);
                const response = await fetch('update_customer_account.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Account updated successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    window.location.href = 'customeraccount.php';
                } else {
                    throw new Error(data.message || 'Failed to update account');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message
                });
            }
        });
    </script>
</body>
</html>
