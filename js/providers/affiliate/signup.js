var CUSR = $('#signupFrm');	
CUSR.on('submit', function (e) {
	e.preventDefault();
	var frmObj = $(this);	
	var btnTxt = $('input[type=submit]', CUSR).attr('disabled', true).val();
	CURFORM = CUSR;
	$.ajax({
		type: 'POST',
		url: frmObj.attr('action'),
		data: frmObj.serialize(),
		dataType: 'json',
		error: function (jqXhr) {                
			$('input[type=submit]', CUSR).attr('disabled', false);
		},        
		beforeSend: function () {				
			$('input[type=submit]', CUSR).attr('disabled', true);
		},
		success: function (op) {											
			if(op.status==200){
				$('.regbox').hide();
				$('.regconfirm').show();
			}
		}
	});
});

$(document).on('click','#gotoLoginBtn',function(){
	window.location.href=$(this).data('url')
})