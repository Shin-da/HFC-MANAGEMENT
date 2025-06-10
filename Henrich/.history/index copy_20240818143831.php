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

<div class="session">
    <div class="left">
     
    </div>
    <form action="" class="log-in" autocomplete="off"> 
      <h4>We are <span>NUVA</span></h4>
      <p>Welcome back! Log in to your account to view today's clients:</p>
      <div class="floating-label">
        <input placeholder="Email" type="text" name="email" id="email" autocomplete="off">
        <label for="email">Email:</label>
      </div>
      <div class="floating-label">
        <input placeholder="Password" type="password" name="password" id="password" autocomplete="off">
        <label for="password">Password:</label>
      </div>
      <button type="submit" onClick="return false;">Log in</button>
      <a href="https://codepen.io/elujambio/pen/YLMVed" class="discrete" target="_blank">Advanced version</a>
    </form>
  </div>

</body>

</html>