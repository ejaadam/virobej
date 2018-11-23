$(document).ready(function () {
    $('#checkid').change(function () {
        if ($(this).is(':checked')) {
            var bs1 = $('#billing_street1').val();
            $('#shipping_street1').val(bs1);
            var bs1 = $('#billing_street2').val();
            $('#shipping_street2').val(bs1);
            var bs1 = $('#billing_city').val();
            $('#shipping_city').val(bs1);
            var bs1 = $('#billing_state').val();
            $('#shipping_state').val(bs1);
            var bs1 = $('#billing_code').val();
            $('#shipping_code').val(bs1);
            var bs1 = $('#billing_country').val();
            $('#shipping_country').val(bs1);
        } else {
            $('#shipping_street1').val('');
            $('#shipping_street2').val('');
            $('#shipping_city').val('');
            $('#shipping_state').val('');
            $('#shipping_code').val('');
            $('#shipping_country').val('');
        }
    });
    $('#hr_register').click(function (event) {
        //alert('123');
        event.preventDefault();
        var acc_name = $('#Account_name').val();
        var industry = $('#industry').val();
        //alert(industry);
        var err = 0;
        $('.errmsg_yellow').text('').hide();
        if (acc_name == '') {
            $('#Account_name').parent().find('span.errmsg_yellow').text('Name should not be empty').show();
            $('#Account_name').addClass('yellow_brd');
            err = 1;
        }
        if ($('#officePhone').val() == '') {
            $('#officePhone').parent().find('span.errmsg_yellow').text('Office Phone should not be empty').show();
            $('#officePhone').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#industry').val() == '') {
            $('#industry').parent().find('span.errmsg_yellow').text('Select one Industry').show();
            $('#industry').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#officeFax').val() == '') {
            $('#officeFax').parent().find('span.errmsg_yellow').text('Enter Your Office Fax Number').show();
            $('#officeFax').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#employees').val() == '') {
            $('#employees').parent().find('span.errmsg_yellow').text('Enter Your Employees').show();
            $('#employees').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#annualRevenue').val() == '') {
            $('#annualRevenue').parent().find('span.errmsg_yellow').text('Enter Your Annual Revenue').show();
            $('#annualRevenue').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#Account_type').val() == '') {
            $('#Account_type').parent().find('span.errmsg_yellow').text('Enter Your Account Type').show();
            $('#Account_type').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#Account_website').val() == '') {
            $('#Account_website').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#Account_website').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#billing_street1').val() == '') {
            $('#billing_street1').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#billing_street1').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#billing_street2').val() == '') {
            $('#billing_street2').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#billing_street2').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#billing_city').val() == '') {
            $('#billing_city').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#billing_city').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#billing_state').val() == '') {
            $('#billing_state').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#billing_state').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#billing_code').val() == '') {
            $('#billing_code').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#billing_code').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#billing_country').val() == '') {
            $('#billing_country').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#billing_country').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#Account_description').val() == '') {
            $('#Account_description').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#Account_description').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#shipping_street1').val() == '') {
            $('#shipping_street1').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#shipping_street1').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#shipping_street2').val() == '') {
            $('#shipping_street2').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#shipping_street2').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#shipping_city').val() == '') {
            $('#shipping_city').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#shipping_city').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#shipping_state').val() == '') {
            $('#shipping_state').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#shipping_state').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#shipping_code').val() == '') {
            $('#shipping_code').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#shipping_code').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if ($('#shipping_country').val() == '') {
            $('#shipping_country').parent().find('span.errmsg_yellow').text('Enter Your Website').show();
            $('#shipping_country').removeClass('normal_brd').addClass('yellow_brd');
            err = 1;
        }
        if (! err) {
            //$('#form-login').submit();    return false;
            $.ajax({
                url: window.location.BASE + '/admin/hr_registration',
                data: {account_name: $('#Account_name').val(), off_phone: $('#officePhone').val(), industry: $('#industry').val(), officeFax: $('#officeFax').val(), employees: $('#employees').val(), annualRevenue: $('#annualRevenue').val(), Account_type: $('#Account_type').val(), Account_website: $('#Account_website').val(), billing_street1: $('#billing_street1').val(), billing_street2: $('#billing_street2').val(), billing_city: $('#billing_city').val(), billing_state: $('#billing_state').val(), billing_code: $('#billing_code').val(), billing_country: $('#billing_country').val(), shipping_street1: $('#shipping_street1').val(), shipping_street2: $('#shipping_street2').val(), shipping_city: $('#shipping_city').val(), shipping_state: $('#shipping_state').val(), shipping_code: $('#shipping_code').val(), shipping_country: $('#shipping_country').val(), Account_description: $('#Account_description').val(), req: 'ajax'},
                error: function () {
                    $('#hr_form_register').before('<div id="loginsg" style="color:red">Please try later. Something went wrong');
                    setTimeout(function () {
                        $("#loginsg").fadeOut('slow', function () {
                            $(this).remove()
                        });
                    }, 2000);
                    $('#login_button').html('LOG IN').attr('disabled', false);
                },
                beforeSend: function () {
                    $('#hr_register').html('Processing...').attr('disabled', 'disabled');
                    $('.errmsg_yellow').text('');
                },
                success: function (op) {
                    //$('#login_button').html('LOG IN').attr('disabled', false);
                    if (op.status == 'OK') {
                        $('#hr_form_register').hide();
                        $('#login_mess').text(op.msg);
                        //window.location.href = op.url;
                    }
                }
            })
        }
    });
});
