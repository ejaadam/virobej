$(function () {
	var order_code = $('#order-details').attr('data-order_code');	
    $.ajax({
        url: document.location.SELLER + 'reports/instore/orders/details/' + order_code,
        success: function (op) {
            $('.order-type', '#order-details').hide();
            $('.' + op.details.order_type_key).show();
			
			var details = op.details;
			var order = op.details;
			var customer = op.details;	
			
			$('#ord-details').html('<span><strong>' + op.details.remarks + '</strong></span><br><div class="row"><div class="col-md-6">Order Number<br><strong>' + op.details.order_code.value + '</strong><br>' + op.details.order_date + '</div><div class="col-md-6">Amount<br><strong>' + op.details.amount.value + '</strong><br><span class="label label-' + op.details.status_class + '">' + op.details.status + '</span></div></div>');
			
			$('#mr-details').html('<div class="row"><div class="col-md-6"><strong>'+details.customer.label+' : </strong>'+details.customer.value+'<br><strong>Member ID : </strong>'+details.account_code.value+'<br><strong>'+details.mobile.label+' : </strong>'+details.mobile.value+'<br><strong>'+details.email.label+' : </strong>'+details.email.value+'</div><div class="col-md-6"></div></div>');
			
			var roww = '';
			if ((op.details.transaction_id != undefined) && (op.details.transaction_id != '')) {
				if (op.details.transaction_id.label != '') {
					roww = roww + '<tr><td>' + op.details.transaction_id.label + '</td><td class="text-right">' + op.details.transaction_id.value + '</td></tr>';
				}
			}
			
			if ((op.details.payment_details != undefined) && (op.details.payment_details != '')) {
                var row = '';
                $.each(op.details.payment_details, function (index, fld) {
                    if (fld.label != '') {
                        row = row + '<tr><td>' + fld.label + '</td><td class="text-right">' + fld.value + '</td></tr>';
                    } else {
                        row = row + '<tr><td>' + fld.value + '</td></tr>';
                    }
                });
                $('#pay-details').html('<p><strong>Payment Details ('+op.details.store_name+' - '+op.details.address+')</strong></p><div class="row"><div class="col-md-12"><table class="table">'+ roww + ' ' + row + '</table></div></div>');
            }		
			
			$('.faq').attr('href',details.support);
			
           /*  $.each(op.details, function (k, v) {
                if (k == 'status') {
                    $('#' + k, '#order-details ' + '.' + op.details.order_type_key).html($('<span>', {class: 'label label-' + op.details.status_class}).html(v));
                }
                else {
                    $('#' + k, '#order-details ' + '.' + op.details.order_type_key).html(v);
                }
            });  */
        }
    });
});