$(document).ready(function() {
    $(".btn-remove").click(function(event) {
        event.preventDefault();

        HoldOn.open({
            theme: "sk-dot",
            message: "Please wait...",
        });

        var orderItemId = $(this).siblings("input[name='order_item_id']").val();

        $.ajax({
            url: "functions/remove_cart.php",
            type: "GET",
            data: { order_item_id: orderItemId },
            dataType: "json",
            success: function(response) {
                console.log(response);
                setTimeout(function() {
                    HoldOn.close();
                }, 3000);

                if (response.status === "success") {
                    setTimeout(function (){
                        location.reload();
                    }, 1000);
                } else if (response.status === "error") {
                    console.error("Error: " + response.message);
                }
            },
            error: function(xhr, status, errorThrown) {
                setTimeout(function() {
                    HoldOn.close();
                }, 3000);
                console.error("Error: " + errorThrown);
            }
        });
    });
});
