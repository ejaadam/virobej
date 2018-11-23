$(document).ready(function () {

	var COMM_FORM = $('#commission_form');	
	var CS = $('#change-settings');
	$.ajax({
        url: COMM_FORM.data('get-url'),
        type: "post",
        success: function (op) {
			console.log(op);
            var opt = '';   
			//$('.myCheckbox').attr('checked', true);
            if (op.settings != '') {
                setting = op.settings;
                current = op.settings.current_commission;
                pending_req = op.settings.pending_request;
                if (setting.accept_vim.value == 1) {
                    $('#vim').iCheck('check');
                }
                if (setting.accept_bp.value == 1) {
                    $('#bp').iCheck('check');
                }
                if (setting.accept_esp.value == 1) {
                    $('#esp').iCheck('check');
                } 
				if (setting.accept_ngo.value == 1) {
                    $('#ngo').iCheck('check');
                } 
				if (setting.accept_pw.value == 1) {
                    $('#pw').iCheck('check');
                }
				
                $('#lb-vim').html('<b>'+setting.accept_vim.label+'</b>');
                $('#lb-esp').html('<b>'+setting.accept_esp.label+'</b>');
                $('#lb-bp').html('<b>'+setting.accept_bp.label+'</b>');
                $('#lb-ngo').html('<b>'+setting.accept_ngo.label+'</b>');
                $('#lb-pw').html('<b>'+setting.accept_pw.label+'</b>');

				$('#ap-notes').text(setting.pay.notes);				
				$('#vim-notes').text(setting.accept_vim.notes);
				$('#esp-notes').text(setting.accept_esp.notes);
				$('#bp-notes').text(setting.accept_bp.notes);
				$('#ngo-notes').text(setting.accept_ngo.notes);				
				$('#pw-notes').text(setting.accept_pw.notes);				
				$('#oc-notes').text(setting.shop_and_earn.notes);				
				
				if (setting.pay.value == 1) {
                    $('#accept_payment').iCheck('check');				
                }
                
                if (setting.shop_and_earn.value == 1) {
                    $('#offer_cashback').iCheck('check');
                }
                $('#is_redeem_otp_required', CS).prop('checked', op.settings.is_redeem_otp_required);
                $('#profit_sharing', CS).val(op.settings.profit_sharing);
                if (parseInt(op.settings.is_cashback_period) == 1) {
                    $('#cashback_start', CS).val(op.settings.cashback_start);
                    $('#cashback_end', CS).val(op.settings.cashback_end);
                } else {
                    $('#cashback_start', CS).attr('readonly', true);
                    $('#cashback_end', CS).attr('readonly', true);
                }
                if (parseInt(op.settings.offer_cashback) == 1) {
                    $('#offer_cashback').iCheck('check');
                }
                if (parseInt(op.settings.pay) == 1) {
                    $('#accept_payment').iCheck('check');
                }
                if (parseInt(op.settings.is_cashback_period) == 1) {
                    $("input[name=is_cashback_period][value=" + op.settings.is_cashback_period + "]").iCheck('check');
                } else {
                    $("input[name=is_cashback_period][value=0]").iCheck('check');
                }
                if ((current != false) && (current != '')) {					
                    $('#current_setting').html('<p><strong>' + current.title + ': </strong> ' + current.profit_sharing.value + '</p>' + '<p> <strong>Period: </strong>' + current.period.value + '</p>' + '<p> <strong>Status: </strong><span class="label label-success">Confirm</p>');
                } else {					
                    $('#current_setting').css('display', 'none');
                } 
                if ((pending_req != false) && (pending_req != '')) {
                    $('#form').css('display', 'none');
                    $('#pending_setting').html('<p><strong>' + pending_req.title + ': </strong> ' + pending_req.profit_sharing.value + '</p>' + '<p> <strong>Period: </strong>' + pending_req.period.value + '</p>' + '<p> <strong>Status: </strong><span class="label label-warning">' + pending_req.status + '</p>');
                } else {
                    $('#pending_setting').css('display', 'none');
                    $('#form').css('display', 'block');
                }
                if (op.settings.is_cashback_period != 1) {
                    $('#change-settings #valid_date').hide();
                    $('#cashback_start').val('');
                    $('#cashback_end').val('');
                }
            }
        }
    });
	
	COMM_FORM.on('submit', function (e) {
        e.preventDefault();
        CURFORM = $('#commission_form');
        $.ajax({
            url: $('#commission_form').attr('action'),
            type: "post",
            data: $('#commission_form').serialize(),
            success: function (data) {
                COMM_FORM[0].reset();                
                COMM_FORM.css('display', 'none');
				
				 $.ajax({
					url: COMM_FORM.data('get-url'),
					type: "post",
					success: function (op) {						
						if (op.settings != '') {
							setting = op.settings;
							current = op.settings.current_commission;
							pending_req = op.settings.pending_request;	
								
							if ((pending_req !== false) && (pending_req !== '')) {
								$('#pending_setting').css('display', 'block');								
								$('#pending_setting').html('<p><strong>' + pending_req.title + ': </strong> ' + pending_req.profit_sharing.value + '</p>' + '<p> <strong>Period: </strong>' + pending_req.period.value + '</p>' + '<p> <strong>Status: </strong><span class="label label-warning">' + pending_req.status + '</p>');
							}
	
							$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+data.msg+'</div>');
						}
					}
				}); 
            }
        });
    });
	
	$('.is_period').on('change', function () { //Do your code
        check = $('.is_period:checked').val();
        if (parseInt(check) == 1) {
            $('#commission_form #valid_date').show();
        } else {
            $('#commission_form #valid_date').hide();
            $('#cashback_start').val('');
            $('#cashback_end').val('');
        }
    });
	
	$('#profit_sharing').on('keyup', function (e) {
        e.preventDefault;
        var comm = $(this).val();
        if (parseInt(comm) < 3) {
            $('#comm_err').attr('for', 'profit_sharing').addClass('errmsg').html("Could not enter minimum 3% commission");
        } else {
            $('#comm_err').removeClass('errmsg').html('');
        }
    });
	
	 CS.on('click', '#submit_setting', function (e) {
        e.preventDefault();
        CURFORM = $('#change-settings');
        $.ajax({
            url: $('#change-settings').attr('action'),
            type: "post",
            data: $('#change-settings').serialize(),
			dataType:'json',
			success: function (data, textStatus, xhr) {
					if(xhr.status == 200){
						$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+data.msg+'</div>');
					}else{
						$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+data.msg+'</div>');
					}
			},
			error: function (data, textStatus, xhr) {
			
				//$('#alt-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-lable="close">&times;</a>'+xhr+'</div>');
			}
        });
    });
	
	
});