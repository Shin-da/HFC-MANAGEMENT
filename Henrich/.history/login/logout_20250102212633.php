/*************  ✨ Codeium Command 🌟  *************/



<?php
session_start();

session_unset();
session_destroy();

header("Location: ../index.php?message=Logged out successfully");
echo '<script type="text/javascript">

          var Toast = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000
            });

            Toast.fire({
              icon: "success",
              title: "Logged out successfully"
            }).then(function() {
              window.location.href = "../index.php";
            });

</script>';

/******  4431e659-d0e3-4b1b-a1bd-506b46d9b0e7  *******/