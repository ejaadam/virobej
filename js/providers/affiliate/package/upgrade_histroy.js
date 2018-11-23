$(function () {
	var t = $('#purchase_upgrade_histroy');
	DT = t.dataTable({ 
		ordering: false,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		 ajax: {
			url: $('#upgrade_history').attr('action'),
			type: 'POST',
			data: function ( d ) {
		 	    d.search_term = $('#search_term').val();
				d.from_date = $('#from_date').val(); 
				d.to_date = $('#to_date').val(); 	 
			},
        },  
		columns: [
	        
             {
			data: 'updated_date',
			name: 'updated_date',
			 },			
	          {
                data: 'package_name',
                name: 'package_name',
                class: 'text-left',
                render: function (data, type, row, meta) {
					//console.log(row);
                    var str = '';
                    str = '<b>' + row.package_name + '</b><br><span class="text-muted">Package QV : ' + row.package_qv + '</span><br><span class="text-muted">Weekly Capping QV : ' + row.weekly_capping_qv + '</span><br><span class="text-muted"><i class=""></i>Refundable Days : ' + row.refundable_days + '</span>';
                    return str;
                }
            },
			 {
			   data: 'amount',
			 name: 'amount',
			},
			 
			  {
				name: 'status',
				class: 'text-right no-wrap',
				data: function (row, type, set) {
                     if(row.status=='4') {
                     //$('tr th:last').remove();					 
					return '<button type="button" id="search_btn1" data-id ="' + row.subscribe_topup_id + '" class="btn btn-sm bg-olive search_btn1">' +'Confirm' +'</button>';  	
			 }	
			 else{
			 
			 }
		  }
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
});
	 $('#purchase_upgrade_histroy').on('click','.search_btn1',function() {

	        var CurEle = $(this);
		    $.ajax({
            type: 'POST',
            url: baseUrl + 'account/package/package_confirm',
			data: {id: CurEle.data('id')},
            success: function (op) {
	                 if (op.status == 200) {	
                        $('#purchase_upgrade_histroy').before('<div class="alert alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>'); 
                        $('.alert').fadeOut(6000);
                        DT.fnDraw();
                     } 
					  else
					  {
					  $('#purchase_upgrade_histroy').before('<div class="alert alert alert-danger">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>'); 
					  $('.alert').fadeOut(6000);
						DT.fnDraw();
					 }
            }
        });
	});
	$('#search_btn').click(function (e) {
		DT.fnDraw();
	});
	
	$('#reset_btn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        DT.fnDraw();
    });