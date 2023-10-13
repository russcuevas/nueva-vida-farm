// ADD TO CART FUNCTION
$(document).ready(function() {
    $(".add-to-cart-form").submit(function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: "functions/add_to_cart.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                console.log(response);
                setTimeout(function() {
                    HoldOn.close();
                }, 3000);

                if (response.status === "success") {
                    HoldOn.open({
                        theme: "sk-dot",
                        message: "Please wait...",
                    });
                    Swal.fire({
                        icon: "success",
                        title: response.message,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                    });
                    setTimeout(function () {
                        HoldOn.close();
                    }, 2000);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else if (response.status === "warning") {
                    Swal.fire({
                        icon: "warning",
                        title: response.message,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: response.message,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 200);
                }
            },
            error: function(errorThrown) {
                setTimeout(function() {
                    HoldOn.close();
                }, 3000);
                
                alert("Try again : " + errorThrown);
            }
        });
    });
});
