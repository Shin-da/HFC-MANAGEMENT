<?php
require 'redirect404.php';
?>
<!DOCTYPE html>
<html>

<head>
	<title>LOGIN</title>
	<link rel="stylesheet" type="text/css" href="css/sidebar.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/colors.css">
	<link rel="stylesheet" type="text/css" href="css/login-copy.css">

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

	<div class="login">

		<div class="login-container">
			<div class="login-visual">

			</div>


			<div class="login-form">
				<form action="login.php" method="post">
					

</body>

</html>