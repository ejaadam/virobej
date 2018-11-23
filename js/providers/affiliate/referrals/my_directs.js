$(function () {
	var t = $('#directslist');
	t.DataTable({
		ordering: true,
		serverSide: true,
		processing: false,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},	
		ajax: {
			url: $('#form_my_directs').attr('action'),
			type: 'POST',
			data: function ( d ) {
				d.search_term = $('#search_term').val();
				d.from_date = $('#from_date').val(); 
				d.to_date = $('#to_date').val(); 
				var filterchk = [];
					 $('#chkbox :checked').each(function() {
					   filterchk.push($(this).val());
					 });		
				d.filterchk = filterchk;  
			},
        },
		columns: [
				{
                    "data": "uname",
                    "name": "uname"
                },
				{
                    "data": "account_id",
                    "name": "account_id"
                },
                {
                    "data": "full_name",
                    "name": "full_name"
                },
                {
                    "data": "direct_sponser_uname",
                    "name": "sponser_uname"
                },
                {
                    "data": "level",
                    "name": "level"
                },
				
				{
                    "data": "rank",
                    "name": "rank"
                },
				{
                    "data": "signedup_on",
                    "name": "signedup_on"
                }, 
					
		],
		responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data.username+": "+data.uname;
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        }
		
	});
	
	$('#searchbtn').click(function (e) {
		t.dataTable().fnDraw();
	});
	
	$('#resetbtn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	
});
