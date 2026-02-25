jQuery(document).ready(function($) {
    
    // Auto select first payment gateway if none selected (WC usually does this, but we enforce it if missing)
    if ($('input[name="payment_method"]:checked').length === 0) {
        $('input[name="payment_method"]:first').prop('checked', true);
    }

    // Add to cart AJAX
    $('.adv-add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var card = button.closest('.adv-product-card');
        var productId = card.data('product-id');
        var qty = card.find('.adv-qty').val();

        if(qty < 1) qty = 1;

        button.text('Adding...').prop('disabled', true);

        $.ajax({
            url: adv_checkout_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'adv_add_to_cart',
                nonce: adv_checkout_obj.nonce,
                product_id: productId,
                quantity: qty
            },
            success: function(response) {
                button.text('Added').prop('disabled', false);
                setTimeout(function(){
                    button.text('Add');
                }, 2000);
                
                // Trigger WooCommerce native update to refetch checkout fragments, shipping, subtotal
                $('body').trigger('update_checkout');
            },
            error: function() {
                button.text('Error').prop('disabled', false);
            }
        });
    });

    // Listen to native WooCommerce checkout update commands
    $(document.body).on('updated_checkout', function(){
        // Fragments updated, anything needed can be refreshed here.
        // E.g. re-attaching event listeners inside dynamically replaced elements if necessary
    });

});
