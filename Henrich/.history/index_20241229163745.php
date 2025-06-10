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
<html>

<head>
    <title>LOGIN</title>
    <?php require './reusable/header.php'; ?>
    
    <!-- sweetalert -->
    <script src="sweetalert2.min.js"></script>
    <script src="/sweetalert/sweetalert.js"></script>
    <link rel="stylesheet" href="sweetalert2.min.css">

    <!-- <link rel="stylesheet" type="text/css" href="resources/css/login-copy.css"> -->
    <link rel="stylesheet" type="text/css" href="resources/css/style.css">
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

</body>
    <!-- <div class="background">
        <div class="blur"></div>
        <div class="img"></div>
    </div> -->

    <div class="session">
        <div class="left">
            <div class="container"></div>

            <img draggable="false" src="./resources/images/henrichlogo.png" alt="henrich logo">
        </div>
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

                        </label>
                    </div>
                    <div class="icon">
                        <i class="bx bx-show" id="togglePassword"></i>
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

<?php require './reusable/footer.php'; ?>
</html>
