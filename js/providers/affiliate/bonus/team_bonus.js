$(function () {

	var t = $('#form_team_bonus');
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
			url: $('#team_bonus').attr('action'),
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
				data: 'created_date',
				name: 'created_date',
			},
			{
				data: 'leftcarryfwd',
				name: 'leftcarryfwd',
			},
			{
				data: 'rightcarryfwd',
				name: 'rightcarryfwd',
			},
		    {
				data: 'leftbinpnt',
				name: 'leftbinpnt',
			},
		    {
				data: 'rightbinpnt',
				name: 'rightbinpnt',
			},
			{
				data: 'totleftbinpnt',
				name: 'totleftbinpnt',
			},
			{
				data: 'totrightbinpnt',
				name: 'totrightbinpnt',
			},
			{
				data: 'bonus_value',
				name: 'bonus_value',
			},
			{
				data: '',
				name: '',
			},
			{
				data: 'income',
				name: 'income',
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
				data: 'paidinc',
				name: 'paidinc',
			},
			{
				name: 'Status',
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
