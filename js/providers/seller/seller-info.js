$(document).ready(function () {
	var current_tab = '';
	
	$('#ac_settings').on('click','.tabs',function(e){
		e.preventDefault();		
		var tab = $(this).attr('href');
		var url = $(this).attr('rel');
		console.log(tab + ' ' + url);		
		if(((current_tab =='') || String(current_tab) !== String(tab)) && url !== undefined){			
			$.ajax({ 
				url: url,            
				dataType:'json',
				success: function (op) {
					if(op.status == 'ok'){
						$(tab).html(op.content);
						current_tab = tab;
					}
					cropper_main();
				},
				error: function (jqXHR, exception, op) {					
					if (jqXHR.status == 406) {
						console.log(jqXHR.responseJSON.msgs);
						$('#err_msg').append('<span for="password" class="errmsg">'+jqXHR.responseJSON.msgs+'</span>');
					} 	
				},
			});
		}
	});
	
	$('#ac_settings').on('click','#edit_mobile_detail',function(e){
		e.preventDefault();	
		$('#ac_settings #change_mob').trigger('click');
	});
	
	
	if($('#ac_settings  a[href="' + window.location.hash + '"]').attr('href') !== undefined){
		$('#ac_settings  a[href="' + window.location.hash + '"]').trigger('click');
	}else{
		$('.tabs').first().trigger('click');
	}
	
	//$('#ac_settings .nav-tabs .active').find('.tabs').trigger('click');
	$('#ac_settings').on('click','#forgot-profile-pin,#resend-verification-code',function(e){
		e.preventDefault();		
		$('#ac_settings #tbb_h .panel-title a').text('Reset PIN');
		var url = $('#reset-pro-pin-form').attr('action');
		$.ajax({
			url:url,
			dataType:'json',
			type:'post',
			success:function(op){
				$('#change-pro-pin-form').css('display','none');
				$('#reset-pro-pin-form').css('display','block');				
				$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
			},
			error: function (jqXHR, textStatus, errorThrown) {
				responseText = $.parseJSON(jqXHR.responseText);
				$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+responseText.msg+'</div>');
			}
		})
	});	
	
	$('#ac_settings #create-pro-pin-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            success: function (op) {		
			    $('#ac_settings #create-pro-pin-form input').val('');		
				$('#ac_settings #tbb_h .panel-title a').text('Change PIN');
				$('#ac_settings #tbb_h').text('Change PIN');				
				$('#ac_settings #create-pro-pin-form').fadeOut('fast', function(){
				    $('#ac_settings #change-pro-pin-form').fadeIn('slow');
				});
				$('#ac_settings #create-pro-pin-form .pwdHS').find('i').attr('class','').attr('class','icon-eye-close');				
				$('#ac_settings #alt-msg').html('<div class="alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
            },
			error: function (jqXHR, textStatus, errorThrown) {
                responseText = $.parseJSON(jqXHR.responseText);
				console.log(responseText);
				if(responseText.msg != '' && responseText.msg != undefined){
				    $('#ac_settings #alt-msg').html('<div class="alert alert-danger">' + responseText.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				}								
            }
        });
    });
	
	$('#ac_settings #change-pro-pin-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serialize(),
			dataType:'json',
            success: function (op, textStatus, xhr) {
                $('#old_pin,#new_pin').val('');
				$('#change-pro-pin-form .pwdHS').find('i').attr('class','').attr('class','icon-eye-close');	
				$('#ac_settings #change-pro-pin-form input').val('');
				if(xhr.status == 200){
					$('#ac_settings #alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}else{
					$('#ac_settings #alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}
            },
			error: function (jqXHR, textStatus, errorThrown) {
                responseText = $.parseJSON(jqXHR.responseText);
				console.log(responseText);
				if(responseText.msg != '' && responseText.msg != undefined){
				    $('#ac_settings #alt-msg').html('<div class="alert alert-danger">' + responseText.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				}								
            }			
        });
    });
		
	$('#ac_settings #reset-pro-pin-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        $.ajax({
            url: CURFORM.attr('data-url'),
            data: CURFORM.serializeObject(),
            success: function (op) {		
			    $('#ac_settings #change-pro-pin-form').css('display','block');
				$('#ac_settings #reset-pro-pin-form').css('display','none');
				$('#ac_settings #reset-pro-pin-form input').val('');
				$('#ac_settings #change-pro-pin-form input').val('');
				$('#ac_settings #tbb_h .panel-title a').text('Change PIN');
				$('#ac_settings #reset-pro-pin-form .pwdHS').find('i').attr('class','').attr('class','icon-eye-close');	
				$('#ac_settings #change-pro-pin-form .pwdHS').find('i').attr('class','').attr('class','icon-eye-close');	
				$('#ac_settings #alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
            },
			error: function (jqXHR, textStatus, errorThrown) {
                responseText = $.parseJSON(jqXHR.responseText);
				console.log(responseText);
				if(responseText.msg != '' && responseText.msg != undefined){
				    $('#ac_settings #alt-msg').html('<div class="alert alert alert-danger">' + responseText.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
				}								
            }
        });
    });
	
	/*$('#ac_settings').on('click','#resetBtn',function(e){
		e.preventDefault();		
		$.ajax({
			url:$('#ac_settings #reset-pro-pin-form').attr('data-url'), //'seller/security-pin/reset', 
			dataType:'json',
			data:$('#reset-pro-pin-form').serialize(),
			type:'post',
			success:function(op){
				$('#change-pro-pin-form').css('display','block');
				$('#reset-pro-pin-form').css('display','none');
				$('#ac_settings #reset-pro-pin-form input').val('');
				$('#ac_settings #change-pro-pin-form input').val('');
				$('#ac_settings #tbb_h .panel-title a').text('Change PIN');
				$('#reset-pro-pin-form .pwdHS').find('i').attr('class','').attr('class','icon-eye-close');	
				$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
			},
			error: function (jqXHR, textStatus, errorThrown) {
				responseText = $.parseJSON(jqXHR.responseText);
				$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+responseText.msg+'</div>');
			}
		});
	});
	
	$('#ac_settings #create-pro-pin-form').on('submit', function(e){
		e.preventDefault();
		CURFOREM = $('#create-pro-pin-form');
		$.ajax({
			url: CURFOREM.attr('action'),		
			data: CURFORM.serializeObject(),
			type:'post',
			success:function(op){
				
			}
		})
	}); */
	
});