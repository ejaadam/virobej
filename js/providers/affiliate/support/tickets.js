
$(function () {	
    var _curTicket = '';
    var t = $('#ticketlist');
	t.DataTable({
		ordering: false,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		columnDefs: [
			{ "width": "15%", "targets": 0 },
			{ "width": "13%", "targets": 1 }
		],
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		ajax: {
			url: baseUrl + 'account/support/tickets',
			type: 'POST',
			data: function ( d ) {
				d.search_term = $('#search_term').val();
				d.from_date=$('#from_date').val();
				d.to_date=$('#to_date').val();
				d.ticket_status_id=$('#ticket_status_id').val();
			},
        },
		columns: [
			{
				data: 'created',
				name: 'created',
				class: 'text-left no-wrap',
			},
		  	{
				data: 'tic_code',
				name: 'tic_code',
				class: 'text-left',
				data: function (row, type, set) {                    
                    return new String('<a href="#" class="detail status_'+row.ticket_id+'" rel="' + row.ticket_id + '">' +row.ticket_code + '</a>');
                }
			},
			{
				data: 'subject',
				name: 'subject',
				class: 'text-left',
				data: function (row, type, set) {
					var priority = '';
					var status_data='';
					var txt=row.subject+'<br><span class="text-muted">'+row.priority_name+': <span class="text-'+row.disp_class+'">'+row.priority+'</span>&nbsp;&nbsp;'+row.status_name+': <span class="text-'+row.status_class+'">'+row.status+'</span>&nbsp;&nbsp;'+row.category+': <span class="text-black">'+row.category_name+'</span></span>';
					return txt;
				}
			},
            {
                class: 'text-right',
                data: function (row, type, set, meta) { 
					var json = $.parseJSON(meta.settings.jqXHR.responseText);
					var status_url = $('#action_url').val();
					var action_buttons = '';
					action_buttons = action_buttons + '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear" aria-hidden="true"></i> <span class="caret"></span></button>';
					if(row.status_id == 3)
					{ 
					   action_buttons = action_buttons + '<ul class="dropdown-menu dropdown-menu-right" role="menu"><li class="make_order_menu"><a href="' + baseUrl + 'account/support/tickets_status" rel="' + row.ticket_id + '" data-status="4" class="change_status">'+row.reopen+'</a></li><li class="make_order_menu"><a href="' + baseUrl + 'account/support/view_ticket_detail" rel="' + row.ticket_id + '" data-status="4" class="detail text-blue">'+row.view_details+'</a></li>';
					}else{
					  action_buttons = action_buttons + '<ul class="dropdown-menu dropdown-menu-right" role="menu"><li class="make_order_menu"><a href="' + baseUrl + 'account/support/view_ticket_detail" rel="' + row.ticket_id + '" data-status="4" class="detail text-blue">'+row.view_details+'</a></li>';
					} 
				action_buttons = action_buttons + '</ul></div>';
				return action_buttons;
                },
            },			
		],
		"fnDrawCallback": function( oSettings ) {
		  	if(!oSettings._iRecordsDisplay){
				$('#exportbtn,#printbtn').hide();
			}
			else {
				$('#exportbtn,#printbtn').show();
			}
		}, 
		responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data.ticket_label+'(#'+data.ticket_code+')';
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                })
            }
        }  
	});
	
	$('#form_ticket').on('click','#searchbtn',function (e) {
		var $term= $('#search_term').val();
		if($term.length>0 && $term.length<3)
		{
			alert($search_term_alert);
			return false;
		}
		else {
			t.dataTable().fnDraw();  
		}
	});
	
	$('#resetbtn_ticket').click(function (e) {
		$('input.form-control,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });	
	
	$('#resetbtn').click(function (e) {
		$('#alert_div').hide();
		$('input.form-control,#ticket_message,#file_attachment,select',$(this).closest('form')).val('');
    });	
	
	$('#file_attachment').on('change',function () {
	   checkformat1($(this),'gif|jpg|jpeg|png|docx|pdf|doc', $file_format);
    });
	
	$('#file_attachment_comment').on('change',function () {
	   checkformat($(this),'gif|jpg|jpeg|png|docx|pdf|doc', $file_format);
    });
	
	
});

function checkformat1(ele, doctypes, $file_format) {
    var fld = $('#file_attachment');
    val = $('#file_attachment').val();
    var valArr = val.split('.');
    txtext = val.split('.')[(valArr.length) - 1];
    txtext = txtext.toLowerCase();
    fformats = doctypes.split('|');
    if (fformats.indexOf(txtext) == -1)
	 {
        alert($file_format);
        val = $('#file_attachment').val('');
    }
}
function checkformat(ele, doctypes, $file_format) {
    var fld = $('#file_attachment_comment');
    val = $('#file_attachment_comment').val();
    var valArr = val.split('.');
    txtext = val.split('.')[(valArr.length) - 1];
    txtext = txtext.toLowerCase();
    fformats = doctypes.split('|');
    if (fformats.indexOf(txtext) == -1)
	{
        alert($file_format);
        val = $('#file_attachment_comment').val('');
    }
}

var status_value={};
var status_class={};
var rel_id={};
    $(document).ready(function () {
		$("#form_new_ticket").validate({
			focusInvalid: false,
			onkeyup: false,
			onfocusout: false,
			errorElement: "div",
			errorClass: "help-block",
			rules: {
			  ticket_category_id: {
				required: true
			  },
			  ticket_priority_id: {
				required:  true
			  },
			  ticket_subject: {
				required: true
			  },
			  ticket_message: {
				required: true
			  },
			},
			messages: $ticket_val_message, 
			submitHandler: function (form, event) {
			event.preventDefault();
			$('.alert').remove();
				if ($(form).valid()) {
					$("#form_new_ticket").ajaxSubmit({type: 'post',
						beforeSubmit: function () {
							$('.errMsg').show();
							$('.alert,div.help-block').remove();
							$('#submit_btn').attr('disabled',true);
							$('body').toggleClass('loaded');
						},
						success: function (data) {
						    $('#new_tickets_button').click();
							$('#submit_btn').attr('disabled',false);
							$('body').toggleClass('loaded');
							$("#form_new_ticket").find("input[type='text'],input[type='file'], select, textarea").val("");
							if (data.status == 200 ) {
								$('#alert_div').html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" area-label="close">&times;</a>'+data.msg+'</div>');
								$('#ticketlist').dataTable().fnDraw();
								$('html, body').animate({scrollBottom: 0}, 500);
								$('.alert').fadeOut(7000);
							} else {
								$('#alert_div').html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' area-label='close'>&times;</a>"+data.msg+"</div>");
								$('.alert').fadeOut(7000);
							}
						},
						error: function (jqXHR, textStatus, errorThrown) {
							$('#submit_btn').attr('disabled',false);
							$('body').toggleClass('loaded');
							responseText = $.parseJSON(jqXHR.responseText);
							$.each(responseText.errs,function(fld,msg){
								if($('#form_new_ticket [name='+fld+']').siblings().hasClass('input-group')){
									  $('#form_new_ticket [name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
								}else {
									  $('#form_new_ticket [name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
								}
							});
							return false;
						}
					});
				}
		     return false;
            }
        });
    });
	
   $(document).on('click', '.detail', function (e) {
        e.preventDefault();
        _curTicket = $(this);		
        $.ajax({
          url: baseUrl + 'account/support/view_ticket_detail',
            data: {id: _curTicket.attr('rel')},
            type: "POST",
            dataType: 'JSON',
            beforeSend: function () {
			    $('.alert,div.help-block').remove();
				$('body').toggleClass('loaded');
                $('#report_div').css('display', 'none');
				$('#accordion').css('display', 'none');
            },
            success: function (res) {
                $('body').toggleClass('loaded');				
				rel_id=_curTicket.attr('rel');
				var $status='In progress';  
				status_class = $('.status-col span',_curTicket.closest('tr')).attr('class');
				status_data=$('.status-col span',_curTicket.closest('tr'));
				status_value=_curTicket.closest('tr').find('td:eq(4) span').text();
				$('#accordion').show();
				
				$('#details_div').show();
				$('#reply_forms .form-control, #reply_msg .alert.alert-success, #reply_msg .alert.alert-failure, #file_attachment_comment').val('');
                if (res.contents.detail[0].status_id == 3)
                { 
                    $('#rate_sub_form,#rating_button,#rating_row').hide();
					$('.feedback_body').show();
                    $('#post_reply_div').hide();
                } else {
                    $('#rate_sub_form,#rating_button,#rating_row').show();
                    $('#post_reply_div').show();
					$('.feedback_body').hide();
                }
                var content = [];
                var i = 0;
                $('#details_div').css('display', 'block');
                $('#details_div').text(res.ticket_id);
				$('#la-status').removeAttr("class");
				$('#la-status').text(res.contents.detail[0].status).addClass('text-'+res.contents.detail[0].status_class);
                $('#la-category').text(res.contents.detail[0].category_name);
                if (res.contents.detail[0].attachment == '')
                {
                    $('#attachement_div').hide();
                }
                else {
                    $('#attachement_div').show();
                    $('#la-attachement').text(res.contents.detail[0].attachment);
                }
                $('#ticket_id').val(res.contents.detail[0].ticket_id);
                $('#ticket_id_reply').val(res.contents.detail[0].ticket_id);
                $('#la-description').text(res.contents.detail[0].description);
                $('#comments_fed').text(res.contents.detail[0].user_comments);
				$('#rating_fed').removeAttr("class");
				$('#rating_fed').text(res.contents.detail[0].rating).addClass('text-'+res.contents.detail[0].rating_class);

                var collap = '';
                var head = '';
                if (res.contents.replies != '')
                { 
                    var cont = '';
                    var cnt = 0;
                    $.each(res.contents.replies, function (key, element) {
                        cnt = $('#accordion .panel').length + 1;
                        cont = '<div class="panel panel-default bx-shadow-none">';
                        cont += '<div class="panel-heading" role="tab" id="head' + cnt + '">'
                        cont += '<h4 class="box-title"><span class="text-muted fa fa-plus"></span> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#' + cnt + '" class="collapsed" aria-expanded="false"  aria-controls="' + cnt + '">' + element.replay_comments + '<span class="pull-right">' + element.create_date + '</span></a></h4></div>';
                        cont += '<div id="' + cnt + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head' + cnt + '"><div class="box-body">';
                        if (element.attachment != '' && element.attachment != 'undefined')
                        { 
                            cont += '<a href="' + baseUrl + 'assets/img/tickets/comments' + '/' + element.attachment + '" target="_blank"><i class="fa fa-download"></i>&nbsp;'+element.download_attachment+'</a>' + '<br>';
                        }
                        cont += '<p>' + element.replay_comments + '</p></div></div></div>';
                        $('#accordion').append(cont);
                    });
                }
                $('#tic_code').text(res.contents.detail[0].ticket_code);
                $('#span_sub').text(res.contents.detail[0].subject);
                $('#opened_at').html(res.contents.detail[0].created);
				if(res.contents.detail[0].solved_at=='01-Jan-1970 00:00:00')
                  $('#solved_at').html('');
				else
                  $('#solved_at').html(res.contents.detail[0].solved_at);
            }
        });
    });
    $(document).on('click', '.close-it', function () {
		$('#accordion').css('display', 'none');
		$('.panel.panel-default.bx-shadow-none').css('display', 'none');
        $('#details_div').css('display', 'none');
        $('#report_div').css('display', 'block');
    });
    $('#rating_form').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
		errorPlacement: function(error, element) {
            if (element.attr("type") == "radio") {
                error.insertBefore(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            comment:{
			  required: true
			},
            rating: {
			  required: true
			},
        },
        messages: $rating_val_message, 
        submitHandler: function (form, event) {
            event.preventDefault();
            $('.alert').remove();
			var ticket_id = $('#ticket_id').val(); 
            if ($(form).valid()) { 
                $("#rating_form").ajaxSubmit({ 
					type: 'post', 
					dataType:'JSON', 
                    beforeSubmit: function () {	
					    $('.alert,div.help-block').remove(); 
					    $('input[type="submit"]', '#rating_form').attr('disabled', true); 
					    $('body').toggleClass('loaded'); 
                    }, 
					error: function (jqXHR, textStatus, errorThrown) { 
					    $('input[type="submit"]', '#rating_form').attr('disabled', false); 
						$('body').toggleClass('loaded'); 
					    responseText = $.parseJSON(jqXHR.responseText); 
					    $.each(responseText.errs,function(fld,msg){ 
							if($('#rating_form [name='+fld+']').siblings().hasClass('input-group')){ 
								if(fld=='rating'){ 
								  $('#rating_form #'+fld+'_err').append("<div class='help-block'>"+msg+"</div>"); 
								} 
								else { 
							      $('#rating_form [name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>"); 
								} 
							}else {
								if(fld=='rating'){
							       $('#rating_form #'+fld+'_err').html('<span class="help-block">'+msg+'</span>');
								}
								else {
								  $(' #rating_form [name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
								}
							}
						});
					    return false;
					},
                    success: function (res) {
					    $('body').toggleClass('loaded'); 
					    $('input[type="submit"]', '#rating_form').attr('disabled', false);
					    $('input[name="rating"]').removeAttr('checked');
						$('#rating_form .form-control').val('');
					    if(res.status==200)
						{    
							$('.feedback_body').show();
							$('#ticketlist').dataTable().fnDraw();
							$('#la-status').removeAttr("class");
							$('#la-status').text(res.data.status_label).addClass('text-'+res.data.status_class);
							$('#rating_row, #rating_button, #rate_sub_form').hide();
							$('#rating_msg').html('<div class="alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+res.msg+'</div>');
							$('#post_reply_div').hide();
							$('#solved_at').text(res.data.solved_date);
							$('#comments_fed').text(res.data[0].user_comments);
							$('#rating_fed').removeAttr("class");
							$('#rating_fed').text(res.data[0].rating).addClass('text-'+res.data[0].rating_class);
							$('.alert').fadeOut(7000);
						}else{
						    $('#rating_msg').html('<div class="alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+res.msg+'</div>');
							$('.alert').fadeOut(7000);
						}
                    },
                });
            }
            return false;
        }
    });
	 
	$('#reply_forms').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            replay_comments: {
         		required:true
			}
        },
        messages: $replay_val_message, 
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($('#reply_forms').valid()) {
                var imgclean = $('#file_attachment_comment');
                var data = new FormData();
                data.append('file_attachment_comment', $('#file_attachment_comment')[0].files[0]);
                data.append('replay_comments', $('#replay_comments').val())
                data.append('ticket_id', $('#ticket_id').val())
				var ticket_id_comment=$('#ticket_id').val();
                $.ajax({
                    url: baseUrl + 'account/support/tickets_comment',
                    type: "POST",
                    data: data,
                    datatype: "json",
                    enctype: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
					    $('.alert,div.help-block').remove();
                        $('input[type="submit"]', '#reply_forms').attr('disabled', true);
						$('body').toggleClass('loaded');
                        $('#err_msg').empty();
                        $('#err_msg').show();
                    },
					error: function (jqXHR, textStatus, errorThrown) {
					    $('input[type="submit"]', '#reply_forms').attr('disabled', false);
						$('body').toggleClass('loaded');
					    responseText = $.parseJSON(jqXHR.responseText);
					    $.each(responseText.errs,function(fld,msg){
							if($('#reply_forms [name='+fld+']').siblings().hasClass('input-group')){
							    $('#reply_forms [name='+fld+']').siblings().after("<div class='help-block'>"+msg+"</div>");
							}else {
							   $('#reply_forms [name='+fld+']').after("<div class='help-block'>"+msg+"</div>");
							}
						});
					    return false;
					},
                    success: function (res) {
					    $('input[type="submit"]', '#reply_forms').attr('disabled', false);
						$('body').toggleClass('loaded');
						$('#ticketlist').dataTable().fnDraw();
						if (res.status == 200 ) { 
						   var cont = '';
						   var cnt = 0;
						   var cmts=$('#replay_comments').val();
						   $('#la-status').removeAttr("class");
						   $('#la-status').text(res.data.status_label).addClass('text-'+res.data.status_class);
						   $('#accordion').show();	
						   $('#reply_forms .form-control,#reply_forms .valid').val('');
						   $('#reply_msg').html('<div class="alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+res.msg+'</div>');
							cnt = $('#accordion .panel').length + 1;
							cont = '<div class="panel panel-default bx-shadow-none">';
							cont += '<div class="panel-heading" role="tab" id="head' + cnt + '">'
							cont += '<h4 class="box-title"><span class="text-muted fa fa-plus"></span> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#' + cnt + '" class="collapsed" aria-expanded="false"  aria-controls="' + cnt + '">' + cmts + '<span class="pull-right">'+res.data.create_date+'</span></a></h4></div>';
							cont += '<div id="' + cnt + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head' + cnt + '"><div class="box-body">';
							if (res.data.attachment != '' && res.data.attachment != 'undefined')
							{ 
								cont += '<a href="' + baseUrl + 'assets/img/tickets/comments' + '/' + res.data.attachment + '" target="_blank"><i class="fa fa-download"></i>&nbsp;'+res.data.download_attachment+'</a>' + '<br>';
							}
							cont += '<p>' + cmts + '</p></div></div></div>';
							$('#accordion').append(cont);
							$('.alert').fadeOut(7000);
						}else{
						   $('#reply_msg').html('<div class="alert alert-danger"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+res.msg+'</div>');
						   $('.alert').fadeOut(7000);
						}
                    },
                });
            }
            return false;
        }
    });
		
	$(document).on('click', '.change_status', function (e) {
        e.preventDefault();
        curLine = $(this);
        $.ajax({
            url: curLine.attr('href'),
            data: {ticket_id: curLine.attr('rel'), status: curLine.attr('data-status')},
            type: "POST",
            dataType: 'JSON',
            beforeSend: function () {
                $('.alert').remove();
            },
            success: function (res) {
                if (res.status == 200)
                {  
					$('#ticketlist').dataTable().fnDraw();
                    $('#alert_msg').html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" area-label="close">&times;</a>'+res.msg+'</div>');
					$('.alert').fadeOut(7000);
                }
                else {
                    $('#alert_msg').html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" area-label="close">&times;</a>'+res.msg+'</div>');
					$('.alert').fadeOut(7000);
                }
            }
        });
    });
	 
	$.fn.accordionUsr = function () {
        $(".expand", $(this)).on("click", function () {
            $(this).next().slideToggle(200);
            $expand = $(this).find(">:first-child");
            if ($expand.text() == "+") {
                $expand.text("-");
            } else {
                $expand.text("+");
            }
        });
    }