$(document).ready(function () {
    var DT = $('#transaction_report_datatable').dataTable({
        ajax: {
            url: window.location.API.SELLER + 'reports/payments',
            data: function (d) {
                return $.extend({}, d, {
                    term: $('#term', $('#transaction_report')).val(),
                    wallet_id: $('#wallet_id', $('#transaction_report')).val(),
                    from: $('#from', $('#transaction_report')).val(),
                    to: $('#to', $('#transaction_report')).val()
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'sosc.created_on',
                class: 'no-wrap text-center',
            },
            {
                name: 'company_name',
                data: 'company_name',
                render: function (data, type, row, meta) {
                    var c = $('<div>').attr({}).append([
                        $('<a>').attr({href: window.location.BASE + 'seller/products/details/' + row.product_code}).append($('<strong>').html(row.product_name))
                    ]);
                    return  c[0].outerHTML;
                }
            },
            {
                name: 'order_code',
                data: 'order_code'
            },
            {
                name: 'order_item_code',
                data: 'order_item_code'
            },
            {
                name: 'mrp_price',
                data: 'mrp_price',
                class: 'no-wrap text-right',
            },
            {
                name: 'discount_per',
                data: 'discount_per',
                class: 'no-wrap text-right'
            },
            {
                name: 'sold_price',
                data: 'sold_price',
                class: 'no-wrap text-right',
            },
            {
                name: 'qty',
                data: 'qty',
                class: 'text-right',
            },
            {
                name: 'price_sub_total',
                data: 'price_sub_total',
                class: 'no-wrap text-right',
            },
            {
                name: 'site_commission_sub_total',
                data: 'site_commission_sub_total',
                class: 'no-wrap text-right',
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                class: 'text-center'
            }
        ]
    });
    $(document.body).on('click', '#search', function () {
        DT.fnDraw();
    });
});
