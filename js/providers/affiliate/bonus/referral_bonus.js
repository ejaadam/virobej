	$(function () {
 //$('#search').click(function () {
	var t = $('#referral_bouns_list');
	t.DataTable({
		ordering:false,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		ajax: {
			url: $('#form_referral_bonus').attr('action'),
			type: 'POST',
			data: function ( d ) {			
				d.search_term = $('#search_term').val();
				d.type_of_package = $('#type_of_package').val();
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
				data: 'created_date',
				name: 'created_date',
			},
			{
				data: 'from_uname',
				name: 'fromusername',
			},
			{
			     data: 'upline_username',
				 name: 'uplineusername',
			},
			{
				data: 'sponser_full_name',
				name: 'referrer',
			},
		    {
				data: 'package_name',
				name: 'packagename',
			},
			{
				data: 'pay_mode',
				name: 'payament',
			},
			{
				data: 'Famount',
				name: 'Amount',
				class: 'text-right no-wrap'
			},
			
			/*{
				data: 'packagepricing',
				name: 'packagepricing',
			},*/
			{
				name: 'Status',
				data:function(row,type,set){
				  return '<label class="label label-'+row.disp_class+'">'+row.status_name+'</label>';
				}
				
			},
			],
			/*responsive: {
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
        },*/
			
 });

	
	$('#searchbtn').click(function (e) {
		t.dataTable().fnDraw();
	});
	
	$('#resetbtn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	
});

