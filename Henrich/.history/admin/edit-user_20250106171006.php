<!-- Admin Page -->
<?php
require '../reusable/redirect404.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Page</title>
        <?php require '../reusable/header.php'; ?>
        <style>
            body,
            html {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f4f4f9;
            }

            .container {
                max-width: 800px;
                width: 100%;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                margin: 20px;
            }

            .edit-user-container {
                padding: 20px;
            }

            .edit-user-form {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .edit-user-form label {
                font-weight: bold;
                margin-bottom: 5px;
            }

            .edit-user-form input,
            .edit-user-form select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                box-sizing: border-box;
            }

            .edit-user-form input[type="button"],
            .edit-user-form input[type="submit"] {
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .edit-user-form input[value="back"] {
                background-color: #f44336;
            }

            .edit-user-form input[type="button"]:hover,
            .edit-user-form input[type="submit"]:hover {
                opacity: 0.8;
            }

            .input-group {
                display: flex;
                flex-direction: column;
            }

            .bottom-input-group {
                display: flex;
                justify-content: space-between;
            }

            .icon {
                cursor: pointer;
                padding-left: 10px;
            }
        </style>
    </head>

    <body>
        <?php include 'admin-sidebar.php'; // Sidebar 
        ?>

        <section class="panel">
            <div class="container">
                <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR 
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
                            <form class="edit-user-form" action="./process/edit-user.process.php" method="post">
                                <input type="hidden" name="uid" value="<?php echo $uid; ?>">

                                <div class="input-group">
                                    <label for="useremail">Email:</label>
                                    <input type="text" name="useremail" value="<?php echo $row['useremail']; ?>">
                                </div>

                                <div class="input-group">
                                    <label for="username">Name:</label>
                                    <input type="text" name="username" value="<?php echo $row['username']; ?>">
                                </div>

                                <div class="input-group">
                                    <label for="password">Password:</label>
                                    <input class="input" type="password" name="password" value="<?php echo $row['password']; ?>" id="password">
                                    <span class="icon" id="togglePassword"><i class="bx bx-show"></i></span>
                                </div>

                                <div class="input-group">
                                    <label for="role">Role:</label>
                                    <select name="role">
                                        <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                        <option value="supervisor" <?php if ($row['role'] == 'supervisor') echo 'selected'; ?>>Manager</option>
                                    </select>
                                </div>

                                <div class="bottom-input-group">
                                    <a href="javascript:history.back()"><input type="button" value="Back"></a>
                                    <input type="submit" value="Save">
                                </div>
                            </form>
                            <script>
                                document.getElementById("togglePassword").addEventListener('click', function() {
                                    const password = document.getElementById("password");
                                    const icon = this.querySelector("i");
                                    if (password.type === "password") {
                                        password.type = "text";
                                        icon.classList.replace('bx-show', 'bx-hide');
                                    } else {
                                        password.type = "password";
                                        icon.classList.replace('bx-hide', 'bx-show');
                                    }
                                });
                            </script>
                    <?php
                        }
                    } else {
                        echo "No user found.";
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
        </section>

        <script src="../resources/js/script.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
        <script src="../resources/js/chart.js"></script>
    </body>

    </html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
