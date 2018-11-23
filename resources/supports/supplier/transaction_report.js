
$(document).ready(function () {
    var DT = $('#transaction_report_datatable').dataTable({
        ajax: {
            url: window.location.API.SELLER + 'reports/transactions',
            data: function (d) {
                return $.extend({}, d, {
                    term: $('#term', $('#transaction_report')).val(),
                    ewallet_id: $('#ewallet_id', $('#transaction_report')).val(),
                    from: $('#from', $('#transaction_report')).val(),
                    to: $('#to', $('#transaction_report')).val()
                });
            }
        },
        columns: [
            {
                data: 'updated_on',
                name: 'updated_on',
                class: 'text-center no-wrap'
            },
            {
                name: 'full_name',
                data: 'full_name',
            },
            {
                data: 'description',
                name: 'description',
                render: function (data, type, row, meta) {
                    return  row.description + ((row.payment_type != '') ? ' through ' + row.payment_type : '');
                }
            },
            {
                data: 'wallet_name',
                name: 'wallet_name'
            },
            {
                name: 'amt',
                data: 'amt',
                class: 'text-right no-wrap',
                render: function (data, type, row, meta) {
                    return $('<span>', {class: row.transaction_type}).append(row.amt)[0].outerHTML;
                }
            },
            {
                name: 'paid_amt',
                data: 'paid_amt',
                class: 'text-right no-wrap',
                render: function (data, type, row, meta) {
                    return $('<span>', {class: row.transaction_type}).append(row.paid_amt)[0].outerHTML;
                }
            },
            {
                name: 'handle_amt',
                data: 'handle_amt',
                class: 'text-right no-wrap',
                render: function (data, type, row, meta) {
                    return $('<span>', {class: row.transaction_type}).append(row.handle_amt)[0].outerHTML;
                }
            },
            {
                name: 'current_balance',
                data: 'current_balance no-wrap',
                class: 'text-right no-wrap',
                render: function (data, type, row, meta) {
                    return $('<span>', {class: row.transaction_type}).append(row.current_balance)[0].outerHTML;
                }
            }
        ],
        order: [0, 'DESC']
    });
    $('#reset_btn').click(function () {
        $('#transaction_report').trigger('reset');
        DT.fnDraw();
    });
    $(document.body).on('click', '#search', function () {
        DT.fnDraw();
    });
});
