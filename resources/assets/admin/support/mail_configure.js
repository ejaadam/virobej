$(document).ready(function(){
	$('.receive_type').change(function(){
		var receive_type = $('.receive_type:checked').val();
		if(receive_type == 2){
			$('#configure_tab').css('display','block');
		}else{
			$('#configure_tab').css('display','none');
		}
	})
	
	$('#submit').click(function(){
		if($('.receive_type:checked').length <= 0){
			alert("Please select mail receive type");
			return false;
		}else{
			return true;
		}
	})
	
	/* Validate */
		$("#configure_form").validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            // Specify the validation rules
            rules: {
				receiver_email:'required',
				api:'required',
				 username: {
					minlength: 3,
					maxlength : 50
				},
				port: {
					digits:true
				},
				
			
            },
            // Specify the validation error messages
            messages: {
				receiver_email:{
					required : 'Please Enter receiver email'
				},
				api:{
					required : 'Please select API'
				},
				port:{
					required : 'Please Enter port',
					digits:'Please enter valid port'
				},
                username: {
                    required: "Please Enter username.",
                    minlength: "First name must be greater than 3 characters",
                    maxlength: "First name must be less than 16 characters"
                },
				password: {
                     required: "Please enter the Password",
                    minlength: "Password must be greater than 5 characters",
                    maxlength: "Password must be less than 20 characters"
                },
				api_key:{
					required : 'Please Enter api key'
				},
				api_password: {
                    required: "Please Enter Last Name .",
                    minlength: "User name must be greater than 3 characters",
                    maxlength: "Last name nust be less than 15 characters"
                }
				
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                   var datastring = $(form).serialize();
				   var user_name  = $('#uname').val();
                    $.ajax({
                        url: $(form).attr('action'), 
                        type: "POST", 
                        data: datastring, 
                        dataType: "json",
						beforeSend:function(){
							$('#submit').text("Processing..");
						},
                        success: function (data) 	
                        {
							 $('#submit').text("Save");
                            if(data.status == 'ok'){
							  $('#success_mess').html(data.msg);
							}else{
								$('#success_mess').html(data.msg);
							}
                        },
                        error: function () {
                            alert('Something went wrong');
                            return false;
                        }
                    });
                }
            }
        });
		$('.receive_type').trigger('change');
})