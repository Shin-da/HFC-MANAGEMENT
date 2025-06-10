<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <?php 
    // include './reusable/header.php'; 

    $sql = "SELECT * FROM user WHERE uid = " . $_GET['uid'];
    ?>
</head>
<body>
    <div>
        <?php
        require '../database/dbconnect.php';

        if (isset($_GET['uid'])) {
            $uid = $_GET['uid'];
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
        ?>
        <form action="./process/editUserProcess.php" method="post">
            <input type="hidden" name="uid" value="<?php echo $uid; ?>">
            <label for="email">Email:</label><br>
            <input type="text" name="email" value="<?php echo $row['email']; ?>"><br>
            <label for="role">Role:</label><br>
            <select name="role">
                <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="manager" <?php if ($row['role'] == 'manager') echo 'selected'; ?>>Manager</option>
            </select><br><br>
            <input type="submit" value="Save">
        </form>
        <?php
                }
            } else {
                echo "0 results";
            }
        } else {
            header("Location: ./admin.php?error=No user ID provided");
            exit();
        }
        $conn->close();
        ?>
    </div>
</body>
</html>

