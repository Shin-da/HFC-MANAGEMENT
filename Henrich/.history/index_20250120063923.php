<?php
session_start();
require_once 'includes/config.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {  // Changed from uid to user_id
    switch($_SESSION['role']) {
        case 'admin':
            header("Location: ./admin/");
            exit();
        case 'supervisor':
            header("Location: ./supervisor/");
            exit();
        case 'ceo':
            header("Location: ./ceo/");
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <?php require './reusable/head.php'; ?>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    
<link rel="stylesheet" type="text/css" href="./assets/css/style.css">

    <style>
        * {
            transition: all 0.3s ease-in-out;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url("./resources/images/warehouse.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-color: rgba(0, 0, 0, 0.5);
            background-blend-mode: multiply;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .session {
            display: flex;
            flex-direction: row;
            margin: 20px;
            padding: 20px;
            background: rgba(118, 118, 118, 0.54);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
        }

        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 150px;
            height: auto;
        }

        /* .alert-message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        } */

        .success-message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            background-color: #f8d7da;
            color: #721c24;
        }

        .left {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            width: 100%;
            height: 100%;
            --s: 194px;
            /* control the size */
            --c1: #f6edb3;
            --c2: #b1443b;
            --_l: #0000 calc(25% / 3), var(--c1) 0 25%, #0000 0;
            --_g: conic-gradient(from 120deg at 50% 87.5%, var(--c1) 120deg, #0000 0);
            background: var(--_g), var(--_g) 0 calc(var(--s) / 2),
                conic-gradient(from 180deg at 75%, var(--c2) 60deg, #0000 0),
                conic-gradient(from 60deg at 75% 75%, var(--c1) 0 60deg, #0000 0),
                linear-gradient(150deg, var(--_l)) 0 calc(var(--s) / 2),
                conic-gradient(at 25% 25%,
                    #0000 50%,
                    var(--c2) 0 240deg,
                    var(--c1) 0 300deg,
                    var(--c2) 0),
                linear-gradient(-150deg, var(--_l)) #7d302a
                /* third color here */
            ;
            background-size: calc(0.866 * var(--s)) var(--s);
            min-height: 320px;
        }


        .login-form {
            flex: 2;
            padding: 40px;
            display: flex;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
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

    <div class="session">
        <div class="left">
            <div class="logo">
                <img src="./resources/images/hfclogo.png" alt="HFC Logo">
            </div>
        </div>
        <div class="login-form">
            <div class="title">
                <h1>Welcome Back</h1>
                <p class="sub-title">Login to your account</p>
            </div>

            <div class="alert-message">
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error-message">' . $_GET['error'] . '</div>';
                } elseif (isset($_GET['success'])) {
                    echo '<div class="success-message">' . $_GET['success'] . '</div>';
                }
                ?>
            </div>



            <!-- Update form action to point to login handler -->
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

                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>

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