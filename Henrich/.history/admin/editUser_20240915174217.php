<<<<<<<<<<<<<<  ✨ Codeium Command 🌟  >>>>>>>>>>>>>>>>
<!-- Admin Page -->
<?php
require '../reusable/redirect404.php';

session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] != 'admin') {
        header("Location: ../index.php");
        exit();
    }
    $uid = $_GET['uid'] ?? '';
    if (empty($uid)) {
        header("Location: admin.php");
        exit();
    }
?>
    <!DOCTYPE html>
    <html>

    include '../database/dbconnect.php';
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Page</title>
        <?php require '../reusable/header.php'; ?>
        <style>
            .edit-user-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 20px;
            }

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
            .edit-user-form {
                width: 100%;
            }

    $sql = "SELECT * FROM user WHERE uid = '$uid'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <!DOCTYPE html>
            <html>
            .edit-user-form label {
                display: block;
                margin-bottom: 10px;
            }

            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Edit User</title>
                <?php require '../reusable/header.php'; ?>
                <style>
                    .edit-user-container {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 20px;
            .edit-user-form input[type="text"],
            .edit-user-form select {
                width: 100%;
                padding: 10px;
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .edit-user-form input[type="submit"] {
                background-color: #4CAF50;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .edit-user-form input[type="submit"]:hover {
                background-color: #45a049;
            }

        </style>
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

                <div class="edit-user-container">
                    <h1 class="title">Edit User</h1>

                    <?php
                    include '../database/dbconnect.php';

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    .edit-user-form {
                        width: 100%;
                    $uid = $_GET['uid'];
                    $sql = "SELECT * FROM user WHERE uid = '$uid'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <form class="edit-user-form" action="./process/editUserProcess.php" method="post">
                                <input type="hidden" name="uid" value="<?php echo $uid; ?>">

                                <label for="email">Email:</label><br>
                                <input type="text" name="email" value="<?php echo $row['email']; ?>"><br>

                                <label for="role">Role:</label><br>
                                <select name="role">
                                    <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                    <option value="manager" <?php if ($row['role'] == 'manager') echo 'selected'; ?>>Manager</option>
                                </select><br><br>

                                <div>
                                    <input type="hidden" name="uid" value="<?php echo $uid; ?>">
                                    <a href="javascript:history.back()"><input type="button" value="Back"></a>
                                    <input type="reset" value="Reset">
                                    <input type="submit" value="Save">
                                </div>
                            </form>

                            <?php
                        }
                    } else {
                        echo "0 results";
                    }

                    .edit-user-form label {
                        display: block;
                        margin-bottom: 10px;
                    }
                    $conn->close();
                    ?>
                </div>

                    .edit-user-form input[type="text"],
                    .edit-user-form select {
                        width: 100%;
                        padding: 10px;
                        margin-bottom: 20px;
                        border: 1px solid #ccc;
                        border-radius: 5px;
                    }
            </div>

                    .edit-user-form input[type="submit"] {
                        background-color: #4CAF50;
                        color: white;
                        padding: 10px 20px;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                    }
        </section>

                    .edit-user-form input[type="submit"]:hover {
                        background-color: #45a049;
                    }
    </body>

                </style>
            </head>
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

<<<<<<<  255a15fd-da01-471c-9cdd-69fd996fa5af  >>>>>>>