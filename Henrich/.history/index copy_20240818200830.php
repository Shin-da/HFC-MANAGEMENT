<?php
// require 'redirect404.php';
?>
<!DOCTYPE html>
<html>

<head>
	<title>LOGIN</title>
	<link rel="stylesheet" type="text/css" href="css/sidebar.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/colors.css">
	<link rel="stylesheet" type="text/css" href="css/login-copy.css">

	<!-- Boxicons CDN Link -->
	<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

	<!-- <script>
		window.addEventListener('DOMContentLoaded', function() {
			console.log('DOMContentLoaded event triggered');
			// Check if the requested page exists
			const xhr = new XMLHttpRequest();
			xhr.open('HEAD', window.location.href, true);
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if (xhr.status === 404) {
						console.log('404 error detected');
						// Redirect to the 404 error page
						window.location.href = '/HenrichProto/404.html';
					}
				}
			};
			xhr.send();
		});
	</script> -->
</head>

<body>

	<div class="session">
		<div class="left">
			<div class="container"></div>

			<img draggable="false" src="images/henrichlogo.png" alt="henrich logo">
		</div>
		<div class="login-form">
			<div class="title">
				<h1>Welcome Back</h1>
				<p class="sub-title">Login to your account</p>
			</div>
			<?php if (isset($_GET['error'])) { ?>
				<div class="error"><?php echo $_GET['error']; ?></div>
			<?php } ?>
			<form action="login.php" method="post" class="log-in" >

				<div class="input-group">
					<div class="icon">
						<i class="bx bx-envelope"></i>
					</div>
					<div class="wave-group">
						<input required="" t type="email" name="email" class="input">
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
						<input required="" type="password" id="password" class="input">
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

					togglePassword.addEventListener('click', function () {
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
							<a href="forgot.php">Forgot Password?</a>
						</div>
					</div>


			</form>
		</div>

	</div>
</body>

</html>