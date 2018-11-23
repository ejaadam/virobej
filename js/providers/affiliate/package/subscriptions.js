$(function () {
	var t = $('#subscriptions');
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
			url: $('#my_packages').attr('action'),
			type: 'POST',
			data: function ( d ) {
				d.search_term = $('#search_term').val();
				d.wallet_id = $('#wallet_id').val();
				d.from_date = $('#from_date').val(); 
				d.to_date = $('#to_date').val(); 				
			},
        },
		columns: [
		  	{
				data: 'purchased_date',
				name: 'purchased_date',
			},
			 
              {
                data: 'package_name',
                name: 'package_name',
                class: 'text-left',
                render: function (data, type, row, meta) {
				//console.log(row); return false;
                    var str = '';
                    str = '<b>' + row.package_name + '</b><br><span class="text-muted">Refundable Days: ' + row.refundable_days + '</span><br><span class="text-muted"><i class="fa fa-calendar"></i>' + row.refund_expire_on + '</span>';
                    return str; 
                }
            },
			
			{
			data: 'amount',
			 name: 'amount',
			 },
			
			
			 
			
			
			/* {
                    "class": "text-center",
                    "orderable": false,
                    "render": function (data, type, row, meta) {
                         console.log(row);
                        var action_buttons =  '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear" aria-hidden="true"></i> <span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right text-left" role="menu">';
						
                        action_buttons = action_buttons+'<li><a href="#" id="search_btn1" rel="" data-status="" class="view_btn text-red">Reject</a></li>';
						
                        action_buttons = action_buttons + '</ul></div>';	
                        return action_buttons;
                    }
                } */
			
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
/* 	 $('#subscriptions').on('click','.search_btn1',function() {

	        var CurEle = $(this);
		    $.ajax({
            type: 'POST',
            url: baseUrl + 'account/package/package_confirm',
			data: {id: CurEle.data('id')},
            success: function (op) {
	     
                    if (op.status == 200) {
                        $('#subscriptions').before('<div class="alert alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>'); 
                       $('.alert').fadeOut(6000);
                        t.dataTable().fnDraw();
              }
            }
        });
	}); */
	
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
