var bal = '';
$(document).ready(function () {

    $('#search_btn').click(function (e) {
        e.preventDefault();
        var mrcode = $('#mrcode').val();
        var url = $('#search_form').attr('action');
        if (mrcode != '') {
            $.ajax({
                url: url,
                data: {mrcode: mrcode},
                type: "post",
                dataType: "json",
                beforeSend: function () {
                    $('#search_btn').css('display', 'none');
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        $('#mrerror').html('');
                        merchant = data.merchant;
                        bal = data.balance;
                        $('#fullname').val(merchant.full_name);
                        $('#account_id').val(merchant.account_id);
                        $('#business').val(merchant.mrbusiness_name);
                        $('#email').val(merchant.email);
                        $('#currency_id option').attr('hidden', true).attr('disabled', true);
                        $('#currency_id').val('');
                        $.each(merchant.currency_id, function (k, v) {
                            $('#currency_id option[value=' + v + ']').removeAttr('hidden').attr('disabled', false);
                        });
                        $('#details_div').css('display', 'block');
                        $('#currency_id').trigger('change');
                    } else if (data.status == 'err') {
                        $('#mrerror').addClass('rext-danger').html('Merchant Not Found');
                        $('#search_btn').css('display', 'block');
                    }
                }
            })
        } else {
            $('#mrerror').addClass('text-danger').html('Please entre merchant code');
        }
    });
    $('#mrcode').keyup(function (e) {
        $('#mrerror').html('');
        $('#fullname').val('');
        $('#account_id').val('');
        $('#business').val('');
        $('#email').val('');
        $('#amount').val('');
        $('#details_div').css('display', 'none');
        $('#search_btn').css('display', 'block');
    });
    $('#amount').keyup(function () {
        var min_amt = $('#min').val();
        var max_amt = $('#max').val();
        var amt = $(this).val();
        var currency = $('#currency_id option:selected').text();

        $('#submit_btn').css('display', 'none');
        if (parseFloat(amt) < min_amt) {
            $('#amt_err').addClass('text-danger').html('Min transfer amount ' + min_amt + ' ' + currency);
        } else if (parseFloat(amt) > max_amt) {
            $('#amt_err').addClass('text-danger').html('Max transfer amount ' + max_amt + ' ' + currency);
        } else {
            $('#amt_err').html('');
            $('#submit_btn').css('display', 'block');
        }

    })
    $('#search_form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            mrcode: {required: true, maxlength: 30},
            amount: {required: true, number: true},
            currency_id: {required: true},
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                $.ajax({
                    url: document.location.BASE + 'admin/finance/fund-transfer/merchant',
                    data: $('#search_form').serialize(),
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                        $('#update_member_details').attr('disabled', true);
                        $('body').toggleClass('loaded');
                    },
                    success: function (res) {
                        $('body').toggleClass('loaded');
                        if (res.status == 'ok') {
                            $('#update_member_details').attr('disabled', false);
                            $('#status_msg').html('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('#mrcode').trigger('keyup');
                            $('.alert').fadeOut(7000);

                        } else {
                            $('#status_msg').html('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('body').toggleClass('loaded');
                        responseText = $.parseJSON(jqXHR.responseText);
                        console.log(responseText);
                        $.each(responseText.errs, function (fld, msg) {
                            if ($('#search_form [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#search_form [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#search_form [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
                });
            }
        }
    });

    $('#currency_id,#wallet').change(function (e) {
        e.preventDefault();
        wallet = $('#wallet option:selected').val();
        currency = $('#currency_id option:selected').val();
        currency_code = $('#currency_id option:selected').text();
		trans_type = $('.trans_type:checked').val();
        if ((wallet != '') && (currency != '')) {
            if (wallet in bal) {
                wallet_bal = bal[wallet][currency];
				if((trans_type == 2) && (wallet_bal == 0)){
					$('#submit_btn').attr('disabled',true);
				}else{
					$('#submit_btn').attr('disabled',false);
				}
                if ((wallet_bal != undefined)) {
                    $('#avail_bal').addClass('text-danger').html('Available balance in wallet ' + wallet_bal + ' ' + currency_code);
                } else {
                    $('#avail_bal').addClass('text-danger').html('Balance not available');
                }
            } else {
                $('#avail_bal').addClass('text-danger').html('Balance not available');
            }
        }
    })

    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
})
