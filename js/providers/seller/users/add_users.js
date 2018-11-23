	$(document).ready(function () {
		$('#submit').click(function (e) {
			e.preventDefault();
			CURFORM = $('#user-form');
			$.ajax({
				url: $('#user-form').attr('action'),
				data: $('#user-form').serialize(),
				type:'post',
				daatType:'json',
				success: function (OP) {
					$('input').val('');
					$('#msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-close="close">&times;</a>User Added Successfully</div>')
				},
				error: function () {
					$('#state_id, #city_id').empty();					
					$('#state_id').val('').prop('disabled', true);
					$('#city_id').val('').prop('disabled', true);
				}
			});
		});
	});

	$.fn.initTransferFrm = function(){												
		$('#pickup_details select,#pickup_details input[type=text],#pickup_details #status').val('');
	   
	}