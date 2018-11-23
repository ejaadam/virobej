$(document).ready(function () {
	/* Pickup Details */
		$('#pickup_details').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#pickup_details');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op, textStatus, xhr) {
				if(xhr.status == 200){
					$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}else{
					$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}
				$('body').toggleClass('loaded');
				//$(this).initTransferFrm();
			},
			error: function (jqXHR, exception, op) {
				if (jqXHR.status == 406) {
					console.log(jqXHR.responseJSON.msgs);
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			},
        });
    });
   
	$('#pickup_postal_code').on('change', function () {
        var pincode = $('#pickup_postal_code').val();
         var country_id = $('#pickup_country_id').val(); 
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode,country_id:country_id},
				success: function (OP) {
					$('#pickup_state_id, #pickup_city_id').prop('disabled', false).empty();					
					$('#pickup_state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
					$.each(OP.cities, function (k, e) {
						$('#pickup_city_id').append($('<option>', {value: e.id}).text(e.text));
					});
					$('#pickup_country_id, #pickup_state_id, #pickup_city_id').trigger('change');
				},
				error: function () {
					$('#pickup_state_id, #pickup_city_id').empty();					
					$('#pickup_state_id').val('').prop('disabled', true);
					$('#pickup_city_id').val('').prop('disabled', true);
				}
			});
		}	
    });
	
	 var postcode = $('#pickup_postal_code').val();
	  var country_id = $('#pickup_country_id').val(); 
	  var city_id = $('#pickup_cityid').val(); 
    if (postcode) {
        $.ajax({
            url: window.location.BASE + 'check-pincode',
            data: {pincode: postcode,country_id:country_id},
            success: function (OP) {
                $('#pickup_country_id, #pickup_state_id, #pickup_city_id').prop('disabled', false).empty();
                //$('#country_id').append($('<option>', {value: OP.country_id}).text(OP.country));
                $('#pickup_state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
                $.each(OP.cities, function (k, e) {
                    $('#pickup_city_id').append($('<option>', {value: e.id}).text(e.text));
                });
                $('#pickup_city_id option[value=' + city_id + ']').attr('selected', true);
            },
            error: function () {
                //$('#country_id').val('').prop('disabled', true);
                $('#pickup_state_id').val('').prop('disabled', true);
                $('#pickup_city_id').val('').prop('disabled', true);
            }
        });
    }
	
	$('input.website').keyup(function () {
        if (! ((this.value.match('^http://')) || (this.value.match('^https://')))) {
            this.value = 'http://' + this.value;
        }
    });
	
});

$.fn.initTransferFrm = function(){												
		
		$('#pickup_details select,#pickup_details input[type=text]').val('');
	   
	}