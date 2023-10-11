$(document).ready(function(){
    $('.registerForm').submit(function(event){
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "functions/register.php",
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log (response);

                if (response.status === 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                    $('.registerForm')[0].reset();
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    })
                }
            }
        });
    });
});