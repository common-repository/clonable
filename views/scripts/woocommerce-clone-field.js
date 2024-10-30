jQuery(function($){
    $('.clonable-language-checkbox').click(function(){
        let checkbox = $(this);
        let checkbox_value = (checkbox.is(':checked') ? 'yes' : 'no' );
        $.ajax({
            type: 'POST',
            url: ajaxurl, // predefined in /wp-admin
            data: {
                action: 'clonable_save_product_inclusion_' + checkbox.attr('data-clone'),
                value: checkbox_value,
                product_id: checkbox.attr('data-product-id'),
                ajax_nonce : checkbox.attr('data-nonce')
            },
            beforeSend: function( xhr ) {
                checkbox.prop('disabled', true );
            },
            success: function(data){
                // enable the checkbox again, and show the success message
                checkbox.prop('disabled', false).addClass('checkbox-saved').delay(5000).queue(function() {
                    $(this).removeClass('checkbox-saved'); // set default styling back
                });
            }
        });
    });
});