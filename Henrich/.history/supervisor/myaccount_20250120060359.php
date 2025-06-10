<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>MY ACCOUNT</title>
    <?php require '../reusable/head.php'; ?>    
    <!-- Replace local SweetAlert2 links with CDN -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .container-fluid {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .account-wrapper {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            margin-top: 1rem;
        }

        .profile-card {
            background: var(--sand);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .avatar {
            width: 120px;
            height: 120px;
            background: var(--primary);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar i {
            font-size: 3rem;
            color: var(--sand);
        }

        .user-info h2 {
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        .user-role {
            color: var(--secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .account-details {
            background: var(--sand);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h3 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border);
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            background: var(--background);
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(56, 90, 65, 0.1);
        }

        .btn-submit {
            background: var(--primary);
            color: var(--sand);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            background: var(--secondary);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .account-wrapper {
                grid-template-columns: 1fr;
            }
            
            .profile-card {
                margin-bottom: 1rem;
            }
        }

        /* Add SweetAlert2 custom styles */
        .swal2-popup {
            font-size: 0.9rem !important;
            border-radius: 12px !important;
        }
        .swal2-toast {
            background: var(--sand) !important;
            color: var(--text-primary) !important;
        }
        .swal2-confirm {
            background: var(--primary) !important;
        }
        .swal2-cancel {
            background: var(--secondary) !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php 
    include '../includes/sidebar.php';
    include '../includes/navbar.php';
    
    // Update session message handling
    if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('" . 
                    (isset($_SESSION['success']) ? 'success' : 'error') . "', '" . 
                    (isset($_SESSION['success']) ? $_SESSION['success'] : $_SESSION['error']) . "'
                );
            });
        </script>";
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }
    
    $uid = $_SESSION['uid'];
    $sql = "SELECT * FROM users WHERE  user_ = '$uid'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $useremail = $row['useremail'];
        $username = $row['username'];
        $role = $row['role'];
    } else {
        header("Location: ../404.php");
        exit();
    }
    ?>
    
    <section class="panel">
        <div class="container-fluid">
            <div class="account-wrapper">
                <div class="profile-card">
                    <div class="avatar">
                        <i class="bx bxs-user"></i>
                    </div>
                    <div class="user-info">
                        <h2><?php echo $username; ?></h2>
                        <p class="user-role"><?php echo $role; ?></p>
                    </div>
                </div>

                <div class="account-details">
                    <form id="editAccountForm" action="./process/edit-account.process.php" method="post" class="form-section">
                        <h3>Account Information</h3>
                        <input type="hidden" name="uid" value="<?php echo $uid; ?>">
                        <div class="input-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo $useremail; ?>" required>
                        </div>
                        <div class="input-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                        </div>
                        <div class="input-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" disabled>
                                <option value="superadmin" <?php if ($role == 'superadmin') echo 'selected'; ?>>Super Admin</option>
                                <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
                                <option value="supervisor" <?php if ($role == 'supervisor') echo 'selected'; ?>>Supervisor</option>
                                <option value="cashier" <?php if ($role == 'cashier') echo 'selected'; ?>>Cashier</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-submit">Save Changes</button>
                    </form>

                    <form id="passwordChangeForm" action="./process/request-password-change.process.php" method="post" class="form-section">
                        <h3>Change Password</h3>
                        <input type="hidden" name="uid" value="<?php echo $uid; ?>">
                        <div class="input-group">
                            <label for="oldpassword">Current Password</label>
                            <input type="password" id="oldpassword" name="oldpassword" required>
                        </div>
                        <button type="submit" class="btn-submit">Request Password Change</button>
                    </form>
                </div>
            </div>
        </div>
        <?php require '../reusable/footer.php'; ?>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editAccountForm = document.getElementById('editAccountForm');
            const passwordChangeForm = document.getElementById('passwordChangeForm');

            if (editAccountForm) {
                editAccountForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = this;
                    
                    showConfirm(
                        'Update Account',
                        'Are you sure you want to update your account information?'
                    ).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }

            if (passwordChangeForm) {
                passwordChangeForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = this;
                    
                    showConfirm(
                        'Change Password',
                        'Are you sure you want to request a password change?',
                        'warning'
                    ).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });

        // Update Account Details Handler
        function updateAccount(formData) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            $.ajax({
                url: 'process/update_account.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        Toast.fire({
                            icon: data.status,
                            title: data.message,
                            customClass: {
                                popup: 'colored-toast'
                            }
                        });
                        if (data.status === 'success') {
                            setTimeout(() => location.reload(), 3000);
                        }
                    } catch (e) {
                        Toast.fire({
                            icon: 'error',
                            title: 'An error occurred while updating',
                            customClass: {
                                popup: 'colored-toast'
                            }
                        });
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Failed to update account details',
                        customClass: {
                            popup: 'colored-toast'
                        }
                    });
                }
            });
        }

        // Password Change Request Handler
        function requestPasswordChange() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            $.ajax({
                url: 'process/request_password_change.php',
                type: 'POST',
                data: $('#passwordChangeForm').serialize(),
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        Toast.fire({
                            icon: data.status,
                            title: data.message,
                            customClass: {
                                popup: 'colored-toast'
                            }
                        });
                    } catch (e) {
                        Toast.fire({
                            icon: 'error',
                            title: 'An error occurred while processing request',
                            customClass: {
                                popup: 'colored-toast'
                            }
                        });
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Failed to send password change request',
                        customClass: {
                            popup: 'colored-toast'
                        }
                    });
                }
            });
        }

        // Form submit handlers
        $('#accountUpdateForm').on('submit', function(e) {
            e.preventDefault();
            updateAccount(new FormData(this));
        });

        $('#passwordChangeForm').on('submit', function(e) {
            e.preventDefault();
            requestPasswordChange();
        });
    </script>
</body>
</html>


