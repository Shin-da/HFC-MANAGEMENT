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
</head>

<body>
    <?php include '../reusable/sidebar.php';  // Sidebar          

    $uid = $_SESSION['uid'];

    $sql = "SELECT * FROM user WHERE uid = '$uid'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $name = $row['name'];
            $useremail = $row['useremail'];
            $username = $row['username'];
            $role = $row['role'];
        }
    } else {
        header("Location: ../404.php");
        exit();
    }
    ?>
<div class="panel">
<?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR ?>    
.container
    <div class="main-content">
        <h2>My Account</h2>
        <form action="./process/edit-account.process.php" method="post">
            <input type="hidden" name="uid" value="<?php echo $uid; ?>">
            <div class="input-group">
                <label for="name">Name:</label>
                <input type="text" name="name" value="<?php echo $name; ?>" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo $username; ?>" required>
            </div>
            <div class="input-group">
                <label for="role">Role:</label>
                <select name="role" disabled>
                    <option value="superadmin" <?php if ($role == 'superadmin') echo 'selected'; ?>>Super Admin</option>
                    <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="supervisor" <?php if ($role == 'supervisor') echo 'selected'; ?>>Supervisor</option>
                    <option value="cashier" <?php if ($role == 'cashier') echo 'selected'; ?>>Cashier</option>
                </select>
            </div>
            <div class="input-group">
                <label for="oldpassword">Old Password:</label>
                <input type="password" name="oldpassword" required>
            </div>
            <div class="input-group">
                <label for="newpassword">New Password:</label>
                <input type="password" name="newpassword" required>
            </div>
            <div class="input-group">
                <label for="renewpassword">Re New Password:</label>
                <input type="password" name="renewpassword" required>
            </div>
            <div class="input-group">
                <input type="submit" value="Save">
            </div>
        </form>
    </div>
</div>
    
    <?php require '../reusable/footer.php'; ?>
    