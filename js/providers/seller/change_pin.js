$(document).ready(function(){
	
    /*$('#tbb_h #change-pro-pin-form').on('submit', function (e) {
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
					$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}else{
					$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.msg+'</div>');
				}
            },
			error:function(op, textStatus, xhr){			
				$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+op.responseJSON.msg+'</div>');
			}
        });
    });*/
});