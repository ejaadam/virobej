var productPrice = [], existingCmb = [];
function updateImgPos() {
    var i = 1;
    $('#image_values tr').each(function () {
        $('td.sort_order', $(this)).text(i);
        $('td span input', $(this)).val(i);
        i ++;
    });
}
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ('0' + parseInt(x).toString(16)).slice(- 2);
    }
    return '#' + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}
$('#gallery_grid').on('click', 'li', function (e) {
    if (rgb2hex($($(this), 'li').css('background-color')) == '#ffffff')
    {
        $($(this), 'li').css('background', '#e8e8e8');
        $($(this)).find('#selected_img').prop('checked', 'checked');
    } else
    {
        $($(this), 'li').css('background', '#ffffff');
        $($(this)).find('#selected_img').prop('checked', false);
    }
});
var PPDT = $('#product-price').dataTable({
    pageLength: - 1,
    ajax: {
        url: window.location.API.SUPPLIER + 'products/price',
        data: function (d) {
            return $.extend({}, d, {
                product_id: product_id,
                product_cmb_id: product_cmb_id,
            });
        }
    },
    columns: [
        {
            data: 'currency',
            name: 'currency'
        },
        {
            data: 'mrp_price',
            name: 'mrp_price'
        },
        {
            data: 'price',
            name: 'price'
        },
        {
            class: 'text-center',
            orderable: false,
            data: 'parent_category_id',
            name: 'parent_category_id',
            render: function (data, type, row, meta) {
                productPrice[row.currency_id] = row;
                return addDropDownMenu(row.actions, true);
            }
        }
    ]
});
function priceListShow() {
    $('#price-form-panel').hide();
    $('#price-list-panel').show();
    PPDT.fnDraw();
}
var CDT = $('#combination_table').dataTable({
    ajax: {
        url: window.location.API.SUPPLIER + 'products/combinations',
        data: function (d) {
            return $.extend({}, d, {
                product_id: product_id,
                supplier_product_id: supplier_product_id,
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
            data: 'stock_on_hand',
            name: 'stock_on_hand'
        },
        {
            data: 'currency_with_charge',
            name: 'currency_with_charge',
        },
        {
            data: 'status',
            name: 'status',
            class: 'text-center status',
            render: function (data, type, row, meta) {
                var content = '';
                return (row.status == Constants.ACTIVE) ? ' <span class="label label-success" id="active">Active</span>' : ' <span id="active" class="label label-danger">Inactive</span><br/>';
            }
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
    $('#product_supplier_id').val(supplier_product_id);
    product_id = (product_id != undefined) ? product_id : 0;
    if (product_id != 0)
    {
        var data = {product_id: product_id};
    }
    $('#product_id').val(product_id);
    $('#combination_list').show();
    $('#combination_list #add_combination_btn').attr('data-product_id', product_id);
    CDT.fnDraw();
}
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
        $('option[value="' + temp[0] + '"]', property_id).attr({disabled: true, hidden: true});
        $('option[value="' + temp[1] + '"]', value_id).attr({disabled: true, hidden: true});
        $('#product_cmb', form).val(product_cmb.trim(','));
    });
    property_id.val('').trigger('change');
    value_id.val('').trigger('change');
    if ($('option', cmb_properties).length >= 1) {
        $('button[type="submit"]', form).removeAttr('disabled');
    }
    else {
        $('button[type="submit]', form).attr('disabled', true);
    }
}
var SDT = $('#shipment-table').dataTable({
    ajax: {
        url: window.location.BASE + 'supplier/products/zones',
        data: function (d) {
            return $.extend({}, d, {
                supplier_product_id: supplier_product_id,
                search_term: $('#supplier_product_list_form #search_term').val(),
            });
        }
    },
    columns: [
        {
            data: 'zone',
            name: 'zone'
        }, {
            data: 'mode',
            name: 'mode'
        },
        {
            data: 'delivery_days',
            name: 'delivery_days',
        },
        {
            data: 'zone_charges',
            name: 'zone_charges',
        },
        {
            class: 'text-center',
            orderable: false,
            render: function (data, type, row, meta) {
                var json = $.parseJSON(meta.settings.jqXHR.responseText);
                existing_geo_zone_id.push(row.geo_zone_id);
                //pss_id.push(row.pss_id);
                var action_buttons = '<div class="btn-group">';
                action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                action_buttons += '<li><a class="edit-btn" href="" data-psc_id="' + row.psc_id + '" data-pss_id="' + row.pss_id + '" data-mode_id="' + row.mode_id + '"  data-supplier_product_id="' + row.supplier_product_id + '" data-geo_zone_id="' + row.geo_zone_id + '" data-delivery_charge="' + row.delivery_charge + '" data-delivery_days="' + row.delivery_days + '" >Configure</a></li>';
                action_buttons += '<li><a class="delete-btn"  data-pss_id="' + row.pss_id + '" href="">Delete</a></li>';
                action_buttons += '</ul></div>';
                return action_buttons;
            }
        }
    ]
});
function get_shippment_list()
{
    $('#message').empty();
    var existing_geo_zone_id = [];
    //var pss_id ='';
    SDT.fnDraw();
}
//manage image related functions
function image_details(product_id, image_id)
{
    product_id = (product_id != undefined) ? product_id : 0;
    image_id = (image_id != undefined) ? image_id : 0;
    var image_data = 0;
    var comb_id = $('#combination_select').val();
    if (product_id != 0)
    {
        var data = {product_id: product_id, combination_id: comb_id};
    }
    else
    {
        var data = {image_id: image_id, combination_id: comb_id};
    }
    $.ajax({
        data: data,
        url: window.location.API.SUPPLIER + 'products/image/details',
        beforeSend: function () {
            $('#image_values').empty();
        },
        success: function (data) {
            if (data.status == 'OK') {
                if (comb_id == 0)
                {
                    print_browse_page(data.contents);
                    updateImgPos();
                }
                else
                {
                    print_table(data.contents);
                    updateImgPos();
                }
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
    /*content += '<td> < </td>*/     $.each(data, function (key, elm) {
        url = '';
        /*<a href="supplier/products/cover_images' id="cover_images' class="btn btn-primary btn-sm cove_image' data-id="' + elm.image_id +'" >Mark As Cover Picture</a></td>*/
        url += window.location.BASE + 'assets/uploads/' + elm.file_path;
        url += '/' + elm.img_path;
        url += '/' + elm.relative_post_id;
        url += '/' + elm.img_file;
        content = '<tr> <td><span  data-priority="' + elm.sort_order + '" data-img_id="' + elm.img_id + '"><input type="hidden" name="sortorder[' + elm.img_id + ']" value="' + elm.sort_order + '"/></span><a class="pull-left" href="#">  <img  src="' + url + '" alt="" width="100px" class="media-object thumbnail" /> </a></td><td class="sort_order">' + elm.sort_order + '</td>';
        if (elm.primary == 1)
        {
            content += '<td> <input type="radio" class="set_default hidden"  name="cover_image" checked="checked" value="' + elm.img_id + '" data-url ="supplier/products/cover_images"> <span data-img_id ="' + elm.img_id + '" class="col-sm-8 pull-right icon-edit hidden"></span><button data-img_id ="' + elm.img_id + '" class="btn btn-small remove pull-right"> <i class="icon-remove"></i></button><span class="pull-right mR10"> <button class="btn btn-small up"><i class="fa fa-arrow-up"></i></button><button class="btn btn-small down"><i class="fa fa-arrow-down"></i></button></span></td></tr>';
        }
        else
        {
            content += '<td> <input type="radio" class="set_default hidden"  name="cover_image" value="' + elm.img_id + '" data-url ="supplier/products/cover_images"><span data-img_id ="' + elm.img_id + '" class="col-sm-8 pull-right icon-edit hidden"></span> <button data-img_id ="' + elm.img_id + '" class="btn btn-small remove pull-right"> <i class="icon-remove"></i></button><span class="pull-right mR10"><button class="btn btn-small up"><i class="fa fa-arrow-up"></i></button><button class="btn btn-small down"><i class="fa fa-arrow-down"></i></button></span></td></tr>';
        }
        $('#image_values').append(content);
        $('#image_values tr').eq(0).find('.set_default').prop('checked', true)
    })
}
function print_browse_page(data)
{
    var content = '';
    var url = '';
    $('#gallery_grid').empty();
    /*content += '<td> < </td>*/
    $.each(data, function (key, elm) {
        url = '';
        /*<a href="supplier/products/cover_images' id="cover_images' class="btn btn-primary btn-sm cove_image' data-id="' + elm.image_id +'" >Mark As Cover Picture</a></td>*/
        url += window.location.BASE + 'assets/uploads/' + elm.file_path;
        url += '/' + elm.img_path;
        url += '/' + elm.relative_post_id;
        url += '/' + elm.img_file;
        content = '<li class="mix user_0 business travel mix_all" data-name="" data-timestamp="1379307600" style="display: inline-block; opacity: 1;"><a href="javascript:void(0);" class="img_wrapper gal_lightbox"><img src="' + url + '" class="img img-responsive img-thumbnail" alt=""><input  id="make_as_def" class="is_default" name="is_default[]" type="checkbox" value="' + elm.img_id + '"/><input class="hidden" id="selected_img" name="selected[]" type="checkbox" value="' + elm.img_id + '"/></img></a></li>';
        $('#gallery_grid').append(content);
    })
}
$(document).ready(function () {
    $('#price-form-panel').hide();
    $('#add-new-price-btn').on('click', function (e) {
        e.preventDefault();
        $('#price-form-panel').show();
        $('#product_id', $('#price_form')).val(product_id);
        $('#mrp_price', $('#price_form')).val('');
        $('#price', $('#price_form')).val('');
        $('#currency_id', $('#price_form')).loadSelect({
            url: window.location.BASE + 'currencies-list',
            key: 'currency_id',
            selected: Constants.DEFAULT_CURRENCY,
            value: 'currency'
        });
    });
    $('#product-price').on('click', '.edit-product-price', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#price-form-panel').show();
        $('#product_id', $('#price_form')).val(product_id);
        $('#currency_id', $('#price_form')).loadSelect({
            url: window.location.BASE + 'currencies-list',
            key: 'currency_id',
            selected: CurEle.data('currency_id'),
            value: 'currency',
            success: function () {
                $('#spp_id', $('#price_form')).val(productPrice[CurEle.data('currency_id')].spp_id);
                $('#price', $('#price_form')).val(productPrice[CurEle.data('currency_id')].price);
                $('#mrp_price', $('#price_form')).val(productPrice[CurEle.data('currency_id')].mrp_price);
                $('#impact_on_price', $('#price_form')).val(productPrice[CurEle.data('currency_id')].impact_on_price);
            }
        });
    });
    $('#currency_id', $('#price_form')).on('change', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        var data = productPrice[CurEle.val()] != undefined ? productPrice[CurEle.val()] : {spp_id: '', price: 0, mrp_price: 0}
        $('#spp_id', $('#price_form')).val(data.spp_id);
        $('#price', $('#price_form')).val(data.price);
        $('#mrp_price', $('#price_form')).val(data.mrp_price);
        $('#impact_on_price', $('#price_form')).val(data.impact_on_price);
    });
    $('#product-price').on('click', '.delete-product-price', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        if (confirm('Are you sure, You wants to delete ' + CurEle.data('currency'))) {
            $.ajax({
                url: window.location.BASE + 'supplier/products/delete-price',
                data: {spp_id: CurEle.data('spp_id')},
                success: function (data) {
                    priceListShow();
                }
            });
        }
    });
    $('#price_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.location.API.SUPPLIER + 'products/price/save',
            data: $('#price_form').serialize(),
            success: function (data) {
                priceListShow();
            }
        });
    });
    $('#back-to-price-list').on('click', function (e) {
        e.preventDefault();
        priceListShow();
    });
    priceListShow();
    $('#tags').select2({
        multiple: true,
        //placeholder: "",
        //minimumInputLength: 1,
        triggerChange: true,
        allowClear: true,
        tags: true,
        //tokenSeparators: [',', ' '],
        separator: ',',
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
        createSearchChoice: function (term, data) {
            if ($(data).filter(function () {
                return this.text.localeCompare(term) === 0;
            }).length === 0) {
                return {id: term, text: term};
            }
        },
        initSelection: function (inputs, callback) {
            var p = [];
            $.each(params, function (key, valu) {
                p.push({id: valu.tag_id, text: valu.tag_name});
            });
            callback(p);
        }
    }).select2('val', []);
    $('#treeview-container').treeview({
        debug: true
    });
    $('#show-values').on('click', function () {
        $('#values').text($('#treeview-container').treeview('selectedValues'));
    });
    $('#image_values tr:first').find('.set_default').attr('checked', true);
    $(document.body).on('change', '#payment_id', function () {
        var mode_id = $(this).val();
        var _this = $(this);
        $.ajax({
            data: {mode_id: mode_id},
            url: window.location.BASE + 'get_available_payment_gateway',
            success: function (data) {
                $('#payment_gateway_select').empty();
                $.each(data, function (key, val) {
                    $('#payment_gateway_select').append('<option value="' + val.payment_type_id + '">' + val.payment_type + '</option>');
                });
            }
        });
    });
    $(document.body).on('click', '#make_as_def', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#message').empty();
        if (confirm('Are you sure Want to make this image as Default ')) {
            $.ajax({
                url: window.location.BASE + 'supplier/products/default_pro_img',
                data: {img_id: CurEle.val(), product_id: $('#configure').data('product_id')},
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#message').html('<div class="alert alert-success">' + data.msg + '</div>');
                        $('.is_default').prop('checked', false);
                        CurEle.prop('checked', true);
                    }
                }
            });
        }
    });
    $(document.body).on('click', '#delete_combination_product', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#message').empty();
        if (confirm('Are you sure Want to delete this Product Combination')) {
            $.ajax({
                url: CurEle.attr('href'),
                data: {product_cmb_id: CurEle.data('product_cmb_id'), product_id: CurEle.data('product_id')},
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#message').html('<div class="alert alert-success">' + data.msg + '</div>');
                        CurEle.parents('tr').remove();
                    }
                }
            });
        }
    });
    $(document.body).on('click', '#add_selected_image', function (e) {
        var data = '';
        var combination = '';
        data = $('#gallery_grid input:checked').serialize();
        combination = $('#combination_select').val();
        if (data != '' && combination != '')
        {
            var ajax_data = data + '&' + $.param({combination_id: combination});
            $.ajax({
                data: ajax_data,
                url: window.location.BASE + 'supplier/products/save_combination_images',
                beforeSend: function (data)
                {
                    $('.alert').remove();
                },
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#gallery_grid').before('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + data.msg + '</div>');
                        updateImgPos();
                        image_details(product_id);
                    }
                    else
                    {
                        $('#gallery_grid').before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + data.msg + '</div>');
                        updateImgPos();
                        image_details(product_id);
                    }
                }
            });
        }
        else
        {
            return false;
        }
    });
    $(document.body).on('click', '#delete_selected_image', function (e) {
        var data = '';
        var combination = '';
        data = $('#gallery_grid input:checked').serialize();
        combination = $('#combination_select').val();
        if (data != '' && combination != '')
        {
            var ajax_data = data + '&' + $.param({combination_id: combination, product_id: product_id});
            $.ajax({
                data: ajax_data,
                url: window.location.BASE + 'supplier/products/delete_selected_image',
                beforeSend: function (data)
                {
                    $('.alert').remove();
                },
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#gallery_grid').before('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + data.msg + '</div>');
                        $('#combination_select').val(0);
                        print_browse_page(data.contents);
                    }
                    else
                    {
                        $('#gallery_grid').before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + data.msg + '</div>');
                    }
                }
            });
        }
        else
        {
            return false;
        }
    });
    $('#configure #combination_list').on('click', '.change_status', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#message').empty();
        if (confirm('Are you sure Want to ' + CurEle.text() + ' this product')) {
            $.ajax({
                data: {status: CurEle.attr('data-status'), product_id: CurEle.data('product_id'), product_cmb_id: CurEle.data('product_cmb_id')},
                url: CurEle.attr('href'),
                beforeSend: function (data)
                {
                    CurEle.attr('data-status', '');
                },
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#message').html('<div class="alert alert-success">' + data.msg + '</div>');
                        if (data.status_val == 1)
                        {
                            var status_val = 0;
                            CurEle.attr('data-status', status_val).text('Inactive')
                            $('#active').removeClass('label label-danger').addClass('label label-success').text('Active');
                        }
                        else
                        {
                            var status_val = 1;
                            CurEle.attr('data-status', status_val).text('Active')
                            $('#active').removeClass('label label-success').addClass('label label-danger').text('Inactive');
                        }
                    }
                }
            });
        }
    });
    $(document.body).on('submit', '#payment_add-form', function (event) {
        event.preventDefault();
        $('#payment_gateway_select').attr('multiselect', true);
        var form_data = $('#payment_add-form').serialize();
        $.ajax({
            data: form_data, url: window.location.BASE + 'supplier/products/save_payment_types',
            success: function (data) {
            },
            error: function (jqXhr) {
                if (jqXhr.status === 422) {
                    var data = jqXhr.responseJSON;
                    if (data.status == 'WARN') {
                        $('#error_msg').html(data.msg)
                    }
                    var message = '';
                    if (data.error != '' && data.error != undefined)
                    {
                        $.each(data.error, function (key, value) {
                            if (message == '')
                            {
                                message = message + value;
                            } else
                            {
                                message = message + '<br>' + value;
                            }
                        });
                        $('#payment_modes').before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + message + '</div>');
                    }
                }
                else {
                    alert('Something went wrong');
                }
            }
        });
    });
    $(document.body).on('click', '#remove-gateway-btn', function () {
        $('#payment_gateway_select option:selected').each(function () {
            $(this).remove();
        });
    });
    $(document.body).on('click', '#delete_payment_method', function (event) {
        event.preventDefault();
        var ptype_id = $(this).data('product_id');
        $.ajax({
            data: {ptype_id: ptype_id},
            url: window.location.BASE + 'supplier/products/delete_payment',
            success: function (data) {
                PDT.fnDraw();
            }
        });
    });
    var PDT = $('#payment_list_table').dataTable({
        sDom: 't',
        ajax: {
            url: window.location.BASE + 'supplier/products/payment_list',
            data: function (d) {
                return $.extend({}, d, {
                    supplier_product_id: $('#configure').data('supplier_product_id'),
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy HH:MM:ss');
                }
            },
            {
                data: 'mode_name',
                name: 'mode_name'
            }, {
                data: 'payment_type',
                name: 'payment_type'
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                    action_buttons += '<li><a data-product_id="' + row.ptype_id + '" id="delete_payment_method" href="">Delete</a></li>';
                    action_buttons += '</ul></div>';
                    return action_buttons;
                }
            }
        ]
    });
    $(document.body).on('click', '#manage_payment', function () {
        PDT.fnDraw();
    });
    $(document.body).on('click', '#add_payment_btn', function () {
        $('#add_payment').show();
    });
    // $('#configure').find('.active a').trigger('click')
    $('#message').empty();
    get_combination_list(product_id)
    get_shippment_list();
    var shipment_list_form = $('#shipment-list-form'),
            shipment_form = $('#shipment-form'),
            shipment_table = $('#shipment-table'),
            modes,
            existing_geo_zone_id = [];
    $('#payment_id').loadSelect({
        url: window.location.BASE + 'payment-mode-list',
        key: 'paymode_id', data: {supplied_id: supplier_id},
        selected: true,
        value: 'mode_name'
    });
    $('#store_id').loadSelect({
        url: window.location.BASE + 'store-list',
        key: 'store_id',
        data: {supplied_id: supplier_id},
        selected: store_id,
        value: 'store_name'
    });
    $('#category_id').loadSelect({
        url: window.location.BASE + 'product-categories-list', key: 'id',
        value: 'name',
    });
    $('#brand_id').loadSelect({
        url: window.location.BASE + 'product-brands-list',
        key: 'brand_id',
        value: 'brand_name'
    });
    $('#condition').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'product-condition-list',
        key: 'condition_id',
        value: 'condition_desc',
    });
    $('#product_visibility').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'product-visibility-list', key: 'visiblity_id',
        value: 'visiblity_desc',
    });
    $('#tax_class_id').loadSelect({
        url: window.location.BASE + 'tax-classes-list',
        key: 'tax_class_id',
        value: 'tax_class'
    });
    /* $('#combination_id').loadSelect({
     url: window.location.BASE + 'supplier/products/combination-list-for-com',
     key: 'product_cmb_id',
     value: 'product_cmb_code',
     data: {product_id: product_id},
     optionData: [{key: 'stock_value', value: 'stock_value'}],
     });*/
    // manage combination related coding
    $('#configure').on('click', '#manage_combo', function (e) {
        CurEle = $(this);
        var data = $('#configure').data('product_id');
        get_combination_list(data);
        $('#currency_id').loadSelect({
            url: window.location.BASE + 'currencies-list',
            key: 'paymode_id',
            data: {supplied_id: supplier_id},
            selected: true,
            value: 'mode_name'});
    });
    $(document.body).on('click', '#combination_table #edit_combination_product', function (e)
    {
        $('#message').empty();
        $('#product-combination-form').find('input[type=text], textarea').val('');
        $('#product-combination-form').trigger('reset');
        var con = $('#product_id').val();
        $('#product-combination-form .panel-body #property_id').loadSelect({
            url: window.location.BASE + 'supplier/products/properties-list-for-com',
            key: 'property_id',
            value: 'property',
            data: {product_id: con},
            success: function () {
                update_property_and_value($('#product-combination-form .panel-body #property_id'), $('#product-combination-form .panel-body #value_id'), $('#product-combination-form .panel-body #product_cmb_properties'), $('#product-combination-form'));
                $('#product-combination-form .panel-body #value_id').loadSelect({
                    url: window.location.BASE + 'supplier/products/values-list-for-com',
                    key: 'value_id',
                    value: 'value',
                    data: {product_id: con},
                    dependingSelector: ['#property_id']
                });
            }
        });
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            data: {product_cmb_id: CurEle.data('product_cmb_id')},
            url: window.location.BASE + 'supplier/products/get_combinations_by_id',
            success: function (data) {
                $('#product_cmb_id').val(data.data[0].product_cmb_id);
                $('#product-combination-form #product_cmb').val(data.data[0].product_cmb);
                $('#product-combination-form #sku').val(data.data[0].sku);
                $('#product-combination-form #product_cmb_properties').html(data.data.output);
                update_property_and_value($('#product-combination-form .panel-body #property_id'), $('#product-combination-form .panel-body #value_id'), $('#product-combination-form .panel-body #product_cmb_properties'), $('#product-combination-form'));
                $('#cmb_ppt_id').val(data.data[0].cmb_ppt_id);
                $('#product-combination-form').show();
                $('#product-combination-form #currency').text(data.data[0].currency);
                $('#product-combination-form #product_impact_price').val(data.data[0].impact_on_price);
                $('#product-combination-form #selling-price').text(data.data[0].mrp_price);
                $('#product-combination-form #price').text(data.data[0].price);
                $('#product-combination-form #product_cmb_properties').html('<option class="property-value" value="' + data.data[0].vals + '">' + data.data[0].labels + '</option>');
            }
        });
    });
    //add combination
    $(document.body).on('click', '#add-product-combination-btn', function (e) {
        e.preventDefault();
        $('#product_cmb_id').val('');
        $('#message').empty();
        $('#product_configure_span').empty();
        $('#cmb_ppt_id').val('');
        $('#product-combination-form #product_cmb_properties').empty();
        $('#product-combination-form').find('input[type=text], textarea').val('');
        $('#product-combination-form').resetForm();
        $('#product-combination-form').show();
        $('#supplier-product-combination-form').hide();
        $('#combination_list').show();
        var con = $('#configure').data('product_id');
        $('#product-combination-form  #product_id').val(con);
        $('#product-combination-form .panel-body #property_id').loadSelect({
            url: window.location.BASE + 'supplier/products/properties-list-for-com',
            key: 'property_id',
            value: 'property',
            data: {product_id: con},
            success: function () {
                $('#product-combination-form .panel-body #value_id').loadSelect({
                    url: window.location.BASE + 'supplier/products/values-list-for-com',
                    key: 'value_id',
                    value: 'value',
                    data: {product_id: con},
                    dependingSelector: ['#property_id']
                });
            }
        });
    });
    $(document.body).on('click', '#add-supplier-product-combination-btn', function (e) {
        e.preventDefault();
        $('#product-combination-form').hide();
        $('#supplier-product-combination-form').show();
        $('#combination_list').show();
        $('#supplier-product-combination-form #product_id').val(product_id);
        $('#supplier-product-combination-form #product_cmb_id').loadSelect({
            url: window.location.BASE + 'supplier/products/combinations-list',
            key: 'product_cmb_id',
            value: 'product_cmb',
            notexistIn: existingCmb,
            data: {product_id: product_id}
        });
    });
    $(document.body).on('submit', '#supplier-product-combination-form', function (e) {
        e.preventDefault();
        var form = $(this);
        $('.alert').remove();
        $.ajax({
            data: form.serialize(),
            url: form.attr('action'),
            success: function (data) {
                $('#add_combination').hide();
                get_combination_list(product_id);
                $('#combination_table #search').trigger('click');
                $('#message').html('<div class="alert alert-success">' + data.msg + '</div>');
                $('#supplier-product-combination-form').find('input[type=text], textarea').val('');
                $('#supplier-product-combination-form').trigger('reset');
                if (data.status == 'OK') {
                }
            },
            error: function (jqXhr) {
                if (jqXhr.status === 422) {
                    var data = jqXhr.responseJSON;
                    var message = '';
                    if (data.error != '' && data.error != undefined)
                    {
                        $.each(data.error, function (key, value) {
                            if (message == '')
                            {
                                message = message + value;
                            } else
                            {
                                message = message + '<br>' + value;
                            }
                        });
                        $('.combination_tit').before('<div class="alert alert-danger">' + message + '</div>');
                    }
                }
                else {
                    alert('Something went wrong');
                }
            }
        });
    });
    $(document.body).on('submit', '#assoc_form', function (event)
    {
        event.preventDefault();
        $.ajax({
            data: $('#assoc_form').serialize(),
            url: window.location.BASE + 'supplier/products/save_association',
            success: function (data) {
                if (data.status == 'OK')
                {
                    $('#configure').before(data.msg)
                }
            },
            error: function () {
                $('stock_value').val('');
                alert('something went wrong');
            }
        });
    });
    // information form submit
    $(document.body).on('click', '.product_save_btn', function (e) {
        e.preventDefault();
        var data = '';
        $('#configure #editor1').val(CKEDITOR.instances.editor1.getData());
        $('#configure form').each(function () {
            data += ((data != '') ? '&' : '') + $(this).serialize();
        });
        $.ajax({
            data: data,
            url: window.location.BASE + 'supplier/products/save_product',
            beforeSend: function () {
                $('.alert').remove();
            },
            success: function (data) {
                $('#message').html(data.msg);
                if (data.status == 'OK') {
                    $('#configure').before(data.msg)
                }
            },
            error: function (jqXhr) {
                $('input[type="submit"]', $('#product_form')).val('Submit').attr('disabled', false);
                if (jqXhr.status === 422) {
                    var data = jqXhr.responseJSON;
                    var message = '';
                    if (data.error != '' && data.error != undefined) {
                        $.each(data.error, function (key, value) {
                            if (message == '')
                            {
                                message = message + value;
                            } else
                            {
                                message = message + '<br>' + value;
                            }
                        });
                        $('#configure').before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + message + '</div>');
                    }
                }
                else {
                    alert('Something went wrong');
                }
            }
        });
    });
    $(document.body).on('click', '#new_zone_btn', function (e)
    {
// $('#pss_id', shipment_form).val(pss_id)
        $('#supplier_product_id_zone').val(supplier_product_id);
        $('input[type=number]').val('');
        $('#psd_id').val('');
        //$('input:textbox').val()
        $('#shipment-form-panel').show();
        $('#currency_id', shipment_form).loadSelect({
            url: window.location.BASE + 'currencies-list',
            key: 'currency_id',
            selected: Constants.DEFAULT_CURRENCY,
            value: 'currency'
        });
        $('#geo_zone_id', shipment_form).loadSelect({
            url: window.location.BASE + 'zone-list',
            key: 'geo_zone_id',
            value: 'zone',
            notexistIn: existing_geo_zone_id
        });
        $('#mode_id', shipment_form).loadSelect({
            url: window.location.BASE + 'courier-mode-list',
            key: 'mode_id',
            value: 'mode',
            notexistIn: modes
        });
    });
    $(document.body).on('click', '.delete-btn', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#message').empty();
        if (confirm('Are you sure Want to delete this Zone')) {
            $.ajax({
                url: window.location.BASE + 'supplier/products/zones/delete',
                data: {pss_id: CurEle.data('pss_id')},
                //cache: false,
                //contentType: 'application/json; charset=utf-8',
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#message').html(data.msg);
                        CurEle.parents('tr').remove();
                    }
                }
            });
        }
    });
    $(document.body).on('click', '.edit-btn', function (e) {
        e.preventDefault();
        var CurEle = $(this), notexistIn = existing_geo_zone_id;
        delete notexistIn.indexOf(CurEle.data('geo_zone_id'));
        shipment_form.trigger('reset');
        $('#psc_id', shipment_form).val(CurEle.data('psc_id'));
        $('#pss_id', shipment_form).val(CurEle.data('pss_id'))
        $('#delivery_charge', shipment_form).val(CurEle.data('delivery_charge'));
        $('#delivery_days', shipment_form).val(CurEle.data('delivery_days'));
        $('#geo_zone_id', shipment_form).loadSelect({
            url: window.location.BASE + 'zone-list',
            key: 'geo_zone_id',
            value: 'zone',
            selected: CurEle.data('geo_zone_id'),
            notexistIn: notexistIn
        });
        $('#mode_id', shipment_form).loadSelect({
            url: window.location.BASE + 'courier-mode-list',
            key: 'mode_id',
            value: 'mode',
            selected: CurEle.data('mode_id'),
            notexistIn: modes
        });
        $('#shipping_list').fadeOut('fast', function () {
            $('#shipment-form-panel').fadeIn('fast');
        });
    });
    $('#shipment-form-panel #cancel_btn').click(function (e) {
        e.preventDefault();
        $('#shipment-form-panel').fadeOut('fast', function () {
            $('#shipping_list').fadeIn('fast');
        });
    });
    $('#shipment-form').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        rules: {
            'supplier_product_zone[geo_zone_id]': 'required'
        },
        messages: {
            'supplier_product_zone[geo_zone_id]': 'Please select Supplier'
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $('#message').empty();
            $.ajax({
                data: $(form).serialize(),
                url: $(form).attr('action'),
                success: function (data) {
                    //$('#message').html(data.msg);
                    if (data.status == 'OK' || data.status == 'WARN') {
                        get_shippment_list();
                        $('#shipment-form-panel #cancel_btn').trigger('click');
                        $('#msg').html(data.msg);
                        $('shipping_list').show();
                    }
                },
                error: function (jqXhr) {
                    if (jqXhr.status === 422) {
                        var data = jqXhr.responseJSON;
                        if (data.status == 'WARN') {
                            $('#configure').before(data.msg)
                        }
                        var message = '';
                        if (data.error != '' && data.error != undefined)
                        {
                            alert('test');
                            $.each(data.error, function (key, value) {
                                if (message == '')
                                {
                                    message = message + value;
                                } else
                                {
                                    message = message + '<br>' + value;
                                }
                            });
                            $('#configure').before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + message + '</div>');
                        }
                    }
                    else {
                        alert('Something went wrong');
                    }
                }
            });
        }
    });
    $('#package-form').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        rules: {
            'supplier_product_zone[geo_zone_id]': 'required'
        },
        messages: {
            'supplier_product_zone[geo_zone_id]': 'Please select Supplier'
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $('#message').empty();
            $.ajax({
                data: $(form).serialize(),
                url: $(form).attr('action'),
                success: function (data) {
                    //$('#message').html(data.msg);
                    if (data.status == 'OK' || data.status == 'WARN') {
                        get_shippment_list();
                        $('#shipment-form-panel #cancel_btn').trigger('click');
                        $('#msg').html(data.msg);
                    }
                },
                error: function (jqXhr) {
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
                                } else
                                {
                                    message = message + '<br>' + value;
                                }
                            });
                            $('#configure').before('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>' + message + '</div>');
                        }
                    }
                    else {
                        alert('Something went wrong');
                    }
                }
            });
        }
    });
    //comination add hide
    $('.back-btn').on('click', function (e) {
        e.preventDefault();
        $('#product-combination-form').hide();
        $('#supplier-product-combination-form').hide();
        $('#add_payment').hide();
        $('#shipment-form-panel').hide();
        var form_id = $(this).closest('form').attr('id');
        $('#' + form_id).resetForm();
    });
    $('#product-combination-form .panel-body #remove-property-btn').on('click', function () {
        $('#product-combination-form .panel-body #product_cmb_properties option:selected').each(function () {
            $(this).remove();
        });
        $('#product-combination-form .panel-body #remove-property-btn').attr('disabled', true);
        update_property_and_value($('#product-combination-form .panel-body #property_id'), $('#product-combination-form .panel-body #value_id'), $('#product-combination-form .panel-body #product_cmb_properties'), $('#product-combination-form'));
    });
    $('#product-combination-form .panel-body #product_cmb_properties').on('change', function () {
        $('#product-combination-form .panel-body #remove-property-btn').attr('disabled', false);
    });
    $('#product-combination-form .panel-body #add-property-btn').on('click', function () {
        $('#product-combination-form .panel-body #remove-property-btn').attr('disabled', false);
        if ($('#product-combination-form .panel-body #property_id').val() != '' && $('#product-combination-form .panel-body #value_id').val() != '') {
            $('#product-combination-form .panel-body #product_cmb_properties').append(
                    $('<option>', {class: 'property-value', value: $('#product-combination-form .panel-body #property_id').val() + ':' + $('#product-combination-form .panel-body #value_id').val()}).html($('#product-combination-form .panel-body #property_id option:selected').text() + ' : ' + $('#product-combination-form .panel-body #value_id option:selected').text())
                    );
            update_property_and_value($('#product-combination-form .panel-body #property_id'), $('#product-combination-form .panel-body #value_id'), $('#product-combination-form .panel-body #product_cmb_properties'), $('#product-combination-form'));
            $('#product-combination-form #product_cmb_properties option').trigger('change');
        }
    });
    //save management form submit
    $('#product-combination-form').on('submit', function (e) {
        var con = $('#configure').data('product_id');
        $('#product-combination-form  #product_id').val(con);
        e.preventDefault();
        var form = $(this);
        $('#product-combination-form #product_cmb_properties option').prop('selected', true);
        $.ajax({
            data: form.serialize(),
            url: form.attr('action'),
            beforeSend: function () {
                $('.alert').remove();
            },
            success: function (data) {
                $('#add_combination').hide();
                get_combination_list(product_id);
                $('#combination_table #search').trigger('click');
                $('#message').html('<div class="alert alert-success">' + data.msg + '</div>');
                $('#product-combination-form').find('input[type=text], textarea').val('');
                $('#product-combination-form').trigger('reset');
                if (data.status == 'OK') {
                }
            },
            error: function (jqXhr) {
                if (jqXhr.status === 422) {
                    var data = jqXhr.responseJSON;
                    var message = '';
                    if (data.error != '' && data.error != undefined)
                    {
                        $.each(data.error, function (key, value) {
                            if (message == '')
                            {
                                message = message + value;
                            } else
                            {
                                message = message + '<br>' + value;
                            }
                        });
                        $('.combination_tit').before('<div class="alert alert-danger mT10">' + message + '</div>');
                    }
                }
                else {
                    alert('Something went wrong');
                }
            }
        });
    });
    //add stock work
    if (combination_count > 0)
    {
        $('#new_stock_value').hide();
        $('#select_combination').show();
        $('#add_stock .panel-body #combination_id').loadSelect({
            url: window.location.BASE + 'supplier/products/combination-list-for-com',
            key: 'product_cmb_id',
            value: 'product_cmb_code',
            data: {product_id: product_id},
            optionData: [{key: 'stock_value', value: 'stock_value'}],
        });
        $('#new_stock_value').show();
        $('#stock_value').val('');
        $(document.body).on('change', '#combination_id', function (event) {
            event.preventDefault();
            $('#combination_current_stock').remove();
            $('#select_combination').after('<div class="row" id="combination_current_stock"><label class="col-md-3">Combination Current Stock</label><div class="col-md-9"><p class="form-control-static" id="current_stock_value" name="current_stock_value">' + $('#combination_id option:selected').data('stock_value') + '</p></div></div>')
        });
    }
    else {
        $('#select_combination').hide();
        $('#new_stock_value').show();
    }
// add stock save area
    $(document.body).on('submit', '#add_stocks', function (e) {
        e.preventDefault();
        $('#err_msg').empty();
        var CurEle = $(this);
        $('#add_stocks').validate({
            errorElement: 'div',
            errorClass: 'error',
            focusInvalid: false,
            rules: {
                stock_value: 'required',
            },
            messages: {
                stock_value: 'Please Enter Stock Value',
            },
            submitHandler: function () {
                $.ajax({
                    data: $('#add_stocks').serialize(),
                    url: CurEle.attr('action'),
                    beforeSend: function () {
                        $('.alert').remove();
                    },
                    success: function (data) {
                        if (data.status == 'OK') {
                            $('#configure').before(data.msg);
                            $('#current_stock_value').text(data.stock.stock_on_hand);
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
    /**************************/
    //image save work
    $(document.body).on('click', '#manage_image', function (e) {
        e.preventDefault();
        $('#new_product_image').show();
        $('#upload_image').show();
        image_details($('#configure').data('product_id'));
    });
    $(document.body).on('change', '#combination_select', function (e) {
        e.preventDefault();
        image_details($('#configure').data('product_id'));
    });
    $(document.body).on('change', '.set_default', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        var image_id = $('.set_default:checked').val();
        $.ajax({
            data: {image_id: image_id},
            url: CurEle.data('url'),
            success: function (data) {
                if (data.status == 'OK') {
                    //$('#succ_img').html(data.msg);
                    $('#add_image').trigger();
                }
            },
            error: function () {
                $('stock_value').val('');
                alert('something went wrong');
            }
        });
    });
    $(document.body).on('click', '#add_new_image', function (e) {
        $('#upload_image').show();
        $('#new_product_image').show();
    });
    $(document.body).on('click', '.remove', function (event) {
        var CurEle = $(this);
        if (confirm('Are you sure Want to delete this Product Image')) {
            $.ajax({
                data: {img_id: CurEle.data('img_id'), product_id: product_id, combination_id: $('#combination_select').val()},
                url: window.location.BASE + 'supplier/products/image_remove',
                beforeSend: function () {
                    $('#image_values').empty();
                },
                success: function (data) {
                    if (data.status == 'OK') {
                        print_table(data.contents);
                        updateImgPos()
                    }
                }
            });
        }
    });
    $('#extraupload').uploadFile({
        url: window.location.BASE + 'supplier/products/add_image',
        method: 'POST',
        fileName: 'file',
        dragDrop: false,
        multiple: true,
        returnType: 'json',
        formData: {product_id: $('#configure').data('product_id')}, //maxFileCount:5,
        //allowedTypes:'jpg,png,gif,doc,pdf',
        sequential: true,
        sequentialCount: 1,
        //acceptFiles:'image/*',
        //maxFileSize:100*1024,
        showFileCounter: false,
        showDelete: true,
        abortStr: 'x',
        deletelStr: 'x',
        uploadStr: '+',
        onSuccess: function (files, data, xhr, pd)
        {
            //alert(JSON.stringify(data));
            //alert(JSON.stringify(data.filename));
            //$('#files').val(JSON.stringify(data.filename));
            //alert(data.filename);
            $('#files').val(data.filename);
            $('#times').val(data.timestamp);
            $('#new_filenames').val(data.new_filename);
            $('#configure').find('#manage_image').trigger('click');
        },
        extraHTML: function ()
        {
            var html = '<div><input type="hidden" id="files" name="files[]" value="" />';
            //html += '<input type="text' id="times' name="times[]' value="' />';
            html += '<input type="hidden" id="new_filenames" name="new_filenames[]" value="" />';
            html += '</div>';
            return html;
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
        var CurEle = $(this);
        var row = $(this).closest('tr');
        var cur_priority = row.find('span input').val();
        var new_priority = row.prev().find('span input').val();
        row.prev().find('span input').val(cur_priority);
        row.find('span input').val(new_priority);
        row.insertBefore(row.prev());
        updateImgPos()
        $('#image_values tr:first').find('.set_default').prop('checked', true);
    });
    $('#image_values').on('click', 'tr .down', function () {
        var row = $(this).closest('tr');
        var cur_priority = row.find('span input').val();
        var new_priority = row.next().find('span input').val();
        row.next().find('span input').val(cur_priority);
        row.find('span input').val(new_priority);
        row.insertAfter(row.next());
        updateImgPos()
        $('#image_values tr:first').find('.set_default').prop('checked', true);
    });
    $(document).on('click', '#save_changes', function () {
        var comb_id = $('#combination_select').val();
        $.ajax({
            url: window.location.BASE + 'supplier/products/update_sortorder',
            data: $('#image_values input[type="hidden"]').serialize() + '&' + $.param({combination_id: comb_id}),
            success: function (data) {
            }
        });
    });
    $(document.body).on('click', '.manage-properties', function () {
        var CurEle = $(this);
        $.ajax({
            data: {product_id: CurEle.data('product_id')},
            url: window.location.API.SUPPLIER + 'products/properties-values-checked',
            success: function (data) {
                $('#product_id', $('#product_properties_form')).val(CurEle.data('product_id'));
                $('#product_list').hide();
                $('#add_property').show();
                $('#properties').loadPropertiesTree(
                        {
                            url: window.location.API.SUPPLIER + 'products/properties-for-checktree',
                            data: {category_id: CurEle.data('category_id')},
                            checked: data.properties,
                            choosable: data.choosable,
                            key_value: data.key_value
                        },
                {
                    url: window.location.BASE + 'property-values-for-checktree',
                    parentKey: 'property_id',
                    checked: data.values
                }
                );
            }
        });
    });
    $('#product_properties_form').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var checked = false;
        $('#product_properties_form input[type="checkbox"]:checked').each(function () {
            checked = true
        });
        if (checked == true) {
            $.ajax({
                data: form.serialize(), url: form.attr('action'),
                success: function (data) {
                    $('#message').html(data.msg);
                    if (data.status == 'OK') {
                    }
                }
            });
        }
    });
    $('#price_form').on('change', '#price', function (e) {
        e.preventDefault();
        $.ajax({
            data: {product_id: $('#configure').data('product_id'), price: $('#price', $('#price_form')).val()},
            url: window.location.API.SUPPLIER + 'products/price/deductions',
            success: function (data) {
                var deductions = [];
                if (data.deductions.length) {
                    $.each(data.deductions, function (k, v) {
                        if (v != null) {
                            deductions.push($('<div>', {class: 'row'}).append([
                                $('<label>', {class: 'control-label col-md-4 text-right'}).append([v.title, $('<span>', {class: 'text-muted'}).html(v.info)]),
                                $('<div>', {class: 'col-md-8'}).append($('<p>', {class: 'form-control-static'}).append(v.value))
                            ]));
                        }
                    });
                }
                $('#deductions').html(deductions);
            }
        });
    });
});
