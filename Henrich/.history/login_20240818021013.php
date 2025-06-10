<?php
session_start();
include "./database/x-dbconnect.php";

if (isset($_POST['email']) && isset($_POST['password'])) {

	function validate($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	$email = validate($_POST['email']);
	$password = validate($_POST['password']);

	if (empty($email)) {
		header("Location: index.php?error=Email is required");
		exit();
	} else if (empty($password)) {
		header("Location: index.php?error=Password is required");
		exit();
	} else {
		// hashing the password
		$password = md5($password);


		$sql = "SELECT * FROM user WHERE email='$email' AND password='$password'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			if ($row['email'] === $email && $row['password'] === $password) {
				$_SESSION['email'] = $row['email'];
				// $_SESSION['name'] = $row['name'];
				$_SESSION['id'] = $row['id'];
				header("Location: home.php");
				exit();
			} else {
				header("Location: index.php?error=Incorrect Email or password 1 ");
				exit();
			}
<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
		} else {
			// $error = "Incorrect Email or password";
			header("Location: index.php?error=Incorrect Email or password 2");

			?>
			<script>
				console.log("Login failed for:<?php echo $email; ?>");
				console.log("<?php echo $sql; ?>");
				console.log("Result:");
				<?php
				$res = mysqli_query($conn, $sql);
				while ($row = mysqli_fetch_assoc($res)) {
					echo "console.log(" . json_encode($row) . ");";
				}
				?>
				console.log()
			</script>
			<?php

			exit();
		}
<<<<<<<  3bc4c1d4-bc82-4406-8034-4e0f595d35ce  >>>>>>>
	}
} else {
	header("Location: index.php");
	exit();
}
