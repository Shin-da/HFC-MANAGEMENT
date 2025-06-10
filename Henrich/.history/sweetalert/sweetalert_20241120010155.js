/*************  ✨ Codeium Command 🌟  *************/
// Sweetalert configuration for the whole system
const createToast = (icon, title, position = 'top-end', timer = 3000) => {
    return Swal.mixin({
        toast: true,
        position: position,
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    }).fire({
        icon: icon,
        title: title
    });
};
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
createToast('success', 'Signed in successfully');
createToast('error', 'Something went wrong');
SystemToast.fire({
    icon: 'success',
    title: 'Signed in successfully'
});

SystemToast.fire({
    icon: 'error',
    title: 'Something went wrong'
});


/******  35c03486-c1e7-40bc-a0d5-3c1f4ccadf1d  *******/