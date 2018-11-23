$(document).ready(function () {
    var DT = $('#customer_list').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('input,select', '.panel_controls').serializeObject());
            },
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy h:m:s');
                }
            },
            {
                data: 'full_name',
                name: 'full_name',
                render: function (data, type, row, meta) {
                    return '<strong>' + row.full_name + '</strong>&nbsp;(' + row.user_code + ')';
                }
            },
            {
                data: 'email',
                name: 'email',
            },
            {
                data: 'last_login',
                name: 'last_login',
                render: function (data, type, row, meta) {
                    if (row.last_login != null)
                    {
                        return new String(row.last_login).dateFormat('dd-mmm-yyyy h:m:s');
                    }
                    else
                    {
                        return '';
                    }
                }
            },
            {
                data: 'status_id',
                name: 'status_id',
                class: 'status',
                render: function (data, type, row, meta) {
                    switch (row.login_block)
                    {
                        case 0:
                            return '<span class="label label-success" data-account_id="' + row.account_id + '" id="status_' + row.account_id + '" >Active</span>';
                            break;
                        case 1:
                            return '<span class="label label-danger" data-account_id="' + row.account_id + '" id="status_' + row.account_id + '" >Blocked</span>';
                            break;
                    }
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right text-left" role="menu">';
                    action_buttons += '<li class="edit" rel="' + row.account_id + '" data-name="' + row.full_name + '" data-code="' + row.user_code + '"  data-email="' + row.email + '"  data-last_login="' + row.last_login + '"><a href="">Edit</a></li>';
                    action_buttons += '<li class="reset" rel="' + row.account_id + '" data-account_id="' + row.account_id + '" ><a href="">Reset Password</a></li>';
                    action_buttons += (row.login_block == Constants.ACTIVE)
                            ? '<li><a data="0" data-account_id="' + row.account_id + '" href="' + json.url + '/admin/customer/change_status" class="customer_status">Unblock</a></li>'
                            : '<li><a data="1"data-account_id="' + row.account_id + '" href="' + json.url + '/admin/customer/change_status" class="customer_status">Block</a></li>';
                    action_buttons += '</ul></div>';
                    return action_buttons;
                }
            },
        ]
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#customer_list').on('click', '.customer_status', function (e) {
        e.preventDefault();
        var msg = '';
        var CurEle = $(this);
        url = $(this).attr('href');
        status = $(this).attr('data');
        var account_id = $(this).data('account_id');
        if (status == 1) {
            msg = 'Block';
        } else if (status == 0) {
            msg = 'Activate';
        }
        if (confirm('Are you sure to ' + msg + ' this Customer')) {
            $.ajax({
                data: {status: status, account_id: account_id},
                url: url,
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#successmsg').html(data.msg);
                        if (status == 1)
                        {
                            $('#status_' + account_id).removeClass('label-success').addClass('label-danger');
                            $('#status_' + account_id).text('Blocked');
                            CurEle.text('Unblock');
                            CurEle.attr('data', 0);
                        }
                        else
                        {
                            $('#status_' + account_id).removeClass('label-danger').addClass('label-success');
                            $('#status_' + account_id).text('Active');
                            CurEle.text('Block');
                            CurEle.attr('data', 1);
                        }
                    }
                }
            });
        }
    });
    $(document.body).on('click', '.reset', function (e) {
        $('#reset_pwd input[type=password]').val('');
        $('#msg').empty();
        e.preventDefault();
        var msg = '';
        var CurEle = $(this);
        account_id = CurEle.data('account_id');
        url = $(this).attr('href');
        $('#reset_pwd_block').show();
        $('#customer_management_block').hide();
        $('#reset_pwd').validate({
            errorElement: 'div',
            errorClass: 'error',
            focusInvalid: false,
            rules: {
                enter_new_pwd: {
                    required: true,
                },
                con_new_pwd: {
                    equalTo: '#enter_new_pwd',
                    required: true,
                },
            },
            messages: {
                enter_new_pwd: {
                    required: 'Please Enter Your  New Password',
                },
                con_new_pwd: {
                    equalTo: 'Password Does Not Match',
                    required: 'Please Enter Your Confirm Password ',
                },
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                    $('.alert').remove();
                    var datastring = $(form).serialize() + '&' + $.param({'account_id': account_id});
                    $.ajax({
                        url: $('#reset_pwd').attr('action'),
                        data: datastring,
                        beforeSend: function () {
                            $('button[type="submit"]', $(form)).val('Processing...').attr('disabled', 'disabled');
                        },
                        success: function (data) {
                            if (data.status == 'OK')
                            {
                                $('#reset_pwd input[type=password]').val('');
                                $('#msg').html('<div class="alert alert-success">' + data.msg + '</div>');
                            }
                            else if (data.status == 'ERR')
                            {
                                $('button[type="submit"]', $(form)).removeAttr('disabled');
                                $('#reset_pwd input[type=password]').val('');
                                $('#msg').html('<div class="alert alert-danger">' + data.msg + '</div>');
                            }
                        },
                        error: function () {
                            //$('.alert').remove();
                            //    $('input[type="text"],textarea',$(form)).val('');
                            $('button[type="submit"]', $(form)).removeAttr('disabled');
                            alert('Something went wrong');
                        }
                    });
                }
            }
        });
        $(document.body).on('click', '#close_zone', function (e) {
            e.preventDefault();
            $('#reset_pwd_block').hide();
            $('#customer_management_block').show()
        });
        /*$.ajax({
         data: {status: status,account_id:account_id},
         url: url,
         success: function (data) {
         if (data.status == 'OK') {
         $('#successmsg').html(data.msg);
         if (status == 1)
         {
         $('#status_' + account_id).removeClass('label-success').addClass('label-danger');
         $('#status_' + account_id).text('Blocked');
         CurEle.text('Unblock');
         CurEle.attr('data', 0);
         }
         else
         {
         $('#status_' + account_id).removeClass('label-danger').addClass('label-success');
         $('#status_' + account_id).text('Active');
         CurEle.text('Block');
         CurEle.attr('data', 1);
         }
         }
         }
         });*/
    });
    /* Add New OPtion js */
});
