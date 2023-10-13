// CONTACT PAGE
$(document).ready(function() {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();

        var name = $('#name').val();
        var mobile = $('#mobile').val();
        var message = $('#message').val();

        if (name === '' || mobile === '' || message === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Fields cannot be empty',
                confirmButtonColor: '#c8ec56'
            });
            return;
        }

        HoldOn.open({
            theme: 'sk-dot',
            message: 'Submitting please wait..'
        });

        $.ajax({
            type: 'POST',
            url: 'functions/contact.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                HoldOn.close();
                console.log(response)
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: true,
                        confirmButtonColor: '#9fef00',
                    })
                    $('#contactForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                    });
                }
            }
        });
    });
});

