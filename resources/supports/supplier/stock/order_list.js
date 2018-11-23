$(document).ready(function () {
    var DT = $('#orders_list').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, {
                    from: $('#start_date').val(),
                    to: $('#end_date').val(),
                });
            }
        },
        columns: [
            {
                data: 'created_date',
                name: 'created_date',
                render: function (data, type, row, meta) {
                    return new String(row.created_date).dateFormat('dd-mmm-yyyy');
                }
            },
            {
                data: 'order_code',
                name: 'order_code'
            },
            {
                data: 'firstname',
                name: 'firstname'
            },
            {
                data: 'qty',
                name: 'qty'
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'discount',
                name: 'discount'
            },
            {
                data: 'net_pay',
                name: 'net_pay'
            },
            {
                data: 'order_status_id',
                name: 'order_status_id',
                render: function (data, type, row, meta) {
                    var content = '';
                    if (row.order_status_id == 0) {
                        content = '<span class="label label-info">Placed</span>';
                    }
                    if (row.order_status_id == 1) {
                        content = '<span class="label label-info">Completed</span>';
                    }
                    if (row.order_status_id == 2) {
                        content = '<span class="label label-info">Processing</span>';
                    }
                    if (row.order_status_id == 3) {
                        content = '<span class="label label-success">Dispatched</span>';
                    }
                    if (row.order_status_id == 4) {
                        content = '<span class="label label-success">Delivered</span>';
                    }
                    if (row.order_status_id == 5) {
                        content = '<span class="label label-danger">Canceled</span>';
                    }
                    return content;
                }
            },
            {
                data: 'updated_date',
                name: 'updated_date',
                render: function (data, type, row, meta) {
                    return new String(row.updated_date).dateFormat('dd-mmm-yyyy');
                }
            }
        ]
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
});
