$(document).ready(function () {
    function updateTotalPrice() {
        var total = 0;
        var hasSelectedProducts = false;

        $('.product-checkbox:checked').each(function () {
            var $row = $(this).closest('tr');
            var quantity = parseInt($row.find('.quantity-input').val());
            var price = parseFloat($row.find('td:eq(3)').text().replace('₱', ''));
            total += quantity * price;
            hasSelectedProducts = true;
        });

        var $totalPriceElement = $('#tableFooter td:eq(5)');
        var $totalPriceLabelElement = $('#tableFooter td:eq(4)');

        if (hasSelectedProducts && total > 0) {
            $totalPriceElement.text('₱' + total.toFixed(2));
            $totalPriceLabelElement.show();
            $totalPriceElement.css({
                'font-size': '30px',
                'font-weight': '800',
                'color': '#dc3545'
            });
        } else {
            $totalPriceElement.text('');
            $totalPriceLabelElement.hide();
        }
    }

    $('#select-all').on('change', function () {
        $('.product-checkbox').prop('checked', this.checked);
        updateTotalPrice();
    });

    $('.product-checkbox, .quantity-input').on('change', function () {
        updateTotalPrice();
    });

    updateTotalPrice();
});