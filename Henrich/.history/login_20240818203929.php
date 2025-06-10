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
	$uid = validate($_POST['uid']);
	$password = validate($_POST['password']);

	if (empty($email)) {
		header("Location: index copy.php?error=Email is required");
		exit();
	} else if (empty($password)) {
		header("Location: index copy.php?error=Password is required");
		exit();
	} else {
		// hashing the password
		// $password = md5($password);


		$sql = "SELECT * FROM user WHERE  email='$email' AND password='$password'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			if ($row['email'] === $email && $row['password'] === $password) {
				$_SESSION['email'] = $row['email'];
				$_SESSION['role'] = $row['role'];
				$_SESSION['uid'] = $row['uid'];
				if ($row['role'] === 'admin') {
					header("Location: admin.php");
				} else {
					header("Location: home.php");
				}
			} elseif ($row['email'] !== $email || $row['password'] !== $password) {

				if ($row['email'] !== $email) {
					header("Location: index copy.php?error=Incorrect Email or password 2 ");
					exit();
				} else if ($row['password'] !== $password) {
					header("Location: index copy.php?error=Incorrect Email or password 3 ");
					exit();
				} 
			} else  {

				
				header("Location: index copy.php?error=Incorrect Email or password 1 ");
				exit();
			}
		} else {
			// $error = "user does not exist";
			header("Location: index copy.php?error=User does not exist");
			exit();
		}
	}
} else {
	header("Location: index copy.php?error=IDONTKNOW");
	exit();
}

