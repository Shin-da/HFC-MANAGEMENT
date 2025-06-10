<!-- swal2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* colors */
    :root {
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
        --accent-color: #A02334;
        --accent-color-dark: #6d1c27;
        --accent-color-dark-inactive: #4b2025;
        --white: #fff;
        --orange-color: #FFAD60;
        --yellow-color: #FFEEAD;
        --blue-color: #96CEB4;
        --blue-color-dark: #2D5B6B;
        --vandyke-color: #362C28;
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
        padding: 20px;
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

    .swal2-styled[aria-hidden='false'] .swal2-input::placeholder {
        color: var(--text-color);
    }

    .colored-toast.swal2-icon-success {
        background-color: var(--success-color) !important;
    }

    .colored-toast.swal2-icon-error {
        background-color: var(--danger-color) !important;
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

    .colored-toast .swal2-title,
    .colored-toast .swal2-close,
    .colored-toast .swal2-html-container {
        color: white;
    }
</style>
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<?php if (isset($_GET['success'])): ?>
    <script>
        // import Swal from 'sweetalert2';

        const Toast = Swal.mixin({
            toast: true,
            position: 'center',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast',
            },
            showConfirmButton: false,
            timer: 4000,
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
