$(function () {	
	var t = $('#listtbl');
	var DT = t.dataTable({
        bStateSave: false,		
        ajax: {
            url: window.location.SELLER + 'reports/instore/orders',
            data: function (d) {
                return $.extend({}, d, $('input,select', '#listfrm').serializeObject());
            }
        },
        columnDefs: [
            {className: 'text-left', targets: [0]},
            {className: 'text-left', targets: [1]},
            {className: 'text-left', targets: [2]},
            {className: 'text-left', targets: [3]},            
                   
        ],
        columns: [            
			{
                name: 'remarks',
                data: 'remarks',
				render: function (data, type, row, meta) {                  
                    return '<span>'+row.remarks+'<br>'+row.order_date+'</span>';
                }
            },            
            {
                name: 'bill_amount',
                data: 'bill_amount'
            },            
            {
                name: 'status',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.status_class + '">' + row.status + '</span>';
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true, 'load-content');
                }
            }
        ]
    });
    $('#listfrm').on('submit', function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#searchbtn').click(function (e) {
        DT.fnDraw();
    });
    $('#resetbtn').click(function (e) {
        $('input,select,input:checkbox', $(this).closest('form')).val('');
        $('input:checkbox').removeAttr('checked');
        DT.fnDraw();
    });	
});