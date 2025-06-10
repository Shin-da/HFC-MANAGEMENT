<?php
require '../reusable/redirect404.php';
// require '../session/session.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <?php
    include '../reusable/header.php';
    ?>
</head>

<body>
    <?php
    // Sidebar 
    include 'admin-sidebar.php';
    ?>
    <section class="panel">
        <?php
        // TOP NAVBAR
        include '../reusable/navbar.html';
        ?>
        <div class="container">
            <?php
            include '../database/dbconnect.php';

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $uid = $_GET['uid'];

            $sql = "SELECT * FROM user WHERE uid = '$uid'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
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

            $conn->close();
            ?>
        </div>
    </section>
    
</body>

</html>