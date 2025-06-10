// Sweetalert configuration for the whole system
const SystemToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Usage examples
SystemToast.fire({
    icon: 'success',
    title: 'Signed in successfully'
);

SystemToast.fire({
    icon: 'error',
    title: 'Something went wrong'
});

