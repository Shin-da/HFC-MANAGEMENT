<!-- META TAGS -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <meta http-equiv="refresh" content="120"> -->

<!-- FAVICON -->
<link rel="icon" href="../resources/images/henrichlogo.png">

<!-- FONTS -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

<!-- STYLESHEETS -->
<link rel="stylesheet" type="text/css" href="../resources/css/style.css">
<link rel="stylesheet" type="text/css" href="../resources/css/sidebar.css">
<link rel="stylesheet" type="text/css" href="resources/css/login.css">
<link rel="stylesheet" type="text/css" href="../resources/css/calendar.css">
<link rel="stylesheet" type="text/css" href="../resources/css/navbar.css">
<!-- <link rel="stylesheet" type="text/css" href="../resources/css/dashboard.css"> -->

<!-- BOXICONS -->
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

<!-- JAVASCRIPTS -->
<script src="../resources/js/datetime.js"></script> <!-- datetime -->
<script src="../resources/js/weather.js"></script> <!-- For weather -->
<script src="../resources/js/product_rank.js"></script> <!-- For Product rankin -->
<script src="../resources/js/holidays.js"></script><!-- For holidays -->
<script src="../resources/js/search.js"> </script> <!-- JS for search -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jquery -->

<!-- DROPDOWN SCRIPT -->
<!-- <script>
       function toggleDropdown() {
              document.getElementById("myDropdown").classList.toggle("show");
       }

       // Close the dropdown if the user clicks outside of it
       window.onclick = function(event) {
              if (!event.target.matches('.dropbtn')) {
                     var dropdowns = document.getElementsByClassName("dropdown-content");
                     var i;
                     for (i = 0; i < dropdowns.length; i++) {
                            var openDropdown = dropdowns[i];
                            if (openDropdown.classList.contains('show')) {
                                   openDropdown.classList.remove('show');
                            }
                     }
              }
       }
</script> -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>

<!-- Add Animate.css for smooth animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- SweetAlert2 Resources -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* SweetAlert2 Custom Styling */
    .swal2-popup {
        font-size: 0.9rem !important;
        border-radius: 12px !important;
    }
    .swal2-toast {
        background: var(--sand) !important;
        color: var(--text-primary) !important;
    }
    .swal2-confirm {