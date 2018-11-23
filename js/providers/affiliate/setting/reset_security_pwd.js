$(document).ready(function () {
	$("#kycgrid").hide();
    var chgpwd = $("#otp_affiliate").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
           otp: "required",
          
        },
        messages: $val_message,
        submitHandler: function (form, event) {
         event.preventDefault();
            if ($(form).valid()) {
                var datastring = $("#otp_affiliate").serialize();
                $.ajax({
                    url: 'affiliate/settings/otp_check',
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {
                        $('.alert').remove();
                      //  $("#update_securitypwd").find('span').text($processing);
                       // $('button[type="submit"]').attr('disabled', 'disabled');
						$('body').toggleClass('loaded');
                    },
                    success: function (data) {
						$('body').toggleClass('loaded');
						//console.log(data);return false;
						if(data.msg=='ok'){
							$("#kycgrid").show();
							$("#otp_affiliate").hide();}
				   else{
					   $('#otp_affiliate').before('<div class="alert ' + data.status +'"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
					    $('button[type="submit"]').attr('disabled', false);
						//$("#update_securitypwd").find('span').text($update);
                        $('input,select','#otp_affiliate').val('');
						}
                    },
                     error: function (jqXHR, textStatus, errorThrown) {
                            $('body').toggleClass('loaded');	
							$('button[type="submit"]').attr('disabled',false);
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,messages){
								if($('#otp_affiliate input[name='+fld+']').siblings().hasClass('input-group')){
									$('#otp_affiliate input[name='+fld+']').siblings().after("<div class='help-block'>"+messages+"</div>");
								}else {
									$('#otp_affiliate input[name='+fld+']').after("<div class='help-block'>"+messages+"</div>");
								}
							});
							return false;
						},
                });
            } 
        }
    });
     var chgpwd = $("#updatetranscationpassword").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
           otp: "required",
           tran_newpassword: {
                required: true,
                minlength: 5,
                maxlength: 20
            },
            tran_confirmpassword: {
                required: true,
                equalTo: "#tran_newpassword"
            }
        },
        messages: $val_message,
        submitHandler: function (form, event) {
         event.preventDefault();
            if ($(form).valid()) {
                var datastring = $("#updatetranscationpassword").serialize();
                $.ajax({
                    url: 'affiliate/settings/reset_update_pwd',
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {
                        $('.alert').remove();
                      //  $("#update_securitypwd").find('span').text($processing);
                        $('button[type="submit"]').attr('disabled', 'disabled');
						$('body').toggleClass('loaded');
                    },
                    success: function (data) {
						//console.log(data);return false;
						
						$('body').toggleClass('loaded');
                        $('#oldpassword_status').hide();
                        $('#updatetranscationpassword').before('<div class="alert ' + data.status +'"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
					    $('button[type="submit"]').attr('disabled', false);
						//$("#update_securitypwd").find('span').text($update);
                        $('input,select','#updatetranscationpassword').val('');
						//$('#forgot_mess').html(data.msg);
                    },
                     error: function (jqXHR, textStatus, errorThrown) {
                            $('body').toggleClass('loaded');	
							$('button[type="submit"]').attr('disabled',false);
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,messages){
								if($('#updatetranscationpassword input[name='+fld+']').siblings().hasClass('input-group')){
									$('#updatetranscationpassword input[name='+fld+']').siblings().after("<div class='help-block'>"+messages+"</div>");
								}else {
									$('#updatetranscationpassword input[name='+fld+']').after("<div class='help-block'>"+messages+"</div>");
								}
							});
							return false;
						},
                });
            } 
        }
    });

 }); 
		

		