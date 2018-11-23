$(document).ready(function () {	
	
	if ($('#fullname').val() !== '') {			
		$('#supplier-sign-up-form').hide();
		$('#check-user').addClass('hidden');	
		$('.heading').addClass('hidden');	
	}		
    /*$('#send-email-verification-link').on('click', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            url: CurEle.data('url'),
            success: function (OP) {
                $('#send-email-verification-link').text('Resend');
            }
        });
    });
    $('#verify-email-id-block').on('click', '.dismiss', function (e) {
        e.preventDefault();
        $('#verify-email-id-block').fadeOut(200);
    }); */
	
	/* Send & Resend verification code */
	$('#verify-email #resend-verification-code').on('click', function (e) {
        e.preventDefault();	
        var CurEle = $(this);
        $.ajax({
            url: CurEle.data('url'),
            success: function (op) {
                $('#verify-email #verification_msg').html('<div class="text-center alert alert-success"><i class="icon-check text-success"></i> <b>' + op.msg + '</b><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');			
				if(op.status == 208){
					$('#verify-email #resend-verification-code').hide();
					$('#verify-email #txt-status').empty().html('<span class="label label-success"><i class="icon-check"></i> Verified</span>');
				}
            },
			error: function (jqXHR, textStatus, errorThrown) {
                responseText = $.parseJSON(jqXHR.responseText);	
				if(responseText.msg != '' && responseText.msg != undefined){
				    $('#verify-email #verification_msg').html('<div class="text-center alert alert-danger"><i class="icon-warning-sign text-danger"></i> <b>' + responseText.msg + '</b><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
					$('div.alert').fadeOut(7000);	
				}								
            }	
        });
    });	
	$('#verify-email #resend-verification-code').trigger('click');
	
	/* $('#verify').on('click', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            url: $('#email-verification-form').attr('action'),
			data:$('#email-verification-form').serialize(),
            success: function (data, textStatus, xhr) {
				if(xhr.status == 200){
					$('#email-verification-form').hide();
					$('.txt').hide();
					$('#txt-status').html('<span class="label label-success">VERIFIED</span>');
				}else{
					
				}
            }
        });
    }); */
	
	$('#change-email').click(function(e){
		e.preventDefault();	
		$('#change-email-modal #error_msg').empty();
		$('#change-email-form #email').val(''); 
		$('#change-email-form #new_email-err').removeClass('errmsg').empty(''); 
		$('#change-email-form').fadeIn('fast');
		$('#change-email-modal').modal('show');
	})
	
	/* Change Email  */
	$('#change-email-modal').on('click','#change-email-btn',function(e){
		e.preventDefault();
		CURFORM = $('#change-email-form');
		$.ajax({
            url: $('#change-email-form').attr('action'),
			data:$('#change-email-form').serialize(),			
			success: function (op) {
				$('#change-email-form').resetForm(); 
				$('#change-email-form').fadeOut('fast',function(){
				    $('#change-email-modal #error_msg').empty().html('<div class="alert alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert"></a></div>');		
				});
            },
			error: function (jqXHR, textStatus, errorThrown) {
                responseText = $.parseJSON(jqXHR.responseText);
				console.log(responseText);
				if(responseText.msg != '' && responseText.msg != undefined){   
				    $('#change-email-form').before('<div class="alert alert alert-danger"><i class="icon-warning-sign"></i> ' + responseText.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				    $('div.alert').fadeOut(6000);
				}								
            }			
			/* success: function (op) {
				if(xhr.status == 200){
					$('#change-email-modal').modal('hide');
					$('#email-fld').text(data.email_id);
					$('#txt-status').html('<span class="label label-danger">NOT VERIFIED</span>');
				}else{					
				}
				}
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);		
				if (jqXHR.status == 406) {
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			}  */           
        });
	})
});
