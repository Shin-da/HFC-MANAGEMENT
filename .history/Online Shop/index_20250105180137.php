<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Responsive Login Page">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.min.css">
    <style>
        /* Make the page responsive */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            max-width: 400px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 100px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #45a049;
        }


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

        @media screen and (max-width: 600px) {
            .error-message {
                font-size: 1rem;
            }
        }

        .forgot {
            text-align: center;
            margin-top: 20px;
        }

        .forgot a {
            color: #4CAF50;
            text-decoration: none;
        }

        .signup {
            text-align: center;
            margin-top: 20px;
        }

        .signup a {
            color: #4CAF50;
            text-decoration: none;
        }

        @media screen and (max-width: 600px) {
            .container {
                max-width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="./resources/images/hfc online logo.png" alt="henrich logo">
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
        <form action="./login/login.php" method="post">
            <div class="form-group">
                <label for="useremail">Email</label>
                <input type="text" name="useremail" id="useremail" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Log in</button>
            </div>
            <div class="forgot">
                <a href="./login/forgotpassword.php">Forgot Password?</a>
            </div>
            <div class="signup">
                <a href="./login/signup.php">Sign up</a>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.min.js"></script>
    <script>
        // Clear URL parameters
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.pathname);
        }

        // Show password toggle
        var togglePassword = document.querySelector("#togglePassword");
        var passwordInput = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                togglePassword.classList.add("bx-hide");
                togglePassword.classList.remove("bx-show");
            } else {
                passwordInput.type = "password";
                togglePassword.classList.add("bx-show");
                togglePassword.classList.remove("bx-hide");
            }
        });

        // Handle login form submission
        document.querySelector("form").addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const useremail = formData.get("useremail");
            const password = formData.get("password");

            fetch("./login/login.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === "success") {
                    Swal.fire({
                        title: "Login Success",
                        text: "You are now logged in",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(function() {
                        window.location.href = "./index.php";
                    });
                } else {
                    Swal.fire({
                        title: "Login Failed",
                        text: data,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            })
            .catch(error => console.error("Error:", error));
        });
    </script>
</body>

</html>


