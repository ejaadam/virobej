
$(document).ready(function () {

    var DT = $('#manage_user_list_table').dataTable({
        ajax: {
            url: $('#manage_user_list_form').attr('action'),
             data: function (d) {
                d.search_term = $('#search_term').val();
                /*d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.status = $('#status').val(); */
            }
        },
        columns: [
		   {
                data: 'full_name',
                name: 'full_name',
                class: 'text-left',
            },
            {
                data: 'email',
                name: 'email',
                class: 'text-left',
            },
			{
                data: 'access_name',
                name: 'access_name',
                class: 'text-left',
            },
			{
                data: 'uname',
                name: 'uname',
                class: 'text-left',
            },
			 {
                name: 'emp_status',
                class: 'text-left',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.status_class + '">' + row.emp_status + '</span> ';
                }
            },
			{
                class: 'text-left',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);
                }
            }
			
        ],
        order: [0, 'DESC']
    });
	
	/* User Block Status */
 	   $('#manage_user_list_table').on('click', '.login_block', function (e) {
         e.preventDefault();
          var CurEle = $(this);
		if(CurEle.data('status')=='block'){
			var status=confirm('Are you sure, You wants to Block a User');
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
				$("#manage_user_list_table").dataTable().fnDraw();
                    $('#manage_user_list_table').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                } else {
                    $('#manage_user_list_table').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
            },
			// 
             error: function (res) {
                $('body').toggleClass('loaded');
                if (res.msg != undefined) {
                    $('#manage_user_list_table').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
                return false;
            } 	
           });
	    }
    }); 
	

	/*User Status */
          $('#manage_user_list_table').on('click', '.user_status', function (e) {
         e.preventDefault();
          var CurEle = $(this);
		if(CurEle.data('status')=='active'){
			var status=confirm('Are you sure, You wants to Active a User');
		  }
	     else {
	  	 var status=confirm('Are you sure, You wants to Inactive a User?');
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
				$("#manage_user_list_table").dataTable().fnDraw();
                    $('#manage_user_list_table').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                } else {
                    $('#manage_user_list_table').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
            },
			// 
             error: function (res) {
                $('body').toggleClass('loaded');
                if (res.msg != undefined) {
                    $('#manage_user_list_table').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
                return false;
            } 	
           });
	    }
    }); 	
	
	/*End */
	
	$('#manage_user_list_table').on('click','.edit', function (e) {
         e.preventDefault();
		 $('.report').css('display','none');
		 $('.add_user').css('display','block');
		 $('#title_user').text('Edit user');
		 $("#submit").text('Update');
         var CurEle = $(this);
		
         $.ajax({
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
			     $("#account_id").val(res.data.account_id);
				 $("#role").val(res.data.access_level);
				 $("#email").val(res.data.email);
				 $("#mobile").val(res.data.mobile);
				 $("#username").val(res.data.uname);
				 $("#full_name").val(res.data.full_name)
				  /* $("#user-form").attr('action',res.url); */ 
				 
				 if(res.data.status==1){
					 $('#status').prop('checked', true);
				 }
				 else{
					  $('#status').prop('checked', false);
				 }
				}
            },
	         error: function (res) {
                $('body').toggleClass('loaded');
                if (res.msg != undefined) {
                    $('#manage_user_list_table').before('<div class="col-sm-12 alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000);
                }
                return false;
            } 	
           });
	    
    });

		$('#manage_user_list_table').on('click','.allocation', function (e) {
         e.preventDefault();
		 $('.report').css('display','none');
		 $('.add_user').css('display','none');
         var CurEle = $(this);
         $.ajax({
            url: CurEle.attr('href'),
            type: "POST",
            dataType: 'JSON',
            beforeSend: function () {
                $('body').toggleClass('loaded');
                $('.alert,div.help-block').remove();
            },
            success: function (res) {
		        $('body').toggleClass('loaded');
             			$('#supplier-stores-list').fadeIn('show');
						$('#supplier-stores-form').attr('action', res.save_url);
						printAdminStore(res);
	        },
	         error: function (res) {
               
            } 	
           });
	    
    });

	function printAdminStore(op) {
        $('#supplier-stores', $('#supplier-stores-list')).empty();
		console.log(op);
        if (op.stores != null) {
            $.each(op.stores, function (k, v) {
                $('#supplier-stores', $('#supplier-stores-list')).append(
                        $('<tr>').append(function () {
						var ele = [];
						ele.push($('<td>').append([$('<b>').text(v.store_name), $('<b>').text("(#"+v.store_code+")"),$('<p>').text(v.address)]));
						ele.push($('<td>', {class: 'supplier-store'}).append($('<input>', {type: 'checkbox', name: 'stores[' + v.store_id + '][store_id]', value: v.store_id, checked: (v.id !== '')?true:false})));
						ele.push($('<td>', {class: 'supplier-store-status', style: (v.id != null ? '' : 'display:none;')}).append($('<label>').append([$('<input>', {type: 'checkbox', name: 'stores[' + v.store_id + '][status]', checked: v.status}), ' Enable'])));
						return ele;
					})
				);
            });

        }
    }	
	
	
    $('#supplier-stores-form').on('click', '.supplier-store input[type=checkbox]', function () {
        var CurEle = $(this);
        if ($('#supplier-stores-form input:checked').length > 0) {
            $('#supplier-stores-form #update_store').attr('disabled', false);
        } else {
            $('#supplier-stores-form #update_store').attr('disabled', true);
        }
        if (CurEle.prop('checked')) {
            CurEle.parents('.supplier-store').siblings('.supplier-store-status').show();
        }
        else {
            CurEle.parents('.supplier-store').siblings('.supplier-store-status').find('input[type=checkbox]').prop('checked', false);
            CurEle.parents('.supplier-store').siblings('.supplier-store-status').hide();
        }
    });
	
	 $('.close_btn').click(function (e) {
        e.preventDefault();
        $('#supplier-stores-list,.add_user').fadeOut('fast', function () {
            $('.report').fadeIn('slow');
        });
    });
	
	$('#supplier-stores-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        if ($('#supplier-stores-form input:checked').length > 0) {
            $.ajax({
                url: CURFORM.attr('action'),
                data: CURFORM.serializeObject(),
                success: function (op) {
                    printAdminStore(op);
					$('#assign_msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-label="close">&times;</a>'+op.msg+'</div>');
                }
            });
        }
    });

	/* For Change Password  */
    $('#manage_user_list_table').on('click', '.reset-password', function (e) {
		e.preventDefault();
        var CurEle = $(this);
        $('#update_user_pwdfrm').trigger('click');
        $('#reset_user_account_id').val(CurEle.data('account_id'));
        $('#uname_label').text(CurEle.data('email'));
        $('#fullname_label').text(CurEle.data('fullname')); 
        $('#full_name').val(CurEle.data('fullname')); 
		$("#update_user_pwdfrm").show();
		$('#reset_password_modal').modal();
    });

	
	
    $('#reset_btn').click(function () {
        $('#manage_user_list_form').trigger('reset');
        DT.fnDraw();
    });
    $(document.body).on('click', '#search', function () {
        DT.fnDraw();
    });
	
	$('#add_user').click(function(e){
		e.preventDefault();
		$('.report').css('display','none');
		$('.add_user').css('display','block');
		
	})
	
	$('.back').on('click',function(e){
		e.preventDefault();
		$('.report').show();
		$('.add_user').css('display','none');
		$('#user-form input').val('');
	})


	$('#update_user_pwdfrm').submit(function (event) {		
        event.preventDefault();
        CURFORM = $('#update_user_pwdfrm');
		var formData = new FormData(this);
        $.ajax({
            url: CURFORM.attr('action'),            
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (op) {
				console.log(op);
				  $("#reset_password_modal").find("input[type=password]").val('');
				 
					$("#reset_password_modal").modal('hide');
			},
			error: function (jqXHR, exception, op) {
				console.log(jqXHR);		
				if (jqXHR.status == 406) {
					console.log(jqXHR.responseJSON.msgs);
					$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
			
				} 	
			},
        });
    });	
});
