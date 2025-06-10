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
			<img src="images/henrichlogo.png" alt="henrich logo">
		</div>
		<div class="login-form">
			<h1 class="title">Welcome Back</h1>

			<form action="" class="log-in" autocomplete="off">

				<div class="wave-group">
					<input required="" type="text" class="input">
					<span class="bar"></span>
					<label class="label">
						<span class="label-char" style="--index: 0">E</span>
						<span class="label-char" style="--index: 2">m</span>
						<span class="label-char" style="--index: 3">a</span>
						<span class="label-char" style="--index: 4">i</span>
						<span class="label-char" style="--index: 5">l</span>
					</label>
				</div>

				<div class="wave-group">
					<input required="" type="password" class="input">
					<span class="bar"></span>
					<label class="label">
						<span class="label-char" style="--index: 0">P</span>
						<span class="label-char" style="--index: 1">a</span>
						<span class="label-char" style="--index: 2">s</span>
						<span class="label-char" style="--index: 3">s</span>
						<span class="label-char" style="--index: 4">w</span></span>

					</label>
				</div>

				<div class="botom-form">
					<button type="submit" onClick="return false;">Log in</button>

				</div>


			</form>
		</div>

	</div>
</body>

</html>