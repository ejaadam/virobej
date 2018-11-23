$(document).ready(function () {

	var HASH_CODE = null;
    var VPF = $('#verify-propin-form');
    var RPF = $('#reset-pinfrm');
    var CPF = $('#changepassword');
	
 	$('#profile-panel').on('click','#general_btn',function (event) {		
        event.preventDefault();
        CURFORM = $('#general_details');
		var formData = new FormData(this);
		var formData = new FormData(this);
		if(CROPPED){
		   CROPPED = false;
		   formData.append('profile_image', uploadImageFormat($('#logo-preview').attr('src')));
		}
		$.each(CURFORM.serializeObject(), function (k, v) {
			formData.append(k, v);
		});
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (data, textStatus, xhr) {
				if(xhr.status == 200){
			    	$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+data.msg+'</div>');
				}else{
					$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+data.msg+'</div>');
				}
			},
			error: function (jqXHR, exception, op) {
				if (jqXHR.status == 406) {
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			},
        });
    });
	
	$('#profile-panel').on('click', '#general_details #editslugBtn', function (e) {
        e.preventDefault();	
		var email_verified = $('#general_details #is_email_verified').val();			
		if(email_verified == 0){	
		    window.location.href = $('#general_details #is_email_verified').attr('data-url');
			//window.open = ($('#general_details #is_email_verified').attr('data-url'), '_blank');
			//window.open = ('http://localhost/dsvb_portal/seller/verify-email', '_blank');
		}
		else {	
			$('#verify_link_success').empty();
			$('#verify_link_success').fadeOut();
			$("#general_details_modal").find("input[type=password]").val('');
			$("#reset-pinfrm,#change-email-new-email,#change-email-confirm").hide();
			//$("#verify-propin-form").show();
			$("#change-email-new-email").show();
			$("#change-email-new-email #email").val(''); 
			$("#change-email-new-email #new_email-err").removeClass('errmsg').empty(); 
			$('#general_details_modal').modal();
			$('#profile_pin', VPF).attr('type', 'password');
			$('.curnt_pin', VPF).find('i').removeClass().addClass('fa fa-eye-slash');	
			//$('#verify-propin-form :submit', $('#change-email')).attr('disabled', 'disabled')
		}
    });
     
	$('#profile-panel #business_details').on('submit',function (event) {		
        event.preventDefault();
	    CURFORM = $('#business_details');
		var formData = new FormData(this);
		if (CROPPED) {
		   CROPPED = false;
		   formData.append('shop_image', uploadImageFormat($('#logo-preview').attr('src')));
		}
		$.each(CURFORM.serializeObject(), function (k, v) {
			formData.append(k, v);
		});
		$.ajax({
			url: CURFORM.attr('action'),            
			data: formData,
			dataType: "json",
			enctype: 'multipart/form-data',
			processData: false,
			contentType: false,
			type: 'POST',
			success: function (data, textStatus, xhr) {
				if(xhr.status == 200){
			    	$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+data.msg+'</div>');
				}else{
					$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+data.msg+'</div>');
				}
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);		
				if (jqXHR.status == 406) {
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			},
		});
    });
	
	if ($('#bank_details #ifsc_code').val()) {
		if ($('#bank_details #ifsc_code').val().length > 10) {
			$.ajax({
				url: $('#ifsc_code').attr('data-url'),            
				data: {ifsc: $('#ifsc_code').val()},
				cache: false,
				contentType: false,
				processData: false,
				success: function (op) {
					console.log(op.address);
					$('#address_title').fadeIn();
					$('#ifsc_bank_details').text('');
					$('#ifsc_bank_details').append($('<p>').text(op.bank));
					$('#ifsc_bank_details').append($('<p>').text(op.branch));
					$('#ifsc_bank_details').append($('<p>').text(op.city));
					$('#ifsc_bank_details').append($('<p>').text(op.state));
					$('#ifsc_bank_details').append($('<p>').text(op.district));
					$('#ifsc_bank_details').append($('<p>').text(op.ifsc));
					$('#ifsc_bank_details').append($('<p>').text(op.address));					
				},
				error: function (jqXHR, exception, op) {
					console.log(jqXHR);							 	
				},
			});
		}
	}
	

	$('#bank_details').on('click', '#no_ifsc', function (e) {
		check = $('#no_ifsc:checked').val();
		if(check==1){
		    $('#change_Member_pin').show(); 
		    $('#retailer-qlogin-model1').modal();
		}
    });
	
	$('#ifsc_code').on('blur', function () {	
		$("#branch_value").val('');
		$("#bank_value").val('');	
		$(".bank_data").hide();
		if ($('#ifsc_code').val().length > 10) {
			$.ajax({
				url: $('#ifsc_code').attr('data-url'),            
				data: {ifsc: $('#ifsc_code').val()},				
				dataType:'JSON',
				success: function (op) {	
				 if(op.status==200){
					 $(".bank_data").hide();
			        $("#bank_det").show();
				    $('#bank_value').val(op.data.bank);
				    $('#branch_value').val(op.data.branch);
				 }
					/*$('#address_title').fadeIn();
					$('#ifsc_bank_details').text('');
					$('#ifsc_bank_details').append($('<p>').text(op.data.bank));
					$('#ifsc_bank_details').append($('<p>').text(op.data.branch));
					$('#ifsc_bank_details').append($('<p>').text(op.data.city));
					$('#ifsc_bank_details').append($('<p>').text(op.data.state));
					$('#ifsc_bank_details').append($('<p>').text(op.data.district));
					$('#ifsc_bank_details').append($('<p>').text(op.data.ifsc));
					$('#ifsc_bank_details').append($('<p>').text(op.data.address));	*/			
				},
				error: function (jqXHR, exception, op) {
					console.log(jqXHR);							 	
				},
			});
		}
	});
	
	$('#bank_details').submit(function (event) {		

        event.preventDefault();
        CURFORM = $('#bank_details');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op, textStatus, xhr) {
				if(xhr.status == 200){
						$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
					}else{
						$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
					}
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);		
				if (jqXHR.status == 406) {
					console.log(jqXHR.responseJSON.msgs);
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			},
        });
    });

	//$('#bank_det').hide();
	$('#ifsc_bank_details_form').submit(function (event) {		
        event.preventDefault();
		CURFORM = $('#bank_details');
		$('#retailer-qlogin-model1').modal('hide');
		var formData = new FormData(this);
        var ifsc_code=$("#ifsc_code_details").val();
        var bank_name=$("#bank_name").val();
        var branch_name=$("#branch_name").val();
		$('#bank_det').show();
		$("#ifsc_code").val(ifsc_code);
		$("#bank_value").val(bank_name);
		$("#branch_value").val(branch_name);
    });
	
	CPF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            success: function (op) {		
			    $('.pwdHS', CPF).find('i').attr('class','').attr('class','icon-eye-close');	
				CURFORM.resetForm(); 
				$('#ac_settings #alt-msg').html('<div class="alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
            },
			error: function (jqXHR, textStatus, errorThrown) {
                responseText = $.parseJSON(jqXHR.responseText);
				console.log(responseText);
				if(responseText.msg != '' && responseText.msg != undefined){
				    $('#ac_settings #alt-msg').html('<div class="alert alert-danger">' + responseText.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				    $('div.alert').fadeOut(7000);
				}								
            }
        });
    });
				
	/* $('#changepassword').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        // Specify the validation rules
        rules: {
            newpassword: {
                required: true,
                minlength: 6
            },
            confirmpassword: {
                required: true,
                equalTo: '#newpassword'
            },
            oldpassword: {
                required: true,
            }
        },
        // Specify the validation error messages
        messages: {
            newpassword: {
                required: 'Please provide a new password',
                minlength: 'Your password must be at least 6 characters long'
            },
            confirmpassword: {
                required: 'Please confirm a new password'
            },
            oldpassword: {
                required: 'Please enter your old password',
            }
        },
        submitHandler: function (form, event) {
            if ($(form).valid()) {
                var datastring = $(form).serialize();
                $.ajax({
                    url: $(form).attr('action'), // Url to which the request is send
                    // Type of request to be send, called as method
                    data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
                    beforeSend: function () {
                        $('.alert').remove();
                    },
                    success: function (data) 		// A function to be called if request succeeds
                    {						
						window.location.assign(window.location.BASE + 'seller/logout');                        
                    },
                    error: function () {
                        alert('Something went wrong');
                    }
                })
            }
        }
    });  */
	
    $('#account_settings').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#account_settings');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),
            //data: CURFORM.serializeObject(),
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op) {
				console.log(op);
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);		
				if (jqXHR.status == 406) {
					console.log(jqXHR.responseJSON.msgs);
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			},
        });
    });

	
	$('#gal_image').change(function(e){
		e.preventDefault();
		$('#gal_image').checkFileFormat('jpg|png|jpeg','Please select valid file format');
	});
	
	$('#profile-panel').on('click','#upload_images',function (event) {		
        event.preventDefault();
        CURFORM = $('#store-img');
		var formData = new FormData();
		var url = $(this).attr('rel');
		jQuery.each(jQuery('#gal_image')[0].files, function(i, file) {
			formData.append('images', file);
		});
        $.ajax({
            url: url,
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op) {
				$('#gal_image').val('');
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);		
				if (jqXHR.status == 406) {
					console.log(jqXHR.responseJSON.msgs);
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			},
        }); 
    });

	$('#profile-panel').on('change','#postal_code',function () {
        var pincode = $('#postal_code').val();
        var country_id = $('#country_id').val(); 
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode,country_id:country_id},
				success: function (OP) {
					$('#state_id, #city_id').prop('disabled', false).empty();					
					$('#state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
					$.each(OP.cities, function (k, e) {
						$('#city_id').append($('<option>', {value: e.id}).text(e.text));
					});
					$('#country_id, #state_id, #city_id').trigger('change');
				},
				error: function () {
					$('#state_id, #city_id').empty();					
					$('#state_id').val('').prop('disabled', true);
					$('#city_id').val('').prop('disabled', true);
				}
			});
		}	
    });
	$('#profile-panel').on('change','#operating_address_postal_code',function () {
        var pincode = $('#operating_address_postal_code').val();
        var country_id = $('#country_id').val(); 
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode,country_id:country_id},
				success: function (OP) {
					$('#operating_address_state_id, #operating_address_city_id').prop('disabled', false).empty();					
					$('#operating_address_state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
					$.each(OP.cities, function (k, e) {
						$('#operating_address_city_id').append($('<option>', {value: e.id}).text(e.text));
					});
					$('#country_id, #operating_address_state_id, #operating_address_city_id').trigger('change');
				},
				error: function () {
					$('#operating_address_state_id, #operating_address_city_id').empty();					
					$('#operating_address_state_id').val('').prop('disabled', true);
					$('#operating_address_city_id').val('').prop('disabled', true);
				}
			});
		}	
    });
	var postcode = $('#postal_code').val();
	var country_id = $('#country_id').val();
    if (postcode) {
        $.ajax({
            url: window.location.BASE + 'check-pincode',
            data: {pincode: postcode,country_id:country_id},
            success: function (OP) {
                $('#country_id, #state_id, #city_id').prop('disabled', false).empty();
                //$('#country_id').append($('<option>', {value: OP.country_id}).text(OP.country));
                $('#state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
                $.each(OP.cities, function (k, e) {
                    $('#city_id').append($('<option>', {value: e.id}).text(e.text));
                });
                $('#city_id option[value=' + city_id + ']').attr('selected', true);
            },
            error: function () {
                //$('#country_id').val('').prop('disabled', true);
                $('#state_id').val('').prop('disabled', true);
                $('#city_id').val('').prop('disabled', true);
            }
        });
    }
	
	$('input.website').keyup(function () {
        if (! ((this.value.match('^http://')) || (this.value.match('^https://')))) {
            this.value = 'http://' + this.value;
        }
    });
	
	$('.free_delivery').on('change', function () { //Do your code
        check = $('.free_delivery:checked').val();		
        if (parseInt(check) == 1) {
            $( "#shipping_info #free_delivery_amt" ).prop( "disabled", false );
        } else {
			$( "#shipping_info #free_delivery_amt" ).val('');
            $( "#shipping_info #free_delivery_amt" ).prop( "disabled", true );
        }
    });
	
	$('.fd_postal_codes').on('change', function () { //Do your code
        check = $('.fd_postal_codes:checked').val();		
        if (parseInt(check) == 1) {
            $( "#shipping_info #postal_codes" ).prop( "disabled", false );
        } else {
			$( "#shipping_info #postal_codes" ).val('');
            $( "#shipping_info #postal_codes" ).prop( "disabled", true );
        }
    });
	
	$('.gstin').on('change', function () { //Do your code
        check = $('.gstin:checked').val();
        if (parseInt(check) == 1) {
            $('#tax_info #gstin_image_block').hide();
        } else {
            $('#tax_info #gstin_image_block').show();            
        }
    });
	
	$('#postal_codes').on('keyup', function () {
		var pcode = $('#postal_codes').val();
		if (parseInt(pcode.length) > 4) {
			$( "#shipping_info #submit" ).prop( "disabled", false );	
		}
	});
	
	$('#shipping_info').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#shipping_info');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),
            //data: CURFORM.serializeObject(),
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op, textStatus, xhr) {
				if(xhr.status == 200){
					$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}else{
					$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);						
			},
        });
    });

	/* Change EmAIL */

	$('#service_type').change(function(e){
		e.preventDefault();
		if($(this).val() == 2){
			$('#cate_fld').css('display','none');
		}else{
			$('#cate_fld').css('display','block');
		}
	});

   function isNumberKeyy(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
        return false;
    }
    return true;
}

    function isNumberKeyy(evt) {
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
			return false;
		}
		return true;
	}

    $('#change-email').on('submit', '#verify-propin-form', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            success: function (op) {
                $('div.alert').remove();
                $('#verify-propin-form', $('#change-email')).fadeOut('fast', function () {
                    $('#change-email-new-email').resetForm().fadeIn('slow');
                    $('#propin_session', $('#change-email-new-email')).val(op.propin_session);
                }); 
            },
            error: function (event, xhr, settings) {
                var op = event.responseJSON;
                $('div.alert').remove();
                VPF.before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
                $('div.alert').fadeOut(8000); 
            }
        });
    });


	 $('#change-email').on('click', '.cancel-change-email', function (e) {
        e.preventDefault();
	   $("#general_details_modal").modal('hide');
      
    });
 
 $('#change-email').on('submit', '#change-email-new-email', function (e) {
        e.preventDefault();
		
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            success: function (op) {
                $('div.alert').remove();
                $('#change-email-new-email').fadeOut('fast', function () {
                    $('#change-email-confirm').resetForm().fadeIn('slow');
                    //$('#change-email-confirm :submit').attr('disabled', 'disabled');
                });
				$('#verify_link_success').fadeIn();
                $('#verify_link_success').empty().html('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert"></a>' + op.msg + '</div>');                
                //$('#change-email-confirm').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');                
            }
        });
    });
   $('#change-email').on('submit', '#change-email-confirm', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            success: function (op) {
                $('div.alert').remove();
                $('#current-email').html('<i class="fa fa-envelope"></i>&nbsp;<b>' + op.email + '</b>');
			    $('#change-email-confirm').resetForm();
				$("#general_details_modal").modal('hide');
                $('#general_details').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
				$("#general_details #email").val(op.new_email);
				
                $('div.alert').fadeOut(8000);
            },
            error: function (event, xhr, settings) {
                var op = event.responseJSON;
                $('div.alert').remove();
                $('#change-email-confirm').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
                $('div.alert').fadeOut(8000);
            }
        });
    });
 
  $('#change-email').on('click', '#new-email-otp-resend', function (e) {
        e.preventDefault();
        var CURFORM = $('#new-email-otp-resend');
        $.ajax({
            url: CURFORM.attr('href'),
            success: function () {
                $('#change-email-confirm').resetForm();
            }
        });
    });
	
	 $('#change-email').on('click', '#resend-forgot-otp,#forgot-pin', function (e) {
        e.preventDefault();
        var Curele = $(this);
        $.ajax({
            url: Curele.attr('href'),
            success: function (op) {
                HASH_CODE = op.hash_code;
                $('div.alert').remove();
                $('#verify-propin-form', '#change-email').fadeOut('fast', function () {
                    $('#reset-pinfrm h5', $('#change-email')).html('<b>Forgot Security PIN</b>');
                    $('#reset-pinfrm', '#change-email').resetForm().fadeIn('slow');
                    $('#reset-pinfrm .verify-profile-pin').fadeIn();
                    $('#reset-pinfrm .new-profile-pin').fadeOut();
                   // $('#reset-pinfrm #verify-verification-code', '#change-email').attr('disabled', 'disabled');
                });
                $('#reset-pinfrm').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
                $('div.alert').fadeOut(8000);
            },
            error: function (event, xhr, settings) {
                var op = event.responseJSON;
                $('div.alert').remove();
                $('#verify-propin-form').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
                $('div.alert').fadeOut(8000);
            }
        });
    });
	    $('.curnt_pin', VPF).on('click', function (e) {
        var x = $('#profile_pin', VPF).attr('type');
        if (x === 'password') {
            $('#profile_pin', VPF).attr('type', 'number');
            $(this).find('i').removeClass().addClass('fa fa-eye');
        } else {
            $('#profile_pin', VPF).attr('type', 'password');
            $(this).find('i').removeClass().addClass('fa fa-eye-slash');
        }
    });
 
 
    $('#change-email #reset-pinfrm').on('keyup', '#code', function (e) {
        e.preventDefault();
        var Curele = $(this);
        if (Curele[0].validity.valid == true) {
            $('#reset-pinfrm #verify-verification-code', '#change-email').attr('disabled', false);
        } else {
            $('#reset-pinfrm #verify-verification-code', '#change-email').attr('disabled', 'disabled');
        }
    });
	
	 $('#change-email').on('click', '#verify-verification-code', function (e) {
        e.preventDefault();
		
        $('#reset-pinfrm span[class="errmsg"]').attr({for : '', class: ''}).empty();
        if ($('#reset-pinfrm #code').val()) {
		
            if (HASH_CODE == $.md5($('#reset-pinfrm #code').val())) {
                $('.verify-profile-pin', $('#change-email')).hide();
                $('#reset-pinfrm h5', $('#change-email')).html('<b>Reset Security PIN</b>');
                $('.new-profile-pin', $('#change-email')).show();
                $('#profile_pin,#confirm_profile_pin', RPF).attr('type', 'password');
                $('.new_pin,.cnfrm_pin', RPF).find('i').removeClass().addClass('fa fa-eye-slash');
            } else {
                $('#reset-pinfrm #code').after(' <span for="code" class="errmsg">Invalid Verification Code, Please try again</span>');
            }
        } else {
            $('#reset-pinfrm #code').after(' <span for="code" class="errmsg">Verification Code is required</span>');
        }
    });
 
   $('#change-email').on('submit', '#reset-pinfrm', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            success: function (op) {
			
                HASH_CODE = null;
                $('div.alert').remove();
				$("#general_details_modal").modal('hide');
				$('#propin_session', $('#change-email-new-email')).val(op.propin_session);
               /* $('#verify-propin-form,#reset-pinfrm', $('#change-email')).fadeOut('fast', function () {
                    $('#change-email-new-email', '#change-email').resetForm().fadeIn('slow');
                    $('#propin_session', $('#change-email-new-email')).val(op.propin_session);
                }); */
				$('#new-profile-pin').resetForm();
                $('#general_details_modal').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
				$('div.alert').fadeOut(8000);
				$("#general_details_modal").modal('hide');
			
            },
            error: function (event, xhr, settings) {
                var op = event.responseJSON;
                $('div.alert').remove();
                $('#reset-pinfrm').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');
                $('div.alert').fadeOut(8000);
            }
        });
    });
 /* End */

	$('#s2id_autogen1').keyup(function(e){
		e.preventDefault();
		value = $(this).val();
		if(value.length > 1){
				$.ajax({
				url: 'seller/get_tags',
				data: {'term':value},
				success: function (op) {
					str = '';
					if((op.tags !== undefined) && (op.tags !='')){
						$.each(op.tags ,function(i,values){
							str =str+'<option value="'+values.tag_id+'">'+values.tag_name+'</option>';
						})
						$('#s2_ext_value option').remove();
						$('#s2_ext_value').append(str);
					}
				},
			});
		} 
	});
	
	$('#profile-panel').on('click','.reset_cropper',function(e){	
		e.preventDefault();
		$($(this).data('preview')).attr('src',$($(this).data('target')).val());
	}); 
	
	$('#general_details').on('click','#logo-preview',function(e){
		e.preventDefault();
		$('#logo').trigger('click');
	}) 

});


	$.fn.extend({resetForm:function() {
			var form = $(this);
			$.each($('input', form), function () {
				if (! $(this).hasClass('ignore-reset'))
				{
					switch ($(this).attr('type'))
					{
						case 'text':
						case 'password':
						case 'textarea':
						case 'hidden':
						case 'number':
						case 'tel':
						case 'url':
						case 'email':
							$(this).val('');
							break;
						case 'radio':
						case 'checkbox':
							$(this).prop('checked', false);
							break;
						case 'file':
							$('#' + $(this).attr('id') + '-preview').attr('src', $(this).data('default'));
							break
					}
				}
			});
			$.each($('p.form-control-static', form), function () {
				if (! $(this).hasClass('ignore-reset'))
				{
					$(this).empty();
				}
			});
			$.each($('textarea', form), function () {
				$(this).val('');
				if (! $(this).hasClass('ignore-reset'))
				{
					if (CKEDITOR != undefined && CKEDITOR.instances[$(this).attr('id')] != undefined) {
						CKEDITOR.instances[$(this).attr('id')].setData(CKEDITOR.instances[$(this).attr('id')].element.$.defaultValue);
					}
					else {
						$(this).val(null);
					}
				}
			});
			$.each($('select', form), function () {
				if (! $(this).hasClass('ignore-reset'))
				{
					$(this).val('');
				}
			});
			return form;
		}
    });	
    
	$("#business_status").click(function(){
    	if ($("#business_status").prop('checked')==true){ 
		     if($("#address").val()!=''){
			 /*  $("#operating_address").val($("#address").val()); */
			   $("#operating_address_status").hide();
		    } 
         }
		else{
			$("#operating_address").val('');
			 $("#operating_address_status").show();
		}
	});
	
	var loadFile = function(event) {	
		var output = document.getElementById('profile');
		output.src = URL.createObjectURL(event.target.files[0]);
	};
	var loadFile2 = function(event) {	
		var output = document.getElementById('shop');
		output.src = URL.createObjectURL(event.target.files[0]);
	};
	var loadFile3 = function(event) {	
		var output = document.getElementById('banner');
		output.src = URL.createObjectURL(event.target.files[0]);
	};
