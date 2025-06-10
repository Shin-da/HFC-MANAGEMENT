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
        <style>
            .edit-user-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .edit-user-form {
                width: 100%;
            }

            .edit-user-form label {
                display: block;
                width: ;
            }

            .edit-user-form input,
            .edit-user-form select {
                width: 100%;
                padding: 10px;
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

            .input-group {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 40px;
                background-color: #ccc;
                margin-bottom: 20px;
            }

            .bottom-input-group {
                width: 40%;
                display: flex;
                justify-content: space-between;
                align-items: center;

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
                include '../reusable/navbarNoSearch.html';
                ?>

                <div class="edit-user-container">
                    <h1 class="title">Edit User</h1>

                    <?php
                    include '../database/dbconnect.php';

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $uid = $_GET['uid'];
                    $sql = "SELECT * FROM user WHERE uid = '$uid'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <form class="edit-user-form" action="./process/editUserProcess.php" method="post">
                                <input type="hidden" name="uid" value="<?php echo $uid; ?>">

                                <div class="input-group">
                                    <label for="email">Email:</label>
                                    <input type="text" name="email" value="<?php echo $row['email']; ?>"><br>
                                </div>

                                <div class="input-group">
                                    <label for="name">Name:</label>
                                    <input type="text" name="name" value="<?php echo $row['name']; ?>"><br>
                                </div>

                                <div class="input-group">
                                    <label for="password">Password:</label>
                                    <input type="password" name="password" value="<?php echo $row['password']; ?>">
                                    <div class="icon">
                                        <i class="bx bx-show" id="togglePassword"></i>
                                    </div>
                                </div>

                                <br>

                                <label for="role">Role:</label>
                                <select name="role">
                                    <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                    <option value="manager" <?php if ($row['role'] == 'manager') echo 'selected'; ?>>Manager</option>
                                </select><br><br>

                                <div class="bottom-input-group">
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

                    $conn->close();
                    ?>
                </div>

            </div>

        </section>

    </body>
    <script>
        var togglePassword = document.getElementById("togglePassword");
        var password = document.getElementById("password");

        togglePassword.addEventListener('click', function() {
            if (password.type === "password") {
                password.type = "text";
                togglePassword.classList.add('bx-hide');
                togglePassword.classList.remove('bx-show');
            } else {
                password.type = "password";
                togglePassword.classList.add('bx-show');
                togglePassword.classList.remove('bx-hide');
            }
        });
    </script>
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