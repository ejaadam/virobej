$(document).ready(function () {
    /*** Change Password Validate ***/
    $('#change_email').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        // Specify the validation rules
        rules: {
            new_email: {
                required: true,
                email: true
            },            
        },
        // Specify the validation error messages
        messages: {
            new_email: {
                required: 'Please provide a new E-mail ID',
                email: 'Provide a valid E-mail ID'
            },            
        },
        submitHandler: function (form, event) {
            if ($(form).valid()) {
                var datastring = $(form).serialize();
				CURFORM = $('#change_email');
                $.ajax({
                    url: $(form).attr('action'), // Url to which the request is send
                    // Type of request to be send, called as method
                    data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
                    beforeSend: function () {
                        $('.alert').remove();
                    },
                    success: function (data) 		// A function to be called if request succeeds
                    {
                        if (data.result == 'OK')
                        {
                            $('#change_email').before('<div class="alert ' + data.alertclass + '"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');                           
                            $('#new_email').val('');
							window.location.assign(window.location.BASE + 'seller/logout');
                        }
                        else
                        {
                            $('#change_email').before('<div class="alert ' + data.alertclass + '"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
                        }
                    },
                    error: function () {
                        //alert('Something went wrong');
                    }
                })
            }
        }
    });       /* end */
});
