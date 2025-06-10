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
            background-color: var(--bg-light);
            font-family: var(--font-sans);
        }

        .session {
            display: flex;
            flex-direction: row;
            margin: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            max-width: 900px;
            width: 100%;
            background: var(--bg-surface);
        }

        .left {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            width: 100%;
            height: 100%;
            --s: 194px;
            --c1: var(--primary-100);
            --c2: var(--primary-500);
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
                linear-gradient(-150deg, var(--_l)) var(--primary-400);
            background-size: calc(0.866 * var(--s)) var(--s);
            min-height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-surface);
        }

        .left img {
            width: 180px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
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

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        .title h1 {
            font-size: var(--font-size-3xl);
            color: var(--text-primary);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .title p {
            font-size: var(--font-size-base);
            color: var(--text-secondary);
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group .icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-tertiary);
        }

        .input-group .input {
            width: 100%;
            padding: 12px 40px;
            border: 2px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: var(--font-size-base);
            color: var(--text-primary);
            background: var(--bg-surface);
            transition: all 0.3s ease;
        }

        .input-group .input:focus {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px var(--primary-100);
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
            margin-bottom: 20px;
            color: var(--text-secondary);
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary-500);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: var(--primary-500);
            color: var(--text-inverse);
            border: none;
            border-radius: var(--radius-md);
            font-size: var(--font-size-base);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            background: var(--primary-600);
            transform: translateY(-1px);
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
            gap: 15px;
            margin-top: 20px;
        }

        .forgot {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .forgot a {
            color: var(--primary-500);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot a:hover {
            color: var(--primary-600);
            text-decoration: underline;
        }

        .signup {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .signup a {
            color: var(--primary-500);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .signup a:hover {
            color: var(--primary-600);
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
            margin-top: 30px;
            text-align: center;
        }

        .social-login p {
            color: var(--text-secondary);
            margin-bottom: 15px;
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: var(--border-light);
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
            gap: 15px;
        }

        .social-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-light);
            background: var(--bg-surface);
            color: var(--text-secondary);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .social-button:hover {
            background: var(--bg-surface-hover);
            color: var(--text-primary);
            transform: translateY(-2px);
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

                <!-- Temporarily disabled reCAPTCHA
                <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>
                -->

                <div class="bottom-form">
                    <button type="submit" class="btn" id="loginButton">
                        <span>Login</span>
                    </button>
                    <div class="forgot">
                        <a href="./login/forgot-password.php">Forgot Password?</a>
                    </div>
                    <div class="signup">
                        Need an account? <a href="./login/request-account.php">Request Access</a>
                    </div>
                </div>
            </form>

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

            // Temporarily disabled reCAPTCHA validation
            /*
            const recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                alert('Please complete the CAPTCHA');
                loginButton.classList.remove('loading');
                loginButton.querySelector('span').textContent = 'Login';
                return;
            }
            */

            // Submit form
            const formData = new FormData(this);
            fetch('./login/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    // Redirect based on role
                    window.location.href = './supervisor/index.php';
                } else {
                    // Show error in a more user-friendly way
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${data}`;
                    
                    const alertContainer = document.querySelector('.alert-message');
                    alertContainer.innerHTML = '';
                    alertContainer.appendChild(errorDiv);
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