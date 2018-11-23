$(document).ready(function () {
    var SUF = $('#supplier-update-form');
    SUF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = SUF;
        $.ajax({
            url: SUF.attr('action'),
            data: SUF.serialize(),
            beforeSend: function () {
                $('input[type="submit"]', SUF).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
                $('input[type="submit"]', SUF).removeAttr('disabled', true).val('Save');
                //    window.location.href = OP.url;
            },
            error: function () {
                $('input[type="submit"]', SUF).removeAttr('disabled', true).val('Save');
            }
        });
    });
	
	

    $('#postal_code', SUF).on('change', function () {
        var pincode = $('#postal_code', SUF).val();
        if (pincode != '' && pincode != null) {
            $.ajax({
                url: window.location.BASE + 'check-pincode',
                data: {pincode: pincode},
                success: function (OP) {
                    $('#country_id', SUF).prop("disabled", false);
                    $('#state_id', SUF).prop("disabled", false);
                    $('#city_id', SUF).prop("disabled", false);
                    $('#country_id, #state_id, #city_id', SUF).empty();
                    $('#country_id', SUF).append($('<option>', {value: OP.country_id}).text(OP.country)).trigger('change');
                    $('#state_id', SUF).append($('<option>', {value: OP.state_id}).text(OP.state)).trigger('change');
                    $.each(OP.cities, function (k, e) {
                        $('#city_id', SUF).append($('<option>', {value: e.id}).text(e.text));
                    });					
					$("#city_id option:last").attr("selected", true);
                },
                error: function () {
                    $('#country_id, #state_id, #city_id', SUF).empty();
                    $('#country_id', SUF).val('').prop("disabled", true);
                    $('#state_id', SUF).val('').prop("disabled", true);
                    $('#city_id', SUF).val('').prop("disabled", true);
                }
            });
        }
    });
    var postcode = $('#postal_code').val();
    if (postcode) {
        $.ajax({
            url: window.location.BASE + 'check-pincode',
            data: {pincode: postcode},
            success: function (OP) {
                $('#country_id', SUF).prop("disabled", false);
                $('#state_id', SUF).prop("disabled", false);
                $('#city_id', SUF).prop("disabled", false);

                $('#country_id, #state_id, #city_id', SUF).empty();
                $('#country_id', SUF).append($('<option>', {value: OP.country_id}).text(OP.country));
                $('#state_id', SUF).append($('<option>', {value: OP.state_id}).text(OP.state));
                $.each(OP.cities, function (k, e) {
                    $('#city_id', SUF).append($('<option>', {value: e.id}).text(e.text));
                });
                console.log(city_id);
				$("#city_id option[value=" + city_id + "]").attr("selected", true);
            },
            error: function () {
                $('#country_id', SUF).val('').prop("disabled", true);
                $('#state_id', SUF).val('').prop("disabled", true);
                $('#city_id', SUF).val('').prop("disabled", true);
            }
        });
    }

    $('#same-as-given').on('change', function () {
        var s = $('#same-as-given').is(':checked');
        $('.same-as-given-field', SUF).each(function (i, e) {
            $(e).val(s ? $(e).data('selected') : '');
            $('[for="' + $(e).attr('name') + '"]', SUF).remove();
        });
        $('#postal_code', SUF).trigger('change');
    });

    $('#firstname, #lastname', SUF).on('keypress', function (e) {
        var code = e.charCode ? e.charCode : e.keyCode;
        if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 116 || code == 32 || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
            return true;
        }
        return false;
    });

    $('#mobile_no, #landline_no', SUF).on('keypress', function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
            return false;
        }
        return true;
    });
	
	function makeInitialTextReadOnly(input) {
        var readOnlyLength = input.value.length;
        input.addEventListener('keydown', function(event) {
            var which = event.which;
            if (((which == 8) && (input.selectionStart <= readOnlyLength))
                    || ((which == 46) && (input.selectionStart < readOnlyLength))) {
                event.preventDefault();
            }
        });
        input.addEventListener('keypress', function(event) {
            var which = event.which;
            if ((event.which != 0) && (input.selectionStart < readOnlyLength)) {
                event.preventDefault();
            }
        });
        input.addEventListener('cut', function(event) {
    	    if (input.selectionStart < readOnlyLength) {
        	    event.preventDefault();
            }
        });
        input.addEventListener('paste', function(event) {
    	    if (input.selectionStart < readOnlyLength) {
        	    event.preventDefault();
            }
        });
    }
	
	 makeInitialTextReadOnly(document.getElementById('website'));
});
