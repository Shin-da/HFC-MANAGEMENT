<!-- Admin Page -->
<?php
require '../reusable/redirect404.php';

session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Page</title>
        <?php require '../reusable/header.php'; ?>
    </head>

    <body>
        <?php
        // Sidebar 
        include 'admin-sidebar.php';
        ?>

        <section class="panel">
            <div class="container">
                <?php
                // TOP NAVBAR
                include '../reusable/navbar.html';
                ?>
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


                        <h1 class="title">Edit User</h1>

                        <form action="./process/editUserProcess.php" method="post">

                            <input type="hidden" name="uid" value="<?php echo $uid; ?>"> <!-- Hidden field for uid   -->

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

    <script src="../resources/js/script.js"></script>

    <!-- ======= Charts JS ====== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="../resources/js/chart.js"></script>

    </html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>