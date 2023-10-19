// AVAILABLE STOCK
function updateAvailableStock(productID, stock) {
    var productElement = $(`#productStocksValue_${productID}`);
    productElement.text(stock);

    if (parseInt(stock) === 0) {
        productElement.closest(".col").addClass("hidden-product");
    } else {
        productElement.closest(".col").removeClass("hidden-product");
    }
}

function fetchAllAvailableStock() {
    setTimeout(function() {
        $.ajax({
            url: 'functions/get_product_stocks.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                data.forEach(function (product) {
                    updateAvailableStock(product.product_id, product.product_stocks);
                });
            },
            error: function () {
                console.log('Reload your page');
                alert('Reload your page');
            }
        });
    }, 5000);
}


setInterval(fetchAllAvailableStock, 5000);
fetchAllAvailableStock();



// ADD TO CART FUNCTION
$(document).ready(function () {
    $(".add-to-cart-form").submit(function (event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: "functions/add_to_cart.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                console.log(response);

                if (response.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: response.message,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                    });
                    setTimeout(function () {
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
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                }
            },
            error: function (errorThrown) {
                setTimeout(function () {
                    HoldOn.close();
                }, 3000);
            }
        });
    });
});
