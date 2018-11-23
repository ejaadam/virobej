// When the browser is ready...
$(function () {
    // Setup form validation on the #create_package element
    $("#commission_release").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        // Specify the validation rules
        rules: {
            username: {required: true},
            commission_type: {required: true},
            for_month: {required: true},
			for_year : { required:true}
        },
        // Specify the validation error messages
        messages: {
            username: "Please enter User name",
            commission_type: {required: "Choose Commission Type"},
            for_month: {required: "Please Choose Month"},
			for_year: {required: "Please Choose Year"}
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $('.alert').remove();
            if ($(form).valid()) {
                
				if(confirm('Are you sure, You wants release '+$("#commission_type option:selected").text()+'?')) {
                    var datastring = $(form).serialize();
					 $("#err").remove();
                    $.ajax({
                        url: "commission_release_franchisee_update", // Url to which the request is send
                        type: "POST", // Type of request to be send, called as method
                        data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
                        dataType: "json",
                        success: function (data) 	// A function to be called if request succeeds
                        {
                            var str = '';
                            if (data.status == 'ok') {
                                str = "<div class='alert alert-success'>" + data.msg + "<br></div>";
                            } else {
                                str = "<div class='alert alert-danger'>" + data.msg + "<br></div>";
                            }
                            $("#uname_check").show();
							$("#user_details").hide();
                           
                            $('#commission_release').before(str);
                            $("#commission_release").trigger('reset');
                            $('#currency').empty();
							$('#fr_uname_check').parents('.form-group').show();
                            $('#user_avail_status').html('');

                        },
                        error: function () {
                            alert('Something went wrong');
                            return false;
                        }
                    });
					}
                }
            }       
    });
	$("#commission_type").change(function(){
		 $("#for_month").html('<option value =""> -Select Month- </option>');
		 $("#for_year").html('<option value =""> -Select Year- </option>');
		 $("#err").remove();
		if ($("#commission_type").val() == '') {
			 $("#Submit").attr("disabled", "disabled");
        	alert('Please choose commission type');
			return false;       
    	} else {										  
			$.ajax({
                url: $('#commission_type').data('url'),
                type: "POST",
                data: {user_id: $("#user_id").val(), commission_type : $("#commission_type").val()},
                dataType: "json",
                success: function (data) {
                    //data = JSON.parse(data);
                 if (data.status == 'ok') {    
				 		$("#Submit").removeAttr("disabled");	
                        $("#for_month").html(data.for_month);
						$("#for_year").html(data.for_year);
                        return true;
                    } else {       
						$("#Submit").after(data.msg);
                        $("#Submit").attr("disabled", "disabled");						
                        return false;
                    }
                },
                error: function () {
                    alert('Something went wrong');
                    return false;
                }
            });
		}
	});

});
/*------Username Checking Function--------*/
var ajaxUrl = "";
var avi_bal = 0;
var ubalance = new Array();
var rec_data = '';
$("#username").keyup(function () {
    $("#uname_check").show();
    $("#user_avail_status").hide();   
	$("#user_details").hide();
    $('.alert').remove();
	$("#for_month").html('<option value =""> -Select Month- </option>');
	$("#for_year").html('<option value =""> -Select Year- </option>');	
	 $("#err").remove();
});

$("#fr_uname_check").click(function () {		
	$("#for_month").html('<option value =""> -Select Month- </option>');
	$("#for_year").html('<option value =""> -Select Year- </option>');	
	$("#err").remove();
    if ($("#username").val() == '') {
        $("#user_avail_status").html('Please Enter the username');
        $("#user_avail_status").addClass("help-block");
       
    } else {        
		$("#fr_uname_check").val('Processing..').attr("disabled", "disabled");
        var username = $("#username").val();
        if (username) {           
            $.ajax({
                url: $('#fr_uname_check').data('url'),
                type: "POST",
                data: {username: $("#username").val()},
                dataType: "json",
                success: function (data) {
                    //data = JSON.parse(data);
                   
					if (data.status == 'ok') {
                       $("#fr_uname_check").parents('.form-group').hide();
						$("#user_details").show();
                        
						$("#rec_email").val(data.email);
						$("#rec_name").val(data.fullname);
						$("#franchisee_type").val(data.franchisee_type);
						$("#franchisee_type_name").val(data.franchisee_type_name);
                        $("#user_avail_status").text(data.msg).show();
                        $("#user_avail_status").removeClass("help-block");
                        $("#Submit").removeAttr("disabled");
                        $("#user_id").val(data.user_id);
                        $("#ewallet_id").val('');
                       
						$("#fr_uname_check").val('Check').removeAttr("disabled");
                        /*var str = "";
                         $.each(bal,function(index,ele){
                         str = str+"<option value='"+ele.currency_id+"'>"+ele.code+"</option>";
                         ubalance[ele.currency_id] = ele.current_balance;
                         });
                         $("#currency").html("<option>--Select Currency--</option>");
                         $('#currency').append(str);*/
                        return true;
                    } else {
                        $("#user_avail_status").text(data.msg).show();
                        $("#user_avail_status").addClass("help-block");
                        $("#Submit").attr("disabled", "disabled");
						$("#fr_uname_check").val('Check').removeAttr("disabled");
                        return false;
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

