/*************  ✨ Codeium Command 🌟  *************/
<?php
session_start();
// The user is logged in, redirect to the home page
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === "admin") {
        header("Location:./admin/admin.php");
        exit();
    } elseif ($_SESSION['role'] === "user") {
        header("Location: ../pages/home.php");
        exit();
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <?php require './reusable/header.php'; ?>

    <!-- sweetalert -->
    <script src="sweetalert2.min.js"></script>
    <script src="/sweetalert/sweetalert.js"></script>
    <link rel="stylesheet" href="sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="resources/css/style.css">

    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f2f2;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .session {
            display: flex;
            flex-direction: row;
            margin: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
        }

        .left {
            flex: 1;
            background: url('./resources/images/henrichlogo.png') no-repeat center center;
            background-size: contain;
            border-radius: 8px 0 0 8px;
            min-height: 320px;
        }

        .login-form {
            flex: 2;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .title h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .title p {
            font-size: 1rem;
            color: #777;
            margin-bottom: 30px;
        }

        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }

        .input-group .icon {
            padding: 10px;
            color: #888;
        }

        .input-group .input {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
            font-size: 1rem;
        }

        .bottom-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .bottom-form .btn {
            width: 100%;
            padding: 10px;
            background-color: #5264AE;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .bottom-form .btn:hover {
            background-color: #4153a1;
        }

        .forgot a {
            color: #5264AE;
            text-decoration: none;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .session {
                flex-direction: column;
            }
            .left {
                display: none;
            }
            .login-form {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($_GET['success'])) : ?>
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: <?php echo json_encode($_GET['success']); ?>,
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true
                });
            <?php endif; ?>

            // Sweet alert test
            Swal.fire({
                icon: 'info',
                title: 'Test Alert',
                text: 'SweetAlert is working!',
                confirmButtonText: 'Cool'
            });
        });
    </script>

    <div class="session">
        <div class="left"></div>
        <div class="login-form">
            <div class="title">
                <h1>Welcome Back</h1>
                <p class="sub-title">Login to your account</p>
            </div>

            <?php if (isset($_GET['error'])) { ?>
                <div class="error">
                    <?php echo $_GET['error']; ?>
                </div>
            <?php } ?>

            <form action="./login/login.php" method="post" class="log-in">
                <div class="input-group">
                    <div class="icon">
                        <i class="bx bx-envelope"></i>
                    </div>
                    <input required type="text" name="useremail" class="input" pattern=".+@henrich\.com" title="Email must end with @henrich.com" placeholder="Email">
                </div>

                <div class="input-group">
                    <div class="icon">
                        <i class="bx bx-lock"></i>
                    </div>
                    <input required type="password" name="password" id="password" class="input" placeholder="Password">
                    <div class="icon" id="togglePassword">
                        <i class="bx bx-show"></i>
                    </div>
                </div>

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

                <div class="bottom-form">
                    <button type="submit" class="btn">Log in</button>
                    <div class="forgot">
                        <a href="./login/forgotpassword.php">Forgot Password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

<?php require './reusable/footer.php'; ?>
</html>


/******  22d6d9df-bbef-4822-9fc1-a328330cd3c8  *******/