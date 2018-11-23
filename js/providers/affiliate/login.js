	/*$("#loginfrm").validate({
	rules:{
		uname:{
			required:true,
			uname_validate:true
		}
	}       
});*/

$.validator.addMethod('uname_validate', function (value, element, params) {
    if (!/[a-z0-9]/i.test(value)) {
        return this.optional(element) || false;
    } else if (/[~`!#$%\^&*+=\-\[\]\\';.,/(){}|\\":<>\?]/g.test(value)) {
        return this.optional(element) || false;
    } else {
        return this.optional(element) || true;
    }
}, function(error, element) {
    var value = $(element).val();
    if (!/[a-z]/i.test(value[0])) {
        return 'INVALIDE'
    }
    if (/[~`!#$%\^&*+=\-\[\]\\';.,/(){}|\\":<>\?]/g.test(value)) {
        return 'No special characters except: _'
    }   
})

$(document).on('submit','#loginfrm',function (e) {
	e.preventDefault();
	$('.alert,.help-block').remove();
	var frmObj = $(this);
	$.ajax({
		type: 'POST',
		url: frmObj.attr('action'),
		data: frmObj.serialize(),
		dataType: 'json',
		error: function (jqXHR, textStatus, errorThrown) {
			$('.alert').remove();
			$('input.form-control',frmObj).prop('disabled',false);
			$('button[type=submit]',frmObj).prop('disabled',false);			
			responseText = $.parseJSON(jqXHR.responseText);
			$.each(responseText.errs,function(fld,msg){
				if($('.signin input[name='+fld+']').parent().hasClass('input-group')){
					$('.signin input[name='+fld+']').parent().after("<div class='help-block'>"+msg+"</div>");
				} else {
					$('.signin input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
				}
			});
		},
		beforeSend: function () {
			$('input.form-control',frmObj).prop('disabled',true);
			$('button[type=submit]',frmObj).prop('disabled',true);
			frmObj.before("<div class='alert alert-info'>Authenticating...</div>").fadeIn('slow');
		},
		success: function (op) {
			$('.alert').remove();
			if (op.status == 'ok') {
				frmObj.before("<div class='alert alert-success'>Login successful. Redirecting...</div>");
				window.location.href = op.url;
			} else if (op.status == 'fail') {
				$('input.form-control',frmObj).prop('disabled',false);
				$('button[type=submit]',frmObj).prop('disabled',false);
				frmObj.before("<div class='alert alert-danger'>"+op.msg+"</div>");
				frmObj.reset;
			}
		}
	});		
});


$('#forgotfrm').submit(function (e) {
	e.preventDefault();
	var frmObj = $(this);
	$('.alert,.help-block').remove();
	$.ajax({
		type: 'POST',
		url: frmObj.attr('action'),
		data: frmObj.serialize(),
		dataType: 'json',
		error: function (jqXHR, textStatus, errorThrown) {
			$('.alert').remove();
			$('#topForgotBtn',frmObj).fadeIn('slow').attr('disabled', false);
			responseText = $.parseJSON(jqXHR.responseText);
			$.each(responseText.errs,function(fld,msg){
				if($('.forgot input[name='+fld+']').parent().hasClass('input-group')){
					$('.forgot input[name='+fld+']').parent().after("<div class='help-block'>"+msg+"</div>");
				} else {
					$('.forgot input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
				}
			});
		},
		beforeSend: function () {
			$('#topForgotBtn').attr('disabled', 'disabled');
			frmObj.before("<div class='alert alert-info'>Authenticating...</div>").fadeIn('slow');
		},
		success: function (op) {
			$('.forgot .alert').remove();
			if (op.status == 'ok') {
				frmObj.before("<div class='alert alert-success'>"+op.msg+"</div>");
				//window.location.href = op.url;
			} else if (op.status == 'fail') {
				frmObj.before("<div class='alert alert-danger'>"+op.msg+"</div>");
				$('.forgot #uname').val("");
			}
		}
	})

});

$(document).on('click','.btn-forgot',function (e) {
	e.preventDefault();
	$('.signin').fadeOut('fast',function(){
		$('.forgot').show().fadeIn('slow');
		$('.signin div.help-block').remove();
	});
});

$(document).on('click','.backtoLogin',function (e) {
	e.preventDefault();	
	$('.forgot').fadeOut('fast',function(){
		$('.signin').fadeIn('slow');
		$('.forgot div.help-block').remove();
	});
});