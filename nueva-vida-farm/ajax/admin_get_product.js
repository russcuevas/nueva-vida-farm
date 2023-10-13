$(document).ready(function() {
  var existingProducts = {};

  $.ajax({
    url: 'get_product.php',
    method: 'POST',
    dataType: 'json',
    success: function(response) {
      existingProducts = response;

      $('input[name="product_name"]').on('input', function() {
        var productName = $(this).val().trim().toLowerCase();
        var productSizeSelect = $('select[name="product_size"]');

        productSizeSelect.find('option').hide();

        var existingSizes = existingProducts[productName] || [];

        productSizeSelect.find('option').each(function() {
          var option = $(this);
          var size = option.val().toLowerCase();
          if (size !== productName && !existingSizes.includes(size)) {
            option.show();
          }
        });

        if (productSizeSelect.find('option:selected').is(':hidden')) {
          productSizeSelect.val('');
        }
      });
    },
    error: function() {
      console.error('Error fetching existing products.');
    }
  });
});
