<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <?php 
    // include './reusable/header.php'; 
    ?>
</head>
<body>
    <div>
        <?php
        require '../database/dbconnect.php';

        if (isset($_POST['uid'])) {
            $uid = $_POST['uid'];
            $sql = "SELECT * FROM user WHERE uid = '$uid'";
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
            echo "No user ID provided";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>

