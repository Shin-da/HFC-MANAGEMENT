<style>

     .colored-toast.swal2-icon-success {
          background-color: #a5dc86 !important;
     }

     .colored-toast.swal2-icon-error {
          background-color: #f27474 !important;
     }

     .colored-toast.swal2-icon-warning {
          background-color: #f8bb86 !important;
     }

     .colored-toast.swal2-icon-info {
          background-color: #3fc3ee !important;
     }

     .colored-toast.swal2-icon-question {
          background-color: #87adbd !important;
     }

     .colored-toast .swal2-title {
          color: white;
     }

     .colored-toast .swal2-close {
          color: white;
     }

     .colored-toast .swal2-html-container {
          color: white;
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