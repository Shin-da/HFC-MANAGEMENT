document.getElementById('myForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    
    showConfirm(
        'Confirm Action',
        'Are you sure you want to proceed with this action?',
        'question'
    ).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
