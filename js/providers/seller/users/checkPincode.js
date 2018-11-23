$(document).ready(function () {
   
	$('#post_code').on('change', function () {
        var pincode = $('#post_code').val();
         var country_id = $('#country_id').val(); 
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode,country_id:country_id},
				success: function (OP) {
					$('#state_id, #city_id').prop('disabled', false).empty();					
					$('#state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
					$.each(OP.cities, function (k, e) {
						$('#city_id').append($('<option>', {value: e.id}).text(e.text));
					});
					$('#country_id, #state_id, #city_id').trigger('change');
				},
				error: function () {
					$('#state_id, #city_id').empty();					
					$('#state_id').val('').prop('disabled', true);
					$('#city_id').val('').prop('disabled', true);
				}
			});
		}	
    });
	
	 var postcode = $('#post_code').val();
	  var country_id = $('#country_id').val(); 
	  var city_id = $('#cityid').val(); 
    if (postcode) {
        $.ajax({
            url: window.location.BASE + 'check-pincode',
            data: {pincode: postcode,country_id:country_id},
            success: function (OP) {
                $('#country_id, #state_id, #city_id').prop('disabled', false).empty();
                //$('#country_id').append($('<option>', {value: OP.country_id}).text(OP.country));
                $('#state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
                $.each(OP.cities, function (k, e) {
                    $('#city_id').append($('<option>', {value: e.id}).text(e.text));
                });
                $('#city_id option[value=' + city_id + ']').attr('selected', true);
            },
            error: function () {
                //$('#country_id').val('').prop('disabled', true);
                $('#state_id').val('').prop('disabled', true);
                $('#city_id').val('').prop('disabled', true);
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