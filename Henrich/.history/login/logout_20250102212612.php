/*************  ✨ Codeium Command 🌟  *************/



<?php
session_start();

session_unset();
session_destroy();

header("Location: ../index.php");
exit();
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

/******  9a2a645e-136c-4d54-bf0d-a345e2db024d  *******/