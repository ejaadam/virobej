$(function () {

	var t = $('#personal_commission');
	t.DataTable({
		ordering:true,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return data.uname;
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        },
		ajax: {
			url: $('#personal_customer_commission').attr('action'),
			type: 'POST',
			data: function ( d ) {
			d.from_date = $('#from_date').val(); 
			d.to_date = $('#to_date').val(); 
			},
        }, 
	 columns: [
		
			{
			    data: 'confirm_date',
				name: 'confirm_date',
			},
			{
				data: 'direct_cv',
				name: 'direct_cv',
			},
			{
				data: 'self_cv',
				name: 'self_cv',
			},
			{
				data: 'slab',
				name: 'slab',
			},
			{
				data: 'total_cv',
				name: 'total_cv',
			},	
			{
				data: 'earnings',
				name: 'earnings',
			},
			{
				data: 'commission',
				name: 'commission',
			},
			
			{
				data: 'tax',
				name: 'tax',
			},
			{
				data: 'ngo_wallet',
				name: 'ngo_wallet',
			},
			
			{
				data: 'net_pay',
				name: 'net_pay',
			},
	        
			{
				name: 'status',
				data:function(row,type,set){
				  return '<label class="label label-'+row.status_dispclass+'">'+row.status+'</label>';
				}
				
			},
			
	
			],
			
 });

	
	$('#searchbtn').click(function (e) {

		t.dataTable().fnDraw();
	});
	
	$('#resetbtn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	
});
