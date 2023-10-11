$(document).ready(function () {
    $(".loginForm").submit(function (event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "../functions/admin_login.php",
            data: formData,
            dataType: 'json',
            success: function (response) {
                console.log(response);

                if (response.status === "success") {
                    HoldOn.open({
                        theme: 'sk-dot',
                        message: 'Please wait...'
                    });

                    setTimeout(function () {
                        HoldOn.close();
                        window.location.href = 'home';
                    }, 1000);
                } else if (response.status === 'warning') {
                    Swal.fire({
                        icon: "warning",
                        title: response.message,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                    })
                } else {
                    Swal.fire({
                        icon: "error",
                        title: response.message,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                    });
                }
            },
        });
    });
});
