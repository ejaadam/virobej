// When the browser is ready...
$(document).ready(function () {
    $("#mobile").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#errmsg").html("Enter Digits Only").show().fadeOut("slow");
            return false;
        }
    });
    /*------Referrel User Checking Function--------*/
    $("#referral_name").blur(function () {
        referrel_user_check();
    });
    function referrel_user_check() {
        var referral_username = $("#referral_name").val();
        if (referral_username) {
            $.ajax({
                url: $("#referral_name").data('url'),
                type: "POST",
                data: {referral_username: $("#referral_name").val()},
                datatype: "json",
                success: function (data) {
                    data = JSON.parse(data);
                    if (data.status == 'ok') {
                        $("#referral_id").val(data.referral_id);
                        $("#referral_name").val($("#referral_name").val());
                        $("#referral_user_avail_status").text(data.msg);
                        $("#referral_user_avail_status").removeClass("help-block");
                    } else if (data.status == 'error') {
                        $("#referral_user_avail_status").text(data.msg);
                        $("#referral_user_avail_status").addClass("help-block");
                    }
                }
            });
        }
    }
    $("#save_user").click(function () {
        var check = 0;
        // Setup form validation on the #create_user element
        $("#create_user").validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            // Specify the validation rules
            rules: {
            
			 company_name: {
                required: true,              
            },
			
			 company_address: {
                required: true,             
            },
			 first_name: {
                required: true,
                alphanumeric: true,
                minlength: 3,
                maxlength: 15
            },
				
            first_name: {
                required: true,
                alphanumeric: true,
                minlength: 3,
                maxlength: 15
            },
			last_name: {
                required: true,
                alphanumeric: true,
                minlength: 3,
                maxlength: 15
            },
			uname: {
                required: true,
                alphanumeric: true,
                minlength: 6,
                maxlength: 15
            },
                email: {
                    required: true,
                    email: true,
                },
                mobile: {
                    digits: true,
                    minlength: 9,
                    maxlength: 16,
                },
           	 password: {
                required: true,
                minlength: 5,
                maxlength: 20
             },
			 tpin: {
                required: true,
                minlength: 5,
                maxlength: 20
             },
                currency: "required",
                state : "required",
                country: "required",
                zipcode: {
					required: true,
			         maxlength: 8,
			         minlength: 6
            	},
				address : "required",
				city : "required"
            },
            // Specify the validation error messages
            messages: {
               	company_name: {
                    required: "Please Enter Support Center Name",                
                },
				company_address: {
                    required: "Please Enter Support Center Address",                  
                },
             	first_name: {
                    required: "Please Enter First Name .",
                   minlength: "First name must be greater then 3 characters",
                    maxlength: "First name must be less then 16 characters"
                },
				last_name: {
                    required: "Please Enter Last Name .",
                    minlength: "Last name must be greater then 3 characters",
                    maxlength: "Last name nust be less then 16 characters"
                },
				uname: {
                     required: "Please Enter User Name.",
                    minlength: "User name must be greater then 6 characters",
                    maxlength: "User name must be less then 16 characters"
                },
                email: {
                    required: "Please provide a valid email.",
                    email: "Please provide a valid email."
                },
                mobile: {
                    digits: "Invalid phone number !! Only numbers allowed",
                    required: "Please provide a valid Phone Number .",
                    maxlength: "Please provide a valid Phone Number",
                    minlength: "Please provide a valid Phone Number"
                },
            	password: {
                     required: "Please enter the Password",
                    minlength: "Password must be greater then 5 characters",
                    maxlength: "Password must be less then 20 characters"
                },
				tpin: {
                    required: "Please enter Security Password",
                    minlength: "Secutiry Password must be greater then 5 characters",
                    maxlength: "Secutiry Password be less then 20 characters"
                },
                currency: "Please select currency",
                country: "Please select country",
               state : "Please select state",
                zipcode: {
					required: "Please enter zip code",
					minlength: "Please Provide Valide zipcode",
                    maxlength: "Please Provide Valide zipcode"
				},
				address : "Please enter address",
				city : "Please enter city"
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                    var user_role = $("#user_role").val();
                    if (user_role != 1) {
                        var refname = $("#referral_name").val();
                        var refid = $("#referral_id").val();
                        if (refname == '' || refid == '' || refid == 0) {
                            alert("Please enter the Valid Referral User Name.");
                            return false;
                        }
                    }
                    var datastring = $(form).serialize();
                    $.ajax({
                        url: $(form).attr('action') + "/franchisee", // Url to which the request is send
                        type: "POST", // Type of request to be send, called as method
                        data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
                        datatype: "json",
                        success: function (data) 	// A function to be called if request succeeds
                        {
                            data = JSON.parse(data);
                            $(".box-body").html(data.msg);
                        },
                        error: function () {
                            alert('Something went wrong');
                            return false;
                        }
                    });
                }
            }
        });
    });
    $(".tabval").each(function () {
        $(this).click(function () {
            $("#user_role").val($(this).val());
        });
    });
	$("#country").change(function () {
        var country_id = $("#country").val();
		$("#city").val('');
		$("#city option").css('display','none');
        $("#state").html('<option value="">Loading...</option>');
		$.post("get_states",{country_id :country_id },function(data){		
				var stateOpt = '<option value="">--Select State--</option>';																   
				stateOpt += data.statelist;	
				$('#state').html(stateOpt);
		},'json');
        $("#phonecode").val($("#phonecode #c_" + country_id).text());
    });
	
	$("#state").change(function () {
        var state_id = $("#state").val();
		if(state_id !=''){
			$("#city").val('');
			$("#city option").attr('hidden', true).attr('disabled', true);
			$("#city option").css('display','none');
			$('#city option[value=""]').removeAttr('hidden').removeAttr('disabled').attr('selected', true);
			$("#city .c_" + state_id).removeAttr('hidden').removeAttr('disabled').css('display','block');
		}else{
			$("#city").val('');
			$("#city option").css('display','none');
		}
    });
});
