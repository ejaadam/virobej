$(document).ready(function () {
	$('#name', $('#signup-success-div')).html($('#fullname').val());		
	$('#mobile-verification-div').css('display','block');
    $('#mobile-verification-form').on('submit', function (e) {		
        e.preventDefault();
        CURFORM = $('#mobile-verification-form');
        $.ajax({
            url: $('#mobile-verification-form').attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: $('#mobile-verification-form').serialize(),
            beforeSend: function () {
                $('input[type=submit]', $('#mobile-verification-form')).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {				
                $('input[type=submit]', $('#mobile-verification-form')).removeAttr('disabled', true).val('VERIFY OTP');
                if ($('#signup-success-div').length) {
                    $('#signup-success-div').show();
                    $('#supplier-sign-up-form,#mobile-verification-div').hide();
                }
				if (OP.url != undefined) {
                    window.location.href = OP.url;
                }
            },
            error: function (jqXhr) {
                $('input[type=submit]', $('#mobile-verification-form')).removeAttr('disabled', true).val('VERIFY OTP');
            }
        });
    });
    $('#resend-verification-code').on('click', function (e) {
        e.preventDefault();
		$('#verification_code').val('');
        var CurEle = $(this);
        $.ajax({
            url: CurEle.data('url'),
			success:function(op){
				$('#mobile-verification-div #msg').html('<span class="text-danger">'+op.msg+'</span>');
			}
			
        });
    });
   
	if ($('#fullname').val() !== '') {				
		$('#supplier-sign-up-form').hide();
		$('#check-user').addClass('hidden');	
		$('.heading').addClass('hidden');	
		 $('#resend-verification-code').trigger('click');
	}
	
	$('#verification_code').on('keypress', function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
			return false;
		}
		return true;
    });
	
	$('#change_mobile').click(function(e){
		e.preventDefault();
		$('#mobile-model').modal('show');
	})
	$('#change-mobile-btn').click(function(e){
		e.preventDefault();
		CURFORM = $('#change-mobile-form');
		$.ajax({
			type:'post',
			dataType:'json',
			data:{'mobile_no':$('#mobile_no').val()},
            url: $('#change-mobile-form').attr('action'),
			beforeSend:function(){
				$('#change-mobile-btn').attr('disabled',true).text('Process..');
			},
			success:function(op){
				if(op.status == 'ok'){
				$('#mobile-model').modal('hide');
				$('#mobile-verification-div #msg').html('<span class="text-danger">'+op.msg+'</span>');
				}else{
					$('#change-mobile-btn').attr('disabled',true).text('SAVE');
				}
			},
			error:function(){
				$('#change-mobile-btn').attr('disabled',false).text('SAVE');
			}
			
        });
	})
	
	
});
