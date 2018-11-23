$(function () {

	var t = $('#transactionlist');
	t.DataTable({
		ordering: true,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		ajax: {
		

			url: baseUrl + 'account/wallet/transactions', 
			type: 'POST',
			data: function ( d ) {
				d.search_term = $('#search_term').val();  
				d.from = $('#from').val(); 
				d.to = $('#to').val();
				d.wallet_id = $('#wallet_id').val();
				//d.currency_id= $('#currency_id').val();
			},
        },
		columns: [
		  	{
				data: 'created_on',
				name: 'created_on',
			},
			{
				data: 'remark',
				name: 'remark',
			},
			{
				data: 'wallet',
				name: 'wallet',
				class: 'no-wrap'
			},
			{
				name: 'paidamt',
				class: 'text-right no-wrap',
				data: function (row, type, set) {
					return '<font color="' + row.color + '">' + row.Fpaidamt + '</font>';
				}
			},
			{
				name: 'current_balance',
				data: 'Fcurrent_balance',
				class: 'text-right no-wrap'
			}
		],
		responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                       var data = row.data();
                        return data[0].span;
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        },
	});
	
	$('#search_btn').click(function (e) {
		t.dataTable().fnDraw();
	});
	
	$('#reset_btn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	/*t.on('processing.dt',function( e, settings, processing ){
		if (processing){
			 $('body').toggleClass('loaded');
			 console.log('sdfg');
		}else {
			$('body').toggleClass('loaded');
			console.log('3453');
		}
	});*/
});