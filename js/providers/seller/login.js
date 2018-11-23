$(document).ready(function () {	
    $('#login_form').submit(function (event) {		
		$('#err_msg').html('');		
        event.preventDefault();
        CURFORM = $('#login_form');
        $.ajax({
            url: $('#login_form').attr('action'),
            data: $('#login_form').serializeObject(),
			beforeSend: function () {
				$('p.alert').remove();				
			},
			success: function (op) {
				console.log(op);			
				if(op.status == 200){				
					CURFORM.before('<p class="alert alert-success">'+op.msgs+'</p>');				
					$('p.alert').fadeOut(10000);
					window.location.href = op.url;
				}
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);	
				if (jqXHR.status == 422) {				
					console.log(jqXHR.responseJSON.msgs);
					//$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
					CURFORM.before('<p class="alert alert-danger">'+jqXHR.responseJSON.msgs+'</p>');				
					$('p.alert').fadeOut(10000);
				} 
				if (jqXHR.status == 308) {				
					console.log(jqXHR.responseJSON.msgs);
					CURFORM.before('<p class="alert alert-success">'+jqXHR.responseJSON.msgs+'</p>');				
					$('p.alert').fadeOut(10000);
				} 
			},
        });
    });
	
    var STEP = 1, FORM = $('#forgot-pwd-panel .step-1');
    $('#forgot-password-btn').on('click', function (e) {
        e.preventDefault();				
		$('#forgotfrm #username').val('');
		$('#forgotfrm span[class="errmsg"]').attr({for : '', class: ''}).empty();
        //window.location.ChangeUrl('Forgot Password', window.location.BASE + 'supplier/forgot-password');
        $('#forgot-pwd-panel').show();
        $('#login-panel').hide();
    });
	
    /*$('#login-btn').on('click', function (e) {
        e.preventDefault();
		alert('good');
        window.location.ChangeUrl('Login', window.location.BASE + 'supplier/login');
        STEP = 1;
        FORM = $('#forgot-pwd-panel .step-1');
        $('#forgot-pwd-panel .step-2,#forgot-pwd-panel .step-3,#forgot-pwd-panel').hide();
        $('#forgot-pwd-panel .step-1,#login-panel').show();
        $('#forgot-pwd-panel form').resetForm();
    });*/
	
    /* $('form', '#forgot-password').on('submit', function (e) {
        e.preventDefault();
        CURFORM = FORM;
        $.ajax({
            url: FORM.attr('action'),
            data: $('#forgot-password input').serializeObject(),
            success: function (op) {
			    console.log(op);
                switch (STEP) {
                    case 1:
                        $('#forgot-pwd-panel .step-1').hide();
                        $('#forgot-pwd-panel .step-2').show();
                        FORM = $('#forgot-pwd-panel .step-2');
                        STEP ++;
                        break;
                    case 2:
                        $('#forgot-pwd-panel .step-2').hide();
                        $('#forgot-pwd-panel .step-3').show();
                        FORM = $('#forgot-pwd-panel .step-3');
                        STEP ++;
                        break;
                    case 3:
                        $('#login-btn').trigger('click');
                        break;
                }
            }
        });
    }); */
	
	var FF = $('#forgotfrm'), FOF = $('#forgot_optfrm'), RF = $('#resetfrm'), RPF = $('#reset_password #password-resetfrm'); Token = null; 
	FF.submit(function (e) {
        e.preventDefault();
        $('.help-block').remove();
        CURFORM = FF;
        $.ajax({
            url: FF.attr('action'),
            data: FF.serialize(),
			success: function (op) {			
                console.log(op);
				$('#username',FF).val('');
				FF.before('<div class="alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert"></a></div>');
				$('div.alert').fadeOut(8000);
            },
			error: function (jqXHR, textStatus, errorThrown) {
                responseText = $.parseJSON(jqXHR.responseText);
				console.log(responseText);
				if(responseText.msg != '' && responseText.msg != undefined){
				    RPF.before('<div class="alert alert-danger"><i class="icon-exclamation"></i> ' + responseText.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert"></a></div>');
				    $('div.alert').fadeOut(8000);
				}								
            }
			
           /* success: function (op) {			
                HASH_CODE = null;				
                $('#options', FOF).empty();
                $('#forgot-pwd-panel #forgot_title').text('Reset Your Password');
                //$('h4', FOF).text(op.msg);
				str ='';
				str ='<p>'+op.msg+'</p>';
                $.each(op.details, function (k, v) {
				    str += '<div class="radio"><label><input type="radio" value="'+v.value+'" name="opt" required="required" data-err-msg-to="#options-err"/>'+v.label+'<br/>'+ (v.id || v.number) +'</label></div>';
					
                    //$('#options', FOF).append($('<div>', {class: 'radio'}).append($('<label>').append([$('<input>', {type: 'radio', value: v.value, name: 'opt', required: 'required', 'data-err-msg-to': '#options-err'}), v.label +"<br>"+ v.id || v.number])));
                });
				$('#options', FOF).append(str);
                Token = op.token;               
                FOF.resetForm();
                FF.fadeOut('fast', function () {
                    FOF.fadeIn('slow');
                });
            }*/
        });
    });
	
    /*  Password Reset Using Link  */
    RPF.on('submit', function (e) {
        e.preventDefault();		
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serialize(),
            success: function (op) {
                $('#newpwd,#confirm_pwd', RPF).attr('type', 'password');
                $('.cnfrm_pwd,.new_pwd', RPF).find('i').attr('class','').attr('class','icon-eye-close');		
				RPF.resetForm();
				$('#reset_password #pwdreset_panel').fadeOut('fast',function(){
				    $('#reset_password #pwdreset_success').fadeIn('slow');
				});
				$('#reset_password #pwdreset_success #success_msg').text(op.msg);
				//RPF.before('<div class="alert alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				//$('div.alert').fadeOut(6000);
                //window.location.assign(op.url);
            },
			error: function (jqXHR, textStatus, errorThrown) {
                responseText = $.parseJSON(jqXHR.responseText);
				console.log(responseText);
				if(responseText.msg != '' && responseText.msg != undefined){
				    RPF.before('<div class="alert alert alert-danger"><i class="icon-exclamation"></i> ' + responseText.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				    $('div.alert').fadeOut(6000);
				}								
            }
        });
    });
	
	/*FOF.submit(function (e) { 
        e.preventDefault();
        $('.help-block').remove();
        CURFORM = FOF;
        if (Token != null) {
            $.ajax({
                url: FOF.attr('action'),
                data: FOF.serialize(),
                headers: {token: Token},
                success: function (op) {
                    Token = op.token;
                    HASH_CODE = op.hash_code;
                    console.log(op);
                    RF.resetForm();
					$('#forgot-pwd-panel #forgot_title').text('Reset Your Password');
                    FOF.fadeOut('fast', function () {
                        RF.fadeIn('slow');
                        $('#verify_code', RF).show();
                        $('#new_password', RF).hide();
                    });
                }
            });
        }
    });
	
	RF.on('input', '#code', function () {
    	//console.log(HASH_CODE+' = '+$.md5($('#code', RF).val())+' = '+$('#code', RF).val());
        if (HASH_CODE == $.md5($('#code', RF).val())) {
            $('#new_password span', RF).attr({class: '', for : ''}).empty();
            $('#verify_code span', RF).attr({class: '', for : ''}).empty();
            $('#verify_code', RF).hide();
            $('#new_password', RF).fadeIn('slow', function () {
                $('#new_password span', RF).attr({class: '', for : ''}).empty();
            });
        } else {
            if ($('#code', RF).val().length == 6) {
                $('#verify_code #errmsg', RF).addClass('errmsg').text('Invalid Verification code, Please try it again!');
            } else {
                $('#verify_code #errmsg', RF).removeClass('errmsg').empty();
            }
        }
    });
	
	$('#resend-forgot-otp').on('click', function (e) {
        e.preventDefault();
        FOF.trigger('submit');
    });

    RF.submit(function (e) {
        e.preventDefault();
        $('.help-block').remove();
        CURFORM = RF;
        if (Token != null) {
            $.ajax({
                url: RF.attr('action'),
                data: RF.serialize(),
                headers: {token: Token},
                success: function (op) {
                    HASH_CODE = null;
                    $('#verify_code', RF).fadeIn('slow', function () {
                        $('#new_password', RF).fadeOut('fast');
                    });
					RF.resetForm();
					$('#resetpwd_success #success_msg').text(op.msg);
					$('#resetfrm').fadeOut('fast',function(){
						$('#resetpwd_success').fadeIn('slow');
					});                   
                    //$('.login_wrapper .backtoLogin').trigger('click');
                }
            });
        }
    });*/
	
	
	$('.login_wrapper').on('click', '.backtoLogin', function (e) {
        e.preventDefault();
        $('#forgot-pwd-panel #forgotfrm #username').val('');
        $('#forgot-pwd-panel #forgotfrm').resetForm();
        $('#forgot-pwd-panel #forgot_optfrm').resetForm();
        $('#forgot-pwd-panel #resetfrm').resetForm();
        $('#forgot-pwd-panel #resetpwd_success').fadeOut('fast');		
        $('#forgot_optfrm, #resetfrm','#forgot-pwd-panel').fadeOut('fast');
        $('#forgot-pwd-panel, #forgotfrm').fadeIn();			
        $('#forgot-pwd-panel').fadeOut('fast', function(){
		    $('#login_panel #login_form').resetForm();			
			$('#login-panel').fadeIn('slow');
		});
    });
	
	/* On click Eye Icon Reset Password*/
    $('.new_pwd', RPF).on('click', function (e) {	
        var x = $('#newpwd', RPF).attr('type');
        if (x === 'password') {
            $('#newpwd', RPF).attr('type', 'text');
            $(this).find('i').attr('class','').attr('class','icon-eye-open');
        } else {
            $('#newpwd', RPF).attr('type', 'password');
            $(this).find('i').attr('class','').attr('class','icon-eye-close');
        }
    });

    $('.cnfrm_pwd', RPF).on('click', function (e) {	
        var x = $('#confirm_pwd', RPF).attr('type');
        if (x === 'password') {
            $('#confirm_pwd', RPF).attr('type', 'text');
            $(this).find('i').attr('class','').attr('class','icon-eye-open');
        } else {
            $('#confirm_pwd', RPF).attr('type', 'password');
            $(this).find('i').attr('class','').attr('class','icon-eye-close');
        }
    });
	
    $('#resend-verification-code').on('click', function (e) {
        e.preventDefault();
        CURFORM = $('#forgot-pwd-panel .step-1');
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject()
        });
    });
	
	$('.login_wrapper').on('click', '.backtoLogin', function (e) {
        e.preventDefault();
        $('#forgot-pwd-panel #forgotfrm #username').val('');
        $('#forgot-pwd-panel #forgotfrm').resetForm();
        $('#forgot-pwd-panel #forgot_optfrm').resetForm();
        $('#forgot-pwd-panel #resetfrm').resetForm();
        $('#forgot_optfrm, #resetfrm','#forgot-pwd-panel').fadeOut('fast');
        $('#forgot-pwd-panel, #forgotfrm').fadeIn();			
        $('#forgot-pwd-panel').fadeOut('fast', function(){
		    $('#login_panel #login_form').resetForm();			
			$('#login-panel').fadeIn('slow');
		});
    });
	
    $('#show-hide-password', $('#forgot-password')).on('click', function () {	
        if ($('#password', $('#forgot-password')).attr('type') === 'password') {
            $('#password', $('#forgot-password')).attr('type', 'text');
        }
        else {
            $('#password', $('#forgot-password')).attr('type', 'password');
        }
        var alt = $('#show-hide-password', $('#forgot-password')).attr('data-alternative');
        $('#show-hide-password', $('#forgot-password')).attr('data-alternative', $('#show-hide-password').text()).text(alt);
    });
});
