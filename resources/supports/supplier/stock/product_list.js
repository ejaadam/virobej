$(document).ready(function () {
    var DT = $('#stock_list').dataTable({
        ajax: {
            url: window.location.API.SUPPLIER + 'products/stock',
            data: function (d) {
                return $.extend({}, d, $('#product_stock_list').serializeObject());
            }
        },
        columns: [
            {
                data: 'updated_on',
                name: 'ssm.updated_on',
                class: 'text-center'
            },
            {
                data: 'product_name',
                name: 'product_name',
                render: function (data, type, row, meta) {
                    return $('<div>').append([
                        $('<a>', {href: row.edit_product}).append([$('<b>').html(row.product_name), ' (' + row.product_code + ')']),
                        $('<h5>').append([
                            $('<span>').append($('<b>').html('Category:'), row.category),
                            $('<span>').append($('<b>').html('Brand:'), row.brand_name)
                        ])
                    ])[0].outerHTML;
                }
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center'
            },
            {
                data: 'stock_on_hand',
                name: 'stock_on_hand',
                class: 'text-right'
            },
            {
                data: 'commited_stock',
                name: 'commited_stock',
                class: 'text-right'
            },
            {
                data: 'sold_items',
                name: 'sold_items',
                class: 'text-right'
            },
        ],
        drawCallback: function (settings) {
            if (settings.json.filters !== undefined) {
                if (settings.json.filters.categories) {
                    $('#product_stock_list #category_id').addOptions(settings.json.filters.categories);
                }
                if (settings.json.filters.brands) {
                    $('#product_stock_list #brand_id').addOptions(settings.json.filters.brands);
                }
            }
        }
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
});
