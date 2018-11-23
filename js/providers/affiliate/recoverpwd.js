$(document).on('submit','#recoverfrm',function (e) {
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
				if($('.recoverPwd input[name='+fld+']').parent().hasClass('input-group')){
					$('.recoverPwd input[name='+fld+']').parent().after("<div class='help-block'>"+msg+"</div>");
				} else {
					$('.recoverPwd input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
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
			if (op.status == 200) {
				frmObj.before("<div class='alert alert-success'>"+op.msg+"</div>");		
				frmObj.remove();				
			} else if (op.status == 'fail') {
				$('input.form-control',frmObj).prop('disabled',false);
				$('button[type=submit]',frmObj).prop('disabled',false);
				frmObj.before("<div class='alert alert-danger'>"+op.msg+"</div>");
				frmObj.reset;
			}
		}
	});		
});