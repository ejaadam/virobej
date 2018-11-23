$(document).ready(function(){
	
   $('#tbb_m #change_phone_number').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serialize(),
            success: function (op) {
              if(op.err=='not_found'){
				 $("#change_mobile_content").text(op.msg);
			  }
			  
            }
        });
    });
	 $("#mobile_verify").hide();
	$("#change_mobile_verify_code").click(function(e) {
		e.preventDefault();
		CURFORM = $("#change_mobile_otp_confirm");
		$.ajax({
				url: $('#change_mobile_verify_code').attr('data-url'),            
				data: {phone_no:$("#phone_code").val()},
				dataType:'JSON',
				type:'POST',
				success: function (op) {
					 console.log(op);
					  $("#mobile_verify").show();
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

	  
	   $('#new-mobile-otp-resend').click (function (e) {
        e.preventDefault();
        var CURFORM = $('#new-mobile-otp-resend');
        $.ajax({
            url: CURFORM.attr('href'),
            success: function () {
               
            }
        });
    });
	  
	$('#change_mobile_otp_confirm').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#change_mobile_otp_confirm');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op) {
				console.log(op);
				 window.location.href = op.url;
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
});