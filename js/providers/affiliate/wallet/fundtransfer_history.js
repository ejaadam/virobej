$(function () {
//	alert(baseUrl);
	var t = $("#fundtransferlist");
	t.DataTable({
		ordering: false,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},	
		ajax: {
			url: $('#form_fundtransfer').attr('action'),
			type: 'POST',
			data: function ( d ) {
			
				d.search_term = $('#search_term').val();
				d.from_date = $('#from_date').val(); 
				d.to_date = $('#to_date').val(); 				
				d.currency_id = $('#currency_id').val(); 
				
				d.wallet_id = $('#wallet_id').val(); 
				
			
			},
        },
		columns: [
		  	{
				data: 'transfered_on',
				name: 'transfered_on',
			},
			{
				data: 'transaction_id',
				name: 'transaction_id',
			},
			{
				data: 'Ffrom_name',
				name: 'from_uname',
				
			},
			{
				data: 'Fto_name',
				name: 'to_uname',
			},
			{
				data: 'wallet_name',
				name: 'wallet_name',
				class: 'no-wrap'
			},
			{
				name: 'amount',
				data: 'Famount',
				class: 'text-right no-wrap'
			},
			{
				name: 'paidamt',
				class: 'text-right no-wrap',
				data: function (row, type, set) {
					return '<span class="text-' + row.tranTypeCls + '">' + row.Fpaidamt + '</span>';
					
				}
			},
			
			{
				name: 'status',
				class: 'text-right no-wrap',
				data: function (row, type, set) {
					return '<span class="label label-' + row.disp_class + '">' + row.status_name + '</span>';
					
				}
			}
		],
		responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function (row ) {
                        var data = row.data();
						console.log(data);
                        return "Trans.ID: #"+data.transaction_id;
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        },
	});
	
	$('#searchbtn').click(function (e) {
		t.dataTable().fnDraw();
	});
	
	$('#resetbtn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	
});




			