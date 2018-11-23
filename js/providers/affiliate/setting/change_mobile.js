var emailcheck = 0, mobilecheck = 0;
$(document).ready(function () {
    var CMF = $('#change-mobile-form');
    CMF.validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
		errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        }, 
        rules: {
            mobile: {
                required: true,
                number: true,
				maxlength:10,
				minlength:10
            }
        },
        messages : $val_message,
		submitHandler: function (form, event) {
            event.preventDefault();
                if (CMF.valid()) {
                    $.ajax({
                        url: CMF.attr('action'),
                        data: CMF.serialize(),
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            $('.alert,div.help-block').remove();
							$('#send_verify_code').attr('disabled','disabled');
							$('body').toggleClass('loaded');
                        }, 
						error: function (jqXHR, textStatus, errorThrown) {
                            $('body').toggleClass('loaded');
                            $('#send_verify_code').attr('disabled',false);							
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,msg){
								if($('#change-mobile-form input[name='+fld+']').parent().hasClass('input-group')){
									$('#change-mobile-form input[name='+fld+']').parent().after("<div class='help-block'>"+msg+"</div>");
								} else {
									$('#change-mobile-form input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
								}
								return false;
							});
						},
                        success: function (data) {
						    $('body').toggleClass('loaded');
							$('#send_verify_code').attr('disabled',false);
						    if(data.status == 'ok'){
								$('#change-mobile-form').fadeOut('fast',function(){
								    $('#code_verify_form').fadeIn('slow');
									$('#code_msg').text(data.msg);
									$('#verification_code').val("");
								});				
                            }else{
								$('#change-mobile-form input[name="mobile"]').after("<div class='help-block'>"+data.msg+"</div>");
                            }	
                        }
                    });
                }
        }
       
    });
    $('input').bind('cut copy paste', function (e) {
        e.preventDefault();
    });
    $('#mobile').on('focus', function () {
	    $('.alert ,div.help-block').remove();
    }); 
	
	/* code_verify_form */
    var CVF = $('#code_verify_form');
    CVF.validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
		rules: {
		   verify_code: {
				required: true,
				digits: true,
				maxlength:6,
				minlength:6
			
		  }
		},
		messages : $val_message, 
		submitHandler: function (form, event) {
            event.preventDefault();
                if (CVF.valid()) {
                    $.ajax({
                        url: CVF.attr('action'),
                        data: CVF.serialize(),
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            $('.alert,div.help-block').remove();
							$('#update_mobile').attr('disabled','disabled');
							$('#verification_code').val("");
							$('body').toggleClass('loaded');
                        }, 
					    error: function (jqXHR, textStatus, errorThrown) {	
                            $('body').toggleClass('loaded');
                            $('#update_mobile').attr('disabled',false);							
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,msg){
								if($('#code_verify_form input[name='+fld+']').siblings().hasClass('input-group')){
									$('#code_verify_form input[name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
								} else {
									$('#code_verify_form input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
								}
								return false;
							});
						}, 
                        success: function (data){ 
							$('body').toggleClass('loaded');
							$('#update_mobile').attr('disabled',false);
						    if(data.status == 200){
							    $('#crnt_number').val($('#mobile').val());
                                $('#code_verify_form').fadeOut('fast',function(){
									$('#change-mobile-form').fadeIn('slow');
									$('#verification_code').val("");
									$('#mobile').val("");
									
								});
                                $('#change-mobile-form').before($('<div >').attr({class: 'alert alert-success'}).html(data.msg)); 
                            }else{
								$('#change-mobile-form').before($('<div >').attr({class: 'alert alert-danger'}).html(data.msg)); 
                            }		
                        }
                    });
                }
        }
    });	
	
    $('#verification_code').on('focus', function () {
	    $('.alert ,div.help-block').remove();
    });
	
	$("#code_verify_form #mobile_no").click(function(event){
	   event.preventDefault();
		if (CMF.valid()) {
			$.ajax({
				url: CMF.attr('action'),
				data: CMF.serialize(),
				dataType: 'JSON',
				type: 'POST',
				beforeSend: function () {
					$('.alert,div.help-block').remove();
					$('#send_verify_code').attr('disabled','disabled');
					$('body').toggleClass('loaded');
					
				}, 
				error: function (jqXHR, textStatus, errorThrown) {
                    $('body').toggleClass('loaded');
                    $('#send_verify_code').attr('disabled',false);					
					responseText = $.parseJSON(jqXHR.responseText);
					$.each(responseText.errs,function(fld,msg){
						if($('#change-mobile-form input[name='+fld+']').siblings().hasClass('input-group')){
							$('#change-mobile-form input[name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
						} else {
							$('#change-mobile-form input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
						}
						return false;
					});
				},
				success: function (data) {
				    $('body').toggleClass('loaded');
					$('#send_verify_code').attr('disabled',false);
					if(data.status == 'ok'){
					    $('#change-mobile-form').fadeOut('fast',function(){
							$('#code_verify_form').fadeIn('slow');
							$('#code_msg').text(data.msg);
							$('#verification_code').val("");
						});	 
					}else{
						$('#change-mobile-form input[name="mobile"]').after("<div class='help-block'>"+data.msg+"</div>");
					}	
			    }
			});
		}
    });
});


$(document).on('click','#cancel',function (e) {
	e.preventDefault();
	$('#mobile').val("");
	$('#code_verify_form').fadeOut('fast',function(){
		$('#change-mobile-form').fadeIn('slow');
	});
});

