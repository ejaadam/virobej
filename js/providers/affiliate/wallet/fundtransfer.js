function user_check() {
	 if ($("#to_user").val() != '') {
        $.ajax({
			 url: "account/wallet/searchacc",
            type: "post",
            data: {username: $("#to_user").val()},
            dataType: "json",
            success: function (data) {
                userchk = 0;
                if (data.status == 'ok') {
                    userchk = 1;
                    $("#to_user_status").removeClass('help-block');
                    $("#to_user_status").html(data.msg);
                    $("#to_account_id").val(data.account_id);
                    $("#rec_email").val(data.email);
                    $("#rec_name").val(data.full_name);
					$(".hidefld2").show();
                    $("#confirm_fund_transfer").removeAttr("disabled");
                    return true;
                } else if (data.status == 'error') {
                    userchk = 0;
                    $("#to_user_status").addClass('help-block');
                    $("#to_user_status").html(data.msg);
                    $(".hidefld2").hide();
                    $(".hidefld3").hide();
                    return false;
                }
            }
        });
    }
}
function checkamount() {
    var amount = $("#totamount").val();
    var min_amount = parseFloat($('#min_trans_amount').val());
    var max_amount = parseFloat($('#max_trans_amount').val());
    $("#amount").val($("#totamount").val());
    amount = parseFloat(amount);
    if (amount > max_amount) {
        $("#amount_status").html($cant_transfer_amt + max_amount + " "+$("#currency_id option:selected").text()+".");
        $(".hidefld3").hide();
        return false;
    } else if (amount < min_amount) {
        $("#amount_status").html($min_transfer_amt + min_amount + " "+$("#currency_id option:selected").text()+ ".");
        $(".hidefld3").hide();
        return false;
    }else{
        $("#amount_status").html('');
        $(".hidefld3").show();
        return true;
    }
}

	$(function () {
    $('body').on('click','#get_tac_code',function (e) {
        e.preventDefault();
        $.ajax({
            url: "account/wallet/send_tac_code", // Url to which the request is send
            type: "POST", // Type of request to be send, called as method
            data: {user_id: $('#from_user_id').val()}, // Data sent to server, a set of key/value pairs representing form fields and values
            dataType: "json",
            beforeSend: function () {
                $('#get_tac_code').text($processing);
                $('#get_tac_code').attr('disabled', 'disabled');
            },
            success: function (data) {  // A function to be called if request succeeds
                $('#get_tac_code').removeAttr('disabled');
                $('#get_tac_code').text($get_tac_code);
                $('#msg').html(data.msg);
                $('#msg').removeClass('hidden');

            },
            error: function () {
                alert('Something went wrong');
                $('#get_tac_code').text($get_tac_code);
            }
        });
});
});

	$('body').on('submit',"#fundtransferform",function(event){
   		 $("#fundtransferform").validate(validate_me2);
		 event.preventDefault();
            if ( $("#fundtransferform").valid()) {
				if($("#totamount").val() === undefined || $("#totamount").val() == ''){
					alert($enter_amt);
					return false;
				}
				var disabled = $("#fundtransferform").find(':input:disabled').removeAttr('disabled');
				var datastring = $("#fundtransferform").serialize()+'&currency_code='+$("#currency_id option:selected").text()+"&ewallet_name="+$("#wallet_id option:selected").text();
				disabled.attr('disabled','disabled');
                $("#confirm_fund_transfer").attr('disabled', 'disabled');
                $("#confirm_fund_transfer").text($processing);
                $.ajax({					
				    url: "account/wallet/fund_transfer_confirm",	 // Url to which the request is send
                    type: "POST", // Type of request to be send, called as method
                    data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values    
					beforeSend: function(){
		                $("#fund_transfer").attr('disabled','disabled');						
						$("#fundtransfer_confirm_form").remove();
						$('body').toggleClass('loaded');
					},
                    success: function (data) { 
					$('body').toggleClass('loaded');
						$("#fund_transfer").removeAttr('disabled');
						$("#fundtransferform").hide();
						$("#fundtransferform").after(data);
                    },
                    error: function () {
						$('body').toggleClass('loaded');
                       $("#fund_transfer").removeAttr('disabled');					   
                       return false;
                    }
                });
            } else {
                 $("#fund_transfer").removeAttr('disabled');
            }	
   });
   
   var validate_me2 = {
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            wallet_id: "required",
            to_user: "required",
            totamount: "required",            
        },
     //   messages: $val_message,       
    };
	$('body').on('click', '#back', function (e) {
		$("#fundtransfer_confirm_form").remove();
		$("#fundtransferform").show();
	});
	$('body').on('click', '#confirm_fund_transfer', function (e) {
	
		var validation_check = 	0 ;
		if($(this).val() != "Back"){
			validation_check = 1;
			$("#fundtransfer_confirm_form").validate(validate_me);				
		}		
		 if(validation_check == 0 || $("#fundtransfer_confirm_form").valid()){
			 	var datastring = $("#fundtransfer_confirm_form").serialize()+"&submit="+$(this).val();
			 	 $.ajax({
                    url: "account/wallet/fund_transfer_save", // Url to which the request is send
                    type: "POST", // Type of request to be send, called as method
                    data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values    
					datatype:"json",
					beforeSend: function(){
							$("#confirm_fund_transfer").attr('disabled','disabled');
							$("#back").attr('disabled','disabled');
							$('body').toggleClass('loaded');
					},
                    success: function (data) {
						$('body').toggleClass('loaded');
						$(this).initTransferFrm();
						
						if(data.status == 'ok'){
							$('#status_msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-label="close">&times;</a>'+data.msg+'</div>');
						}
						if(!(data.viewdata === undefined)){
							$("#form_fields").html(data.viewdata);
							$('#msg').html(data.msg);
						}else{
							$("#fundtransfer_confirm_form").remove();
							$("#fundtransferform").show();
							$(".alert ").show();
							$('#msg').html(data.msg);
							
						}
                    },
                    error: function () {
						$('body').toggleClass('loaded');
                        $("#confirm_fund_transfer").removeAttr('disabled');
                        alert($wrong_msg);
                        return false;
						
                    }
                });
		 }
		
	});
	var validate_me = {
		errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        // Specify the validation rules
        rules: {           
            tac_code: "required"
        },
      //  val_message:$tac_code,
        submitHandler: function (form, event) {
            event.preventDefault();			
            if ($(form).valid()) {
				 $.ajax({
                    url: $("#fundtransfer_confirm_form").attr('action'), // Url to which the request is send
                    type: "POST", // Type of request to be send, called as method
                    data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values                   
                    success: function (data) {  // A function to be called if request succeeds
						$("#form_fields").html(data);
                        //location.reload();
                    },
                    error: function () {
                        $("#confirm_fund_transfer").removeAttr('disabled');
                        alert($wrong_msg);
                        return false;
                    }
                });
            } else {
                $("#confirm_fund_transfer").attr("disabled", "disabled");
            }	
			
		}
	};
	
	$.fn.initTransferFrm = function(){												
		$('.hidefld,.hidefld1,.hidefld2,.hidefld3').hide();
		$('#fundtransferform select,#fundtransferform input[type=text]').val('');
	    $('#fundtransferform #user_balance,#fundtransferform #to_user_status').text('');
	}