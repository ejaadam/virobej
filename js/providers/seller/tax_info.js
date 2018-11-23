$(document).ready(function () {
  	$('#tax_info').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#tax_info');
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
	
	
	// GSTIN Information 
	$('#gstin_form').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#gstin_form');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			
			success: function (op) {
				 window.location.reload();
			},
			error: function (jqXHR, exception, op) {
				if (jqXHR.status == 406) {
					console.log(jqXHR.responseJSON.msgs);
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
				} 	
			},
        });
    });
	
	$('#id_proof_details').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#id_proof_details');
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


   
   
	$('#postal_code').on('change', function () {
        var pincode = $('#postal_code').val();
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
	
	 var postcode = $('#postal_code').val();
	 var country_id = $('#country_id').val(); 
	
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
	
	$('.gstin').on('change', function () { //Do your code
  
        check = $('.gstin:checked').val();
        if (parseInt(check) == 0) {
			
            $('#gstin_form #gstin_image_block').hide();
            $('#gstin_form #tan_image_block').hide();
        } else {
            $('#gstin_form #gstin_image_block').show();            
            $('#gstin_form #tan_image_block').show();            
        }
    });
	
});
var loadFile = function(event) {	
	var output = document.getElementById('profile');
	output.src = URL.createObjectURL(event.target.files[0]);
};
var loadFile2 = function(event) {	
	var output = document.getElementById('tan_image').value;
	//alert(output);
};


var loadFile4 = function(event) {	
	var output = document.getElementById('proof');
	output.src = URL.createObjectURL(event.target.files[0]);
};

    $('#id_proof_details').on('change', '#id_image', function () {
        var st = $('#id_image').checkFileFormat($(this).data('formatallowd'), $(this).data('error'));
        if (st) {
            $('button#' + $(this).attr('id') + '_btn').removeClass('hidden');
        }
        else {
            if (! $('button#' + $(this).attr('id') + '_btn').hasClass('hidden')) {
                $('button#' + $(this).attr('id') + '_btn').addClass('hidden');
            }
        }
    })
$('#id_proof_details').on('change', '#address_image', function () {
        var st = $('#address_image').checkFileFormat($(this).data('formatallowd'), $(this).data('error'));
        if (st) {
            $('button#' + $(this).attr('id') + '_btn').removeClass('hidden');
        }
        else {
            if (! $('button#' + $(this).attr('id') + '_btn').hasClass('hidden')) {
                $('button#' + $(this).attr('id') + '_btn').addClass('hidden');
            }
        }
    })
	
	
	$('#tax_info').on('change', '#pan_card_upload', function () {
        var st = $('#pan_card_upload').checkFileFormat($(this).data('formatallowd'), $(this).data('error'));
        if (st) {
            $('button#' + $(this).attr('id') + '_btn').removeClass('hidden');
        }
        else {
            if (! $('button#' + $(this).attr('id') + '_btn').hasClass('hidden')) {
                $('button#' + $(this).attr('id') + '_btn').addClass('hidden');
            }
        }
    })
	
	$('#gstin_form').on('change', '#gstin_image', function () {
        var st = $('#gstin_image').checkFileFormat($(this).data('formatallowd'), $(this).data('error'));
        if (st) {
            $('button#' + $(this).attr('id') + '_btn').removeClass('hidden');
        }
        else {
            if (! $('button#' + $(this).attr('id') + '_btn').hasClass('hidden')) {
                $('button#' + $(this).attr('id') + '_btn').addClass('hidden');
            }
        }
    })
	$('#gstin_form').on('change', '#tan_image', function () {
        var st = $('#tan_image').checkFileFormat($(this).data('formatallowd'), $(this).data('error'));
	
        if (st) {
            $('button#' + $(this).attr('id') + '_btn').removeClass('hidden');
        }
        else {
            if (! $('button#' + $(this).attr('id') + '_btn').hasClass('hidden')) {
				$(".fileupload-preview").text('');
                $('button#' + $(this).attr('id') + '_btn').addClass('hidden');
            }
        }
    })
	
function alpha_checkwithspace(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || code == 32 || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
	
$.fn.initTransferFrm = function(){		
									
		$('#tax_info input[type=text],#tax_info input[type=file],#tax_info select,#id_proof_details input[type=text],#id_proof_details input[type=file],#id_proof_details select').val('');

	}