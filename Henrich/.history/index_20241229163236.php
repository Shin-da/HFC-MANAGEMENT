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
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <?php require './reusable/header.php'; ?>
    
    <!-- sweetalert -->
    <script src="sweetalert2.min.js"></script>
    <script src="/sweetalert/sweetalert.js"></script>
    <link rel="stylesheet" href="sweetalert2.min.css">

    <!-- sweetalert -->
    <script src="sweetalert2.min.js"></script>
    <script src="/sweetalert/sweetalert.js"></script>
    <link rel="stylesheet" href="sweetalert2.min.css">
    <!-- <link rel="stylesheet" type="text/css" href="resources/css/login-copy.css"> -->
    <link rel="stylesheet" type="text/css" href="resources/css/style.css">
</head>

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
<body>

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

        .left {
            flex: 1;
            background: url('./resources/images/henrichlogo.png') no-repeat center center;
            background-size: contain;
            border-radius: 8px 0 0 8px;
            min-height: 320px;
        }
        // Sweet alert test
        Swal.fire({
            icon: 'info',
            title: 'Test Alert',
            text: 'SweetAlert is working!',
            confirmButtonText: 'Cool'
        });
    });
</script>

        .login-form {
            flex: 2;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
</body>
    <!-- <div class="background">
        <div class="blur"></div>
        <div class="img"></div>
    </div> -->

        .title h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #333;
        }
    <div class="session">
        <div class="left">
            <div class="container"></div>

        .title p {
            font-size: 1rem;
            color: #777;
            margin-bottom: 30px;
        }
            <img draggable="false" src="./resources/images/henrichlogo.png" alt="henrich logo">
        </div>
        <div class="login-form">
            <div class="title">
                <h1>Welcome Back</h1>
                <p class="sub-title">Login to your account</p>
            </div>

        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
            <?php if (isset($_GET['error'])) { ?>
                <div class="error">
                    <?php echo $_GET['error']; ?>
                </div>
            <?php } ?>

        .input-group .icon {
            padding: 10px;
            color: #888;
        }
            <form action="./login/login.php" method="post" class="log-in">

        .input-group .input {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
            font-size: 1rem;
        }
                <div class="input-group">
                    <div class="icon">
                        <i class="bx bx-envelope"></i>
                    </div>
                    <div class="wave-group">
                        <input required="" type="text" type="useremail" name="useremail" class="input" pattern=".+@henrich\.com" title="Email must end with @henrich.com">
                        <span class="bar"></span>
                        <label class="label">
                            <span class="label-char" style="--index: 0">E</span>
                            <span class="label-char" style="--index: 2">m</span>
                            <span class="label-char" style="--index: 3">a</span>
                            <span class="label-char" style="--index: 4">i</span>
                            <span class="label-char" style="--index: 5">l</span>
                        </label>
                    </div>
                </div>

        .bottom-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
                <div class="input-group">
                    <div class="icon">
                        <i class="bx bx-lock"></i>
                    </div>
                    <div class="wave-group">
                        <input required="" type="password" name="password" id="password" class="input">
                        <span class="bar"></span>
                        <label class="label">
                            <span class="label-char" style="--index: 0">P</span>
                            <span class="label-char" style="--index: 1">a</span>
                            <span class="label-char" style="--index: 2">s</span>
                            <span class="label-char" style="--index: 3">s</span>
                            <span class="label-char" style="--index: 4">w</span>
                            <span class="label-char" style="--index: 5">o</span>
                            <span class="label-char" style="--index: 6">r</span>
                            <span class="label-char" style="--index: 7">d</span>

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
                        </label>
                    </div>
                    <div class="icon">
                        <i class="bx bx-show" id="togglePassword"></i>
                    </div>
                </div>

        .bottom-form .btn:hover {
            background-color: #4153a1;
        }
                <script>
                    var togglePassword = document.getElementById("togglePassword");
                    var password = document.getElementById("password");

        .forgot a {
            color: #5264AE;
            text-decoration: none;
            font-size: 0.9rem;
        }
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
                <div class="bottom-form">
                    <button type="submit" class="btn">Log in</button>

<body>
                    <div class="forgot">
                        <a href="./login/forgotpassword.php">Forgot Password?</a>
                    </div>
                </div>
            </form>
        </div>

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
    </div>

            // Sweet alert test
            Swal.fire({
                icon: 'info',
                title: 'Test Alert',
                text: 'SweetAlert is working!',
                confirmButtonText: 'Cool'
            });
        });
    </script>
	<div>
    <h1>How to use and customize <img src="https://sweetalert2.github.io/images/swal2-logo.png"></h1>
    <div>
      <h4>Modal Type</h4>
      <p>Sweet alert with modal type and customize message alert with html and css</p>
      <button id="success">Success</button>
      <button id="error">Error</button>
      <button id="warning">Warning</button>
      <button id="info">Info</button>
      <button id="question">Question</button>
    </div>
    <br>
    <div>
      <h4>Custom image and alert size</h4>
      <p>Alert with custom icon and background icon</p>
      <button id="icon">Custom Icon</button>
      <button id="image">Custom Background Image</button>
    </div>
    <br>
    <div>
      <h4>Alert with input type</h4>
      <p>Sweet Alert with Input and loading button</p>
      <button id="subscribe">Subscribe</button>
    </div>
    <br>
    <div>
      <h4>Redirect to visit another site</h4>
      <p>Alert to visit a link to another site</p>
      <button id="link">Redirect to Utopian</button>
    </div>
  </div>
</body>

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
<?php require './reusable/footer.php'; ?>
</html>

/******  722880d9-5c24-44f8-9103-99f2d605407c  *******/