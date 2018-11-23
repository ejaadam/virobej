var CP = $('#configure');
$(document).ready(function () {
    CP.productConfigure();
});
$.fn.extend({
    productConfigure: function (options) {
        var _this = $(this);
        _this.options = _this.data();
        _this.options = $.extend(_this.options, options);
        _this.data = null;
        $.ajax({
            data: _this.options,
            url: window.location.API.SELLER + 'products/configure',
            success: function (data) {
                _this.data = data;
                updateProductDetails(_this.data);
            }
        });
    }
});
function updateProductDetails(Pro) {
    var productPrice = [],
            existingCmb = [],
            SF = $('#shipment-form'),
            modes,
            existing_geo_zone_id = [],
            PCF = $('#product-combination-form'),
            PCPPCF = $('#product_cmb_properties', PCF),
            SPCF = $('#supplier-product-combination-form');
    $('.title').text(Pro.product_details.product_name);
    $('#product_id', '#meta_info_form, #assoc_form, #add_stocks, #payment_add-form, #price_form, #product_properties_form').val(Pro.product_id);
    $('#product_id', PCF).val(Pro.product_id);
    $('#product_id', SPCF).val(Pro.product_id);
    $('#supplier_product_id', '#meta_info_form,#assoc_form,#product-combination-form,#payment_add-form,#shipment-form').val(Pro.product_details.supplier_product_id);
    $('#product_cmb_id', '#price_form').val(Pro.product_details.product_cmb_id);
    $('#is_exclusive', '#assoc_form').val(Pro.product_details.is_exclusive);
    $('#sku_code', '#add_stocks').text(Pro.product_details.sku);
    $('#current_stock_value', '#add_stocks').text(Pro.product_details.stock_on_hand);
    $('#is_replaceable', SPCF).val(Pro.product_details.is_replaceable);
    $('#pre_order', SPCF).val(Pro.product_details.pre_order);
    $('#product_name.form-control', $('#product_info_form')).val(Pro.product_details.product_name).prop('readonly', ! Pro.editable);
    $('#sku.form-control', $('#product_info_form')).val(Pro.product_details.sku).prop('readonly', ! Pro.editable);
    CKEDITOR.instances['description'].setData(Pro.product_details.description);
    $('#description .form-control', $('#product_info_form')).prop('readonly', ! Pro.editable);
    $('#weight.form-control', $('#product_info_form')).val(Pro.product_details.weight).prop('readonly', ! Pro.editable);
    $('#width.form-control', $('#product_info_form')).val(Pro.product_details.width).prop('readonly', ! Pro.editable);
    $('#height.form-control', $('#product_info_form')).val(Pro.product_details.height).prop('readonly', ! Pro.editable);
    $('#length.form-control', $('#product_info_form')).val(Pro.product_details.length).prop('readonly', ! Pro.editable);
    $('#eanbarcode', $('#product_info_form')).val(Pro.product_details.eanbarcode);
    $('#upcbarcode', $('#product_info_form')).val(Pro.product_details.upcbarcode);
    $('#product_id', $('#product_info_form')).val(Pro.product_id);
    $('#product_id', $('#country')).val(Pro.product_id);
    $('#supplier_product_id', $('#product_info_form')).val(Pro.product_details.supplier_product_id);
    $('#supplier_product_id.ignore-reset', $('#add_stocks')).val(Pro.product_details.supplier_product_id);
    $('#brand_id', $('#product-combination-form')).val(Pro.product_details.brand_id);
    $('#category_id', $('#product-combination-form')).val(Pro.product_details.category_id);
    if (Pro.meta_product_info) {
        $('#description', $('#seo')).val(Pro.meta_product_info.description).prop('readonly', ! Pro.editable);
        $('#meta_keys', $('#seo')).val(Pro.meta_product_info.meta_keys).prop('readonly', ! Pro.editable);
        $('#relative_post_id', $('#seo')).val(Pro.product_id);
    }
    if (Pro.product_details.is_featured) {
        $('#featured', $('#product_info_form')).prop('checked', true);
    }
    if (Pro.product_details.promote_to_homepage) {
        $('#promote_to_homepage', $('#product_info_form')).prop('checked', true);
    }
    $('#exclusive', $('#assoc_form')).val(Pro.product_details.is_exclusive).attr("selected", "selected");
    $('#cnty_id', $('#country')).val(Pro.product_details.product_country);
    if (Pro.product_details.is_combinations) {
        $('#impact_on_price').closest('.row').show();
        $('#new_stock_value').hide();
        $('#select_combination').show();
        $('#add_stock .panel-body #combination_id').loadSelect({
            url: window.location.API.SELLER + 'products/combinations/select-list',
            key: 'product_cmb_id',
            value: 'product_cmb_code',
            data: {product_id: Pro.product_id},
            optionData: [{key: 'stock_value', value: 'stock_value'}]
        });
        $('#new_stock_value').show();
        $('#stock_value').val('');
        CP.on('change', '#combination_id', function (event) {
            event.preventDefault();
            $('#combination_current_stock').remove();
            $('#select_combination').after('<div class="row" id="combination_current_stock"><label class="col-md-3">Combination Current Stock</label><div class="col-md-9"><p class="form-control-static" id="current_stock_value" name="current_stock_value">' + $('#combination_id option:selected').data('stock_value') + '</p></div></div>');
        });
    }
    else {
        $('#impact_on_price').closest('.row').hide();
        $('#select_combination').hide();
        $('#new_stock_value').show();
    }
    $('#payment_id').loadSelect({
        url: window.location.BASE + 'payment-mode-list',
        key: 'paymode_id', data: {supplied_id: Pro.product_details.supplier_id},
        selected: true,
        value: 'mode_name'
    });
    $('#category_id').loadSelect({
        url: window.location.BASE + 'product-categories-list', key: 'id',
        value: 'name',
        selected: Pro.product_details.category_id
    });
    $('#brand_id').loadSelect({
        url: window.location.BASE + 'product-brands-list',
        key: 'brand_id',
        value: 'brand_name',
        selected: Pro.product_details.brand_id
    });
    $('#condition').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'product-condition-list',
        key: 'condition_id',
        value: 'condition_desc',
        selected: Pro.product_details.condition_id
    });
    $('#product_visibility, #prod_visibility').loadSelect({
        firstOption: {key: '', value: 'All'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'product-visibility-list', key: 'visiblity_id',
        value: 'visiblity_desc',
        selected: Pro.product_details.visiblity_id
    });

    $('#tax_class_id').loadSelect({
        url: window.location.BASE + 'tax-classes-list',
        key: 'tax_class_id',
        value: 'tax_class',
        selected: Pro.product_details.tax_class_id
    });
    $('#combination_select').loadSelect({
        url: window.location.API.SELLER + 'products/combinations/select-list',
        key: 'product_cmb_id',
        value: 'product_cmb_code',
        data: {product_id: Pro.product_id},
        optionData: [{key: 'stock_on_hand', value: 'stock_on_hand'}]
    });

    if (Pro.is_shipment) {
        $('a[href=#shipping_det]').parent('li').show();
    }
    else {
        $('a[href=#shipping_det]').parent('li').hide();
    }
    $('#manage_country').on('click', function (e) {
        $('#country_id').loadSelect({
            url: window.location.BASE + 'countries-list',
            key: 'id',
            value: 'text',
            selected: $('#cnty_id').val(),
        });
    });
    $('.tags').select2({
        multiple: true,
        tags: true,
        //placeholder: '',
        //minimumInputLength: 1,
        triggerChange: true,
        allowClear: true,
        tags: true,
                //tokenSeparators: [',', ' '],
                separator: ',',
        ajax: {
            type: 'POST',
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
            $.each(Pro.get_tags, function (key, valu) {
                p.push({id: valu.tag_id, text: valu.tag_name});
            });
            callback(p);
        }
    }).select2('val', []);
    $('#treeview-container').treeview({
        debug: true
    });
    var PPDT = $('#product-price').dataTable({
        pageLength: - 1,
        ajax: {
            url: window.location.API.SELLER + 'products/price',
            data: function (d) {
                return $.extend({}, d, {
                    product_id: Pro.product_id,
                    product_cmb_id: Pro.product_details.is_combinations ? Pro.product_details.product_cmb_id : null
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
    var CDT = $('#combination_table').dataTable({
        ajax: {
            url: window.location.API.SELLER + 'products/combinations',
            data: function (d) {
                return $.extend({}, d, {
                    product_id: Pro.product_id,
                    supplier_product_id: Pro.product_details.supplier_product_id,
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
                data: 'product_cmb_id',
                name: 'product_cmb_id'
            },
            {
                data: 'stock_on_hand',
                name: 'stock_on_hand'
            },
            {
                data: 'currency_with_charge',
                name: 'currency_with_charge'
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center status',
                render: function (data, type, row, meta) {
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
    var SDT = $('#shipment-table').dataTable({
        ajax: {
            url: window.location.BASE + 'seller/products/zones',
			method: 'POST',
            data: function (d) {
                return $.extend({}, d, {
                    supplier_product_id: Pro.product_details.supplier_product_id,
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
    var PDT = $('#payment_list_table').dataTable({
        sDom: 't',
        ajax: {
            url: window.location.BASE + 'seller/products/payment_list',
            data: function (d) {
                return $.extend({}, d, {
                    supplier_product_id: Pro.product_details.supplier_product_id,
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
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                    action_buttons += '<li><a data-product_id="' + row.ptype_id + '" class="delete_payment_method" href="">Delete</a></li>';
                    action_buttons += '</ul></div>';
                    return action_buttons;
                }
            }
        ]
    });
    $('#add-new-price-btn').on('click', function (e) {
        e.preventDefault();
        $('#price-form-panel').show();
        $('#mrp_price', $('#price_form')).val('');
        $('#price', $('#price_form')).val('');
        $('#currency_id', $('#price_form')).loadSelect({
            url: window.location.BASE + 'currencies-list',
            key: 'currency_id',
            selected: Constants.DEFAULT_CURRENCY,
            value: 'currency'
        });
    });
    $('#back-to-price-list').on('click', function (e) {
        e.preventDefault();
        $('#price-form-panel').hide();
        $('#price-list-panel').show();
        PPDT.fnDraw();
    });
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
    $('#product-price').on('click', '.edit-product-price', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#price-form-panel').show();
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
                $('#is_shipping_beared', $('#price_form')).prop('checked', true);
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
                url: window.location.API.SELLER + 'products/price/delete',
                data: {spp_id: CurEle.data('spp_id')},
                success: function (data) {
                    $('#back-to-price-list').trigger('click');
                }
            });
        }
    });
    $('#save_price').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.location.API.SELLER + 'products/price/save',
            data: $('#price_form').serialize(),
            success: function (data) {
                $('#back-to-price-list').trigger('click');
            }
        });
    });
    $('#country_save_btn').on('click', function (e) {
        e.preventDefault();
        CURFORM = $('#country');
        $.ajax({
            url: window.location.API.SELLER + 'products/country/save',
            data: $('#country').serialize(),
            success: function (data) {
                if (data.countries !== undefined && data.countries > 0) {
                    $('#conutry-list', CP).empty();
                    $.each(Pro.countries, function (k, e) {
                        $('#conutry-list', CP).append($('<li>').append(e));
                    });
                }
            }
        });
    });
    $('#conutry-list').on('click', '.delete-product-county', function (e) {
        e.preventDefault();
        var Cele = $(this);
        $.ajax({
            url: window.location.API.SELLER + 'products/country/delete',
            data: Cele.data(),
            success: function (data) {
                if (data.countries !== undefined && data.countries.length !== []) {
                    addCounties(Pro.countries);
                }
            }
        });
    });
    $('#show-values').on('click', function () {
        $('#values').text($('#treeview-container').treeview('selectedValues'));
    });
    $('#image_values tr:first').find('.set_default').attr('checked', true);
    CP.on('click', '#manage_combo', function (e) {
        $('#combination_list').show();
        $('#currency_id').loadSelect({
            url: window.location.BASE + 'currencies-list',
            key: 'paymode_id',
            data: {supplied_id: Pro.product_details.supplier_id},
            selected: true,
            value: 'mode_name'
        });
    });
    $('#shipment-form-panel #cancel_btn').on('click', function (e) {
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
            $.ajax({
                data: $(form).serialize(),
                url: window.location.BASE + 'seller/products/zones/save',
                success: function (data) {
                    if (data.status == 'OK' || data.status == 'WARN') {
                        get_shippment_list();
                        $('#shipment-form-panel #cancel_btn').trigger('click');
                        $('shipping_list').show();
                    }
                },
                error: function (jqXhr) {
                    if (jqXhr.status === 422) {
                        var data = jqXhr.responseJSON;
                        if (data.status == 'WARN') {
                            CP.before(data.msg);
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
                            CP.before($('<div>', {class: 'alert alert-danger'}).append([
                                $('<a>', {class: 'close', href: '#', 'data-dismiss': 'alert', 'aria-label': 'close'}).text('×'),
                                message
                            ]));
                        }
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
            $.ajax({
                data: $(form).serialize(),
                url: $(form).attr('action'),
                success: function (data) {
                    if (data.status == 'OK' || data.status == 'WARN') {
                        get_shippment_list();
                        $('#shipment-form-panel #cancel_btn').trigger('click');
                    }
                },
                error: function (jqXhr) {
                    if (jqXhr.status === 422) {
                        var data = jqXhr.responseJSON;
                        if (data.status == 'WARN') {
                            CP.before(data.msg);
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
                            CP.before($('<div>', {class: 'alert alert-danger'}).append([
                                $('<a>', {class: 'close', href: '#', 'data-dismiss': 'alert', 'aria-label': 'close'}).text('×'),
                                message
                            ]));
                        }
                    }
                }
            });
        }
    });
    $('.back-btn').on('click', function (e) {
        e.preventDefault();
        PCF.hide();
        SPCF.hide();
        $('#add_payment').hide();
        $('#shipment-form-panel').hide();
        var form_id = $(this).closest('form').attr('id');
        $('#' + form_id).resetForm();
    });
    $('.panel-body #remove-property-btn', PCF).on('click', function () {
        $('option:selected', PCPPCF).each(function () {
            $(this).remove();
        });
        $('.panel-body #remove-property-btn', PCF).attr('disabled', true);
        update_property_and_value($('.panel-body #property_id', PCF), $('.panel-body #value_id', PCF), PCPPCF, PCF);
    });
    PCPPCF.on('change', function () {
        $('.panel-body #remove-property-btn', PCF).attr('disabled', false);
    });
    $('.panel-body #add-property-btn', PCF).on('click', function () {
        $('.panel-body #remove-property-btn', PCF).attr('disabled', false);
        if ($('.panel-body #property_id', PCF).val() != '' && $('.panel-body #value_id', PCF).val() != '') {
            PCPPCF.append(
                    $('<option>', {class: 'property-value', value: $('.panel-body #property_id', PCF).val() + ':' + $('.panel-body #value_id', PCF).val()}).html($('.panel-body #property_id option:selected', PCF).text() + ' : ' + $('.panel-body #value_id option:selected', PCF).text())
                    );
            update_property_and_value($('.panel-body #property_id', PCF), $('.panel-body #value_id', PCF), PCPPCF, PCF);
            $('option', PCPPCF).trigger('change');
        }
    });
    //save management form submit
    PCF.on('click', '#product_comb', function (e) {
        e.preventDefault();
        $('#product-combination-form #descrip').val(CKEDITOR.instances.description.getData());
        var form = $(this);
        $('option', PCPPCF).prop('selected', true);
        $.ajax({
            data: $('#product-combination-form').serialize(),
            url: window.location.API.SELLER + 'products/combinations/save',
            beforeSend: function () {
                $('.alert').remove();
            },
            success: function (data) {
                $('#add_combination').hide();
                $('#combination_list').show();
                CDT.fnDraw();
                $('#combination_table #search').trigger('click');
                PCF.find('input[type=text], textarea').val('');
                PCF.trigger('reset');
            },
            error: function (jqXhr) {
                $('input[type="button"]', PCF).removeAttr('disabled', true).val('Save');
                if (jqXhr.status == 422) {
                    var data = jqXhr.responseJSON;
                    PCF.appendLaravelError(data.error);
                }
                else {
                    alert('Something went wrong');
                }
            }
        });
    });
    $('#extraupload').uploadFile({
        url: window.location.API.SELLER + 'products/image/add',
        method: 'POST',
        fileName: 'file',
        dragDrop: false,
        multiple: true,
        returnType: 'json',
        formData: {product_id: Pro.product_id},
        //maxFileCount:5,
        allowedTypes: 'jpeg,jpg,png,gif',
        sequential: true,
        sequentialCount: 1,
        acceptFiles: 'image/*',
        //maxFileSize:100*1024,
        showFileCounter: false,
        showDelete: true,
        abortStr: 'x',
        deletelStr: 'x',
        uploadStr: '+',
        onSuccess: function (files, data, xhr, pd) {
            $('#files').val(data.filename);
            $('#times').val(data.timestamp);
            $('#new_filenames').val(data.new_filename);
            CP.find('#manage_image').trigger('click');
        },
        extraHTML: function () {
            return $('<div>').append([
                $('<input>', {type: 'hidden', id: 'files', name: 'files[]'}),
                $('<input>', {type: 'hidden', id: 'new_filenames', name: 'new_filenames[]'})
            ])[0].outerHTML;
        }
    });
    $('#image_values').on('click', 'tr .up', function () {
        var row = $(this).closest('tr');
        var cur_priority = row.find('span input').val();
        var new_priority = row.prev().find('span input').val();
        row.prev().find('span input').val(cur_priority);
        row.find('span input').val(new_priority);
        row.insertBefore(row.prev());
        updateImgPos();
        $('#image_values tr:first').find('.set_default').prop('checked', true);
    });
    $('#image_values').on('click', 'tr .down', function () {
        var row = $(this).closest('tr');
        var cur_priority = row.find('span input').val();
        var new_priority = row.next().find('span input').val();
        row.next().find('span input').val(cur_priority);
        row.find('span input').val(new_priority);
        row.insertAfter(row.next());
        updateImgPos();
        $('#image_values tr:first').find('.set_default').prop('checked', true);
    });
    $('#product_properties_form').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var checked = false;
        $('#product_properties_form input[type=checkbox]:checked').each(function () {
            checked = true;
        });
        if (checked === true) {
            $.ajax({
                data: form.serialize(), 
				url: window.location.API.SELLER + 'products/properties/save',
                success: function (data) {
                }
            });
        }
    });
    $('#price_form').on('change', '#price,#is_shipping_beared', function (e) {
        e.preventDefault();
        if (parseInt($('#price', $('#price_form')).val()) > 0) {
            $.ajax({
                data: {product_id: Pro.product_id, price: $('#price', $('#price_form')).val(), is_shipping_beared: $('#is_shipping_beared', $('#price_form')).is(':checked') ? 1 : 0},
                url: window.location.API.SELLER + 'products/price/deductions',
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
        } 
    });
    function updateImgPos() {
        var i = 1;
        $('#image_values tr').each(function () {
            $('td.sort_order', $(this)).text(i);
            $('td span input', $(this)).val(i);
            i ++;
        });
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
            $('button[type=submit]', form).removeAttr('disabled');
        }
        else {
            $('button[type=submit]', form).attr('disabled', true);
        }
    }
//manage image related functions
    function image_details(P_ID, image_id) {
        P_ID = (P_ID != undefined) ? P_ID : 0;
        image_id = (image_id != undefined) ? image_id : 0;
        var image_data = 0;
        var comb_id = $('#combination_select').val();
        if (P_ID != 0) {
            var data = {product_id: P_ID, combination_id: comb_id};
        }
        else {
            var data = {image_id: image_id, combination_id: comb_id};
        }
        $.ajax({
            data: data,
            url: window.location.API.SELLER + 'products/image/details',
            beforeSend: function () {
                $('#image_values').empty();
            },
            success: function (data) {
                if (data.status != undefined && data.status == 'image_details') {
                    image_data = data.contents;
                    $('#upload_image').find('#img_up').attr('src', data.url);
                    $('#upload_image').find('#img_up').attr('data-image_id', image_id);
                    $('#upload_image').find('#img_up').attr('data-img_file', data.contents.img_file);
                    $('#upload_image').find('h4').text('Edit Image');
                }
                else {
                    if (comb_id == 0) {
                        print_browse_page(data.contents);
                        updateImgPos();
                    }
                    else {
                        print_table(data.contents);
                        updateImgPos();
                    }
                }
            },
            error: function () {
                $('stock_value').val('');
            }
        });
        if (image_data != '')
        {
            return image_data;
        }
    }
    function print_table(data) {
        var content = '';
        $.each(data, function (key, elm) {
            content = '<tr> <td><span  data-priority="' + elm.sort_order + '" data-img_id="' + elm.id + '"><input type="hidden" name="sortorder[' + elm.id + ']" value="' + elm.sort_order + '"/></span><a class="pull-left" href="#">  <img  src="' + elm.img_path + '" alt="" width="100px" class="media-object thumbnail" /> </a></td><td class="sort_order">' + elm.sort_order + '</td>';
            if (elm.primary == 1)
            {
                content += '<td> <input type="radio" class="set_default hidden"  name="cover_image" checked="checked" value="' + elm.id + '" data-url ="seller/products/cover_images"> <span data-img_id ="' + elm.id + '" class="col-sm-8 pull-right icon-edit hidden"></span><button data-img_id ="' + elm.id + '" class="btn btn-small remove pull-right"> <i class="icon-remove"></i></button><span class="pull-right mR10"> <button class="btn btn-small up"><i class="fa fa-arrow-up"></i></button><button class="btn btn-small down"><i class="fa fa-arrow-down"></i></button></span></td></tr>';
            }
            else
            {
                content += '<td> <input type="radio" class="set_default hidden"  name="cover_image" value="' + elm.id + '" data-url ="seller/products/cover_images"><span data-img_id ="' + elm.id + '" class="col-sm-8 pull-right icon-edit hidden"></span> <button data-img_id ="' + elm.id + '" class="btn btn-small remove pull-right"> <i class="icon-remove"></i></button><span class="pull-right mR10"><button class="btn btn-small up"><i class="fa fa-arrow-up"></i></button><button class="btn btn-small down"><i class="fa fa-arrow-down"></i></button></span></td></tr>';
            }
            $('#image_values').append(content);
            $('#image_values tr').eq(0).find('.set_default').prop('checked', true)
        })
    }
    function print_browse_page(data) {
        $('#gallery_grid').empty();
        $.each(data, function (key, elm) {
            $('#gallery_grid').append($('<li>', {class: 'mix user_0 business travel mix_all', style: 'display: inline-block; opacity: 1;'}).append([
                $('<a>', {class: 'img_wrapper gal_lightbox', href: 'javascript:void(0);'}).append([
                    $('<img>', {class: 'img img-responsive', src: elm.img_path}),
                    $('<input>', {type: 'checkbox', class: 'is_default', id: 'make_as_def', name: 'is_default[]'}).val(elm.id),
                    $('<input>', {type: 'checkbox', class: 'selected_img hidden', name: 'selected[]'}).val(elm.id)
                ])
            ]));
        });
    }
    CP.on('change', '#payment_id', function () {
        var mode_id = $(this).val();
        $.ajax({
            data: {mode_id: mode_id},
            url: window.location.BASE + 'get_available_payment_gateway',
            success: function (data) {
                $('#payment_gateway_select').empty();
                $.each(data, function (key, val) {
                    $('#payment_gateway_select').append($('<option>', {value: val.payment_type_id}).text(val.payment_type));
                });
            }
        });
    });
    CP.on('click', '#make_as_def', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        if (confirm('Are you sure Want to make this image as Default ')) {
            $.ajax({
                url: window.location.API.SELLER + 'products/image/make-default',
                data: {img_id: CurEle.val(), product_id: Pro.product_id},
                success: function (data) {

                    $('.is_default').prop('checked', false);
                    CurEle.prop('checked', true);
                }
            });
        }
    });
    CP.on('click', '#delete_combination_product', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        if (confirm('Are you sure Want to delete this Product Combination')) {
            $.ajax({
                url: CurEle.attr('href'),
                data: {product_cmb_id: CurEle.data('product_cmb_id'), product_id: Pro.product_id},
                success: function (data) {
                    if (data.status == 'OK') {
                        CurEle.parents('tr').remove();
                    }
                }
            });
        }
    });
    CP.on('click', '#add_selected_image', function (e) {
        var data = $('#gallery_grid input:checked').serialize();
        var combination = $('#combination_select').val();
        if (data != '' && combination != '') {
            $.ajax({
                data: data + '&' + $.param({combination_id: combination}),
                url: window.location.API.SELLER + 'seller/products/image/combination/add',
                success: function (data) {
                    updateImgPos();
                    image_details(Pro.product_id);
                }
            });
        }
        else
        {
            return false;
        }
    });
    CP.on('click', '#delete_selected_image', function (e) {
        e.preventDefault();
        var data = $('#gallery_grid input:checked').serialize();
        var combination = $('#combination_select').val();
        if (data != '' && combination != '')
        {
            var ajax_data = data + '&' + $.param({combination_id: combination, product_id: Pro.product_id});
            $.ajax({
                data: ajax_data,
                url: window.location.API.SELLER + 'products/image/delete-selected',
                success: function (data) {
                    $('#combination_select').val(0);
                    print_browse_page(data.contents);
                }
            });
        }
    });
    $('#combination_list', CP).on('click', '.change_status', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        if (confirm('Are you sure Want to ' + CurEle.text() + ' this product')) {
            $.ajax({
                data: {status: CurEle.attr('data-status'), product_id: Pro.product_id, product_cmb_id: CurEle.data('product_cmb_id')},
                url: CurEle.attr('href'),
                beforeSend: function (data) {
                    CurEle.attr('data-status', '');
                },
                success: function (data) {
                    if (data.status == 'OK') {
                        if (data.status_val == 1)
                        {
                            var status_val = 0;
                            CurEle.attr('data-status', status_val).text('Inactive');
                            $('#active').removeClass('label label-danger').addClass('label label-success').text('Active');
                        }
                        else
                        {
                            var status_val = 1;
                            CurEle.attr('data-status', status_val).text('Active');
                            $('#active').removeClass('label label-success').addClass('label label-danger').text('Inactive');
                        }
                    }
                }
            });
        }
    });
    CP.on('click', '#payment_add', function (event) {
        event.preventDefault();
        $('#payment_gateway_select').attr('multiselect', true);
        var form_data = $('#payment_add-form').serialize();
        $.ajax({
            data: form_data, url: window.location.BASE + 'seller/products/save_payment_types',
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
                        $('#payment_modes').before($('<div>', {class: 'alert alert-danger'}).append([
                            $('<a>', {class: 'close', href: '#', 'data-dismiss': 'alert', 'aria-label': 'close'}).text('×'),
                            message
                        ]));
                    }
                }
            }
        });
    });
    CP.on('click', '#remove-gateway-btn', function () {
        $('#payment_gateway_select option:selected').each(function () {
            $(this).remove();
        });
    });
    CP.on('click', '.delete_payment_method', function (event) {
        event.preventDefault();		
        $.ajax({
            data: {ptype_id: $(this).attr('data-product_id')},
            url: window.location.BASE + 'seller/products/delete_payment',
            success: function (data) {
                PDT.fnDraw();
            }
        });
    });
    CP.on('click', '#manage_payment', function () {
        PDT.fnDraw();
    });
    CP.on('click', '#add_payment_btn', function () {
        $('#add_payment').show();
    });
    CP.on('click', '#combination_table #edit_combination_product', function (e) {
        e.preventDefault();
        PCF.find('input[type=text], textarea').val('');
        PCF.trigger('reset');
        $('.panel-body #property_id', PCF).loadSelect({
            url: window.location.API.SELLER + 'products/combinations/properties-list',
            key: 'property_id',
            value: 'property',
            data: {product_id: Pro.product_id},
            success: function () {
                update_property_and_value($('.panel-body #property_id', PCF), $('.panel-body #value_id', PCF), PCPPCF, PCF);
                $('.panel-body #value_id', PCF).loadSelect({
                    url: window.location.API.SELLER + 'products/combinations/values-list',
                    key: 'value_id',
                    value: 'value',
                    data: {product_id: Pro.product_id},
                    dependingSelector: ['#property_id']
                });
            }
        });
        var CurEle = $(this);
        $.ajax({
            data: {product_cmb_id: CurEle.data('product_cmb_id')},
            url: window.location.API.SELLER + 'products/combinations/details',
            success: function (data) {
                $('#product_cmb_id').val(data.data[0].product_cmb_id);
                $('#product_cmb', PCF).val(data.data[0].product_cmb);
                $('#sku', PCF).val(data.data[0].sku);
                PCPPCF.html(data.data.output);
                update_property_and_value($('.panel-body #property_id', PCF), $('.panel-body #value_id', PCF), PCPPCF, PCF);
                $('#cmb_ppt_id').val(data.data[0].cmb_ppt_id);
                PCF.show();
                $('#currency', PCF).text(data.data[0].currency);
                $('#product_impact_price', PCF).val(data.data[0].impact_on_price);
                $('#selling-price', PCF).text(data.data[0].mrp_price);
                $('#price', PCF).text(data.data[0].price);
                PCPPCF.html('<option class="property-value" value="' + data.data[0].vals + '">' + data.data[0].labels + '</option>');
            }
        });
    });
    CP.on('click', '#add-product-combination-btn', function (e) {
        e.preventDefault();
        $('#product_cmb_id').val('');
        $('#product_configure_span').empty();
        $('#cmb_ppt_id').val('');
        PCPPCF.empty();
        PCF.find('input[type=text], textarea').val('');
        PCF.resetForm();
        PCF.show();
        SPCF.hide();
        $('#combination_list').show();
        $('.panel-body #property_id', PCF).loadSelect({
            url: window.location.API.SELLER + 'products/combinations/properties-list',
            key: 'property_id',
            value: 'property',
            data: {product_id: Pro.product_id},
            success: function () {
                $('.panel-body #value_id', PCF).loadSelect({
                    url: window.location.API.SELLER + 'products/combinations/values-list',
                    key: 'value_id',
                    value: 'value',
                    data: {product_id: Pro.product_id},
                    dependingSelector: ['#property_id']
                });
            }
        });
    });
    CP.on('click', '#add-supplier-product-combination-btn', function (e) {
        e.preventDefault();
        PCF.hide();
        SPCF.show();
        $('#combination_list').show();
        $('#product_cmb_id', SPCF).loadSelect({
            url: window.location.API.SELLER + 'products/combinations/list',
            key: 'product_cmb_id',
            value: 'product_cmb',
            notexistIn: existingCmb,
            data: {product_id: Pro.product_id}
        });
    });
    CP.on('submit', SPCF, function (e) {
        e.preventDefault();
        var form = $(this);
        $('.alert').remove();
		//console.log(form.serialize());exit;
        $.ajax({
            data: form.serialize(),
            url: window.location.API.SELLER + 'products/save',
            success: function (data) {
                $('#add_combination').hide();
                $('#combination_list').show();
                CDT.fnDraw();
                $('#combination_table #search').trigger('click');
                SPCF.find('input[type=text], textarea').val('');
                SPCF.trigger('reset');
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
                        $('.combination_tit').before($('<div>', {class: 'alert alert-danger'}).text(message));
                    }
                }
            }
        });
    });
    /*   CP.on('submit', '#assoc_form', function (event) {
     event.preventDefault();
     console.log('555');
     $.ajax({
     data: $('#assoc_form').serialize(),
     //url: window.location.BASE + 'supplier/products/save_association',
     url: window.location.API.SUPPLIER + 'products/save-product',
     success: function (data) {
     console.log('ok');
     },
     error: function () {
     $('stock_value').val('');
     }
     });
     }); */
    CP.on('click', '.product_save_btn', function (e) {
        e.preventDefault();
        var data = '';
        $('#product_info_form #description').val(CKEDITOR.instances.description.getData());
        $('form', CP).each(function () {
            data += ((data != '') ? '&' : '') + $(this).serialize();
        });
        $.ajax({
            data: data,
            url: window.location.API.SELLER + 'products/save-product',
            beforeSend: function () {
                $('.alert').remove();
            },
            success: function (data) {

            },
            error: function (jqXhr) {
                $('input[type=submit]', $('#product_form')).val('Submit').attr('disabled', false);
                if (jqXhr.status === 422) {
                    var data = jqXhr.responseJSON;
                    var message = '';
                    if (data.error != '' && data.error != undefined) {
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
                        CP.before($('<div>', {class: 'alert alert-danger'}).append([
                            $('<a>', {class: 'close', href: '#', 'data-dismiss': 'alert', 'aria-label': 'close'}).text('×'),
                            message
                        ]));
                    }
                }
            }
        });
    });
    CP.on('click', '.delete-btn', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        if (confirm('Are you sure? You want to delete this Zone?')) {
            $.ajax({
                url: window.location.BASE + 'seller/products/zones/delete',
                data: {pss_id: CurEle.data('pss_id')},
                //cache: false,
                //contentType: 'application/json; charset=utf-8',
                success: function (data) {
                    if (data.status == 'OK') {
                        CurEle.parents('tr').remove();
                    }
                }
            });
        }
    });
    CP.on('click', '.edit-btn', function (e) {
        e.preventDefault();
        var CurEle = $(this), notexistIn = existing_geo_zone_id;
        delete notexistIn.indexOf(CurEle.data('geo_zone_id'));
        SF.trigger('reset');
        $('#psc_id', SF).val(CurEle.data('psc_id'));
        $('#pss_id', SF).val(CurEle.data('pss_id'));
        $('#delivery_charge', SF).val(CurEle.data('delivery_charge'));
        $('#delivery_days', SF).val(CurEle.data('delivery_days'));
        $('#geo_zone_id', SF).loadSelect({
            url: window.location.BASE + 'zone-list',
            key: 'geo_zone_id',
            value: 'zone',
            selected: CurEle.data('geo_zone_id'),
            //        notexistIn: notexistIn
        });
        $('#mode_id', SF).loadSelect({
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
    CP.on('click', '#add_stock', function (e) {
        e.preventDefault();
        $('#err_msg').empty();
        $.ajax({
            data: $('#add_stocks').serialize(),
            url: window.location.API.SELLER + 'products/update-stock',
            beforeSend: function () {
                $('.alert').remove();
            },
            success: function (data) {
                $('#current_stock_value').text(data.stock.stock_on_hand);
            },
            error: function () {
                $('stock_value').val('');
            }
        });
    });
    CP.on('click', '#manage_image', function (e) {
        e.preventDefault();
        $('#new_product_image').show();
        $('#upload_image').show();
        image_details(Pro.product_id);
    });
    CP.on('change', '#combination_select', function (e) {
        e.preventDefault();
        image_details(Pro.product_id);
    });
    CP.on('change', '.set_default', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        var image_id = $('.set_default:checked').val();
        $.ajax({
            data: {image_id: image_id},
            url: CurEle.data('url'),
            success: function (data) {
                if (data.status == 'OK') {
                    $('#add_image').trigger();
                }
            },
            error: function () {
                $('stock_value').val('');
            }
        });
    });
    CP.on('click', '#add_new_image', function (e) {
        $('#upload_image').show();
        $('#new_product_image').show();
    });
    CP.on('click', '.remove', function (event) {
        var CurEle = $(this);
        if (confirm('Are you sure Want to delete this Product Image')) {
            $.ajax({
                data: {img_id: CurEle.data('img_id'), product_id: Pro.product_id, combination_id: $('#combination_select').val()},
                url: window.location.API.SELLER + 'products/image/delete',
                beforeSend: function () {
                    $('#image_values').empty();
                },
                success: function (data) {

                    print_table(data.contents);
                    updateImgPos();

                }
            });
        }
    });
    CP.on('click', '.icon-edit', function (event) {
        $('#upload_image').show();
        $('#new_product_image').hide();
        var image_id = $(this).data('img_id');
        var product_id = 0;
        image_details(product_id, image_id);
        $('#img_up').attr('src', data.src);
    });
    CP.on('click', '#save_changes', function () {
        var comb_id = $('#combination_select').val();
        $.ajax({
            url: window.location.API.SELLER + 'products/update-sort-order',
            data: $('#image_values input[type=hidden]').serialize() + '&' + $.param({combination_id: comb_id}),
            success: function (data) {
            }
        });
    });
    CP.on('click', '.manage-properties', function () {
        $.ajax({
            data: {product_id: Pro.product_id},
            url: window.location.API.SELLER + 'products/properties-values-checked',
            success: function (data) {
                $('#product_list').hide();
                $('#add_property').show();
                $('#properties').loadPropertiesTree(
                        {
                            url: window.location.API.SELLER + 'products/properties-for-checktree',
                            data: {category_id: Pro.product_details.category_id},
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
    CP.on('click', '#new_zone_btn', function (e) {
// $('#pss_id', shipment_form).val(pss_id)
        $('input[type=number]').val('');
        $('#psd_id').val('');
        //$('input:textbox').val()
        $('#shipment-form-panel').show();
        $('#currency_id', SF).loadSelect({
            url: window.location.BASE + 'currencies-list',
            key: 'currency_id',
            selected: Constants.DEFAULT_CURRENCY,
            value: 'currency'
        });
        $('#geo_zone_id', SF).loadSelect({
            url: window.location.BASE + 'zone-list',
            key: 'geo_zone_id',
            value: 'zone',
            notexistIn: existing_geo_zone_id
        });
        $('#mode_id', SF).loadSelect({
            url: window.location.BASE + 'courier-mode-list',
            key: 'mode_id',
            value: 'mode',
            notexistIn: modes
        });
    });
    CP.on('click', '#same_details', function () {
        if ($(this).prop('checked') == true) {
            $('#prod_info').addClass('hidden');
        }
        else
        {
            $('#prod_info').removeClass('hidden');
        }
    });
    if (Pro.countries !== undefined && Pro.countries !== []) {
        addCounties(Pro.countries);
    }
    function addCounties(countries) {
        $('#conutry-list', CP).empty();
        $.each(countries, function (k, e) {
            $('#conutry-list', CP).append($('<li>').append([$('<a>', {href: '#', class: 'delete-product-county pull-right', 'data-country_id': k, 'data-product_id': Pro.product_id}).append('Delete'), e]));
        });
    }
}
