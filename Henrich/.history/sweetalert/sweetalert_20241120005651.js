/*************  ✨ Codeium Command 🌟  *************/
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
// sweetalert
const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    
    Toast.fire({
        icon: 'success',
        title: 'Signed in successfully'
    })

// Usage examples
SystemToast.fire({
    icon: 'success',
    title: 'Signed in successfully'
});
    Toast.fire({
        icon: 'error',
        title: 'Something went wrong'
    })

SystemToast.fire({
    icon: 'error',
    title: 'Something went wrong'
});


/******  00ef468b-57c8-4bd0-aed8-f22c5c23ab84  *******/