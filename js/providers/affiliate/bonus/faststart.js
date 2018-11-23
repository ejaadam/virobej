$(function () {
var t = $('#faststart_bouns_list');
	t.DataTable({
		ordering:false,
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
			url: $('#form_faststart_bonus').attr('action'),
			type: 'POST',
			data: function ( d ) {
			d.from_date = $('#from_date').val(); 
			d.to_date = $('#to_date').val(); 
			},
        }, 
		columns: [
		    {
				data: 'from_uname',
				name: 'from_uname',
			},
			{
				data: 'package_name',
				name: 'package_name',
			},
			{
			     data: 'created_date',
				 name: 'created_date',
			},
			{
				data: 'Famount',
				name: 'Amount',
				class: 'text-right no-wrap'
			},
		    {
				data: 'qv',
				name: 'qv',
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
	
			/*{
				name: 'Status',
				data:function(row,type,set){
			 return '<label class="label label-sucess">'+row.status_name+'</label>';
				
			}, */
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

