$(document).ready(function () {
    $('#login_button').click(function (event) {
        event.preventDefault();
        var uname = $('#user_login').val();
        var pwd = $('#user_password').val();
        var err = 0;
        $('.errmsg_yellow').text('').hide();
        if (uname == '') {
            $('#user_login').parent().find('span.errmsg_yellow').text('Username should not be empty').show();
            $('#user_login').removeClass('normal_brd').addClass('yellow_brd');
            $('#user_login').parent().find('span.input-group-addon').removeClass('normal_brd1').addClass('yellow_brd1');
            err = 1;
        }
        if (pwd == '') {
            $('#user_password').parent().find('span.errmsg_yellow').text('Password should not be empty').show();
            $('#user_password').removeClass('normal_brd').addClass('yellow_brd');
            $('#user_password').parent().find('span.input-group-addon').removeClass('normal_brd1').addClass('yellow_brd1');
            err = 1;
        }
        if (!err) {
            //$('#form-login').submit();    return false;
            $.ajax({
                url: $('#loginfrm').attr('action'),
                data: {username: $('#user_login').val(), password: $('#user_password').val(), req: 'ajax'},
                error: function () {
                    $('#form-login').before('<div id="loginsg" style="color:red">Please try later. Something went wrong');
                    setTimeout(function () {
                        $("#loginsg").fadeOut('slow', function () {
                            $(this).remove()
                        });
                    }, 2000);
                    $('#login_button').html('LOG IN').attr('disabled', false);
                },
                beforeSend: function () {
                    $('#login_button').html('Processing...').attr('disabled', 'disabled');
                    $('#user_login,#user_password').removeClass('yellow_brd').addClass('normal_brd');
                    $('#user_login').parent().find('span.input-group-addon').removeClass('yellow_brd1').addClass('normal_brd1');
                    $('#user_password').parent().find('span.input-group-addon').removeClass('yellow_brd1').addClass('normal_brd1');
                    $('.errmsg_yellow').text('');
                },
                success: function (op) {
                    $('#login_button').html('LOG IN').attr('disabled', false);
                    if (op.status == 'OK') {
                        $('#login_modal').modal('hide');
                        $('#top_navigation').after('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Login Session Updated.</strong> Please Continue Your Process.</div>');
                    } else if (op.status == 'ERR') {
                        $('#login_mess').text(op.msg);
                        $('#user_login,#user_password').removeClass('normal_brd').addClass('yellow_brd');
                        $('#user_login').parent().find('span.input-group-addon').removeClass('normal_brd1').addClass('yellow_brd1');
                        $('#user_password').parent().find('span.input-group-addon').removeClass('normal_brd1').addClass('yellow_brd1');
                    }
                }
            })
        }
    });
});
