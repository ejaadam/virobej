$(document).ready(function () {
	/* Pickup Details */
		$('#shipping_details').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#shipping_details');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op, textStatus, xhr) {
				
				$('body').toggleClass('loaded');
				if(xhr.status == 200){
						$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
					}else{
						$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
					}
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

   
	$('#shipping_postal_code').on('change', function () {
	    var pincode = $('#shipping_postal_code').val();
         var country_id = $('#shipping_country_id').val(); 
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode,country_id:country_id},
				success: function (OP) {
					$('#shipping_state_id, #shipping_city_id').prop('disabled', false).empty();					
					$('#shipping_state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
					$.each(OP.cities, function (k, e) {
						$('#shipping_city_id').append($('<option>', {value: e.id}).text(e.text));
					});
					$('#shipping_country_id, #shipping_state_id, #shipping_city_id').trigger('change');
				},
				error: function () {
					$('#shipping_state_id, #shipping_city_id').empty();					
					$('#shipping_state_id').val('').prop('disabled', true);
					$('#shipping_city_id').val('').prop('disabled', true);
				}
			});
		}	
    });
	
	 var postcode = $('#shipping_postal_code').val();
	  var country_id = $('#shipping_country_id').val(); 
	  var city_id = $('#cityid').val(); 
    if (postcode) {
        $.ajax({
            url: window.location.BASE + 'check-pincode',
            data: {pincode: postcode,country_id:country_id},
            success: function (OP) {
                $('#shipping_country_id, #shipping_state_id, #shipping_city_id').prop('disabled', false).empty();
                //$('#country_id').append($('<option>', {value: OP.country_id}).text(OP.country));
                $('#shipping_state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
                $.each(OP.cities, function (k, e) {
                    $('#shipping_city_id').append($('<option>', {value: e.id}).text(e.text));
                });
                $('#shipping_city_id option[value=' + city_id + ']').attr('selected', true);
            },
            error: function () {
                //$('#country_id').val('').prop('disabled', true);
                $('#shipping_state_id').val('').prop('disabled', true);
                $('#shipping_city_id').val('').prop('disabled', true);
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
		
		$('#shipping_details select,#shipping_details input[type=text]').val('');
	   
	}