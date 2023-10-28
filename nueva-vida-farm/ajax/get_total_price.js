$(document).ready(function () {
    function updateTotalPrice() {
        var total = 0;
        var hasSelectedProducts = false;

        $('.product-checkbox:checked').each(function () {
            var $row = $(this).closest('tr');
            var subtotal = parseFloat($row.find('td:eq(3)').text().replace('₱', ''));
            total += subtotal;
            hasSelectedProducts = true;
        });

        var $totalPriceElement = $('#total-price');
        if (hasSelectedProducts) {
            $totalPriceElement.text('₱' + total.toFixed(2));
            $totalPriceElement.css({
                'font-size': '30px',
                'font-weight': '800',
                'color': '#dc3545'
            });
        } else {
            $totalPriceElement.text('₱0.00');
            $totalPriceElement.css({
                'font-size': '30px',
                'font-weight': '800',
                'color': '#dc3545'
            });
        }
    }

    $('.product-checkbox, .quantity-input').on('change', function () {
        updateTotalPrice();
    });

    $('#select-all').on('change', function () {
        $('.product-checkbox').prop('checked', this.checked);
        updateTotalPrice();
    });

    updateTotalPrice();
});
