$(document).ready(function () {
    var supplier_id = $('#product_id').data('supplier_id');
    $('#product_stock_log #product_id').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'supplier-product-list',
        data: {'supplier_id': supplier_id},
        key: 'product_id',
        value: 'product_name'
    });
    var DT = $('#table3').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, {
                    'search_term': $('#search_term').val(),
                    from: $('#start_date').val(),
                    to: $('#end_date').val(),
                    'product_id': $('#product_id').val(),
                    'supplier_id': $('#supplier_id').val(),
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('yyyy-mmm-dd');
                }
            },
            {
                data: 'product_name',
                name: 'product_name',
            },
            {
                data: 'company_name',
                name: 'product_name',
            },
            {
                data: 'stock_value',
                name: 'stock_value',
            },
            {
                data: 'current_stock_value',
                name: 'current_stock_value',
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, row, meta) {
                    if (row.status == 1) {
                        return ' <span class="label label-success">Active</span>'
                    } else {
                        return ' <span class="label label-danger">Inactive</span>'
                    }
                }
            },
            {
                class: 'text-center',
                orderable: false,
                data: 'parent_category_id',
                name: 'parent_category_id',
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '<div class="btn-group">';
                    action_buttons = action_buttons + '<button type="button" class="btn btn-xs btn-primary ">Action</button>';
                    action_buttons = action_buttons + '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    /*action_buttons = action_buttons + '<ul class="dropdown-menu dropdown-menu-right" role="menu">';
                     action_buttons = action_buttons + '<li><a class="edit" href="' + json.url + '/supplier/products/edit/' + row.product_id + '">Configure</a></li>';
                     action_buttons = action_buttons + '<li><a href="' + json.url + '/supplier/products/delete/' + row.product_id + '" class="delete_btn" data="' + row.product_id + '" >Delete</a></li>';
                     if (row.status == 1)
                     {
                     action_buttons = action_buttons + '<li><a data="0" href="' + json.url + '/supplier/products/product_status/' + row.product_id + '" class="product_status"  >Inactive</a></li>';
                     }
                     else
                     {
                     action_buttons = action_buttons + '<li><a data="1" href="' + json.url + '/supplier/products/product_status/' + row.product_id + '" class="product_status"  >Active</a></li>';
                     }
                     action_buttons = action_buttons + '<li><a class="edit_stock" href="' + json.url + '/supplier/products/edit_stock/' + row.product_id + '">Stock</a></li>';
                     action_buttons = action_buttons + '</ul></div>';*/
                    return action_buttons;
                }
            },
        ]
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
});
