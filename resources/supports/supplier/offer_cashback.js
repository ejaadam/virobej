$(document).ready(function () {
    $('#search-customer-cashback-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            success: function (op) {
                $('#customer', $('#get-bill-amount-cashback-form')).text(op.data.customer_full_name + ' (' + op.data.customer_uname + ')');
                $('#mobile', $('#get-bill-amount-cashback-form')).text(op.data.customer_mobile);
                $('#currency', $('#get-bill-amount-cashback-form')).text(op.data.currency);
                $('#search-customer-cashback-form').fadeOut('fast', function () {
                    $('#get-bill-amount-cashback-form').fadeIn('slow');
                });
            }
        });
    });
    $('#get-bill-amount-cashback-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            success: function (op) {
                $('#search-customer-cashback-form').resetForm();
                $('#get-bill-amount-cashback-form').resetForm();
                $('#get-bill-amount-cashback-form').fadeOut('fast', function () {
                    $('#search-customer-cashback-form').fadeIn('slow');
                });
            }
        });
    });
});
