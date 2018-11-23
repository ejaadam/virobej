$(document).ready(function () {	
	var ES = $('#edit_suppliers');	
	var postcode = $('#Postcode').val();
	if (postcode) {
		$.ajax({
			url: window.location.BASE + 'check-pincode',
			data: {pincode: postcode},
			success: function (OP) {					
				$('#country', ES).prop("disabled", false);
				$('#state', ES).prop("disabled", false);
				$('#city', ES).prop("disabled", false);                
				
				$('#country, #state, #city', ES).empty();
				$('#country', ES).append($('<option>', {value: OP.country_id}).text(OP.country));
				$('#state', ES).append($('<option>', {value: OP.state_id}).text(OP.state));
				$.each(OP.cities, function (k, e) {
					$('#city', ES).append($('<option>', {value: e.id}).text(e.text));
				});							
				$("#city option[value=" + city + "]").attr("selected", true);
			},
			error: function () {									
				$('#country', ES).val('').prop("disabled", true);
				$('#state', ES).val('').prop("disabled", true);
				$('#city', ES).val('').prop("disabled", true);
			}
		});
	}	
		
	$('#account-details #Postcode').on('change', function () {
        var pincode = $('#account-details #Postcode').val();		
        if (pincode != '' && pincode != null) {
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode},
				success: function (OP) {					
				    $('#country', ES).prop("disabled", false);
					$('#state', ES).prop("disabled", false);
					$('#city', ES).prop("disabled", false);				
					$('#country, #state, #city', ES).empty(); 
					$('#account-details #country', ES).append($('<option>', {value: OP.country_id}).text(OP.country));
					$('#account-details #state', ES).append($('<option>', {value: OP.state_id}).text(OP.state));
					$.each(OP.cities, function (k, e) {
						$('#account-details #city', ES).append($('<option>', {value: e.id}).text(e.text));
					});					
				},
				error: function () {
					$('#country, #state, #city_id', ES).empty();
					$('#country', ES).val('').prop("disabled", true);
					$('#state', ES).val('').prop("disabled", true);
					$('#city_id', ES).val('').prop("disabled", true); 
				}
			});
		}
    });
	
	var postal_code = $('#postal_code').val();
	if (postal_code) {
		$.ajax({
			url: window.location.BASE + 'check-pincode',
			data: {pincode: postal_code},
			success: function (OP) {					
				$('#store_country', ES).prop("disabled", false);
				$('#store_state', ES).prop("disabled", false);
				$('#store_city', ES).prop("disabled", false); 			
				$('#store_country, #store_state, #store_city', ES).empty();
				$('#store_country', ES).append($('<option>', {value: OP.country_id}).text(OP.country));
				$('#store_state', ES).append($('<option>', {value: OP.state_id}).text(OP.state));
				$.each(OP.cities, function (k, e) {
					$('#store_city', ES).append($('<option>', {value: e.id}).text(e.text));
				});							
				$("#store_city option[value=" + store_city + "]").attr("selected", true);
			},
			error: function () {									
				$('#store_country', ES).val('').prop("disabled", true);
				$('#store_state', ES).val('').prop("disabled", true);
				$('#store_city', ES).val('').prop("disabled", true);
			}
		});
	}
	
	$('#store-details #postal_code').on('change', function () {
        var pincode = $('#store-details #postal_code').val();		
        if (pincode != '' && pincode != null) {
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode},
				success: function (OP) {					
				    $('#store_country', ES).prop("disabled", false);
					$('#store_state', ES).prop("disabled", false);
					$('#store_city', ES).prop("disabled", false);				
					$('#store_country, #store_state, #store_city', ES).empty(); 
					$('#store-details #store_country', ES).append($('<option>', {value: OP.country_id}).text(OP.country));
					$('#store-details #store_state', ES).append($('<option>', {value: OP.state_id}).text(OP.state));
					$.each(OP.cities, function (k, e) {
						$('#store-details #store_city', ES).append($('<option>', {value: e.id}).text(e.text));
					});					
				},
				error: function () {
					$('#store_country, #store_state, #store_city', ES).empty();
					$('#store_country', ES).val('').prop("disabled", true);
					$('#store_state', ES).val('').prop("disabled", true);
					$('#store_city', ES).val('').prop("disabled", true); 
				}
			});
		}
    });
	
	var post_code = $('#post_code').val();
	if (post_code) {
		$.ajax({
			url: window.location.BASE + 'check-pincode',
			data: {pincode: post_code},
			success: function (OP) {					
				$('#bank_country', ES).prop("disabled", false);
				$('#bank_state', ES).prop("disabled", false);
				$('#city_id', ES).prop("disabled", false); 			
				$('#bank_country, #bank_state, #city_id', ES).empty();
				$('#bank_country', ES).append($('<option>', {value: OP.country_id}).text(OP.country));
				$('#bank_state', ES).append($('<option>', {value: OP.state_id}).text(OP.state));
				$.each(OP.cities, function (k, e) {
					$('#city_id', ES).append($('<option>', {value: e.id}).text(e.text));
				});							
				$("#city_id option[value=" + bank_city + "]").attr("selected", true);
			},
			error: function () {									
				$('#bank_country', ES).val('').prop("disabled", true);
				$('#bank_state', ES).val('').prop("disabled", true);
				$('#city_id', ES).val('').prop("disabled", true);
			}
		});
	}
	
    $('#edit_suppliers').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        rules: {
            street1: {required: true, },
            street2: {required: true, },
            city: {required: true, },
            state: {required: true, },
            country: {required: true, },
            Postcode: {required: true, },
            email: {required: true, email: true},
            officeFax: {required: true, },
            officePhone: {required: true, },
        },
        messages: {
            street1: {required: 'Please enter your street1', },
            street2: {required: 'Please enter your street2', },
            city: {required: 'Please enter your city', },
            state: {required: 'Please enter your state', },
            country: {required: 'Please enter your country', },
            Postcode: {required: 'Please enter your post code', },
            email: {required: 'Please enter your email', email: 'Please enter a valid email'},
            officeFax: {required: 'Please enter your fax no', },
            officePhone: {required: 'Please enter your Phone no', },
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $('.alert').remove();
            if ($(form).valid()) {
                var datastring=$('#edit_suppliers').serialize();
                var url=$('#edit_suppliers').attr('action');
                $.ajax({
                    url: url,
                    data: datastring,
                    success: function (data) {
                        $(form).before(data.msg);
                        $('#edit_data').modal('hide');
                        $('#msg').html(data.msg);
                        $('#search').trigger('click');
                    }
                });
            }
        }
    });
	
	
    
});
