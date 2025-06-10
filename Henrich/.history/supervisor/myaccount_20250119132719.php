<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php'; 
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>MY ACCOUNT</title>
    <?php require '../reusable/header.php'; ?>    
    <link href="../assets/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <style>
        .container-fluid {
            padding: 2rem;
            max-width: 1200px;
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
    </style>
</head>

<body>
    <?php 
    include '../reusable/sidebar.php';
    include '../reusable/navbar.html';
    
    // Move session message checks here
    if (isset($_SESSION['success'])) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '" . $_SESSION['success'] . "',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        </script>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . $_SESSION['error'] . "',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        </script>";
        unset($_SESSION['error']);
    }
    
    $uid = $_SESSION['uid'];
    $sql = "SELECT * FROM user WHERE uid = '$uid'";
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
                    
                    Swal.fire({
                        title: 'Update Account',
                        text: 'Are you sure you want to update your account information?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
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
                    
                    Swal.fire({
                        title: 'Change Password',
                        text: 'Are you sure you want to request a password change?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, proceed!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }

            // Display alerts if they exist in session
            <?php if (isset($_SESSION['success'])) : ?>
                Toast.fire({
                    icon: 'success',
                    title: '<?php echo $_SESSION['success']; ?>'
                });
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])) : ?>
                Toast.fire({
                    icon: 'error',
                    title: '<?php echo $_SESSION['error']; ?>'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>


