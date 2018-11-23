// When the browser is ready...
$(function () {
    // Setup form validation on the #create_package element
    $("#deduct_funds").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        // Specify the validation rules
        rules: {
            username: {required: true},
            ewallet_id: {required: true},
            amount: {required: true, number: true},
			comment : { required:true,
						minlength: 8,
						maxlength: 100 }
        },
        // Specify the validation error messages
        messages: {
            username: "Please enter User name",
            amount: {required: "Please Enter the Amount", number: "Please Enter only Numeric values"},
            ewallet_id: {required: "Please Select the Wallet"},
			comment: {required: "Please Give Admin Comments",
			          minlength : "Comment length must be minimum 8 character and maximum 100",
			          maxlength : "Comment length must be minimum 8 character and maximum 100"}
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $('.alert').remove();
            if ($(form).valid()) {
                var available_balance = $("#avi_bal").val();
                var entered_value = $("#amount").val();
				if (parseInt(entered_value) == 0){
					 $("#amount_err").text('Please Enter the Valid Amount');
					 return false;
				}
                else if (parseInt(entered_value) > parseInt(available_balance)) {
                    $("#amount_err").text('Please Enter the Valid Amount');
					return false;
                } else {
					if (confirm('Are you sure, You wants to debit fund?')) {
                    var datastring = $(form).serialize();
                    $.ajax({
                        url: "deduct_funds_franchisee_update", // Url to which the request is send
                        type: "POST", // Type of request to be send, called as method
                        data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
                        dataType: "json",
                        success: function (data) 	// A function to be called if request succeeds
                        {
                            var str = '';
                            if (data.status == 'ok') {
                                str = "<div class='alert alert-success'>" + data.msg + "<br></div>";
                            } else {
                                str = "<div class='alert alert-danger'>" + data.msg + "<br></div>";
                            }
                            $("#fr_uname_check").show();
							$("#user_details").hide();
                            $("#deduct_details").hide();
                            $('#deduct_funds').before(str);
                            $("#deduct_funds").trigger('reset');
                            $('#currency').empty();
							$('#fr_uname_check').parents('.form-group').show();
                            $('#user_avail_status').html('');

                        },
                        error: function () {
                            alert('Something went wrong');
                            return false;
                        }
                    });
					}
                }
            }
        }
    });

});
/*------Username Checking Function--------*/
var ajaxUrl = "";
var avi_bal = 0;
var ubalance = new Array();
var rec_data = '';
$("#username").keyup(function () {
    $("#fr_uname_check").show();
    $("#user_avail_status").hide();
    $("#deduct_details").hide();
	$("#user_details").hide();
    $('.alert').remove();
    $('#balTxt').html('');
});
$("#currency_id,#ewallet_id").change(function () {
    $('#balTxt').text("Available Balance: ");
    $('#avi_bal').val();
    avi_bal = 0;
    $wallet_id = $("#ewallet_id option:selected").val();
    $currency_id = $("#currency_id option:selected").val();
    $.each(rec_data, function (key, ele) {
        if (ele.wallet_id == $wallet_id && ele.currency_id == $currency_id)
        {
            avi_bal = ele.current_balance;
        }
    });
    if (avi_bal <= 0) {
        $('#amount_div').hide();
    } else {
        $('#amount_div').show();
    }
	var decimal_places = get_decimal_value(avi_bal);
    $('#balTxt').text("Available Balance: " + avi_bal.toFixed(decimal_places));
    $('#avi_bal').val(avi_bal.toFixed(decimal_places));
    $("#amount").val('');
    $("#amount_err").text('');
});
$("#fr_uname_check").click(function () {		
    if ($("#username").val() == '') {
        $("#user_avail_status").html('Please Enter the username');
        $("#user_avail_status").addClass("help-block");
        $('#balTxt').html('');
    } else {
        $('#balTxt').html('');
		$("#fr_uname_check").val('Processing..').attr("disabled", "disabled");
        var username = $("#username").val();
        if (username) {
            balArray[1] = 0;
            balArray[2] = 0;
            $.ajax({
                url: $('#fr_uname_check').data('url'),
                type: "POST",
                data: {username: $("#username").val(), deduct_fund : 1},
                dataType: "json",
                success: function (data) {
                    //data = JSON.parse(data);
                    rec_data = data.uballist;
					if (data.status == 'ok') {
                       $("#fr_uname_check").parents('.form-group').hide();
						$("#user_details").show();
                        $("#deduct_details").show();
						$("#rec_email").val(data.email);
						$("#rec_name").val(data.fullname);
						$("#franchisee_type").val(data.franchisee_type);
						$("#franchisee_type_name").val(data.franchisee_type_name);
                        $("#user_avail_status").text(data.msg).show();
                        $("#user_avail_status").removeClass("help-block");
                        $("#Submit").removeAttr("disabled");
                        $("#user_id").val(data.user_id);
                        $("#ewallet_id").val('');
                        var balarr = data.uballist;
                        var bal = data.uballist[0];
						$("#fr_uname_check").val('Check').removeAttr("disabled");
                        /*var str = "";
                         $.each(bal,function(index,ele){
                         str = str+"<option value='"+ele.currency_id+"'>"+ele.code+"</option>";
                         ubalance[ele.currency_id] = ele.current_balance;
                         });
                         $("#currency").html("<option>--Select Currency--</option>");
                         $('#currency').append(str);*/
                        return true;
                    } else {
                        $("#user_avail_status").text(data.msg).show();
                        $("#user_avail_status").addClass("help-block");
                        $("#Submit").attr("disabled", "disabled");
						$("#fr_uname_check").val('Check').removeAttr("disabled");
                        return false;
                    }
                },
                error: function () {
                    alert('Something went wrong');
                    return false;
                }
            });

            /*$('#currency').change(function(){
             $('#balTxt').text("Available Balance: "+ubalance[$(this).val()]);
             $('#avi_bal').val(ubalance[$(this).val()]);
             var avi_bal	=  ubalance[$(this).val()];
             if(avi_bal <= 0)	{
             $('#amount_div').hide();
             }	else {
             $('#amount_div').show();
             }
             $("#amount").val('');
             $("#amount_err").text('');
             })	*/
            $('#amount').blur(function () {
                $("#amount_err").text('');
                var available_balance = $("#avi_bal").val();
                var entered_value = $("#amount").val();
                if (parseInt(entered_value) > parseInt(available_balance)) {
                    $("#amount_err").text('Please Enter the Valid Amount');
                }
            })
        }
    }
});

$("#amount").keypress(function (e) {
    //$("#amount_err").text('');
    var available_balance = $("#avi_bal").val();
    var entered_value = $("#amount").val();
    if (parseInt(entered_value) > parseInt(available_balance)) {
        $("#amount_err").text('Please Enter the Valid Amount');
        return false;
    }
	var charCode = (e.which) ? e.which : e.keyCode;
			if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
				  $("#amount_err").html("Enter Numeric Values Only");
				  return false;
			}
    //if the letter is not digit then display error and don't type anything
   /* if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#amount_err").html("Enter Digits Only");
        return false;
    }*/

});