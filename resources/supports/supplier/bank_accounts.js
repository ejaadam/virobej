$(document).ready(function () {
    var SBF = $('#store-bank-form');
	$('#postal_code', SBF).on('change', function () {
        var pincode = $('#postal_code', SBF).val();
        if (pincode != '' && pincode != null)
            $.ajax({
                url: window.location.BASE + 'check-pincode',
                data: {pincode: pincode},
                success: function (OP) {
                    $('#country_id, #state_id, #city_id', SBF).prop('disabled', false).empty();
                    $('#country_id', SBF).append($('<option>', {value: OP.country_id}).text(OP.country));
                    $('#state_id', SBF).append($('<option>', {value: OP.state_id}).text(OP.state));
                    $.each(OP.cities, function (k, e) {
                        $('#city_id', SBF).append($('<option>', {value: e.id}).text(e.text));
                    });
                    $('#country_id, #state_id, #city_id', SBF).trigger('change');
                },
                error: function () {
                    $('#country_id, #state_id, #city_id', SBF).empty();
                    $('#country_id', SBF).val('').prop('disabled', true);
                    $('#state_id', SBF).val('').prop('disabled', true);
                    $('#city_id', SBF).val('').prop('disabled', true);
                }
            });
    });
	
	SBF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = SBF;
        $.ajax({
            url: SBF.attr('action'),
            data: SBF.serialize(),
            beforeSend: function () {
                //$('input[type=submit]', SBF).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
                //    SBF.before(OP.msg);
                $('input[type=submit]', SBF).removeAttr('disabled', true).val('Save');
                // window.location.href = OP.url;
            }
        });
    });
	
	$('#add_accounts').on('click', function () {
		$('#account_list').fadeOut();
		$('#add_bank_accounts').fadeIn();
	});
	
	$('.delete').on('click', function () {		
		$.ajax({
            url: $(this).attr('data-url'),
            data: {'id': $(this).attr('id')},
            beforeSend: function () {
					
            },
            success: function (OP) {                
				 window.location.href = OP.url;
            }
        });
	});
	
	$('.edit').on('click', function () {		
		$('#account_list').fadeOut();
		$('#add_bank_accounts').fadeIn();				
		$.ajax({
			url: $(this).attr('data-url'),
			data: {'id': $(this).attr('id')},
			success: function (op) {
					console.log(op.data.payment_settings);
					//$('#store-bank-form').attr('action', window.location.SELLER);
					$('#store-bank-form').attr('action', window.location.API.SELLER+'setup/update-bank-info');
					$('#bank_name').val(op.data.payment_settings.bank_name);
					$('#account_holder_name').val(op.data.payment_settings.account_holder_name);
					$('#account_no').val(op.data.payment_settings.account_no);
					$('#account_type').val(op.data.payment_settings.account_type);
					$('#ifsc_code').val(op.data.payment_settings.ifsc_code);
					$('#branch').val(op.data.payment_settings.branch);
					$('#pan').val(op.data.payment_settings.pan);
					$('#address1').val(op.data.payment_settings.address1);
					$('#address2').val(op.data.payment_settings.address2);
					$('#postal_code').val(op.data.payment_settings.postal_code);
					$('#city_id').val(op.data.payment_settings.city_id);
					$('#sps_id').val(op.data.payment_settings.id);
					$('#submit').val('Update');
					$('#postal_code').trigger('change');
			},
			error: function () {
				
			}
		});		
	});
	
	var postcode = $('#postal_code').val();
    if (postcode) {
        $.ajax({
            url: window.location.BASE + 'check-pincode',
            data: {pincode: postcode},
            success: function (OP) {
                $('#country_id, #state_id, #city_id', SBF).prop('disabled', false).empty();
                $('#country_id', SBF).append($('<option>', {value: OP.country_id}).text(OP.country));
                $('#state_id', SBF).append($('<option>', {value: OP.state_id}).text(OP.state));
                $.each(OP.cities, function (k, e) {
                    $('#city_id', SBF).append($('<option>', {value: e.id}).text(e.text));
                });
                $('#city_id option[value=' + city_id + ']').attr('selected', true);
                $('#country_id, #state_id, #city_id', SBF).trigger('change');
            },
            error: function () {
                $('#country_id', SBF).val('').prop('disabled', true);
                $('#state_id', SBF).val('').prop('disabled', true);
                $('#city_id', SBF).val('').prop('disabled', true);
            }
        });
    }
	
});
