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
	$id = (['id']);
	$password = validate($_POST['password']);

	if (empty($email)) {
		header("Location: index.php?error=Email is required");
		exit();
	} else if (empty($password)) {
		header("Location: index.php?error=Password is required");
		exit();
	} else {
		// hashing the password
		// $password = md5($password);


		$sql = "SELECT * FROM user WHERE id='$id' AND email='$email' AND password='$password'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			if ($row['email'] === $email && $row['password'] === $password) {
				$_SESSION['email'] = $row['email'];
				$_SESSION['role'] = $row['role'];
				$_SESSION['id'] = $row['id'];
				header("Location: home.php");
				exit();
			} else {
				header("Location: index.php?error=Incorrect Email or password 1 ");
				exit();
			}
		} else {
			// $error = "Incorrect Email or password";
			header("Location: index.php?error=Incorrect Email or password 2");
			exit();
		}
	}
} else {
	header("Location: index.php?error=IDONTKNOW");
	exit();
}
