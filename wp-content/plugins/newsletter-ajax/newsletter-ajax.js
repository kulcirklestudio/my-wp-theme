jQuery(document).ready(function ($) {

    // Prevent multiple bindings
    $(document).off('submit', '#newsletter-subscription-form');

    $(document).on('submit', '#newsletter-subscription-form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $button = $form.find('.submit-btn');
        const $response = $('#newsletter-response');

        // Disable button
        $button.prop('disabled', true).text('Subscribing...');

        const data = {
            action: 'newsletter_subscribe',
            name: $('#name').val(),
            email: $('#email').val(),
            nonce: newsletter_ajax.nonce
        };

        $.ajax({
            url: newsletter_ajax.ajax_url,
            type: 'POST',
            data: data,
            dataType: 'json',

            success: function (response) {
                if (response.success) {
                    $response.html('<p class="success">' + response.data + '</p>');
                    $form[0].reset();
                    // Optional: hide form after success
                    // $form.fadeOut(400);
                } else {
                    $response.html('<p class="error">' + (response.data || 'Subscription failed.') + '</p>');
                }
            },

            error: function (xhr, status, error) {
                console.log('AJAX Error:', xhr.responseText);
                $response.html('<p class="error">An error occurred. Please try again.</p>');
            },

            complete: function () {
                $button.prop('disabled', false).text('Subscribe');
            }
        });
    });
});