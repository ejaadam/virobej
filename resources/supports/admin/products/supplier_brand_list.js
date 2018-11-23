$(document).ready(function () {
    $('#brand_list #supplier_id').loadSelect({
        firstOption: {key: '', value: 'All Suppliers'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'seller-list',
        key: 'id',
        value: 'name',
        copyTo: [{selector: '#brand_form #supplier_id', key: 'id', value: 'name', autoSelect: false}]
    });
    $('#brand_list #brand_id').loadSelect({
        firstOption: {key: '', value: 'All Brands'},
        firstOptionSelectable: true,
        url: window.location.BASE + 'product-brands-list',
        key: 'brand_id',
        value: 'brand_name'
    });
    var DT = $('#table3').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('input,select', '.panel_controls').serializeObject());
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on'
            },
            {
                data: 'company_name',
                name: 'company_name'
            },
            {
                data: 'brand_name',
                name: 'brand_name',
                render: function (data, type, row, meta) {
                    return $('<b>').append(function () {
                        var d = [];
                        d.push(row.brand_name + '  ');
                        if (row.is_exclusive_for_supplier) {
                            d.push($('<span>', {class: 'label label-primary'}).text('Exclusive'));
                        }
                        return d;
                    })[0].outerHTML;
                }
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return $('<span>', {class: row.status_class, 'data-brand_id': row.brand_id, id: 'status_' + row.brand_id}).text(row.status)[0].outerHTML;
                }
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return $('<span>', {class: row.verification_class, 'data-brand_id': row.brand_id, id: 'verification-' + row.brand_id}).text(row.verification)[0].outerHTML;
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                    action_buttons += (row.status_id == Constants.ACTIVE)
                            ? '<li><a data="0" data-brand_id="' + row.brand_id + '" href="' + json.url + '/admin/catalog/products/seller/brands/update-status/' + row.brand_id + '" class="brand_status"  >Inactive</a></li>'
                            : '<li><a data="1" data-brand_id="' + row.brand_id + '" href="' + json.url + '/admin/catalog/products/seller/brands/update-status/' + row.brand_id + '" class="brand_status"  >Active</a></li>';
                    if (row.main_is_verified === Constants.ACTIVE) {
                        action_buttons += (row.is_verified === Constants.ACTIVE)
                                ? '<li><a data="0" data-brand_id="' + row.brand_id + '" href="' + json.url + '/admin/catalog/products/seller/brands/update-verification" class="brand-verification"  >Unverify</a></li>'
                                : '<li><a data="1" data-brand_id="' + row.brand_id + '" href="' + json.url + '/admin/catalog/products/seller/brands/update-verification" class="brand-verification"  >Verify</a></li>';
                    }
                    action_buttons += '<li><a href="' + json.url + '/admin/catalog/products/seller/brands/delete/' + row.brand_id + '" class="delete_btn" data="' + row.brand_id + '" >Delete</a></li>';
                    action_buttons += '</ul></div>';
                    return action_buttons;
                }
            }
        ]
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#create_brand').click(function (e) {
        e.preventDefault();
        $('#new_brand').modal('show');
    });
    $('#brand_form').submit(function (e) {
        e.preventDefault();
        CURFORM = $('#brand_form');
        $.ajax({
            data: $('#brand_form').serializeObject(),
            url: window.location.BASE + 'admin/catalog/products/seller/brands/save',
            success: function (data) {
                $('#new_brand').modal('hide');
                DT.fnDraw();
            }
        });
    });
    $(document.body).on('click', '.back-btn', function (e) {
        e.preventDefault();
        $('#list').show();
        $('#meta_form').hide();
    });
    $('#table3').on('click', '.brand_status', function (e) {
        e.preventDefault();
        var CurEle = $(this), status = parseInt($(this).attr('data')), brand_id = $(this).data('brand_id');
        if (confirm('Are you sure? to ' + ((status === 1) ? 'active' : 'inactive') + ' this brand?')) {
            $.ajax({
                data: {status: status},
                url: CurEle.attr('href'),
                success: function (data) {
                    if (status === 1)
                    {
                        $('#status_' + brand_id).removeClass('label-danger').addClass('label-success').text('Active');
                        CurEle.text('Inactive').attr('data', 0);
                    }
                    else
                    {
                        $('#status_' + brand_id).removeClass('label-success').addClass('label-danger').text('Inactive');
                        CurEle.text('Active').attr('data', 1);
                    }
                }
            });
        }
    });
    $('#table3').on('click', '.brand-verification', function (e) {
        e.preventDefault();
        var CurEle = $(this), is_veriied = parseInt(CurEle.attr('data')), brand_id = CurEle.data('brand_id');
        if (confirm('Are you sure? You want to ' + (is_veriied === 1 ? 'verify' : 'unverify') + ' this brand?')) {
            $.ajax({
                data: {status: is_veriied, brand_id: brand_id},
                url: CurEle.attr('href'),
                success: function (data) {
                    if (is_veriied === 1)
                    {
                        $('#verification-' + brand_id).removeClass('label-danger').addClass('label-success').text('Verified');
                        CurEle.text('Unverify').attr('data', 0);
                    }
                    else
                    {
                        $('#verification-' + brand_id).removeClass('label-success').addClass('label-danger').text('Not Verified');
                        CurEle.text('Verify').attr('data', 1);
                    }
                }
            });
        }
    });
    $('#table3').on('click', '.delete_btn', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (confirm('Are you sure? You want to delete this Brand?')) {
            $.ajax({
                url: url,
                success: function (data) {
                    DT.fnDraw();
                }
            });
        }
    });
});
