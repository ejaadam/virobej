$(document).ready(function () {
    var chgpwd = $("#changepassword").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
       rules: {
            oldpassword: "required",
            newpassword: {
				
                required: true,
                minlength: 5,
                maxlength: 20
            },
            confirmpassword: {
                required: true,
                equalTo: "#newpassword"
            }
        },
        messages: $val_message,
        submitHandler: function (form, event) {
            event.preventDefault();
			//alert("hii")
            if ($(form).valid()) {
                var datastring = $(form).serialize();
                $.ajax({
                    url: "affiliate/settings/update_password",
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {
                        $('.alert').remove();
                        $("#update_password").find('span').text($processing);
                        $('button[type="submit"]').attr('disabled', 'disabled');
						$('body').toggleClass('loaded');
                    },
                    success: function (data) {
						$('body').toggleClass('loaded');
						$('button[type="submit"]').attr('disabled',false);
                        $('#oldpassword_status').hide();
                        $('#changepassword').before('<div class="alert ' + data.alertclass + '"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
                        $('button[type="submit"]').attr('disabled', false);
                        $("#update_password").find('span').text($update);
                        $('#changepassword').trigger('reset');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                            $('body').toggleClass('loaded');	
							$('button[type="submit"]').attr('disabled',false);
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,messages){
								if($('#changepassword input[name='+fld+']').siblings().hasClass('input-group')){
									$('#changepassword input[name='+fld+']').siblings().after("<div class='help-block'>"+messages+"</div>");
								}else {
									$('#changepassword input[name='+fld+']').after("<div class='help-block'>"+messages+"</div>");
								}
							});
							return false;
						},
                });
            }
        }
    });
        
 $("#oldpassword").blur(function () {
        var oldpassword = $(this).val();
        if (!(oldpassword == '')) {
            $.ajax({
                type: 'POST',
                data: {'oldpassword': oldpassword},
                url: 'affiliate/settings/password_check',
                dataType: "json",
				beforeSend: function () {
					$('body').toggleClass('loaded');
				},
                success: function (data) {
					$('body').toggleClass('loaded');
                    if (data.status == 'ok') {
                        $('#oldpassword_status').html('');
                        return true;
                    } else if (data.status == 'error') 
					{
                        $('#newpassword').val('');
                        $('#confirmpassword').val('');
                        $('#oldpassword_status').show().html(data.msg);
                        return false;
                    }
                },
				error: function (jqXHR, textStatus, errorThrown) {
                            $('body').toggleClass('loaded');					
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,msg){
								if($('#changepassword input[name='+fld+']').siblings().hasClass('input-group')){
									$('#changepassword input[name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
								}else {
									$('#changepassword input[name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
								}
								return false;
							});
						},
                /* error: function () {
					$('body').toggleClass('loaded');
					<div for="newpassword" class="help-block">$wrong_msg</div>
                    alert();
			 } */
            });
        }
    });
});
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
  