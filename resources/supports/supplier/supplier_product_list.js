var product_id = null, products = [];
var SPLDT = $('#supplier_product_list_table').dataTable({
    ajax: {
        url: window.location.API.SELLER + 'products',
        data: function (d) {
            return $.extend({}, d, $('#supplier_product_list_form').serializeObject());
        }
    },
    columns: [
        {
            data: 'spi_created_on',
            name: 'sp.spi_created_on',
            class: 'text-center'
        },
        {
            data: 'img_path',
            name: 'img_path',
            render: function (data, type, row, meta) {
                return $('<a>', {class: 'pull-left', href: row.imgs[0].img_path}).append([
                    $('<img>', {src: row.imgs[0].img_path, alt: '', width: '100px', class: 'media-object thumbnail'})
                ])[0].outerHTML;
            }
        },
        {
            data: 'product_name',
            name: 'product_name',
            render: function (data, type, row, meta) {
                return $('<div>').append([
                    $('<b>').html(row.product_name),
                    ' (' + row.product_code + ')',
                    $('<h5>').append([
                        $('<span>').append($('<b>').html('Category:'), row.category),
                        $('<span>').append($('<b>').html('Brand:'), row.brand_name)
                    ])
                ])[0].outerHTML;
            }
        },
        {
            data: 'mrp_price',
            name: 'mrp_price',
            class: 'text-right'
        },
        {
            data: 'price',
            name: 'price',
            class: 'text-right',
            render: function (data, type, row, meta) {
                return row.price + ' ' + $('<span>', {class: 'label label-warning'}).html(row.off_per)[0].outerHTML;
            }
        },
        {
            data: 'stock_on_hand',
            name: 'stock_on_hand',
            class: 'text-right'
        },
        {
            data: 'spi_status',
            name: 'spi_status',
            class: 'text-center status',
            render: function (data, type, row, meta) {
                //return (' <span class="' + row.verification_class + '">' + row.verification + '</span> ') + (' <span class="' + row.status_class + '">' + row.status + '</span> ');
                return (' <span class="' + row.verification_class + '">' + row.verification + '</span> ') + (' <span class="' + row.status_class + '">' + row.status + '</span> ');
            }
        },
        {
            class: 'text-center',
            orderable: false,
            render: function (data, type, row, meta) {
                var json = $.parseJSON(meta.settings.jqXHR.responseText);
                products[row.supplier_product_code] = row;
                var action_buttons = '<div class="btn-group">';
                action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                action_buttons += '<li><a data-supplier_product_code="' + row.supplier_product_code + '" class="edit_product" href="' + json.url + '/seller/products/' + row.supplier_product_code + '">Configure</a></li>';
                action_buttons += (row.spi_status == Constants.ACTIVE)
                        ? '<li><a data-supplier_product_id="' + row.supplier_product_id + '" data-status="' + Constants.INACTIVE + '" href="' + json.url + '/api/v1/seller/products/change-status" class="change_status">Inactive</a></li>'
                        : '<li><a data-supplier_product_id="' + row.supplier_product_id + '" data-status="' + Constants.ACTIVE + '" href="' + json.url + '/api/v1/seller/products/change-status" class="change_status">Active</a></li>';
                action_buttons += '<li><a data-supplier_product_id="' + row.supplier_product_id + '" class="delete_btn" href="' + json.url + '/api/v1/seller/products/delete">Delete</a></li>';
                action_buttons += '</ul></div>';
                return action_buttons;
            }
        }
    ],
    drawCallback: function (settings) {
        if (settings.json.filters !== undefined) {
            if (settings.json.filters.categories) {
                $('#supplier_product_list_form #panel_category_id').addOptions(settings.json.filters.categories);
            }
            if (settings.json.filters.brands) {
                $('#supplier_product_list_form #panel_brand_id').addOptions(settings.json.filters.brands);
            }
        }
    }
});
$('#search').click(function (e) {
    e.preventDefault();
    SPLDT.fnDraw();
});
function configure(data)
{
    $('#configure').show();
    $('#configure').attr('data-product_id', data.product_id);
    $('#configure').attr('data-supplier_product_id', data.supplier_product_id);
    $('#add-edit-form #product_supplier_id').val(data.supplier_product_id);
    $('#add-edit-form #supplier_product_id').val(data.supplier_product_id);
    $('#supplier_product_form #supplier_product_id_com').val(data.supplier_product_id);
    $('#configure').show();
    $('#configure input').each(function (event) {
        var id = '';
        var name = $(this).attr('id');
        if (data[name] != undefined)
        {
            $(this).attr('value', data[name]);
        }
    });
    $('#configure').attr('data-product_id', data.product_id);
    //$('#configure').attr('data-supplier_product_id',data.supplier_product_id);
    $('#supplier_product_form #supplier_product_id_com').val(data.supplier_product_id);
    $('#supplier_product_form #mrp_price').val(data.mrp_price);
    $('#supplier_product_form #price').val(data.price);
    $('#supplier_product_form #pre_order').val(data.pre_order);
    $('#supplier_product_form #supplier_id').loadSelect({
        url: window.location.BASE + 'supplier-list',
        key: 'id',
        value: 'name',
        selected: data.supplier_id
    });
    $('#supplier_product_list_form #store_ids').loadSelect({
        url: window.location.BASE + 'store-list',
        key: 'store_id',
        value: 'store_name',
        data: {supplied_id: supplier_id},
        selected: data.store_id
    });
    $('#supplier_product_list_form #panel_category_id').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.API.SELLER + 'categories-list',
        key: 'id',
        value: 'name'
    });
    $('#supplier_product_list_form #panel_brand_id').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.API.SELLER + 'brands-list',
        key: 'brand_id',
        value: 'brand_name'
    });
    $('#supplier_product_list_form #panel_currency_id').loadSelect({url: window.location.BASE + 'currencies-list',
        key: 'currency_id',
        value: 'currency'
    });
    $('#supplier_product_list_form #product_id').loadSelect({
        url: window.location.BASE + 'products-list',
        key: 'product_id',
        value: 'product_name',
        dependingSelector: ['#supplier_product_form #category_id', '#supplier_product_form #brand_id'],
        selected: data.product_id
    });
    $('#supplier_product_form #currency_id').loadSelect({
        url: window.location.BASE + 'currencies-list',
        key: 'currency_id',
        value: 'currency',
        selected: data.currency_id
    });
    $('#list').hide();
}
function add_new_products()
{
    $('#add_new').show();
    $('#list').hide();
    $('#form').hide();
    $('#tags').select2({
        ajax: {
            url: window.location.BASE + 'get-tags',
            data: function (params) {
                return {
                    search_term: params
                };
            },
            results: function (data) {
                return {
                    results: data
                };
            }
        },
        tags: true,
        tokenSeparators: [',', ' '],
        minimumInputLength: 3
    });
    $('#product_form #category_id').loadSelect({
        url: window.location.BASE + 'product-categories-list',
        key: 'id',
        value: 'name'});
    $('#product_form #brand_id').loadSelect({
        url: window.location.BASE + 'product-brands-list',
        key: 'brand_id',
        value: 'brand_name'
    });
    $('#add_new #info #product_visibility').loadSelect({
        firstOption: {key: '', value: '--Select--'},
        firstOptionSelectable: false,
        url: window.location.BASE + 'product-visibility-list',
        key: 'visiblity_id',
        value: 'visiblity_desc'
    });
    $('#add_new #info #condition').loadSelect({
        firstOption: {key: '', value: '--Select--'},
        firstOptionSelectable: false,
        url: window.location.BASE + 'product-condition-list',
        key: 'condition_id',
        value: 'condition_desc'
    });
    //upload($url = window.location.BASE + 'supplier/product', $id = 'extraupload')
}
$('#add-product-btn').on('click', function (e) {
    e.preventDefault();
    $('#list', $('#product_list')).hide();
    $('#add-new-product').show();
});
$('#back-to-list').on('click', function (e) {
    e.preventDefault();
    $('#add-new-product').hide();
    $('#list', $('#product_list')).show();
});
function repoFormatResult(pro) {
    return $('<div>', {class: 'row'}).append([
        $('<div>', {class: 'col-xs-3'}).append([
            $('<img>', {class: 'img', src: pro.img})
        ]),
        $('<div>', {class: 'col-xs-9'}).append([
            $('<div>', {class: 'row'}).append([
                $('<h3>', {class: ''}).append(pro.name),
                $('<div>', {class: 'col-xs-6'}).append(['EAN Barcode: ', pro.eanbarcode]),
                $('<div>', {class: 'col-xs-6'}).append(['UPC Barcode: ', pro.upcbarcode])
            ])
        ])
    ]);
}
function repoFormatSelection(pro) {
    $('#upcbarcode', '#add-new-product-form').val(pro.upcbarcode);
    $('#eanbarcode', '#add-new-product-form').val(pro.eanbarcode);
    return $('<div>', {class: 'row'}).append([
        $('<div>', {class: 'col-xs-3'}).append([
            $('<img>', {class: 'img', src: pro.img})
        ]),
        $('<div>', {class: 'col-xs-9'}).append([
            $('<div>', {class: 'row'}).append([
                $('<h3>', {class: ''}).append(pro.name),
                $('<div>', {class: 'col-xs-6'}).append(['EAN Barcode: ', pro.eanbarcode]),
                $('<div>', {class: 'col-xs-6'}).append(['UPC Barcode: ', pro.upcbarcode])
            ])
        ])
    ]);
}
$('#product_search', '#add-new-product-form').select2({
    ajax: {
        type: 'POST',
        url: window.location.API.SELLER + 'products/addables',
        data: function (params) {
            return {
                search_term: params
            };
        },
        results: function (data) {
            return {
                results: data
            };
        }
    },
    formatResult: repoFormatResult,
    formatSelection: repoFormatSelection,
    escapeMarkup: function (m) {
        return m;
    },
    minimumInputLength: 3
});
$('#add-new-product-form').on('submit', function (e) {
    e.preventDefault();
    $('.alert').remove();
    var form = $(this);
    $.ajax({
        data: form.serialize(),
        url: form.attr('action'),
        success: function (data) {
            if (data.status == 'OK') {
                window.location.href = data.url;
            }
            else {
                form.before($('<div>').attr({class: 'alert alert-danger'}).append(data.msg));
            }
        }
    });
});
$('#add_new').on('change', '#status', function (event) {
    event.preventDefault();
    if ($('#status option:selected').val() == 0)
    {
        $('#redirect_status').show();
        $('#add_new #info #redirect_stat').loadSelect({
            firstOption: {key: '', value: 'All'},
            firstOptionSelectable: true,
            url: window.location.BASE + 'redirect-disabled-list',
            key: 'redirect_lookup_id',
            value: 'redirect_desc'
        });
    }
    else
    {
        $('#redirect_status').hide();
    }
});
$('#add_new').on('click', '#assoc_tab', function (event) {
    event.preventDefault();
    $('#add_new #assoc #category_id').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'product-categories-list',
        key: 'id',
        value: 'name'
    });
    $('#add_new #assoc #brand_id').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'product-brands-list',
        key: 'brand_id',
        value: 'brand_name'
    });
});
function update_property_and_value(property_id, value_id, cmb_properties, form) {
    var product_cmb = '';
    $('option', property_id).each(function () {
        if ($(this).val() != '') {
            $(this).attr({disabled: false, hidden: false});
        }
    });
    $('option', value_id).each(function () {
        if ($(this).val() != '') {
            $(this).attr({disabled: false, hidden: false})
        }
    });
    $('option', cmb_properties).each(function () {
        product_cmb += ((product_cmb != '') ? ',' : '') + $(this).text();
        var temp = new String($(this).val()).split(':', 2);
        $('option[value=' + temp[0] + ']', property_id).attr({disabled: true, hidden: true});
        $('option[value=' + temp[1] + ']', value_id).attr({disabled: true, hidden: true});
        $('#product_cmb', form).val(product_cmb.trim(':'));
    });
    property_id.val('').trigger('change');
    value_id.val('').trigger('change');
    if ($('option', cmb_properties).length >= 1) {
        $('button[type="submit"]', form).removeAttr('disabled');
    }
    else {
        $('button[type="submit"]', form).attr('disabled', true);
    }
}
function get_add_stock(product_id, product_name, sku, stock_value, product_code)
{
    var title = 'Combinations';
    var url = window.location.BASE + 'seller/products/add_stock/' + product_code;
    window.location.ChangeUrl(title, url);
    $('#product_list').hide();
    $('#add_stock').show();
    $('#add_stock #add_stocks').find('#pro_name').text(product_name);
    $('#add_stock #add_stocks').find('#sku_code').text(sku);
    $('#add_stock #add_stocks').find('#current_stock_value').text(stock_value);
}
var CDT = $('#combination_table').dataTable({
    ajax: {
        url: window.location.API.SELLER + 'products/combinations',
        data: function (d) {
            return $.extend({}, d, {
                product_id: product_id,
                supplier_product_id: $('#product_supplier_id').val(),
                search_term: $('#supplier_product_list_form #search_term').val(),
                category_id: $('#supplier_product_list_form #category_id').val(),
                brand_id: $('#supplier_product_list_form #brand_id').val(),
                from: $('#supplier_product_list_form #start_date').val(),
                to: $('#supplier_product_list_form #end_date').val()
            });
        }
    },
    columns: [
        {
            data: 'created_on',
            name: 'created_on',
            class: 'text-center'
        },
        {
            data: 'product_cmb_code',
            name: 'product_cmb_code'
        },
        {
            data: 'stock_value',
            name: 'stock_value'
        },
        {
            data: 'impact_on_price',
            name: 'impact_on_price',
        },
        {
            class: 'text-center',
            orderable: false,
            render: function (data, type, row, meta) {
                return addDropDownMenu(row.actions, true);
            }
        }
    ]
});
function get_combination_list(product_id)
{
    product_id = (product_id != undefined) ? product_id : 0;
    if (product_id != 0)
    {
        var data = {product_id: product_id};
    }
    $('#combination_list').show();
    $('#combination_list #add_combination_btn').attr('data-product_id', product_id)
    CDT.fnDraw();
}
$(document.body).on('click', '#combination_table #edit_combination_product', function (e) {
    $('#add-edit-form').find('input[type=text], textarea').val('');
    $('#add-edit-form').trigger('reset');
    var con = $('#product_id').val();
    $('#add-edit-form .panel-body #property_id').loadSelect({
        url: window.location.BASE + 'seller/products/properties-list-for-com',
        key: 'property_id',
        value: 'property',
        data: {'product_id': con},
        success: function () {
            $('#add-edit-form .panel-body #value_id').loadSelect({
                url: window.location.BASE + 'seller/products/values-list-for-com',
                key: 'value_id',
                value: 'value',
                data: {'product_id': con},
                dependingSelector: ['#property_id']
            });
        }
    });
    e.preventDefault();
    var CurEle = $(this);
    $.ajax({
        data: {'product_cmb_id': CurEle.data('product_cmb_id')},
        url: window.location.BASE + 'seller/products/get_combinations_by_id',
        success: function (data) {
            $('#product_cmb_id').val(data.data[0].product_cmb_id);
            $('#add-edit-form #product_cmb').val(data.data[0].product_cmb);
            $('#add-edit-form #sku').val(data.data[0].sku);
            /*$.each(data.data.property_product_cmd, function(key, value) {
             $('#product_cmb_properties')
             .html($('<option>', { value : '' })
             .text(data.data.property_product_cmd));
             });*/
            $('#add-edit-form #product_cmb_properties').html(data.data.output);
            $('#add_combination').show();
            $('#add-edit-form #product_stock_value').val(data.data[0].stock_value);
            $('#add-edit-form #product_impact_price').val(data.data[0].impact_on_price);
        }
    });
});
$(document.body).on('click', '.manage-properties', function (e) {
    e.preventDefault();
    var CurEle = $(this);
    $.ajax({
        data: {product_id: CurEle.data('product_id')},
        url: window.location.BASE + 'seller/products/properties-values-checked',
        success: function (data) {
            $('#product_id', $('#product_properties_form')).val(CurEle.data('product_id'));
            $('#add_property #panel-title').text(CurEle.data('product'));
            $('#product_list').hide();
            $('#add_property').show();
            $('#properties').loadPropertiesTree(
                    {
                        url: window.location.BASE + 'seller/products/properties-for-checktree',
                        data: {category_id: CurEle.data('category_id')},
                        checked: data.properties,
                        choosable: data.choosable,
                        key_value: data.key_value
                    },
            {
                url: window.location.BASE + 'seller/products/property-values-for-checktree',
                parentKey: 'property_id',
                checked: data.values
            }
            );
        }
    });
});
$(document.body).on('click', '.add_stock', function (event) {
    event.preventDefault();
    var CurEle = $(this);
    var product_id = CurEle.data('product_id');
    var product_code = CurEle.data('product_code');
    var product_name = CurEle.data('product_name');
    var stock_value = CurEle.data('stock_value');
    var sku = CurEle.data('sku');
    $('#add_stock .panel-body #product_id').val(product_id);
    if (CurEle.data('stock_value') > 0)
    {
        $('#new_stock_value').hide();
        $('#select_combination').show();
        $('#add_stock .panel-body #combination_id').loadSelect({url: window.location.BASE + 'seller/products/combination-list-for-com', key: 'product_cmb_id',
            value: 'product_cmb_code',
            data: {product_id: product_id},
            optionData: [{key: 'stock_value', value: 'stock_value'}],
        });
        $('#new_stock_value').show();
        $('#stock_value').val('');
        $(document.body).on('change', '#add_stock .panel-body #combination_id', function (event) {
            event.preventDefault();
            $('#add_stock .panel-body #stock_value').val($('#combination_id option:selected').data('stock_value'));
        });
        $('#add_stock .panel-body #stock_value').attr('name', 'combination_value');
    }
    else
    {
        $('#select_combination').hide();
        $('#new_stock_value').show();
    }
    get_add_stock(product_id, product_name, sku, stock_value, product_code);
});
$('.manage-products').click(function (e) {
    e.preventDefault();
    $('#add_property:visible').fadeOut(function () {
        $('#product_list').show();
    });
    $('#add-combinations:visible').fadeOut(function () {
        $('#product_list').show();
    });
});
$('#add_new').on('click', '.product_save_btn', function (e) {
    e.preventDefault();
    var data = '';
    $('#add_new #editor1').val(CKEDITOR.instances.editor1.getData());
    $('#add_new form').each(function () {
        data += ((data != '') ? '&' : '') + $(this).serialize();
    });
    $.ajax({
        data: data,
        url: window.location.BASE + 'seller/products/save-product',
        success: function (data) {
            $('#message').html(data.msg);
            if (data.status == 'OK') {
                window.location = window.location.BASE + 'seller/products/' + data.product_code;
            }
        },
        error: function (jqXhr) {
            $('input[type="submit"]', $('#product_form')).val('Submit').attr('disabled', false);
            if (jqXhr.status === 422) {
                var data = jqXhr.responseJSON;
                if (data.status == 'WARN') {
                    $('#configure').before(data.msg)
                }
                var message = '';
                if (data.error != '' && data.error != undefined)
                {
                    $.each(data.error, function (key, value) {
                        if (message == '')
                        {
                            message = message + value;
                        }
                        else
                        {
                            message = message + '<br>' + value;
                        }
                    });
                    $('#add_new').before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + message + '</div>');
                }
            }
            else {
                alert('Something went wrong');
            }
        }
    });
});
$('#product_properties_form').on('submit', function (e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        data: form.serialize(), url: form.attr('action'),
        success: function (data) {
            $('#message').html(data.msg);
            if (data.status == 'OK') {
            }
        }
    });
});
$('.back-btn').on('click', function (e)
{
    $('#add_combination').hide();
});
$(document.body).on('click', '#add_combination_btn', function (e) {
    $('#message').empty();
    $('#add-edit-form #product_cmb_properties').empty();
    $('#add-edit-form').find('input[type=text], textarea').val('');
    $('#add-edit-form #product_cmb_properties').empty();
    $('#add-edit-form').find('input[type=text], textarea').val('');
    $('#add-edit-form').trigger('reset');
    e.preventDefault();
    $('#add_combination').show();
    $('#combination_list').show();
    var _this = $(this);
    var con = $('#configure').data('product_id');
    $('#add-edit-form  #product_id').val(con);
    $('#add-edit-form .panel-body #property_id').loadSelect({
        url: window.location.BASE + 'seller/products/properties-list-for-com',
        key: 'property_id',
        value: 'property',
        data: {product_id: con},
        success: function () {
            $('#add-edit-form .panel-body #value_id').loadSelect({
                url: window.location.BASE + 'seller/products/values-list-for-com',
                key: 'value_id',
                value: 'value',
                data: {product_id: con},
                dependingSelector: ['#property_id']
            });
        }
    });
});
$('#add-edit-form .panel-body #remove-property-btn').on('click', function () {
    $('#add-edit-form .panel-body #product_cmb_properties option:selected').each(function () {
        $(this).remove();
    });
    $('#add-edit-form .panel-body #remove-property-btn').attr('disabled', true);
    update_property_and_value($('#add-edit-form .panel-body #property_id'), $('#add-edit-form .panel-body #value_id'), $('#add-edit-form .panel-body #product_cmb_properties'), $('#add-edit-form'));
});
$('#add-edit-form .panel-body #add-property-btn').on('click', function () {
    $('#add-edit-form .panel-body #remove-property-btn').attr('disabled', false);
    if ($('#add-edit-form .panel-body #property_id').val() != '' && $('#add-edit-form .panel-body #value_id').val() != '') {
        $('#add-edit-form .panel-body #product_cmb_properties').append(
                $('<option>', {class: 'property-value', value: $('#add-edit-form .panel-body #property_id').val() + ':' + $('#add-edit-form .panel-body #value_id').val()})
                .html($('#add-edit-form .panel-body #property_id option:selected').text() + ' : ' + $('#add-edit-form .panel-body #value_id option:selected').text())
                );
        update_property_and_value($('#add-edit-form .panel-body #property_id'), $('#add-edit-form .panel-body #value_id'), $('#add-edit-form .panel-body #product_cmb_properties'), $('#add-edit-form'));
    }
});
$('#add-edit-form').on('submit', function (e) {
    e.preventDefault();
    var form = $(this);
    $('#add-edit-form #product_cmb_properties option').each(function () {
        $(this).attr('selected', true);
    });
    $.ajax({
        data: form.serialize(),
        url: form.attr('action'),
        success: function (data) {
            $('#add_combination').hide();
            var product = $('product_id').val();
            get_combination_list();
            $('#combination_table #search').trigger('click');
            $('#message').html('<div class="alert alert-success">' + data.msg + '</div>');
            $('#add-edit-form').find('input[type=text], textarea').val('');
            $('#add-edit-form').trigger('reset');
            if (data.status == 'OK') {
            }
        }
    });
});
function image_details(product_id, image_id)
{
    product_id = (product_id != undefined) ? product_id : 0;
    image_id = (image_id != undefined) ? image_id : 0;
    var image_data = 0;
    if (product_id != 0)
    {
        var data = {product_id: product_id};
    }
    else
    {
        var data = {image_id: image_id};
    }
    $.ajax({
        data: data,
        url: window.location.BASE + 'seller/products/image_details',
        beforeSend: function () {
            $('#image_values').empty();
        },
        success: function (data) {
            if (data.status == 'OK') {
                print_table(data.contents);
            }
            if (data.status == 'image_details')
            {
                image_data = data.contents;
                $('#upload_image').find('#img_up').attr('src', data.url);
                $('#upload_image').find('#img_up').attr('data-image_id', image_id);
                $('#upload_image').find('#img_up').attr('data-img_file', data.contents.img_file);
                $('#upload_image').find('h4').text('Edit Image');
            }
        },
        error: function () {
            $('stock_value').val('');
            alert('something went wrong');
        }
    });
    if (image_data != '')
    {
        return image_data;
    }
}
function print_table(data)
{
    var content = '';
    var url = '';
    /*content += '<td> < </td>*/
    $.each(data, function (key, elm) {
        url = '';
        /*<a href="supplier/products/cover_images" id="cover_images" class="btn btn-primary btn-sm cove_image" data-id="' + elm.image_id + '" >Mark As Cover Picture</a></td>*/
        url += window.location.BASE + 'assets/uploads/' + elm.file_path;
        url += '/' + elm.img_path;
        url += '/' + elm.relative_post_id;
        url += '/' + elm.img_file;
        content = '<tr> <td><a class="pull-left" href="#">  <img  src="' + url + '" alt="" width="100px" class="media-object thumbnail" /> </a></td><td>' + elm.sort_order + '<span class="" data-priority="' + elm.sort_order + '" data-img_id="' + elm.img_id + '"><input type="hidden" name="sortorder[' + elm.img_id + ']" value="' + elm.sort_order + '"></span></td>';
        if (elm.primary == 1)
        {
            content += '<td> <input type="radio" class="set_default"  name="cover_image" checked="checked" value="' + elm.img_id + '" data-url ="seller/products/cover_images"> <span data-img_id =' + elm.img_id + ' class="col-sm-8 pull-right icon-edit hidden"></span><button data-img_id =' + elm.img_id + ' class="btn btn-small remove pull-right"> <i class="icon-remove"></i></button><span class="pull-right mR10"> <button class="btn btn-small up"><i class="fa fa-arrow-up"></i></button><button class="btn btn-small down"><i class="fa fa-arrow-down"></i></button></span></td></tr>';
        }
        else
        {
            content += '<td> <input type="radio" class="set_default"  name="cover_image" value="' + elm.img_id + '" data-url ="seller/products/cover_images"><span data-img_id =' + elm.img_id + ' class="col-sm-8 pull-right icon-edit hidden"></span> <button data-img_id =' + elm.img_id + ' class="btn btn-small remove pull-right"> <i class="icon-remove"></i></button><span class="pull-right mR10"><button class="btn btn-small up"><i class="fa fa-arrow-up"></i></button><button class="btn btn-small down"><i class="fa fa-arrow-down"></i></button></span></td></tr>';
        }
        $('#image_values').append(content);
    })
}
$(document).load(function () {
    $('#new_product_image').hide();
    $('#image_resize').hide();
});
$(document).ready(function () {
    $('#add_new a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
    var var1 = $('#configure').find('.active').attr('id');
    if (var1 == 'manage_combi')
    {
        alert(product_id);
        if (product_id != 0)
        {
            get_combination_list(product_id);
        }
        else
        {
            alert('sd');
        }
    }
//CKEDITOR.instances['editor1'].setData('');
    $('#product_list').show();
    $('#new_product_image').hide();
    $('#image_resize').hide();
    $('#add_product').on('click', function (e) {
    });
    var sku_valid = '';
    $('#sku').on('change', function (event) {
        $('#sku').addClass('loading');
        var sk = $('#sku').val()
        if (sk != '')
        {
            $.ajax({
                url: window.location.BASE + 'seller/products/sku_valid',
                data: {sku: sk},
                beforeSend: function () {
                    $('#sku').addClass('loading');
                    $('#sku').removeClass('load_success');
                    $('#sku').removeClass('load_failure');
                    $('.error_msg').remove();
                },
                success: function (data) {
                    $('#message').html(data.msg);
                    if (data.status == 'OK') {
                        $('#sku').removeClass('loading');
                        $('#sku').addClass('load_success');
                        $('#sku').after(data.msg);
                        sku_valid = true;
                    }
                    else {
                        $('#sku').removeClass('load_success');
                        $('#sku').addClass('load_failure');
                        $('#sku').after(data.msg);
                    }
                }
            });
        }
        else
        {
        }
    });
    $('#product_form #currency_id_1').loadSelect({
        url: window.location.BASE + 'currencies-list',
        key: 'currency_id',
        value: 'currency'
    });
    $('#product_form').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        submitHandler: function (form, event) {
            event.preventDefault();
            $('#message').empty();
            $.ajax({
                data: $(form).serialize(),
                url: $(form).attr('action'),
                success: function (data) {
                    $('#message').html(data.msg);
                    if (data.status == 'OK') {
                        $('#list').show();
                        $('#form').hide();
                        $('#add_product_div').hide();
                        $('#search').trigger('click');
                    }
                },
                error: function (jqXhr) {
                    $('input[type="submit"]', $('#product_form')).val('Submit').attr('disabled', false);
                    if (jqXhr.status === 422) {
                        var data = jqXhr.responseJSON;
                        $('#product_form').appendLaravelError(data.error);
                    }
                    else {
                        alert('Something went wrong');
                    }
                }
            });
        }
    });
    $('#supplier_product_list_form #panel_currency_id').loadSelect({url: window.location.BASE + 'currencies-list',
        key: 'currency_id',
        value: 'currency'
    });
    $('#supplier_product_form #currency_id').loadSelect({
        url: window.location.BASE + 'currencies-list',
        key: 'currency_id',
        value: 'currency'
    });
    $('fileupload-new').click(function (event) {
        event.preventDefault();
        $('#logo_image').click();
        $('#image_upload_show1').attr('src', '');
        $('a#img_upload_button1').attr('disabled', true).html('Processing...');
    });
    $(document.body).on('click', '#add_new_image', function (e) {
        $('#upload_image').show();
        $('#new_product_image').hide();
    });
    $(document.body).on('click', '.add_image', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        var product_id = CurEle.data('product_id');
        var product_code = CurEle.data('product_code');
        $('#upload_image').find('#logo_image').attr('data-product_id', product_id);
        var title = 'Image Details';
        var url = window.location.BASE + 'seller/products/image/' + product_code;
        window.location.ChangeUrl(title, url);
        e.preventDefault();
        $('#product_list').hide();
        $('#new_product_image').show();
        image_details(product_id);
    });
    $(document.body).on('click', '.manage_combinations', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        var product_id = $('#product_id').val();
        alert(product_id);
        var product_code = CurEle.data('product_code');
        var title = 'Combinations';
        var url = window.location.BASE + 'seller/products/combinations/' + product_code;
        window.location.ChangeUrl(title, url);
        $('#product_list').hide();
        get_combination_list(product_id);
    });
    $(document.body).on('change', '.set_default', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        var image_id = $('.set_default:checked').val();
        e.preventDefault();
        $.ajax({
            data: {image_id: image_id},
            url: CurEle.data('url'),
            success: function (data) {
                if (data.status == 'OK') {
                    alert('OK');
                    $('#succ_img').html(data.msg);
                    $('#add_image').trigger();
                }
            },
            error: function () {
                $('stock_value').val('');
                alert('something went wrong');
            }
        });
    });     /* $('#err_msg').empty();
     var CurEle = $(this);
     alert(CurEle.data('current_stock_values'))
     $('#add_stocks #pro_name').text(CurEle.data('product_name'));
     $('#add_stocks #sku_code').text(CurEle.data('sku'));
     $('#add_stocks #current_stock_value').text(CurEle.data('current_stock_values'));
     $('#add_stock').modal('show');
     $('#add_stocks').validate({      errorElement: 'div',
     errorClass: 'error',
     focusInvalid: false,
     rules: {
     'stock_value': 'required',
     },
     messages: {
     'stock_value': 'Please Enter Stock Value',
     },
     submitHandler: function () {
     $.ajax({
     data: $('#add_stocks').serialize(),
     url: CurEle.attr('href'),
     success: function (data) {
     $('stock_value').val('');
     $('#message').html(data.msg);
     if (data.status =='OK') {
     $('stock_value').val('');;
     $('#add_stock').modal('hide');
     $('#search').trigger('click');
     }
     },
     error: function () {
     $('stock_value').val('');
     alert('something went wrong');
     }
     });
     }
     });*/
    $(document.body).on('submit', '#add_stocks', function (e) {
        e.preventDefault();
        $('#err_msg').empty();
        var CurEle = $(this);
        $('#add_stocks').validate({
            errorElement: 'div',
            errorClass: 'error',
            focusInvalid: false,
            rules: {
                'stock_value': 'required',
            },
            messages: {
                'stock_value': 'Please Enter Stock Value',
            },
            submitHandler: function () {
                $.ajax({
                    data: $('#add_stocks').serialize(),
                    url: CurEle.attr('action'),
                    success: function (data) {
                        $('stock_value').val('');
                        $('#message').html(data.msg);
                        if (data.status == 'OK') {
                            $('stock_value').val('');
                            $('#add_stock').modal('hide');
                            $('#search').trigger('click');
                        }
                    },
                    error: function () {
                        $('stock_value').val('');
                        alert('something went wrong');
                    }
                });
            }
        });
    });
    $('#new_supplier_product_btn').click(function (e) {
        e.preventDefault();
        $('#list').hide();
        priceListShow();
        $('#supplier_product_form #supplier_id').loadSelect({
            url: window.location.BASE + 'supplier-list',
            key: 'id',
            value: 'name'
        });
        $('#supplier_product_form #category_id').loadSelect({
            url: window.location.BASE + 'product-categories-list',
            key: 'id',
            value: 'name'
        });
        $('#supplier_product_form #brand_id').loadSelect({
            url: window.location.BASE + 'product-brands-list',
            key: 'brand_id',
            value: 'brand_name'
        });
        $('#supplier_product_form #product_id').loadSelect({
            url: window.location.BASE + 'products-list',
            key: 'product_id',
            value: 'product_name',
            dependingSelector: ['#supplier_product_form #category_id', '#supplier_product_form #brand_id']
        });
        $('#supplier_product_form #currency_id').loadSelect({
            url: window.location.BASE + 'currencies-list',
            key: 'currency_id',
            value: 'currency'
        });
        $('#form').show();
    });
    $('#configure').on('click', '#manage_combo', function (e) {
        CurEle = $(this);
        var data = $('#configure').data('product_id');
        get_combination_list(data);
    });
    $('#product_list').on('click', '#add_product', function (e) {
        e.preventDefault();
        // var title = 'Combinations';         var CurEle = $(this);
        // var url = window.location.BASE + 'supplier/products/' + CurEle.data('supplier_product_code');
        //window.location.ChangeUrl(title, url);
        //configure(products[$(this).data('product_code')]);
        //$('#form').show();
        add_new_products();
    });
    $('#cancel_btn').click(function (e) {
        e.preventDefault();
        $('#list').show();
        $('#form').hide();
        $('#add_product_div').hide();
    });
    $('#product_cancel_btn').click(function (e) {
        e.preventDefault();
        $('#list').show();
        $('#form').hide();
        $('#add_product_div').hide();
    });
    $('.tabbable').find('#supplier_product_form').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        submitHandler: function (form, event) {
            event.preventDefault();
            $('#message').empty();
            $.ajax({
                data: $(form).serialize(),
                url: $(form).attr('action'),
                success: function (data) {
                    $('#message').html(data.msg);
                    if (data.status == 'OK' || data.status == 'WARN') {
                        //$('#list').show();
                        //$('#form').hide();
                        //$('#add_product_div').hide();
                        //$('#search').trigger('click');                     }
                    }
                },
                error: function (jqXhr) {
                    $('input[type="submit"]', $(form)).val('Submit').attr('disabled', false);
                    if (jqXhr.status === 422) {
                        var data = jqXhr.responseJSON;
                        $(form).appendLaravelError(data.error);
                    }
                    else {
                        alert('Something went wrong');
                    }
                }
            });
        }
    });
    $('#supplier_product_list_table').on('click', '.change_status', function (e) {
        e.preventDefault();
        $('#message').empty();
        var CurEle = $(this);
        if (confirm('Are you sure? You want to ' + CurEle.text() + ' this product?')) {
            $.ajax({
                data: {supplier_product_id: CurEle.data('supplier_product_id'), status: CurEle.data('status')},
                url: CurEle.attr('href'),
                success: function (data) {
                    SPLDT.fnDraw();
                    if (data.status == 'OK') {
                        $('#message').html(data.msg);
                        //$('#search').trigger('click');
                    }
                }
            });
        }
    });
    /* $.ajax({
     url: url,
     success: function (data) {
     if (data.status == 'OK') {
     $('.modal-title').html('Edit Product');
     $('#add_product_model #new_product_form').html(data.content);
     $('#add_product_model').modal('show');
     } else {
     alert('something Went Wrong');
     }
     }
     });*/
    $('#supplier_product_list_table').on('click', '.delete_btn', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#message').empty();
        if (confirm('Are you sure? You want to delete this product?')) {
            $.ajax({
                data: {supplier_product_id: CurEle.data('supplier_product_id')},
                url: CurEle.attr('href'),
                success: function (data) {
                    SPLDT.fnDraw();
                    if (data.status == 'OK') {
                        $('#message').html(data.msg);
                        CurEle.parents('tr').remove();
                    }
                }
            });
        }
    });
    $('#store_id,#store_ids').loadSelect({
        url: window.location.BASE + 'seller/store-list',
        key: 'store_id',
        value: 'store_name'
    });
    $(document.body).on('click', '.remove', function (event) {
        var CurEle = $(this);
        if (confirm('Are you sure Want to delete this Product Image')) {
            $.ajax({
                data: {img_id: CurEle.data('img_id'), product_id: product_id},
                url: window.location.BASE + 'seller/products/image_remove',
                beforeSend: function () {
                    $('#image_values').empty();
                },
                success: function (data) {
                    if (data.status == 'OK') {
                        print_table(data.contents);
                    }
                }
            });
        }
    });
    $(document.body).on('click', '.icon-edit', function (event) {
        var CurEle = $(this);
        $('#upload_image').show();
        $('#new_product_image').hide();
        var image_id = $(this).data('img_id');
        var product_id = 0;
        image_details(product_id, image_id);
        $('#img_up').attr('src', data.src);
    });
    $('#image_values').on('click', 'tr .up', function () {
        var row = $(this).closest('tr');
        var cur_priority = row.find('span input').val();
        var new_priority = row.prev().find('span input').val();
        row.prev().find('span input').val(cur_priority);
        row.find('span input').val(new_priority);
        row.insertBefore(row.prev());
    });
    $('#image_values').on('click', 'tr .down', function () {
        var row = $(this).closest('tr');
        var cur_priority = row.find('span input').val();
        var new_priority = row.next().find('span input').val();
        row.next().find('span input').val(cur_priority);
        row.find('span input').val(new_priority);
        row.insertAfter(row.next());
    });
    $(document).on('click', '#save_changes', function () {
        $.ajax({
            url: window.location.BASE + 'seller/products/update_sortorder',
            data: $('#image_values input[type="hidden"]').serialize(),
            success: function (data) {
            }
        });
    });
});
