$(document).ready(function () {
    var chgpwd = $("#changetranscationpassword").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            tran_oldpassword: "required",
           tran_newpassword: {
                required: true,
                minlength: 5,
                maxlength: 20
            },
            tran_confirmpassword: {
                required: true,
                equalTo: "#tran_newpassword"
            }
        },
        messages: $val_message,
        submitHandler: function (form, event) {
         event.preventDefault();
		//alert("hii")
            if ($(form).valid()) {
                var datastring = $(form).serialize();
                $.ajax({
                    url: 'affiliate/settings/update_securitypwd',
                    type: "POST",
                    data: datastring,
                    dataType: "json",
                    beforeSend: function () {
                        $('.alert').remove();
                        $("#update_securitypwd").find('span').text($processing);
                        $('button[type="submit"]').attr('disabled', 'disabled');
						$('body').toggleClass('loaded');
                    },
                    success: function (data) {
						$('body').toggleClass('loaded');
                        $('#oldpassword_status').hide();
                        $('#changetranscationpassword').before('<div class="alert ' + data.alertclass +'"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
					    $('button[type="submit"]').attr('disabled', false);
                        $("#update_securitypwd").find('span').text($update);
                        $('input,select','#changetranscationpassword').val('');
						//$('#forgot_mess').html(data.msg);
                    },
                     error: function (jqXHR, textStatus, errorThrown) {
                            $('body').toggleClass('loaded');	
							$('button[type="submit"]').attr('disabled',false);
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,messages){
								if($('#changetranscationpassword input[name='+fld+']').siblings().hasClass('input-group')){
									$('#changetranscationpassword input[name='+fld+']').siblings().after("<div class='help-block'>"+messages+"</div>");
								}else {
									$('#changetranscationpassword input[name='+fld+']').after("<div class='help-block'>"+messages+"</div>");
								}
							});
							return false;
						},
                });
            }
        }
    });
  
  
    $("#tran_oldpassword").blur(function () {
        var oldpassword = $(this).val();
        if (!(oldpassword == '')) {
            $.ajax({
                type: 'POST',
                data: {'oldpassword': oldpassword},
                url: 'affiliate/settings/check_securitypwd',
                dataType: "json",
				beforeSend: function () {
					$('body').toggleClass('loaded');
				},
                success: function (data) {
					$('body').toggleClass('loaded');
                    if (data.status == 'ok') {						
                        $('#oldpassword_status').html('');
                        return true;
                    } else if (data.status == 'error') {
                        $('#tran_newpassword').val('');
                        $('#tran_confirmpassword').val('');
                        $('#oldpassword_status').show().html(data.msg);
                        return false;
                     }
                },
                error: function () {
                    alert($wrong_msg);
			 }
            });
        }
		
    });
	
	
	$('#changetranscationpassword').on('click',"#forgot_sec_pwd",function (e) {
		e.preventDefault();
        var datastring = '';
        $("#processing").replaceWith('');
        $.ajax({
            url: $(this).attr('href'), // Url to which the request is send
            type: "POST", // Type of request to be send, called as method
            data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
            datatype: "json",           
            beforeSend: function () {
                $("#forgot_sec_pwd").append('<p id="processing">'+$processing+'...</p>');
            },
			success: function (data) 	// A function to be called if request succeeds
            {
				alert("dsD");
			    $("#forgot_mess").css("display","block");
				$("#forgot_mess").html(data.msg);
                $("#forgot_mess").delay(15000).fadeOut();
                $("#processing").replaceWith('');
            },
            error: function () {
               /*  alert('Something went wrong'); */
                return false;
            }
        });
    });
	
});
		
	