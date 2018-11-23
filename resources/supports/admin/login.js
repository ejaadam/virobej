$(function () {
    $('#login_form').submit(function (event) {
        event.preventDefault();
        CURFORM = $('#login_form');
        $.ajax({
            url: $('#login_form').attr('action'),
            data: $('#login_form').serializeObject(),
			success: function (op) {
				window.location.href=op.url;
            }
        });
    });
    $('#forgot-password-btn').on('click', function (e) {
        e.preventDefault();
        window.location.ChangeUrl($('.forgor-pwd-panel .login_head h1').text(), window.location.BASE + 'admin/forgot-password');
        $('.forgor-pwd-panel').show();
        $('.login-panel').hide();
    });
    $('#login-btn').on('click', function (e) {
        e.preventDefault();
        window.location.ChangeUrl($('.login-panel .login_head h1').text(), window.location.BASE + 'admin/login');
        $('.forgor-pwd-panel').hide();
        $('.login-panel').show();
    });
    $('#forgot-password').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $('#forgot-password');
        $.ajax({
            url: $('#forgot-password').attr('action'),
            data: $('#forgot-password').serializeObject(),
            success: function () {
                $('#login-btn').trigger('click');
            }
        });
    });
    $('.send-verification-code').on('click', function (e) {
        e.preventDefault();
        CURFORM = $('#forgot-password');
        $.ajax({
            url: $('.send-verification-code').data('url'),
            data: $('#forgot-password').serializeObject(),
            success: function () {
                $('.reset-verification').show();
                $('.send-verification-code').parent('div.login_submit').hide();
            }
        });
    });
    $('#show-hide-password').on('click', function () {
        if ($('#pass_key').attr('type') === 'password') {
            $('#pass_key').attr('type', 'text');
        }
        else {
            $('#pass_key').attr('type', 'password');
        }
        var alt = $('#show-hide-password').attr('data-alternative');
        $('#show-hide-password').attr('data-alternative', $('#show-hide-password').text()).text(alt);
    });
});
