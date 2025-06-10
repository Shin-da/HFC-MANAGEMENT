<!-- swal2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.6.1/dist/sweetalert2.min.css" integrity="sha256-6jYHmVZsXmJm7q3Uf2Jv3f1yTbqOaRTIxNHKcij1/oA=" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.6.1/dist/sweetalert2.min.js" integrity="sha256-kX2Q3E5q4yfR0QjwX7X9eot+UyX5JrWS/0JjHnqF5YI=" crossorigin="anonymous"></script>


<style>

    /* colors */
    :root {
          /* ===== Colors ===== */
	--body-color: #E4E9F7;
	--border-color: #d7d7d7;
	--panel-color: #E7E6E1;
    --container-color: #fff;
	--text-color-white: #faf9f5;
	--text-color: #3a3a3a;
	--toggle-color: #e9ecef;

	--grey-active: #717171;
	--grey-inactive: #3a3b3c;
	--grey-hover-color: #a1a1a1;

	/* --primary-color: #5F8D4E;
	--accent-color: #5F8D4E;
	--accent-color-dark: #495235; */

	/* --primary-color:  #FF686B; */
	--accent-color: #FF686B;
	--accent-color-dark:#6d1c27;
	--accent-color-dark-inactive: #4b2025;

    --white : #fff;
	--orange-color: #FFAD60;
	--yellow-color: #FFEEAD;
	--blue-color: #96CEB4;
	--blue-color-dark: #2D5B6B;
	--vandyke-color: #362C28;

	--white: #fff;
	--black: #313638;

	
	--warning-color: #f0ad4e;
	--dark-teal: #00a1ba;
	--success-color: #55b86c;
	--danger-color: #d9534f;
	--danger-color-dark: #a02334;
    }

    /* Custom CSS for SweetAlert2 */
     .swal2-popup {
          font-family: inherit;
     }

     .swal2-title {
          font-size: 1.5em;
     }

     .swal2-styled.swal2-confirm {
          background-color: var(--accent-color) !important;
          border-left-color: var(--accent-color) !important;
          border-right-color: var(--accent-color) !important;
     }

     .swal2-styled.swal2-confirm:hover {
          background-color: var(--hover-color) !important;
     }

     .swal2-styled.swal2-cancel {
          background-color: var(--secondary-color) !important;
          border-left-color: var(--secondary-color) !important;
          border-right-color: var(--secondary-color) !important;
     }

     .swal2-styled.swal2-cancel:hover {
          background-color: var(--secondary-hover-color) !important;
     }

     .swal2-styled.swal2-deny {
          background-color: var(--error-color) !important;
          border-left-color: var(--error-color) !important;
          border-right-color: var(--error-color) !important;
     }

     .swal2-styled.swal2-deny:hover {
          background-color: var(--error-hover-color) !important;
     }

     .swal2-styled[aria-hidden='false'] .swal2-title {
          color: var(--text-color);
     }

     .swal2-styled[aria-hidden='false'] .swal2-content {
          color: var(--text-color);
     }

     .swal2-styled[aria-hidden='false'] .swal2-input {
          color: var(--text-color);
          border-color: var(--border-color);
     }

     .swal2-styled[aria-hidden='false'] .swal2-input:focus {
          border-color: var(--accent-color);
     }

     .swal2-styled[aria-hidden='false'] .swal2-input::-webkit-input-placeholder {
          color: var(--text-color);
     }

     .swal2-styled[aria-hidden='false'] .swal2-input::-moz-placeholder {
          color: var(--text-color);
     }

     .swal2-styled[aria-hidden='false'] .swal2-input:-ms-input-placeholder {
          color: var(--text-color);
     }

     .swal2-styled[aria-hidden='false'] .swal2-input::-ms-input-placeholder {
          color: var(--text-color);
     }

     .swal2-styled[aria-hidden='false'] .swal2-input::placeholder {
          color: var(--text-color);
     }
</style>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<?php if (isset($_GET['success'])): ?>
     <script>
          
          const Toast = Swal.mixin({
               toast: true,
               position: 'center',
               iconColor: 'var(--text-color)',
               customClass: {
                    popup: 'colored-toast',
               },
               showConfirmButton: false,
               timer: 2000,
               timerProgressBar: true,
          });
          (async () => {
               await Toast.fire({
                    icon: 'success',
                    title: 'Logged in Successfully.',
               });
          })();
     </script>
<?php endif; ?>
