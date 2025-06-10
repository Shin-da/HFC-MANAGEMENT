/*************  ✨ Codeium Command 🌟  *************/
// Alert Modal Type
$(document).on('click', '#success', function(e) {
    swal(
        'Success',
        'You clicked the <b style="color:green;">Success</b> button!',
        'success'
    )
});
$(document).on('click', '#error', function(e) {
    swal(
        'Error!',
        'You clicked the <b style="color:red;">error</b> button!',
        'error'
    )
});
$(document).on('click', '#warning', function(e) {
    swal(
        'Warning!',
        'You clicked the <b style="color:coral;">warning</b> button!',
        'warning'
    )
});
$(document).on('click', '#info', function(e) {
    swal(
        'Info!',
        'You clicked the <b style="color:cornflowerblue;">info</b> button!',
        'info'
    )
});
$(document).on('click', '#question', function(e) {
    swal(
        'Question!',
        'You clicked the <b style="color:grey;">question</b> button!',
        'question'
    )
});
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

// Alert With Custom Icon and Background Image
$(document).on('click', '#icon', function(event) {
    swal({
        title: 'Custom icon!',
        text: 'Alert with a custom image.',
        imageUrl: 'https://image.shutterstock.com/z/stock-vector--exclamation-mark-exclamation-mark-hazard-warning-symbol-flat-design-style-vector-eps-444778462.jpg',
        imageWidth: 200,
        imageHeight: 200,
        imageAlt: 'Custom image',
        animation: false
    })
});
$(document).on('click', '#image', function(event) {
    swal({
        title: 'Custom background image, width and padding.',
        width: 700,
        padding: 150,
        background: '#fff url(https://image.shutterstock.com/z/stock-vector--exclamation-mark-exclamation-mark-hazard-warning-symbol-flat-design-style-vector-eps-444778462.jpg)'
    })
});
// Usage examples
createToast('success', 'Signed in successfully');
createToast('error', 'Something went wrong');

// Alert With Input Type
$(document).on('click', '#subscribe', function(e) {
    swal({
        title: 'Submit email to subscribe',
        input: 'email',
        inputPlaceholder: 'Example@email.xxx',
        showCancelButton: true,
        confirmButtonText: 'Submit',
        showLoaderOnConfirm: true,
        preConfirm: (email) => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    if (email === 'example@email.com') {
                        swal.showValidationError(
                            'This email is already taken.'
                        )
                    }
                    resolve()
                }, 2000)
            })
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.value) {
            swal({
                type: 'success',
                title: 'Thank you for subscribe!',
                html: 'Submitted email: ' + result.value
            })
        }
    })
});
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

// Alert Redirect to Another Link
$(document).on('click', '#link', function(e) {
    swal({
        title: "Are you sure?",
        text: "You will be redirected to https://utopian.io",
        type: "warning",
        confirmButtonText: "Yes, visit link!",
        showCancelButton: true
    })
        .then((result) => {
            if (result.value) {
                window.location = 'https://utopian.io';
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your stay here :)',
                    'error'
                )
            }
        })
});
// Usage examples
showAlert('info', 'Information', 'This is an information alert');
showAlert('warning', 'Warning!', 'This action cannot be undone', true, 'Yes, delete it!');


/******  65d3dead-78d1-4fd5-9dcc-34d608f6bed7  *******/