// AUTO UPDATE IF QUANTITY IS BEING CLICKED
function updateDatabase(input) {
    const orderItemId = input.getAttribute('data-order-item-id');
    const productId = input.getAttribute('data-product-id');
    const newQuantity = input.value;

    HoldOn.open({
        theme: "sk-dot",
        message: "Please wait...",
    });

    fetch('functions/update_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_item_id=${orderItemId}&new_quantity=${newQuantity}`,
    })
    .then(response => {
        if (response.ok) {
            setTimeout(function () {
                location.reload();
            }, 10);
        } else {
            console.error('Failed to update quantity.');
            return Promise.reject('Failed to update quantity.');
        }
    })
    .then(data => {
        if (data.status === 'success') {
            const productPriceElement = document.querySelector(`[data-product-price="${productId}"]`);
            const productPrice = parseFloat(productPriceElement.textContent.replace('₱', ''));
            const subtotalElement = document.getElementById(`product-subtotal-${orderItemId}`);
            const newSubtotal = productPrice * newQuantity;
            subtotalElement.textContent = '₱' + newSubtotal.toFixed(2);

        } else {
            console.error('Failed to fetch updated product price.');
        }
    })
    .catch(error => {
        console.error('An error occurred:', error);
    })
    .finally(() => {
        HoldOn.close();
    });
}


    
// NUMBER OF MIN AND MAX OF CART
document.getElementById('cart-form').addEventListener('input', function (e) {
    if (e.target.tagName === 'INPUT' && e.target.type === 'number') {
        const input = e.target;
        const min = parseFloat(input.getAttribute('min'));
        const max = parseFloat(input.getAttribute('max'));
        const newValue = parseFloat(input.value);

        if (newValue < min) {
            input.value = min;
        } else if (newValue > max) {
            input.value = max;
        }
    }
});
