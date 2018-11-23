/* [ ---- Ebro Admin - invoices ---- ] */

	$(function() {
		ebro_invoice.init();
	})
	
	ebro_invoice = {
		init: function() {
			if($('#invoice_qrcode').length) {
				var qr_base_size = '60',
					qr_text = 'http://themeforest.net/user/tzd/portfolio',
					qr_size = isRetina() ? qr_base_size*2 : qr_base_size;
				
				$('#invoice_qrcode').qrcode({
					render: 'image',
					size: qr_size,
					text: qr_text
				}).children('img').prop('title',qr_text).css('width',qr_base_size);
			}
		}
	}