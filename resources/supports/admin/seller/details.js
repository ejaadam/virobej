$(document).ready(function () {

	$('#back-to-list').click(function (e) {
		e.preventDefault();
        window.location.href = "admin/seller";
    });
	 var DT = $('#seller_store_list');	
	$('#store_list').click(function (e) {
       		
        if (! $.fn.dataTable.isDataTable(DT)) {			
            DT.dataTable({               
                ajax: {
                    url: window.location.BASE + 'admin/seller/stores',
					type: 'POST',
                    data: {id: $('#suppliercode', '#merchant_profile').text()},
                },
                columns: [
                    {
                        data: 'created_on',
                        name: 'created_on',
                    },
                    {
                        data: 'store_name',
                        name: 'store_name',
                        render: function (data, type, row, meta) {
                            return '<b>' + row.store_name + ' (' + row.store_code + ')</b><p>' + ([row.locality, row.district, row.state, row.country].join(', ')) + '</p>';
                        }
                    },
                    {
                        name: 'logo',
                        data: function (row, type, set) {
                            return '<img class="img img-responsive" src="' + row.logo + '" alt="' + row.store_name + '"/> ';
                        }
                    },
                    {
                        data: 'mobile',
                        name: 'mobile',
                    },
                    {
                        name: 'status',
                        data: function (row, type, set) {
                            return '<span class="label label-' + row.status_class + '">' + row.status + '</span> ';
                        }
                    },
                    {
                        name: 'is_approved',
                        data: function (row, type, set) {
                            return '<span class="label label-' + row.is_approved_class + '">' + row.is_approved + '</span> ';
                        }
                    },
					{
                        name: 'is_premium',
                        data:'is_premium'
                    },
                    {
                        data: 'updated_by_uname',
                        name: 'updated_by_uname',
                        render: function (data, type, row, meta) {
                            return row.updated_by_full_name + ' (' + row.updated_by_uname + ')';
                        }
                    },
                   {
                     class: 'text-center',
                     orderable: false,
                     render: function (data, type, row, meta) {
						return addDropDownMenu(row.actions, true);
                     }
                   } 

                ],
                /* responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return data.store_name;
                            }
                        }),
                        renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                            tableClass: 'table'
                        })
                    }
                } */
            });
        }
    });
	
	DT.on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (op) {
            if (op.msg != undefined && op.msg != null) {
                $('#msg').html(op.msg);
            }
            DT.fnDraw();
            
        });
    });
	
	$('#cashiers-info').click(function (e) {
        var t = $('#admin-details');
        if (! $.fn.dataTable.isDataTable(t)) {
            t.dataTable({                
                ajax: {
                    url: window.location.BASE + 'admin/seller/admin-info',
                    type: "POST",
                    dataType: 'JSON',
                    data: {id: $('#suppliercode', '#merchant_profile').text()},
                },
                columns: [
                    {
                        data: 'signedup_on',
                        name: 'signedup_on',
                    },
                    {
                        data: 'uname',
                        name: 'uname',
                    },
                    {
                        data: 'full_name',
                        name: 'full_name',
                    },
                    {
                        name: 'mobile',
                        data: 'mobile',
                    },
                    {
                        data: 'email',
                        name: 'email',
                    },
                    {
                        data: 'store_name',
                        name: 'store_name'

                    },
                ],
               /*  responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return data.store_name;
                            }
                        }),
                        renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                            tableClass: 'table'
                        })
                    }
                } */

            });
        }
    });	
	
});
