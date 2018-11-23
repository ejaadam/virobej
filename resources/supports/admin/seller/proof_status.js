$(document).ready(function () {
$('#update_status').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#update_status');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op) {
				console.log(op);
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);		
				 $('#proof_verification_details').fnDraw();
				if (jqXHR.status == 406) {
					console.log(jqXHR.responseJSON.msgs);
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			},
        });
    });
});