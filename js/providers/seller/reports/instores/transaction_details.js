$(function () {
	var order_code = $('#order-details').attr('data-order_code');	
    $.ajax({
        url: document.location.SELLER + 'reports/instore/transaction/details/' + order_code,
        success: function (op) {
            $('.order-type', '#order-details').hide();
            $('.' + op.details.order_type_key).show();
			
			var details = op.details;
			var order = op.details;
			var customer = op.details;
			$('#ord-details').html('<span><strong>'+details.remarks+'</strong></span><br><div class="row"><div class="col-md-6">Order Number<br><strong>'+details.order_code.value+'</strong><br>'+details.order_date+'</div><div class="col-md-6">Amount<br><strong>'+details.bill_amount.value+'</strong><br><span class="label label-'+details.status_class+'">'+details.status+'</span></div></div>');
			
			$('#mr-details').html('<div class="row"><div class="col-md-6"><strong>'+details.customer.label+' : </strong>'+details.customer.value+'<br><strong>Member ID : </strong>'+details.account_code.value+'<br><strong>'+details.mobile.label+' : </strong>'+details.mobile.value+'<br><strong>'+details.email.label+' : </strong>'+details.email.value+'</div><div class="col-md-6"></div></div>');
			
			$('#pay-details').html('<p><strong>Payment Details ('+details.store_name+' - '+details.store_address+')</strong></p>');
			if (details.order_type == 'Pay') {
				$('#pay-details').append('<div class="row"><div class="col-md-6">PG Transaction No : '+details.pg_trans_no+'</div></div>');
			}
			$('#pay-details').append('<div class="row"><div class="col-md-6">Payment Type : '+details.order_type+'</div></div>');
			$('#pay-details').append('<div class="row"><div class="col-md-6"><strong>Bill Amount:</strong></div><div class="col-md-6 pull-right"><strong>'+details.bill_amount.value+'</strong></div><div class="col-md-6"><strong>Fees:</strong></div><div class="col-md-6 pull-right">'+details.fees+'</div><div class="col-md-6">Tax (GST) </div><div class="col-md-6 pull-right">'+details.tax+'</div><div class="col-md-6">Paid at Outlet </div><div class="col-md-6 pull-right">'+details.collected_amount.value+'</div><div class="col-md-6">Net Pay </div><div class="col-md-6 pull-right"><span class="label label-'+details.mr_settlement_class+'">'+details.balance+'</span></div></div>');			
			
			$('.faq').attr('href',details.support)
			
           
        }
    });
});