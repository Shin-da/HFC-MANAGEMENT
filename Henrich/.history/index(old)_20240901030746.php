<?php
require 'redirect404.php';
?>
<!DOCTYPE html>
<html>

<head>
	<title>LOGIN</title>

	<?php require 'reusable/header.php'; ?>	

	<script>
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
	</script>
</head>

<body>
	<div class="login ">

		<div class="login-container">
			<div class="login-header">
				<img draggable="false" src="./resources/images/henrichlogo.png" alt="henrich logo" style="user-select: none; ">
			</div>
		</div>

		<div class="login-container">

			<div class="login-form">
				<form action="login.php" method="post">
					<p class="title">Login</p>
					<?php if (isset($_GET['error'])) { ?>
						<p class="error"><?php echo $_GET['error']; ?></p>
					<?php } ?>
					<label>Email</label>
					<input type="email" name="email" placeholder="Enter Email"><br>

					<label>Password</label>
					<input type="password" name="password" placeholder="Enter Password"><br>
					
					<div class="bottom-form">
						<button type="submit">Login</button>
						<div class="forgot">
							<a href="forgot.php">Forgot Password?</a>
						</div>

					</div>
				</form>
			</div>
		</div>

</body>

</html>