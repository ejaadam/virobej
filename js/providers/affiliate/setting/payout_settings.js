$(document).ready(function () {
    $.validator.addMethod("alpha", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z ]+$/);
    }, $characters);
    $.validator.addMethod("alphanumeric", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-z0-9A-Z]+$/);
    }, $characters_number);
	
	$("#new_bank").click(function(){
		$("#new_bank_panel").removeClass("hidden");
		$("#new_bank_panel").show();
	});
	$("#cancelbtn").click(function(){
		$("#new_bank_panel").hide();
	});
	
	$('.add').click(function(){
		btn = $(this);
		from_id = btn.closest('form').attr('id');
		//alert( from_id );return false;
		$('#'+from_id).validate(
		{
			errorElement: 'div',
			errorClass: 'help-block', focusInvalid: false,
			rules: 
			{
				 currency_id: {
				 required: true
				 },
				 nick_name: {
				 required: true,
				 },
				 account_name: {
				 required: true,
				 },
				 bank_account_type: {
				 required: true
				 },
				 account_no: {
				 required: true,
				 alphanumeric: true,
				 },
				 bank_name: {
				 required: true
				 },
				 bank_branch: {
				 required: true
				 },
				 ifsccode: {
				 required: true,
				 alphanumeric: true,
				 minlength: 11
				 },
				 status: {
				 required: true,
				 },
				 tpin:{
				 required: true
				 }
			 },
				messages: $bank_Payment,
				submitHandler: function (form, event) 
				{
					event.preventDefault();
					if ($(form).valid()) { 
						$.ajax(
						{
							url: "account/settings/checkTpin",
							type: "POST",
							data: {tpin: $('#' + from_id + '_tpin').val()},
							dataType: "json",
							beforeSend: function () { 
							  $(".alert-success").css("display", "none");
							  $(".alert-danger").css("display", "none");
							  loadPreloader();
							},
							success: function (data) {						
							   if (data.status == 200) 
								{ 
										$.ajax(
										{ 
											url: $(form).attr('action'),
											type: "POST",
											data: $(form).serialize(),
											dataType: "json",
											success: function (res) { 
												removePreloader();
												
												$(form).prepend(res.msg);		
												$('#' + from_id + '_tpin').val('');
													setTimeout(function () {
														$('.bank_transferfrmad').each(function(){ 
															this.reset();
														});
														$("#new_bank_panel").addClass("hidden");
															//$(".bank").accordion("refresh"); 
															//var newDiv = $("#acdiv").last().html();
															
															//$("#acdiv").siblings(":last").append(newDiv)
															//$("#acdiv").siblings(":last").accordion("refresh");        
														
													}, 4000);
													$('.alert').delay(5000).fadeOut(1000);		
													return false;
											},
											error: function (res) {
												alert($wrong_msg);
											return false;
											}
										});
								}		else {
											alert($ecsp);
											removePreloader();
											$(function() {
											$('#' + from_id + '_tpin').focus();
											$('#' + from_id + '_tpin').val('');
											});
										}
							}
						});
				}
			}
		});
	});
	
	$('.cashfree').click(function(){
		btn = $(this);
		from_id = btn.closest('form').attr('id');
		$('#'+from_id).validate(
	{
		errorElement: 'div',
		errorClass: 'help-block', focusInvalid: false,
		rules: 
		{
			 currency_id: {
			 required: true
			 },
			 cashfree_account_id: {
			 required: true,
			 },
			 account_name: {
			 required: true,
			 },
			 
			 status: {
			 required: true,
			 },
			 tpin:{
			 required: true
			 }
		 },
			messages: $cashfree_Payment,
			submitHandler: function (form, event) 
			{
				event.preventDefault();
				if ($(form).valid()) {
					$.ajax(
					{
						url: "account/settings/checkTpin",
						type: "POST",
						data: {tpin: $('#' + from_id + '_tpin').val()},
						beforeSend: function () {
						  $(".alert-success").css("display", "none");
						  $(".alert-danger").css("display", "none");
						  loadPreloader();
						},
						success: function (data) {						
						   if (data.status == 200) 
							{ 
									$.ajax(
									{ 
										url: $(form).attr('action'),
										type: "POST",
										data: $(form).serialize(),
										dataType: "json",
										success: function (res) { 
											removePreloader();
											$(form).prepend(res.msg);
											$('.alert').delay(5000).fadeOut(1000);
											$('#' + from_id + '_tpin').focus();											
											$('#' + from_id + '_tpin').val('');	
											return false;
										},
										error: function (res) {
											alert($wrong_msg);
										return false;
										}
									});
							}		else {
										alert($ecsp);
										removePreloader();
										
										$('#' + from_id + '_tpin').focus();
										$('#' + from_id + '_tpin').val('');
										
									}
						}
					});
			}
		}
	});
});
	$('.paytm').click(function(){
		btn = $(this);
		from_id = btn.closest('form').attr('id');
		$('#'+from_id).validate(
	{
		errorElement: 'div',
		errorClass: 'help-block', focusInvalid: false,
		rules: 
		{
			 currency_id: {
			 required: true
			 },
			 paytm_account_id: {
			 required: true,
			 },
			 account_name: {
			 required: true,
			 },
			 
			 status: {
			 required: true,
			 },
			 tpin:{
			 required: true
			 }
		 },
			messages: $paytm_Payment,
			submitHandler: function (form, event) 
			{
				event.preventDefault();
				if ($(form).valid()) {
					$.ajax(
					{
						url: "account/settings/checkTpin",
						type: "POST",
						data: {tpin: $('#' + from_id + '_tpin').val()},
						dataType: "json",
						beforeSend: function () {
						  $(".alert-success").css("display", "none");
						  $(".alert-danger").css("display", "none");
						  loadPreloader();
						},
						success: function (data) {						
						   if (data.status == 200) 
							{ 
									$.ajax(
									{ 
										url: $(form).attr('action'),
										type: "POST",
										data: $(form).serialize(),
										dataType: "json",
										success: function (res) { 
											removePreloader();
											$(form).prepend(res.msg);
											$('.alert').delay(5000).fadeOut(1000);	
											$('#' + from_id + '_tpin').focus();											
											$('#' + from_id + '_tpin').val('');	
											return false;
										},
										error: function (res) {
											alert($wrong_msg);
										return false;
										}
									});
							}		else {
										alert($ecsp);
										removePreloader();
										$(function() {
										$('#' + from_id + '_tpin').focus();
										$('#' + from_id + '_tpin').val('');
										});
									}
						}
					});
			}
		}
	});
});
  
});
    

