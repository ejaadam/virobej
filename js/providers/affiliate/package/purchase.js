(function(){
	var purchaseObj = {};
	var packsObj = [];
	var purchaseObj = {};
	var walObj = {};
	var curPack = {};
	var curPanel = '';
	
	$.fn.searchPack = function(){
		var op = {};
		var id =  $(this).data('id');
		$.each(packsObj,function(k,elm){
			if(elm.package_id ==id){				
				op.package_id =  elm.package_id;
				op.package_name =  elm.package_name;
				op.currency_id =  elm.currency_id;
				op.price =  elm.price;
				op.currency_code =  elm.currency_code;
			}			
		});		
		return op;
	};
	
	
	$('.buy_now').each(function(){
		packsObj.push($(this).data('info'));	
	});
	
	$('.buy_now').removeAttr('data-info');	

	$('.pricing-column').on('click','.package_more_details',function(e){
		e.preventDefault();															 	
		$('.package_more_section',$(this).closest('.pricing-column')).show();
	});
	
	$('.pricing-column').on('click','.package_more_section .closeBtn',function(e){
		e.preventDefault();
		$(this).parent().hide();
	});
	
	$('#package_purchase').on('click','.buy_now',function(evt){
		evt.preventDefault();	
		var curBtn = $(this);
		$.ajax({
			url:$(this).attr('href'),
			method:'POST',
			dataType:'json',
			beforeSend: function(){
				$('body').toggleClass('loaded');
			},
			success:function(op){			
				purchaseObj = curBtn.searchPack();
				$.each(op.purchase_paymodes,function(k,elm){
					$('#package_purchase .selpaymode').append(
						$('<li>').append(
							$('<a>',{name:'payment_gateways',class:'paymode_types',href:elm.url,'data-id':elm.payment_type_id}).append(
								$('<img>',{src:elm.icon,width:'10%'}),
								$('<h4>').text(elm.payment_type),
								$('<p>').text(elm.description),
							)
						)
					)
				});					
				$('#packInfo .pkname').text(purchaseObj.package_name);
				$('#packInfo .pkamt').text(purchaseObj.price+' '+purchaseObj.currency_code);
				$('body').toggleClass('loaded');				
				$('#packagegrid').pageSwapTo('#paymodes');
			}
		});
	});
	
	$('#package_purchase').on('click',".paymode_types",function (evt) {
		evt.preventDefault();
		$('.paymode').hide();
		var curBtn = $(this);
		$.ajax({
			url:$(this).attr('href'),
			method:'POST',
			dataType:'json',
			beforeSend: function(){
				$('body').toggleClass('loaded');
			},
			success:function(response){			
				purchaseObj.paymode = curBtn.data('id');
				$('#paymentprocess').append(response.template);
				if(typeof(response.uwdata) != "undefined" ){
					walObj = response.uwdata;
					if(walObj!=null){						
						$.each(walObj,function(k,elm){
							$('#walletinfo #wallet_id').append('<option value="'+elm.wallet_id+'">'+elm.wallet+'</option>')
						});						
						$('#paymentprocess #wallet_id').trigger('change');
					}
				}	
				$('body').toggleClass('loaded');
			}
		});
	});
	
	var wallet_vallang = {
		'nobal': 'Insufficiant balance'
	};
	
	$(document).on('change',"select#wallet_id",function () {
		var id = $(this).val();
		var wallet = {};
		avi_bal = 0;
        purchaseObj.wallet_id = parseInt($(this).val());	
		$('#paymentprocess .balinfo span').hide();
		$('#paymentprocess .dedbalinfo').hide();
		$("#paymentprocess .balinfo .help-block").remove();		
		$.each(walObj,function(k,elm){
			if(elm.wallet_id==id){
				wallet = elm;
			}
		});
		
		if (wallet && wallet.current_balance >= purchaseObj.price) {			
			$('#paymentprocess .balinfo .usrbal').text(wallet.current_balance);
			$('#paymentprocess .balinfo .usrcur').text(wallet.currency_code);
			$('#paymentprocess .dedbalinfo .usrbal').text(wallet.current_balance-purchaseObj.price);
			$('#paymentprocess .dedbalinfo .usrcur').text(wallet.currency_code);
			$('#paymentprocess .balinfo span').show();
			$('#paymentprocess .dedbalinfo').show();
			$('#paymentprocess .panel .panel-body').append($('<div>').addClass('form-group'));
			
		} else {
			$('#paymentprocess .balinfo span').hide();
			$('#paymentprocess .dedbalinfo').hide();
			$('#paymentprocess .dedbalinfo .usrbal').text('');
			$('#paymentprocess .dedbalinfo .usrcur').text('');
			$('#paymentprocess .balinfo').append('<span class="err help-block">'+wallet_vallang.nobal+'</span>');
			
		}
	});
	
	$('#package_purchase').on('click','#purchasebyWbtn',function(){
		var fdata = $.param(purchaseObj);
		curPanel = $(this).closest('.panel');
		if(fdata != null){
			var curBtn = $(this);
			$.ajax({
				url: curBtn.data('url'),
				method:'POST',
				data: fdata,
				dataType:'json',
				beforeSend: function(){
					$('body').toggleClass('loaded');
				},
				success:function(res){	
					if(res)
					$('.panel-body',curPanel).html("<div class='alert alert-"+res.msgtype+"'>"+res.msg+"</div>");
					$('body').toggleClass('loaded');
				},
				error:function(res){
					
				}
			});
		}
	})	
})(jQuery);