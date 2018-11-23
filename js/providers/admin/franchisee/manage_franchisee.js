$(function () {
	
    var t = $('#mange_center');
	
    var DT = t.dataTable({
        bPagenation: true,
        bProcessing: true,
        bFilter: false,
        bAutoWidth: false,
        oLanguage: {
            sSearch: "<span>Search:</span> ",
            sInfo: "Showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries",
            sLengthMenu: "_MENU_ <span>entries per page</span>"
        },
      	
        ajax: {
            url: $('#form').attr('action'),
            type: 'POST',
            data: function (d) {
		
                return $.extend({}, d, $('input,select', '#form').serializeObject());
            },
        },
        columns: [
            {
                data: "signedup_on",
                name: "signedup_on",
                class: "text-right"

             },
			 {
                data: "uname",
                name: "uname",
                class: "text-right"

            },
			{
                data: "company_name",
                name: "company_name",
                class: "text-right",
				data: function (row, type, set) {
				var txt = '';
				var txt='<span class="">'+row.company_name+'<br></span><span class=""> ('+row.access_details+')</span>';
				return txt;
					}

            },
			{
                data: "franchisee_type_name",
                name: "franchisee_type_name",
                class: "text-right"

            },
			{
                data: "country_name",
                name: "country_name",
                class: "text-right"

            },
			/*{
                data: "district_frname",
                name: "district_frname",
                class: "text-right"

            },
			{
                data: "state_frame_result",
                name: "state_frame_result",
                class: "text-right"

            },
			{
                data: "region_frname_result",
                name: "region_frname_result",
                class: "text-right"

            },
			{
                data: "country_frname_result",
                name: "country_frname_result",
                class: "text-right"
            },*/

			{
                data: "block_status",
                name: "block_status",
                class: "text-right"

            },
			 {
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            }
		
        ],
		
    });
    $('#form').on('submit', function (e) {
        DT.fnDraw();
    });
    $('#searchbtn').click(function (e) {
        DT.fnDraw();
    });
    $('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        DT.fnDraw();
    });

});

/* User Block Status */
 	   $('#mange_center').on('click', '.block_status', function (e) {
        e.preventDefault();
        var CurEle = $(this);
		if(CurEle.data('status')=='block'){
			var status=confirm('Are you sure, You wants to Block :');
		}
	   else {
		var status=confirm('Are you sure, You wants to UnBlock a User?');
	  }
	   if (status) {
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
				$("#mange_center").dataTable().fnDraw();
                    $('#mange_center').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                } else {
                    $('#mange_center').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
            },
			// 
             error: function (res) {
                $('body').toggleClass('loaded');
                if (res.msg != undefined) {
                    $('#mange_center').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
                return false;
            } 	
        });
		
	}
    }); 
	
	
	/* User Block Status */
 	   $('#mange_center').on('click', '.login_block', function (e) {
        e.preventDefault();
        var CurEle = $(this);
		if(CurEle.data('status')=='block'){
			var status=confirm('Are you sure, You wants to Block login :');
		}
	   else {
		var status=confirm('Are you sure, You wants to UnBlock a User?');
	  }
	   if (status) {
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
				$("#mange_center").dataTable().fnDraw();
                    $('#mange_center').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                } else {
                    $('#mange_center').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
            },
			// 
             error: function (res) {
                $('body').toggleClass('loaded');
                if (res.msg != undefined) {
                    $('#mange_center').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
                return false;
            } 	
        });
		
	}
    }); 

	/* $("#update_member_pwd").click(function (e){
            event.preventDefault();
			CURFORM = $(form);
                $.ajax({
                    url: $("#update_member_pwdfrm").attr('action'),
					data:({
					"account_id": $('#uname_label').val(),
					"full_name": $('#fullname_label').text(),
					"new_pwd": $('#new_pwd').val()
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
          
        
    }); */
	
	/* For Change Password  */
    $('#mange_center').on('click', '.change_password', function (e) {
		 e.preventDefault();
        var CurEle = $(this);

        $('#update_member_pwdfrm').trigger('click');
        $('#support_label').text(CurEle.data('support_center'));
        $('#uname_label').val(CurEle.data('account_id'));
        $('#fullname_label').text(CurEle.data('first_name'));
		//$('#users-list-panel').hide();
		$('#change_Member_pwd').show(); 
		$('#retailer-qlogin-model').modal();
    });
	/* Update Member Password*/

 $('#mange_center').on('click', '.change_pin', function (e) {
		 e.preventDefault();
        var CurEle = $(this);
   
        $('#update_memberpin').trigger('click');
        $('#support_pin').text(CurEle.data('support_center'));
        $('#fullname_pin').text(CurEle.data('first_name'));
		$('#uname_pin').val(CurEle.data('account_id'));
		//$('#users-list-panel').hide();
		$('#change_Member_pin').show(); 
		$('#retailer-qlogin-model1').modal();
    });
	
	
	 $('#mange_center').on('click', '.edit_info', function (e) {
            e.preventDefault();
	     
     $("#view_user_profile .modal-title").html("Edit Profile of Support Center User");
            $("#view_user_profile .modal-body").html("Loading....");
            $("#view_user_profile").modal();
            $.post($(this).data('url'), {uname: $(this).data('uname')}, function (data) {
            $("#view_user_profile .modal-body").html(data.content);
            }, 'json');  
        });
	$('#update_memberpin').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                confirm_tpin: {
                    equalTo: '#new_tpin'
                }
            },
            messages: {
                confirm_tpin: {
                    required: "Confirm tpin is required",
                }
            },
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },
            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
                $(e).remove();
            },
            submitHandler: function (form) {
                if ($(form).valid()) {
                    $.ajax({
                        type: 'POST',
                        url: $('#update_memberpin').attr('action'),
                        data: $('#update_memberpin').serialize(),
                        dataType: 'json',
                        beforeSend: function () {
                            $('.alert').remove();
                        },
                        success: function (res) {
				       $('body').toggleClass('loaded');
                        $('#update_member_pin').attr('disabled', false);
                        if (res.status == 200) {
                            $('#update_memberpin').trigger('click');
                            $("#update_memberpin").before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+res.uname + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
						
                          $('#retailer-qlogin-model1').fadeOut(8000, function () {
                          $('#users-list-panel').fadeIn('slow');
                           });
                        } 
						else {
                            $("#update_memberpin").before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                            $('.alert').fadeOut(7000);
                        }
                        }
                    }); 
                }
                return false;
            },
            invalidHandler: function (form) {
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