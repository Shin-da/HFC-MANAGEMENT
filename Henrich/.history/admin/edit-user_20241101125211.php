<!-- Admin Page -->
<?php
require '../reusable/redirect404.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

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
                margin-right: 10px;
            }

            .edit-user-form input,
            .edit-user-form select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .edit-user-form input[value="back"] {
                background-color: #f44336;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .edit-user-form .bottom-input-group input[value="back"]:hover {
                background-color: #d32f2f;
            }

            .edit-user-form .bottom-input-group input[type="submit"] {
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
                /* width: 100%; */
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
                /* background-color: #515151; */
                gap: 4px;
            }

            .input-group .icon {
                padding: 10px;
                /* border: 1px solid #515151; */
                color: #999;
            }

            .input-group .icon i {
                font-size: 16px;
                color: #999;
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
        <?php include 'admin-sidebar.php'; // Sidebar     
        ?>

        <section class="panel">
            <div class="container">
                <?php include '../reusable/navbarNoSearch.html';      // TOP NAVBAR  
                ?>
                <div class="edit-user-container">
                    <h1 class="title">Edit User</h1>

                    <?php   include '../database/dbconnect.php';
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $uid = $_GET['uid'];
                    $sql = "SELECT * FROM user WHERE uid = '$uid'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <form class="edit-user-form" action="./process/edit-user.process.php" method="post">
                                <input type="hidden" name="uid" value="<?php echo $uid; ?>">

                                <div class="input-group">
                                    <label for="useremail">Email:</label>
                                    <input type="text" name="useremail" value="<?php echo $row['useremail']; ?>"><br>
                                </div>

                                <div class="input-group">
                                    <label for="username">Name:</label>
                                    <input type="text" name="username" value="<?php echo $row['username']; ?>"><br>
                                </div>

                                <div class="input-group">
                                    <label for="password">Password:</label>
                                    <input class="input" type="text" name="password" value="<?php echo $row['password'];  ?>" id="password">
                                    <div class="icon">
                                        <i class="bx bx-show" id="togglePassword"></i>
                                    </div>
                                </div>

                                <br>

                                <div class="input-group">
                                    <label for="role">Role:</label>
                                    <select name="role">
                                        <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                        <option value="supervisor" <?php if ($row['role'] == 'supervisor') echo 'selected'; ?>>Manager</option>

                                    </select><br><br>
                                </div>
                                <div class="bottom-input-group">
                                    <input type="hidden" name="uid" value="<?php echo $uid; ?>">
                                    <a href="javascript:history.back()"><input type="button" value="Back"></a>
                                    <input type="submit" value="Save">
                                </div>
                            </form>
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