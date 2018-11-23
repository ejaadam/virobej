$(function () {
	 var t = $('#car_bonus_commission');
	t.DataTable({
		ordering:true,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		
		ajax: {
			url: $('#car_bonus_details').attr('action'),
			type: 'POST',
			data: function ( d ) {
			d.from_date = $('#from_date').val(); 
			d.to_date = $('#to_date').val(); 
			},
      }, 
 	   columns: [
		    {
				data: 'created_on',
				name: 'created_on',
			},
			{
				data: 'confirm_date',
				name: 'confirm_date',
			},
			{
				data: 'amount',
				name: 'amount',
			},
			{
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return '<span class="label label-' + row.status_dispCls + '">' + row.status + '</span>';
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
