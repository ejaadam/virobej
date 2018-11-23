$(document).ready(function () {
	
	var SDB = $('#seller_dashboard');
	
	
	function profname_color(){	
	  var rand = Math.floor(Math.random() * 1000000) + 1;
	  //console.log(rand);
	  $('#top_header .profile-name').css('background','#'+rand);
	}
	//profname_color();
	
	function widget(trans = null){
		if ($('.widget',SDB)) {	 
		   var Widget = (trans == true) ? $('#transactions',SDB) : $('.widget',SDB);
		   var a = Widget.map(function () {
						return $(this).attr('data-widget-name');
					}).get();			
			if(a.length > 0){
				$.ajax({
					url: SDB.attr('data-url'),
					data: {widgets: a},
					dataType: 'JSON',
					success: function (resData) {
						$.each(resData, function (k, dataSet) {
							eval(k + "(dataSet)");
						});
					}
				});
			}
		}
	}
	widget();	
	setInterval( function() { widget(trans = true); }, 5000 );	
		
	/*  Recent Transaction */
	function transactions (dataSet) {
	    $('#transaction_tbl tbody',SDB).empty();
        if(dataSet.data.length > 0) {		
	        $.each(dataSet.data, function (k, v) {	
                $('#transaction_tbl tbody',SDB).append($('<tr>').append([$('<td>').append(v.statementline),$('<td>').append(v.amount)]))
			});	
			$('#transactions .panel-footer').css('display','block');
        }else{
			$('#transactions .panel-body',SDB).html('No recent transaction available');
		}
	}	
		
	function sales (dataSet) {
	    $('#total_sales',SDB).html(dataSet.total_sales);		
	    $('#today_sales',SDB).html(dataSet.today_sales);		
		$.each(dataSet.other_months, function (k, v) {
			$('#'+k,'#seller_dashboard #month-box').html(v);	
		});	
	}	
		
	function orders (dataSet) {
	    $('#total_orders',SDB).text(dataSet.total_order);
	}
	  
	function visitors (dataSet) {
	    $('#total_visitorss',SDB).text(dataSet.total_visitors);
	}
	
	function acc_balance (dataSet) {	
	    $('#current_balance',SDB).html(dataSet.data.current_balance);		
	}
	function acc_manager (dataSet) {	
		if(dataSet !== ''){
			$('#manager_details',SDB).html('<p>'+dataSet.full_name+'<br><i class="icon-envelope"></i>&nbsp;'+dataSet.email+'<br><i class="icon-phone"></i>&nbsp;'+dataSet.mobile+'<p>');		
		}else{
			$('#manager_details',SDB).html('<p>Not available<p>');		
			
		}
	}
	
	/* Verify Email */
	$('.verify_email').on('click', function (e) {
        e.preventDefault();	
        CURELE = $(this);
        $.ajax({
            url: CURELE.attr('href'),
            data: {email: CURELE.attr('data-email')},
            success: function (op) {
               /*  $('div.alert').remove();
                $('#change-email-new-email').fadeOut('fast', function () {
                    $('#change-email-confirm').resetForm().fadeIn('slow');
                    //$('#change-email-confirm :submit').attr('disabled', 'disabled');
                });
				$('#verify_link_success').fadeIn();
                $('#verify_link_success').empty().html('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert"></a>' + op.msg + '</div>');       */          
                //$('#change-email-confirm').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + op.msg + '</div>');                
            }
        });
    });
   
});
