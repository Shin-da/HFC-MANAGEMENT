/*************  ✨ Codeium Command 🌟  *************/
<?php
session_start();
include "../database/dbconnect.php";

if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    echo '<script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "You are logged in",
            showConfirmButton: false,
            timer: 3000
        });
    </script>';
} elseif (isset($_POST['useremail']) && isset($_POST['password'])) {
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
if (isset($_POST['useremail']) && isset($_POST['password'])) {
	function validate($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

    $useremail = validate($_POST['useremail']);
    $password = validate($_POST['password']);
	$useremail = validate($_POST['useremail']);
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
    } else {
        // hashing the password
        // $password = md5($password);
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
	} else {
		// hashing the password
		// $password = md5($password);

        $sql = "SELECT * FROM user WHERE useremail='$useremail' AND password='$password'";
        $result = mysqli_query($conn, $sql);
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
        }
    }
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
		}
	}
} else {
    header("Location: index.php?error=You've been logged out");
    exit();
	header("Location: index.php?error=You've been logged out");
	exit();
}

$login_success = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    $login_success = true;
    unset($_SESSION['login_success']);
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

/******  8e4fba5e-1a13-4ece-aeec-3a5b77e41ae9  *******/