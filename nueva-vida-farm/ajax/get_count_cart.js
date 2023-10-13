
// REAL TIME COUNT CART
function updateCartCount() {
    $.ajax({
        url: "functions/get_cart_counts.php",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                var cartCount = response.cart_count;
                $("#cart-count").text("(" + cartCount + ")");
            } else {
            }
        },
        error: function(errorThrown) {
            alert("Error: " + errorThrown);
        }
    });
}

updateCartCount();
setInterval(updateCartCount, 3000);
