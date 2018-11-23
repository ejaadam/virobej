$(document).ready(function () {
    var chgpwd = $("#changepassword").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            oldpassword: "required",
            newpassword: {
                required: true,
                minlength: 5,
                maxlength: 20
            },
            confirmpassword: {
                required: true,
                equalTo: "#newpassword"
            }
        },
        messages: $val_message,
        });
		});
		$(document).ready(function () {
        var chgpwd = $("#changepassword").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            oldpassword: "required",
            newpassword: {
                required: true,
                minlength: 5,
                maxlength: 20
            },
            confirmpassword: {
                required: true,
                equalTo: "#newpassword"
            }
        },
        messages: $val_message,
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                var datastring = $(form).serialize();
                $.ajax({
                    url: "update_password",
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {
                        $('.alert').remove();
                        $("#update_password").find('span').text($processing);
                        $('button[type="submit"]').attr('disabled', 'disabled');
                    },
                    success: function (data) {
                        $('#oldpassword_status').hide();
                        $('#changepassword').before('<div class="alert ' + data.alertclass + '"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
                        $('button[type="submit"]').attr('disabled', false);
                        $("#update_password").find('span').text($update);
                        $('#changepassword').trigger('reset');
                    },
                    error: function () {
                        $('input[type="submit"]').removeAttr("disabled");
                        $("#update_password").val($update);
                        alert($wrong_msg);
                    }
                });
            }
        }
    });
    $("#oldpassword").blur(function () {
        var oldpassword = $(this).val();
        if (!(oldpassword == '')) {
            $.ajax({
                type: 'POST',
                data: {'oldpassword': oldpassword},
                url: 'password_check',
                dataType: "json",
                success: function (data) {
                    if (data.status == 'ok') {
                        $('#oldpassword_status').html('');
                        return true;
                    } else if (data.status == 'error') {
                        $('#oldpassword').val('');
                        $('#newpassword').val('');
                        $('#confirmpassword').val('');
                        $('#oldpassword_status').show().html(data.msg);
                        return false;
                    }
                },
                error: function () {
                    alert($wrong_msg);
                }
            });
        }
    });
});
