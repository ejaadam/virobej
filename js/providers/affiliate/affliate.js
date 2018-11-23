
$("#passord").hide();
$("#View_details").hide();
$(document).on('submit','#signupFrm',function (e) {
	e.preventDefault();
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
			//$('input.form-control',frmObj).prop('disabled',true);
			//$('button[type=submit]',frmObj).prop('disabled',true);
	
		},
		success: function (op) {
			$('.alert').remove();
		  /* var spo_error=op.msg;
		   $("#display_error").text(spo_error); */
			 if (op.status == 'available') {
		       $("#referral").hide();
			   $("#passord").show();
			   $("#uname").val(op.user_data['email']);
			} 
		} 
	});		

});
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
			$('#toploginBtn',frmObj).fadeIn('slow').attr('disabled', false);
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
			frmObj.before("<div class='alert alert-info'>Authenticating...</div>").fadeIn('slow');
		},
		success: function (op) {
			if (op.status == 'ok') {
				$("#passord").hide();
				$("#View_details").show();
				$("#first_name").text(op.userdata['first_name']);
				$("#last_name").text(op.userdata['last_name']);
				$("#email_signup").text(op.userdata['email']);
				$("#uname_signup").text(op.userdata['uname']);
				
			} else if (op.status == 'fail') {
				/* frmObj.before("<div class='alert alert-danger'>"+op.msg+"</div>");
				frmObj.reset; */
			}
		}
	});		 
});
