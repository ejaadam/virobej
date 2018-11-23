$(document).ready(function () {
    $.validator.addMethod("alpha", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z ]+$/);
    }, $characters);
    $.validator.addMethod("alphanumeric", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-z0-9A-Z]+$/);
    }, $characters_number);
    $.validator.addMethod("perfectmoneyid", function (value, element) {
        return this.optional(element) || value == value.match(/^[U]{1}[0-9]{7,10}$/);
    }, $digits);
    $("#new_bank").click(function(){
		$("#new_bank_panel").removeClass("hidden");
	});
    $('.bank_transferfrmad').validate({
        errorElement: 'div',
        errorClass: 'help-block', focusInvalid: false,
        rules: {
     currency_id: {
     required: true
     },
     nick_name: {
     required: true,
     },
     account_name: {
     required: true,
     
     },
     payout_account_type: {
     required: true
     },
     account_no: {
     required: true,
      alphanumeric: true,
     },
     bank_name: {
     required: true
     },
     bank_branch: {
     required: true
     },
     ifsccode: {
     required: true,
     alphanumeric: true,
     minlength: 11
     },
     status: {
     required: true,
     },
	 tpin:{
	 required: true
	 }
     },
    messages: 
    $bank_Payment,
	
    submitHandler: function (form, event) {
        event.preventDefault();
        if ($(form).valid()) {
            $.ajax({
                url: "account/withdrawal/checkTpin",
                type: "POST",
                data: {tpin: $('#' + $(form).attr('id') + '_tpin').val()},
                dataType: "json",
                beforeSend: function () {
                  $(".alert-success").css("display", "none");
                  $(".alert-danger").css("display", "none");
				  loadPreloader();
                },
                success: function (data) {						
                   if (data.status == 200) { 
                    $.ajax({ 
						url: $(form).attr('action'),
                        type: "POST",
                        data: $(form).serialize(),
                        dataType: "json",
                        success: function (res) { 
							removePreloader();
                            $('button[type=submit]', $(form)).text($added);								
                            $(form).prepend(res.msg);		
							$('#' + $(form).attr('id') + '_tpin').val('');
                                setTimeout(function () {
                                    $("#new_bank_panel").addClass("hidden");
									location.reload();
                                }, 10000);
                return false;
						},
                           error: function (res) {
                            alert($wrong_msg);
							removePreloader();
							$(form).prepend(res.msg);
                            return false;
                           }
                    });
                   } 
						else {	
							
							alert($ecsp);
							removePreloader();
							$(function() {
							$('#' + $(form).attr('id') + '_tpin').focus();
							});                           
                        }
                }
            });
        }
    }
});
    $('#divoty_form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            currency_id: "required",
            account_id: {
                required: true,
                number: true,
                maxlength: 10
            },
            account_name: {
                required: true,
                alpha: true
            },
            withdrawal_status: "required",
            tpin: "required"
        },
        messages: $divoty_form,
           /* currency_id: "Please select a Currency",
            account_id: {
                required: "Please enter the Account ID",
                number: "Please the Number",
                maxlength: $.format("Account ID must have {0} characters")
            },
            account_name: {
                required: "Please enter Account Holder Name",
                alpha: "Enter only alphabets"
            },
            withdrawal_status: "Please Select the status",
            tpin: "Please enter Security pin"*/
        
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                $.ajax({
                    url: "checkTpin",
                    type: "POST",
                    data: {tpin: $('#' + $(form).attr('id') + '_tpin').val()},
                    dataType: "json",
                    beforeSend: function () {
                        $(".alert-success").css("display", "none");
                        $(".alert-danger").css("display", "none");
                        $('button[type=submit]', $(form)).text($update);
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            $.ajax({
                                url: $(form).attr('action'),
                                type: "POST",
                                data: $(form).serialize(),
                                dataType: "json",
                                success: function (res) {
                                    $('button[type=submit]', $(form)).text($update_now);
                                    $(form).prepend(res.msg);
                                    $('#' + $(form).attr('id') + '_tpin').val('');
                                    if (res.status == 'OK') {
                                        setTimeout(function () {
                                            document.location.reload(true);
                                        }, 3000);
                                    }
                                    return false;
                                },
                                error: function () {
                                    alert($wrong_msg);
                                    return false;
                                }
                            });
                        } else {
                          alert($ecsp);
                            $('button[type=submit]', $(form)).text($update_now);
                        }
                    }
                });
            }
        }
    });
	
	$('#solid_trust_pay_form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            currency_id: "required",
            account_id: {
                required: true, 
				alphanumeric :true
            },
            account_name: {
                required: true,
                alpha: true
            },
            withdrawal_status: "required",
            tpin: "required"
        },
        messages: 
            $solid_trust_pay ,
        
        submitHandler: function (form, event) {
			
            event.preventDefault();
			
            if ($(form).valid()) {
                $.ajax({
                    url: "checkTpin",
                    type: "POST",
                    data: {tpin: $('#' + $(form).attr('id') + '_tpin').val()},
                    dataType: "json",
                    beforeSend: function () {
                        $(".alert-success").css("display", "none");
                        $(".alert-danger").css("display", "none");
                        $('button[type=submit]', $(form)).text($update);
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            $.ajax({
                                url: $(form).attr('action'),
                                type: "POST",
                                data: $(form).serialize(),
                                dataType: "json",
                                success: function (res) {
                                    $('button[type=submit]', $(form)).html($update_now);
                                    $(form).prepend(res.msg);
                                    $('#' + $(form).attr('id') + '_tpin').val('');
                                    if (res.status == 'OK') {
                                        setTimeout(function () {
                                            document.location.reload(true);
                                        }, 3000);
                                    }
                                    return false;
                                },
                                error: function () {
                                    alert($wrong_msg);
                                    return false;
                                }
                            });
                        } else {
                            alert($ecsp);
                            $('button[type=submit]', $(form)).text($update_now);
                        }
                    }
                });
            }
        
		}
    });
	
	$('#bkash_form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            currency_id: "required",
            account_id: {
                required: true,
                number: true,
              //  maxlength: 10
            },
            account_name: {
                required: true,
                alpha: true
            },
            withdrawal_status: "required",
            tpin: "required"
        },
        messages: 
			 $bkash,
            /*currency_id: "Please select a Currency",
            account_id: {
                required: "Please enter the Account Number",
                number: "Please enter the Account Number",
              //  maxlength: $.format("Account Number must have {0} characters")
            },
            account_name: {
                required: "Please enter Account Holder Name",
                alpha: "Enter only alphabets"
            },
            withdrawal_status: "Please Select the status",
            tpin: "Please enter Security pin"*/
        
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                $.ajax({
                    url: "checkTpin",
                    type: "POST",
                    data: {tpin: $('#' + $(form).attr('id') + '_tpin').val()},
                    dataType: "json",
                    beforeSend: function () {
                        $(".alert-success").css("display", "none");
                        $(".alert-danger").css("display", "none");
                        $('button[type=submit]', $(form)).text($update);
                    },
                    success: function (data) {
                        if (data.status == 200) {
                            $.ajax({
                                url: $(form).attr('action'),
                                type: "POST",
                                data: $(form).serialize(),
                                dataType: "json",
                                success: function (res) {
                                    $('button[type=submit]', $(form)).text($update_now);
                                    $(form).prepend(res.msg); alert(res.msg);
                                    $('#' + $(form).attr('id') + '_tpin').val('');
                                    if (res.status == 200) {
                                        setTimeout(function () {
                                            document.location.reload(true);
                                        }, 3000);
                                    }
                                    return false;
                                },
                                error: function () {
                                    alert($wrong_msg);
                                    return false;
                                }
                            });
                        } else {
                            alert($ecsp);
                            $('button[type=submit]', $(form)).text($update_now);
                        }
                    }
                });
            }
        }
    });
	
    var validate_me = {
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            currency_id: "required",
            account_id: {
                required: true,
                email: true,
                maxlength: 50
            },
            account_name: {
                required: true,
                alpha: true
            },
            withdrawal_status: "required",
            tpin: "required"
        },
        messages: $Payza_Payment,
           /* currency_id: "Please select a Currency",
            account_id: {
                required: "Please enter the Email-ID",
                email: "Please Enter the valid Email-ID",
                maxlength: $.format("Email ID must less then {0} characters")
            },
            account_name: {
                required: "Please enter Account Holder Name",
                alpha: "Only Characters Allowed."
            },
            withdrawal_status: "Please Select the status",
            tpin: "Please enter Security pin"*/
        
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                $.ajax({
                    url: "checkTpin",
                    type: "POST",
                    data: {tpin: $('#' + $(form).attr('id') + '_tpin').val()},
                    dataType: "json",
                    beforeSend: function () {
                        $(".alert-success").css("display", "none");
                        $(".alert-danger").css("display", "none");
                        $('button[type=submit]', $(form)).text($update);
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            $.ajax({
                                url: $(form).attr('action'),
                                type: "POST",
                                data: $(form).serialize(),
                                dataType: "json",
                                success: function (res) {
                                    $('button[type=submit]', $(form)).text($update_now);
                                    $(form).prepend(res.msg);
                                    $('#' + $(form).attr('id') + '_tpin').val('');
                                    if (res.status == 'OK') {
                                        setTimeout(function () {
                                            document.location.reload(true);
                                        }, 3000);
                                    }
                                    return false;
                                },
                                error: function () {
                                    alert($wrong_msg);
                                    return false;
                                }
                            });
                        } else {
                            alert($ecsp);
                            $('button[type=submit]', $(form)).text($update_now);
                        }
                    }
                });
            }
        }
    }
    $('.bank_transferfrmad').validate(validate_me);
    $('#payza_form').validate(validate_me);
    $('#skrill_form').validate(validate_me);    
    $('#bkash_form').validate(validate_me);
    $('.status_btn').on("click", function (event) {
        event.preventDefault();
        $(".alert").remove();
        $curLink = $(this);
        $curStr = $curLink.text();
        $curLink.text('Processing...');
        $.ajax({
            url: $curLink.attr('href'),
            type: "POST", data: $curLink.serialize(),
            dataType: "json",
            success: function (res) {
                if (res.status == 'OK') {
                    if ($curStr == 'Enabled')
                        $curLink.text('Disabled');
                    else {
                        if (res.btn_change == 'true') {
                            $curLink.text('Enabled');
                        }
                        else {
                            $curLink.closest('td').html(res.btn_change);
                        }
                    }
                }
                $('#payout_list').prepend(res.msg);
            },
            error: function () {
                alert('Something went wrong');
                return false;
            }
        });
    });
});
