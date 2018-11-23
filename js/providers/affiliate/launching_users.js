$(function () {
	$('body').on('change', '#country', function (e) {		
		var country = $(this).val();
		$('#country_code').text(country.split(':')[1])
	});
	$('body').on('click', '#frmSubmitBtn', function (e) {		
		$('#launchingFrm').submit();
	});
    $('body').on('submit', '#launchingFrm', function (e) {		
        e.preventDefault();			
        var frmObj = $(this);	
        CURFORM = frmObj;
        $.ajax({
            type: 'POST',
            url: frmObj.attr('action'),
            data: frmObj.serialize(),
            dataType: 'json',
            error: function (jqXHR, textStatus, errorThrown) {			
	
                $('input.form-control', frmObj).prop('disabled', false);
                $('button[type=submit]', frmObj).prop('disabled', false);
                frmObj.reset;
            },
            beforeSend: function () {
                $('input.form-control', frmObj).prop('disabled', true);
                $('button[type=submit]', frmObj).prop('disabled', true);
            },
            success: function (op) {		
            }
        });
    });	
	$("#submitBtn").click(function() {   
		$('#regmodal').modal("show")
	});	
});