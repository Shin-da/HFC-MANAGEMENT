


<?php
session_start();

session_unset();
session_destroy();

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
