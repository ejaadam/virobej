$(document).ready(function () {
						
    var DT = $('#manage_user_list').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
        columns: [
            {
                data: 'signedup_on',
                name: 'signedup_on',
            },
			{
                data: 'uname',
                name: 'uname',
				 render: function (data, type, row, meta) {
                var txt = '';
				var txt='<span class="text-muted">Uname: </span><b>'+row.uname+'</b><br><span class="text-muted">Full Name: </span><b>'+row.fullname+'</b><br><span class="text-muted">Email: </span><b>'+row.email+'</b><br><span class="text-muted">Mobile No: </span><b>'+row.mobile+'</b>';
				return txt;
				 }
            },
				{
					data: 'country_name',
					name: 'country_name',
				},
				{
						"data": "referred_by",
						"name": "referred_by"
				 },
                {
                    "data": "referrer_group",
                    "name": "referrer_group"
                },
				{
                    "data": "rootuser",
                    "name": "rootuser"
                },
			{
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
					var verify = ''
					var active = ''
                 
                     active = (row.status == 1) ? $('<span>', {class: 'label label-success'}).text('Active')[0].outerHTML : $('<span>', {class: 'label label-danger'}).text('Inactive')[0].outerHTML;
					 return  active;
                }
            },
			{
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            }
        ]
    });
    $('#search').click(function () {
        DT.fnDraw();
    });
});
	/* View and Edit Details */
	    $("#manage_user_list").on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (op) {
            if (op.details != undefined && op.details != null) {
                 $('#users-list-panel,#edit_details').fadeOut('fast', function () {
                    $('#view_details').fadeIn('slow');
                });
                /*$('#member_img', '#view_details').attr('src', document.location.BASE + op.details.profile_image); */
                $('#doj_label', '#view_details').text(op.details.signedup_on);
                $('#uname_label', '#view_details').text(op.details.uname + " (" + op.details.fullname + ")");
                $('#email_label', '#view_details').text(op.details.email);
                $('#doa_label', '#view_details').text(op.details.activated_on);
                $('#mobile_label', '#view_details').text(op.details.mobile);
                $('#gender_label', '#view_details').text(op.details.dob);
                $('#reffered_by', '#view_details').text(op.details.referred_by);

              /*  $('#status_label', '#view_details').html('<span class="label label-success">' + op.details.status_name + '</span>');  */
            }
            else if (op.edit != undefined && op.edit != null) {
	
                  $('#users-list-panel,#view_details').fadeOut('fast', function () {
                    $('#edit_details').fadeIn('slow');
                });
                 $('#uname', '#user_updatefrm').val(op.edit.uname);
                $('#first_name', '#user_updatefrm').val(op.edit.firstname);
                $('#last_name', '#user_updatefrm').val(op.edit.lastname);
                $('#dob', '#user_updatefrm').val(op.edit.dob);
            }
            else {
               	$("#manage_user_list").dataTable().fnDraw();
            }
        });
    });
	
	/* For Change Password  */
    $('#manage_user_list').on('click', '.change_password', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#update_member_pwdfrm').trigger('click');
        $('#uname_label').text(CurEle.data('account_id'));
        $('#fullname_label').text(CurEle.data('fullname'));
		$('#users-list-panel').hide();
		 $('#change_Member_pwd').show();
    });
	
	$('#manage_user_list').on('click', '.change_pin', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#update_member_pinfrm').trigger('click');
        $('#uname_pin').text(CurEle.data('account_id'));
        $('#fullname_pin').text(CurEle.data('fullname'));
		$('#users-list-panel').hide();
		 $('#change_Member_security_pin').show(); 
    });
$('#manage_user_list').on('click', '.edit_email', function (e) {
        e.preventDefault();
        var CurEle = $(this);
	    $('#old_emails').text(CurEle.data('email'));
	    $('#old_email').text(CurEle.data('email'));
	    var value=CurEle.data('uname');
		$('#user_value').text(" ("+value+")");
	    $('#user_name').val(value);
		$('#users-list-panel').hide();
		 $('#change_email').show();
    });
		/* For Change Mobile */
	  $('#manage_user_list').on('click', '.edit_mobile', function (e) {
        e.preventDefault();
		var CurEle = $(this);
	    $('#old_mobile').text(CurEle.data('mobile'));
	    $('#old_no').val(CurEle.data('mobile'));
		var value=CurEle.data('uname');
		var value1=CurEle.data('uname1');
		$('#user_mobile_val').text(" ("+value1+")");
	    $('#uname_mobile').val(value);
		$('#users-list-panel').hide();
		 $('#change_mobile').show();
    });
	    /* User Block Status */
 	   $('#manage_user_list').on('click', '.block_status', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            data: {uname: CurEle.attr('rel'), status: CurEle.data('status'), id: CurEle.data('account_id')},
            url: CurEle.attr('href'),
            type: "POST",
            dataType: 'JSON',
            beforeSend: function () {
                $('body').toggleClass('loaded');
                $('.alert,div.help-block').remove();
            },
            success: function (res) {
                $('body').toggleClass('loaded');
               
                if (res.status == 200) {
				$("#manage_user_list").dataTable().fnDraw();
                    $('#manage_user_list').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                } else {
                    $('#manage_user_list').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
            },
			// 
             error: function (res) {
                $('body').toggleClass('loaded');
                if (res.msg != undefined) {
                    $('#manage_user_list').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
                return false;
            } 	
        });
    }); 
	/*Email Check */

    $('#change_email').on('click', '#check_email', function (e) {
        e.preventDefault();
        var email = $('#email').val();
        var old_email = $('#old_email').val();
        if ($('#email').valid()) {
            $('.alert,div.help-block,.help').remove();
            $('#update_member_email').attr('disabled', false);
            if (email != '' && email != old_email) {
	
                $.ajax({
                    url: document.location.BASE + 'admin/account/email_check',
                    type: "POST",
                    data: ({
					"email" : $('#email').val(),
					"uname": $('#user_name').val(),
					}),
                    dataType: 'JSON',
                    type: 'POST',
                            beforeSend: function () {
                                $('.alert,div.help-block').remove();
                                $('#update_member_email').attr('disabled', true);
                                $('#check_email').attr('disabled', true);
                                $('body').toggleClass('loaded');
                            },
                      success: function (res) {
                        $('body').toggleClass('loaded');
                        $('#check_email', '#change_email_form').attr('disabled', false);
                        if (res.status == 200) {
                            $('#update_member_email').attr('disabled', false);
                            if ($('#change_email_form [name="email"]').parent().hasClass('input-group')) {
                                $('#change_email_form [name="email"]').parent().after("<div class='help text-success'>" + res.msg + "</div>");
                            } else {
                                $('#change_email_form [name="email"]').after("<div class='help text-success'>" + res.msg + "</div>");
                            }
							
                            $('.help').fadeOut(7000);
                        } else
                        {
                            if ($('#change_email_form [name="email"]').parent().hasClass('input-group')) {
                                $('#change_email_form [name="email"]').parent().after("<div class='help-block text-danger'>" + res.msg + "</div>");
                            } else {
                                $('#change_email_form [name="email"]').after("<div class='help-block text-danger'>" + res.msg + "</div>");
                            }
                            $('.help-block').fadeOut(7000);
                        }
						$("#email").val('');
						
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
					
                        $('body').toggleClass('loaded');
                        $('#check_email', '#change_email_form').attr('disabled', false);
                        $('#update_member_details').attr('disabled', false);
                        responseText = $.parseJSON(jqXHR.responseText);
                        console.log(responseText);
                        $.each(responseText.errs, function (fld, msg) {
						
                            if ($('#change_email_form [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#change_email_form [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#change_email_form [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
					
					
                });
            }
        }
        return false;
    });
	/*change Mobile */
	$("#change_mobile_form").validate({
         errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            mobile: {required: true, number: true, maxlength: 10, minlength: 10},	
				
        }, 
		messages: {
                mobile: 'Please enter Mobile Number',
            },
        /*  messages: $member_pwdval_msg, */
        submitHandler: function (form, event) {
            event.preventDefault();
			CURFORM = $(form);
            if ($(form).valid()) {
                $.ajax({
                    url: $("#change_mobile_form").attr('action'),
					data:({
					"uname": $('#uname_mobile').val(),
					"mobile": $('#mobile').val(),
			
					}),
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                        $('#change_mobile_form').attr('disabled', true);
                        $('body').toggleClass('loaded');
                    },
                    success: function (res) {
                        $('body').toggleClass('loaded');
                        $('#change_mobile_form').attr('disabled', false);
                        if (res.status == 200) {
						$("#manage_user_list").dataTable().fnDraw();
                            $('#change_mobile_form').trigger('click');
                            $("#change_mobile_form").before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ res.msg + '</div>');
                            $('.alert').fadeOut(7000);
							/* $('#change_Member_pwd').hide();
							$('#users-list-panel').show(); */
							$("#mobile").val('');
                        $('#change_mobile').fadeOut(8000, function () {
                          $('#users-list-panel').fadeIn('slow');
         
                           });
                        } 
						

						else {
                            $("#change_mobile_form").before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
                        }
                    },
					
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('body').toggleClass('loaded');
                        $('#change_mobile_form').attr('disabled', false);
                        responseText = $.parseJSON(jqXHR.responseText);
                        console.log(responseText);
                        $.each(responseText.errs, function (fld, msg) {
                            if ($('#change_mobile_form [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#change_mobile_form [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#change_mobile_form [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
                });
            }
        }
    });
	 	/* Change Email */
	    $("#change_email_form").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
           email: {required: true, email: true},
				
        }, 
		messages: {
                email: 'Please enter Email  Number',
            },
        /*  messages: $member_pwdval_msg, */
        submitHandler: function (form, event) {
            event.preventDefault();
			CURFORM = $(form);	
            if ($(form).valid()) {
                $.ajax({
                    url: $("#change_email_form").attr('action'),
					data:({
					"uname": $('#user_name').val(),
					"email": $('#email').val(),
			
					}),
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                        $('#change_email_form').attr('disabled', true);
                        $('body').toggleClass('loaded');
                    },
                    success: function (res) {
                        $('body').toggleClass('loaded');
                        $('#change_email_form').attr('disabled', false);
                        if (res.status == 200) {
						$("#manage_user_list").dataTable().fnDraw();
                            $('#change_email_form').trigger('click');
                            $("#change_email_form").before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ res.msg + '</div>');
                            $('.alert').fadeOut(7000);
							/* $('#change_Member_pwd').hide();
							$('#users-list-panel').show(); */
							$("#email").val('');
                        $('#change_email').fadeOut(8000, function () {
                          $('#users-list-panel').fadeIn('slow');
         
                           });
                        } 
						

						else {
                            $("#change_email_form").before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
                        }
                    },
					
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('body').toggleClass('loaded');
                        $('#change_email_form').attr('disabled', false);
                        responseText = $.parseJSON(jqXHR.responseText);
                        console.log(responseText);
                        $.each(responseText.errs, function (fld, msg) {
                            if ($('#change_email_form [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#change_email_form [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#change_email_form [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
                });
            }
        }
    });
	    $('.close_btn').click(function (e) {
        e.preventDefault();
        $('#view_details,#change_Member_pwd,#edit_details,#change_email,#change_mobile,#change_Member_security_pin').fadeOut('fast', function () {
            $('#users-list-panel').fadeIn('slow');
        });
      });
	 /* Update Member Password*/
       $("#update_member_pwdfrm").validate({
	     errorElement: 'div',
         errorClass: 'help-block',
        focusInvalid: false,
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            new_pwd: {required: true, minlength: 6},
          
        },
        submitHandler: function (form, event) {
            event.preventDefault();
			//CURFORM = $(this);
			CURFORM = $(form);
            if ($(form).valid()) {
                $.ajax({
                    url: $("#update_member_pwdfrm").attr('action'),
					data:({
					"account_id": $('#uname_label').text(),
					"full_name": $('#fullname_label').text(),
					"new_pwd": $('#new_pwd').val(),
				
					}),
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                        $('#update_member_pwd').attr('disabled', true);
                        $('body').toggleClass('loaded');
                    },
                    success: function (res) {
                        $('body').toggleClass('loaded');
                        $('#update_member_pwd').attr('disabled', false);
                        if (res.status == 200) {
                            $('#update_member_pwdfrm').trigger('click');
                            $("#update_member_pwdfrm").before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+res.uname + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
							$('#new_pwd').val('');
                           $('#change_Member_pwd').fadeOut(8000, function () {
                          $('#users-list-panel').fadeIn('slow');
         
                           });
                        } 
						
						else {
                            $("#update_member_pwdfrm").before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
                        }
                    },
					
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('body').toggleClass('loaded');
                        $('#update_member_pwd').attr('disabled', false);
                        responseText = $.parseJSON(jqXHR.responseText);
                        console.log(responseText);
                        $.each(responseText.errs, function (fld, msg) {
                            if ($('#update_member_pwdfrm [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#update_member_pwdfrm [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#update_member_pwdfrm [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
                });
            }
        }
    });
	
		 /* Update Member Pin*/
       $("#update_member_pinfrm").validate({
	     errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            new_pin: {required: true, minlength: 6},
          
        },
        submitHandler: function (form, event) {
            event.preventDefault();
			//CURFORM = $(this);
			CURFORM = $(form);
            if ($(form).valid()) {
                $.ajax({
                    url: $("#update_member_pinfrm").attr('action'),
					data:({
					"account_id": $('#uname_pin').text(),
					"full_name": $('#fullname_pin').text(),
					"new_pin": $('#new_pin').val(),
				
					}),
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                        $('#update_member_pwd').attr('disabled', true);
                        $('body').toggleClass('loaded');
                    },
                    success: function (res) {
                        $('body').toggleClass('loaded');
                        $('#update_member_pin').attr('disabled', false);
                        if (res.status == 200) {
                            $('#update_member_pinfrm').trigger('click');
                            $("#update_member_pinfrm").before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+res.uname + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
							$('#new_pin').val('');
                           $('#change_Member_security_pin').fadeOut(8000, function () {
                          $('#users-list-panel').fadeIn('slow');
         
                           });
                        } 
						
						else {
                            $("#update_member_pinfrm").before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
                        }
                    },
					
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('body').toggleClass('loaded');
                        $('#update_member_pin').attr('disabled', false);
                        responseText = $.parseJSON(jqXHR.responseText);
                        console.log(responseText);
                        $.each(responseText.errs, function (fld, msg) {
                            if ($('#update_member_pinfrm [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#update_member_pinfrm [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#update_member_pinfrm [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
                });
            }
        }
    });
	
 /* Update User details*/
       $("#user_updatefrm").validate({
	     errorElement: 'div',
         errorClass: 'help-block',
        focusInvalid: false,
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            first_name: {required: true},
            last_name: {required: true},
            dob: {required: true},
          
        },
        submitHandler: function (form, event) {
            event.preventDefault();
			CURFORM = $(form);
            if ($(form).valid()) {
                $.ajax({
                    url: $("#user_updatefrm").attr('action'),
					data:({
					"uname":$("#uname").val(),
					"first_name": $('#first_name').val(),
					"last_name": $('#last_name').val(),
					"dob": $('#dob').val(),
					}),
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                        $('#update_member_details').attr('disabled', true);
                        $('body').toggleClass('loaded');
                    },
                    success: function (res) {
                        $('body').toggleClass('loaded');
                        $('#update_member_details').attr('disabled', false);
                        if (res.status == 200) {
                            $('#user_updatefrm').trigger('click');
                            $("#user_updatefrm").before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ res.msg + '</div>');
                            $('.alert').fadeOut(7000);
							$('#first_name').val('');
							$('#last_name').val('');
							$('#dob').val('');
                           $('#edit_details').fadeOut(8000, function () {
                          $('#users-list-panel').fadeIn('slow');
         
                           });
                        } 
						
						else {
                            $("#user_updatefrm").before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
                        }
                    },
					
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('body').toggleClass('loaded');
                        $('#update_member_details').attr('disabled', false);
                        responseText = $.parseJSON(jqXHR.responseText);
                        console.log(responseText);
                        $.each(responseText.errs, function (fld, msg) {
                            if ($('#user_updatefrm [name=' + fld + ']').parent().hasClass('input-group')) {
                                $('#user_updatefrm [name=' + fld + ']').parent().after("<div class='help-block'>" + msg + "</div>");
                            } else {
                                $('#user_updatefrm [name=' + fld + ']').after("<div class='help-block'>" + msg + "</div>");
                            }
                        });
                    }
                });
            }
        }
    });
   function addDropDownMenu(arr, text) {
    arr = arr || [];
    text = text || false;
    var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-xs btn-primary dropdown-toggle', 'data-toggle': 'dropdown'})
            .append([$('<i>', {class: 'fa fa-gear'}), $('<span>').attr({class: 'caret'})]),
            $('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
        var options = [], data = {};
        $.each(arr, function (k, v) {
            data = {};
            if (! v.redirect) {
                v.class = v.class || (v.url ? 'actions' : 'show-modal');
            }
            else {
                data['target'] = v.target || '_blank';
            }
            v.url = v.url || '#';
            v.data = v.data || {};
            $.each(v.data, function (key, val) {
                data['data-' + key] = val;
            });
            options.push($('<li>').append($('<a>', {class: v.class}).attr($.extend({href: v.url}, data)).text(v.label)));
        });
        return options;
    }));
    return text ? content[0].outerHTML : content;
}
function addDropDownMenuActions(e, callback) {
    var Ele = e, data = Ele.data();
    callback = callback || null;
    if (Ele.data('confirm') == undefined || (Ele.data('confirm') != null && Ele.data('confirm') != '' && confirm(Ele.data('confirm')))) {
        if (data.confirm != undefined) {
            delete data.confirm;
        }
        $.ajax({
            url: Ele.attr('href'),
            data: data,
            success: function (data) {
                if (callback !== null) {
                    callback(data);
                }
            }
        });
    }
}