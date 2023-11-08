$(document).ready(() => {
    if ($('#tab-simplifycommerce').length > 0) {
        $('a[href="#tab-simplifycommerce"]').tab('show');
    }
    $("button.btn_refund").on('click', function (e) {
        $('.payment_button_wrapper').hide();
        $('.refund_reason_container').show();
        $('.partial_refund_row').hide();
    });
    $("button.cancel_refund_button").on('click', function (e) {
        $('.payment_button_wrapper').show();
        $('.refund_reason_container').hide();
    });

    $("button.btn_partial_refund").on('click', function (e) {
        $('.partial_refund_row').show();
        $('.payment_button_wrapper').hide();
        $('.refund_reason_container').show();
    });

    $(".refund_reason_container .cancel_refund_button").on('click', function (e) {
        $('.refund_reason_container .refunded_amount').val("");
        $('.refund_reason_container .refund_reason').val("");
    });

    $(".refunded_amount , .partial_refunded_amount").on("input", function () {
        // Remove any non-numeric characters except the decimal point
        $(this).val($(this).val().replace(/[^0-9.]/g, ''));

        // Ensure that there's only one decimal point
        if ($(this).val().split('.').length > 2) {
            $(this).val($(this).val().slice(0, -1)); // Remove the last character
        }

        // Limit to a maximum of two decimal places
        const parts = $(this).val().split('.');
        if (parts[1] && parts[1].length > 2) {
            parts[1] = parts[1].substring(0, 2);
            $(this).val(parts.join('.'));
        }
    });

    // Prevent invalid characters from being entered
    $(".refunded_amount , .partial_refunded_amount").on("keypress", function (e) {
        const inputValue = e.key;
        if (!/[\d.]/.test(inputValue)) {
            e.preventDefault();
        }
    });
});
