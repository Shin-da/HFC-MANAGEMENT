/*************  ✨ Codeium Command 🌟  *************/
<?php
session_start();
include "../database/dbconnect.php";

if (isset($_POST['useremail']) && isset($_POST['password'])) {
	function validate($data) {

	function validate($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	$useremail = validate($_POST['useremail']);
	$uid = validate($_POST['uid']);
	$password = validate($_POST['password']);

	if (empty($useremail)) {
		echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Useremail is required'
				});
			});
		</script>";
		header("Location: ../index.php?error=useremail is required");
		exit();
	} else if (empty($password)) {
		echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Password is required'
				});
			});
		</script>";
		header("Location: ../index.php?error=Password is required");
		exit();
	} else {
		// hashing the password
		// $password = md5($password);

		$sql = "SELECT * FROM user WHERE useremail='$useremail' AND password='$password'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			if ($row['useremail'] === $useremail && $row['password'] === $password) {
				$_SESSION['useremail'] = $row['useremail'];
				$_SESSION['role'] = $row['role'];
				$_SESSION['uid'] = $row['uid'];
				$_SESSION['login_success'] = true;
				if ($row['role'] === 'admin') {
					header("Location: ../admin/index.php");
				} else if ($row['role'] === 'supervisor') {
					header("Location: ../supervisor/index.php");
				} else if ($row['role'] === 'ceo') {
					header("Location: ../ceo/index.php");
				}
				exit();
			} else {
				echo "<script>
					document.addEventListener('DOMContentLoaded', function() {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'User does not exist'
						});
					});
				</script>";
				exit();
	// $error = "user does not exist";
	header("Location: ../index.php?error=User does not exist");
	exit();
			}
		} else {
			echo "<script>
				document.addEventListener('DOMContentLoaded', function() {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: 'Incorrect useremail or password'
					});
				});
			</script>";
			exit();
		
				// $error = "Incorrect useremail or password";
				header("Location: ../index.php?error=Incorrect useremail or password");				exit();
		}
	}
} else {
	header("Location: index.php?error=You've been logged out");
	exit();
}

$login_success = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
	$login_success = true;
	unset($_SESSION['login_success']);
}

if ($login_success) : ?>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			Swal.fire({
				position: 'top-end',
				icon: 'success',
				title: 'Logged in successfully',
				showConfirmButton: false,
				timer: 3000
			});
		});
	</script>
<?php endif; ?>
/******  c87b0f80-7760-4190-819c-c4462a5308b3  *******/
