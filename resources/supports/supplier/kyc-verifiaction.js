$(document).ready(function () {	
    var KV = $('#kyc-verifiaction');
    function readURL(input, ele) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#' + ele.attr('id') + '_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    KV.on('change', 'input[type="file"]', function (e) {
        readURL(this, $(this));
    });
    KV.on('submit', function (e) {
        e.preventDefault();
        $('.alert').remove();
        var formdata = new FormData();
        $.each($('input[type="file"]', KV), function (k, e) {
            formdata.append(e.name, e.files[0]);
        });
        $.each(KV.serializeArray(), function (k, e) {
            formdata.append(e.name, e.value);
        });
        $.ajax({
            url: KV.attr('action'),
            processData: false,
            contentType: false,
            data: formdata,
            beforeSend: function () {
                $('input[type="submit"]', KV).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
                $('input[type="submit"]', KV).removeAttr('disabled', true).val('Save');
                KV.before(OP.msg);
            },
            error: function (jqXhr) {
                $('input[type="submit"]', KV).removeAttr('disabled', true).val('Save');
                if (jqXhr.status == 422) {
                    var data = jqXhr.responseJSON;
                    KV.appendLaravelError(data.error);
                }
                else {
                    //alert('Something went wrong');
                }
            }
        });
    });
	
	$('#pan_card_no, #vat_no, #cst_no, #vat_no, #gstin', KV).on('keypress', function (e) {
		var code = e.charCode ? e.charCode : e.keyCode;
		if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
			return true;
		}
		return false;
	});
	
	$('#pan_card_name, #auth_person_name', KV).on('keypress', function (e) {
		var code = e.charCode ? e.charCode : e.keyCode;
		if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 116 || code == 32 || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
			return true;
		}
		return false;
	});	
	
});
