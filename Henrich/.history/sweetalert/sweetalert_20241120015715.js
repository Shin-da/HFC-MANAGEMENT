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

// Usage examples
createToast('success', 'Signed in successfully');
createToast('error', 'Something went wrong');

// Reusable SweetAlert function
const showAlert = (icon, title, text, showCancelButton = false, confirmButtonText = 'OK') => {
    return Swal.fire({
        icon: icon,
        title: title,
        text: text,
        showCancelButton: showCancelButton,
        confirmButtonText: confirmButtonText
    });
};

// Usage examples
showAlert('info', 'Information', 'This is an information alert');
showAlert('warning', 'Warning!', 'This action cannot be undone', true, 'Yes, delete it!');

