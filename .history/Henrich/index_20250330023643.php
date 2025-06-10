<?php
session_start();
// The user is logged in, redirect to the home page
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === "admin") {
        header("Location:./admin/");
        exit();
    } elseif ($_SESSION['role'] === "supervisor") {
        header("Location: ./supervisor/");
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
    <title>LOGIN - HFC Management System</title>
    <?php require './reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="./assets/css/variables.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        .session {
            display: flex;
            flex-direction: row;
            margin: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            max-width: 900px;
            width: 100%;
            background: #fff;
            overflow: hidden;
        }

        .left {
            width: 45%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .left img {
            width: 200px;
            height: auto;
            object-fit: contain;
        }

        .login-form {
            flex: 1;
            padding: 48px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .title {
            text-align: left;
            margin-bottom: 40px;
        }

        .title h1 {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .title p {
            font-size: 14px;
            color: #666;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group .icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
        }

        .input-group .input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            color: #1a1a1a;
            background: #f8f9fc;
            transition: all 0.3s ease;
        }

        .input-group .input:focus {
            border-color: #4a6ee0;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(74, 110, 224, 0.1);
            outline: none;
        }

        .input-group .input.error {
            border-color: var(--error);
        }

        .input-group .input.success {
            border-color: var(--success);
        }

        .input-group .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-tertiary);
            transition: color 0.3s ease;
        }

        .input-group .toggle-password:hover {
            color: var(--text-primary);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            color: #666;
            font-size: 14px;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4a6ee0;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #4a6ee0;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #3d5cba;
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn.loading::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .bottom-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            margin-top: 24px;
        }

        .forgot, .signup {
            font-size: 14px;
            color: #666;
        }

        .forgot a, .signup a {
            color: #4a6ee0;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot a:hover, .signup a:hover {
            text-decoration: underline;
        }

        .alert-message {
            margin-bottom: 20px;
        }

        .error-message {
            background: var(--error-light);
            color: var(--error-dark);
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: var(--font-size-sm);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .success-message {
            background: var(--success-light);
            color: var(--success-dark);
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: var(--font-size-sm);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .social-login {
            margin-top: 32px;
            text-align: center;
        }

        .social-login p {
            color: #666;
            font-size: 14px;
            margin-bottom: 16px;
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #e0e0e0;
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .social-button {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e0e0e0;
            background: #fff;
            color: #666;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .social-button:hover {
            border-color: #4a6ee0;
            color: #4a6ee0;
        }

        @media (max-width: 768px) {
            .session {
                flex-direction: column;
                margin: 10px;
            }

            .left {
                display: none;
            }

            .login-form {
                padding: 20px;
            }

            .title h1 {
                font-size: var(--font-size-2xl);
            }
        }
    </style>
</head>

<body>
    <div class="session">
        <div class="left">
            <img src="./resources/images/hfclogo.png" alt="HFC Logo">
        </div>
        <div class="login-form">
            <div class="title">
                <h1>Welcome Back</h1>
                <p>Login to your account</p>
            </div>

            <div class="alert-message">
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error-message"><i class="fas fa-exclamation-circle"></i>' . htmlspecialchars($_GET['error']) . '</div>';
                } elseif (isset($_GET['success'])) {
                    echo '<div class="success-message"><i class="fas fa-check-circle"></i>' . htmlspecialchars($_GET['success']) . '</div>';
                }
                ?>
            </div>

            <form action="./login/login.php" method="post" class="log-in" id="loginForm">
                <div class="input-group">
                    <div class="icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input required type="email" name="useremail" class="input" 
                           pattern=".+@henrich\.com" 
                           title="Email must end with @henrich.com" 
                           placeholder="Email"
                           autocomplete="email">
                </div>

                <div class="input-group">
                    <div class="icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input required type="password" name="password" id="password" 
                           class="input" placeholder="Password"
                           autocomplete="current-password">
                    <div class="toggle-password" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>

                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>

                <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>

                <button type="submit" class="btn" id="loginButton">
                    <span>Login</span>
                </button>

                <div class="forgot">
                    <a href="./login/forgot-password.php">Forgot Password?</a>
                </div>

                <div class="signup">
                    Don't have an account? <a href="./login/signup.php">Sign up</a>
                </div>

                <div class="social-login">
                    <p>Or continue with</p>
                    <div class="social-buttons">
                        <button type="button" class="social-button" title="Login with Google">
                            <i class="fab fa-google"></i>
                        </button>
                        <button type="button" class="social-button" title="Login with Microsoft">
                            <i class="fab fa-microsoft"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // Form validation
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Add loading state
            loginButton.classList.add('loading');
            loginButton.querySelector('span').textContent = 'Logging in...';

            // Validate reCAPTCHA
            const recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                alert('Please complete the CAPTCHA');
                loginButton.classList.remove('loading');
                loginButton.querySelector('span').textContent = 'Login';
                return;
            }

            // Submit form
            const formData = new FormData(this);
            fetch('./login/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    window.location.href = './dashboard.php';
                } else {
                    alert(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                loginButton.classList.remove('loading');
                loginButton.querySelector('span').textContent = 'Login';
            });
        });

        // Clear URL parameters
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }
    </script>
</body>

<?php require './reusable/footer.php'; ?>

</html>