$(function () {
	var t = $('#listtbl');
	var DT = t.dataTable({
        bStateSave: false,
        ajax: {
            url: window.location.SELLER + 'reports/instore/commissions',
            data: function (d) {
                return $.extend({}, d, $('input,select', '#listfrm').serializeObject());
            }
        },
        columnDefs: [
            {className: 'text-center', targets: [0]},
            {className: 'text-center', targets: [1]},
            {className: 'text-left', targets: [2]},
            {className: 'text-left', targets: [3]},
            {className: 'text-right', targets: [4]},
            {className: 'text-left', targets: [5]},
            {className: 'text-center', targets: [6]},
            {className: 'text-center', targets: [7]}
        ],
         columns: [
            {
                name: 'created_on',
                data: 'created_on',
                class: 'text-left'
            },
            {
                name: 'order_code',
                data: 'order_code',
                render: function (data, type, row, meta) {
                    var str = '';
                    str = ' ' + row.order_code + ' ';
                    return str;
                },
                class: 'text-left'
            },
            {
                name: 'store_name',
                data: 'store_name',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    meta.settings.aoColumns[meta.col].bVisible = (row.store_code != undefined) ? true : false;
                    return (row.store_code != undefined) ? '<b>' + row.store_name + ' (#' + row.store_code + ')</b>' : '';
                }
            },
            {
                name: 'order_amt',
                data: 'order_amt',
                class: 'text-right'
            },
            {
                name: 'system_comm',
                data: 'system_comm',
                class: 'text-right'
            },
            {
                name: 'tax',
                data: 'tax',
                class: 'text-right'
            },
            {
                name: 'handling_charges',
                data: 'handling_charges',
                class: 'text-right'
            },
            {
                name: 'store_recd_amt',
                data: 'store_recd_amt',
                class: 'text-right'
            },            
			{
                name: 'status',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.mr_settlement_class + '">' + row.mr_settlement + '</span>';
                }
            },
            {
                name: 'status',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.status_class + '">' + row.status + '</span>';
                }
            }
        ],
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