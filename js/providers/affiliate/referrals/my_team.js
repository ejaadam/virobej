$(function () {
//	alert(baseUrl);
	var t = $('#downlinelist');
		
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
			url: $('#downline_form').attr('action'),
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
			}
        },
		columns: [
		    {   
			    data: 'uname',
                name: 'uname',
                class: 'text-left',
                render: function (data, type, row, meta) {
					
                    var str = '';
                    str = '<span class="text-muted"><b>Username : </b>' + row.uname + '</span><br><span class="text-muted"><b>Name : </b>' + row.full_name + '</span>';
                    return str;
                }
              },
                {
                    "data": "direct_sponser_uname",
                    "name": "direct_sponser_uname"
                },
				{
                    "data": "signedup_on",
                    "name": "signedup_on"
                },
                {
                    "data": "upline_uname",
                    "name": "upline_uname"
                },
                {
                    "data": "rank",
                    "name": "rank"
                },
				{
					"data": "",
                    "name": ""
				},

                {
                    "data": "qv",
                    "name": "qv"
                },
				{
                    "data": "cv",
                    "name": "cv"
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
