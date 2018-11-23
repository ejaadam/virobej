$(document).ready(function () {
    $('fileupload-new').click(function (event) {
        event.preventDefault();
        $('#logo_image').click();
        $('#image_upload_show1').attr('src', '');
        $('a#img_upload_button1').attr('disabled', true);
        $('a#img_upload_button1').html('Processing...');
    });
    var DT = $('#table3').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, {
                    search_term: $('#search_term').val(),
                    currency_id: $('#currency_id').val(),
                    from: $('#start_date').val(),
                    to: $('#end_date').val(),
                    category: $('#category').val(),
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'pi.created_on',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('yyyy-mmm-dd');
                }
            },
            {
                data: 'product_name',
                name: 'product_name',
                render: function (data, type, row, meta) {
                    return String('<a href="' + window.location.BASE + '/supplier/products/edit/' + row.product_id + '" class="edit">' + row.product_name + '</a><br><strong>ID : </strong>#' + row.product_code + '<br><strong>SKU : </strong>' + row.sku + '<br><strong>Brand : </strong>' + row.brand_name + '<br><strong>Category: </strong>' + row.category_name);
                }
            },
            {
                data: 'img_path',
                name: 'img_path',
                render: function (data, type, row, meta) {
                    var product_image = '';
                    var path = $('#product_image_path').val();
                    var typeArr = ['jpg', 'jpeg', 'gif', 'png'];
                    if (row.img_path != null && row.img_path != '') {
                        var extension = row.img_path.substr((row.img_path.lastIndexOf('.') + 1));
                        if (checkformat(extension, 'gif|jpg|jpeg|png', '')) {
                            if (row.img_path != '') {
                                product_image = '<a href=' + path + '/' + row.img_path + ' target="_blank"><img style="height:100px ;width:100px;" src=' + row.file_path + row.img_path + '></a>';
                            }
                            else
                            {
                                product_image = '<a href=' + path + '/' + row.img_path_default + ' target="_blank"><img style="height:100px ;width:100px;" src=' + path + row.img_path + '></a>';
                            }
                        }
                    }
                    return new String(product_image);
                }
            },
            {
                data: 'price',
                name: 'price',
                render: function (data, type, row, meta) {
                    return (parseFloat(row.price)).toFixed(0).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + ' ' + row.code;
                }
            },
            {
                data: 'in_stock',
                name: 'in_stock',
                render: function (data, type, row, meta) {
                    if (row.in_stock == 1) {
                        return '<span class="label label-success">In Stock</span>';
                    } else {
                        return '<span class="label label-danger">Out of Stock</span>';
                    }
                }
            },
            {
                data: 'stock_value',
                name: 'stock_value',
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, row, meta) {
                    return ((row.is_verified == 1) ? ' <span class="label label-success">Verified</span>' : ' <span class="label label-danger">Not Verified</span>') + ((row.status == 1) ? ' <span class="label label-success">Active</span>' : ' <span class="label label-danger">Inactive</span>');
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
                    action_buttons = action_buttons + '<ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    action_buttons = action_buttons + '<li><a class="edit" href="' + json.url + '/supplier/products/edit/' + row.product_id + '">Configure</a></li>';
                    action_buttons = action_buttons + '<li><a href="' + json.url + '/supplier/products/delete/' + row.product_id + '" class="delete_btn" data="' + row.product_id + '" >Delete</a></li>';
                    if (row.status == 1)
                    {
                        action_buttons = action_buttons + '<li><a data="0" href="' + json.url + '/supplier/products/product_status/' + row.product_id + '" class="product_status"  >Inactive</a></li>';
                    }
                    else
                    {
                        action_buttons = action_buttons + '<li><a data="1" href="' + json.url + '/supplier/products/product_status/' + row.product_id + '" class="product_status">Active</a></li>';
                    }
                    action_buttons = action_buttons + '<li><a class="edit_stock" href="' + json.url + '/supplier/products/edit_stock/' + row.product_id + '">Stock</a></li>';
                    action_buttons = action_buttons + '<li><a class="add_stock" data-product_name="' + row.product_name + '"  data-current_stock_value="' + row.current_stock_value + '" data-sku="' + row.sku + '"  href="' + json.url + '/supplier/products/add_stock/' + row.product_id + '"> Add Stock</a></li>';
                    action_buttons = action_buttons + '</ul></div>';
                    return action_buttons;
                }
            }
        ]
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#table3').on('click', '.edit', function (e) {
        e.preventDefault();
        $('#image_resize .cropper-container').remove();
        $('#err_msg').empty();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            success: function (data) {
                if (data.status == 'OK') {
                    $('.modal-title').html('Edit Product');
                    $('#submit_product').val('Update Product');
                    $('#add_product_model #new_product_form').html(data.content);
                    $('#add_product_model').modal('show');
                    crop_js();
                } else {
                    alert('something Went Wrong');
                }
            }
        });
    })
    /* Edit stock */
    $('#table3').on('click', '.edit_stock', function (e) {
        e.preventDefault();
        $('#err_msg').empty();
        var url = $(this).attr('href');
        $.ajax({
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
        });
    })
    $('#table3').on('click', '.create_stock', function (e) {
        e.preventDefault();
        $('#err_msg').empty();
        var product_name = $(this).data('product_name');
        var current_stock_value = $(this).data('current_stock_value');
        var sku = $(this).data('sku');
        var url = $(this).attr('href');
        $('.add_stock').modal('show');
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
    })
    $('#add_product_model').on('click', '#update_stock', function (e) {
        e.preventDefault();
        var btn = $(this);
        var url = window.location.BASE + 'supplier/products/update_stock';
        var stock_value = $('#stock_value').val();
        var op_type = $('input[type="radio"]:checked').val();
        var product_id = $('#product_id').val();
        if (stock_value != '') {
            var current_stock = $('#current_value').val();
            if (op_type == 2) {
                type_text = 'reduce stock';
                check_stock = parseInt(current_stock) - parseInt(stock_value);
                if (check_stock < 0) {
                    $('#stock_msg').html('You cannot reduce more then available stock');
                    return false;
                }
            } else {
                type_text = 'add stock';
            }
            $('#stock_msg').empty();
            if (confirm('Are you sure? You want to ' + type_text + '?'))
            {
                $.ajax({
                    url: url,
                    data: {type: op_type, stock_value: stock_value, product_id: product_id},
                    beforeSend: function () {
                        btn.attr('disabled', true);
                    },
                    success: function (data) {
                        btn.attr('disabled', false);
                        if (data.status == 'OK') {
                            btn.val('Update');
                            $('#update_stock').attr('disabled', true);
                            $('#add_product_model').modal('hide');
                            $('#alert-msg').html(data.msg);
                            DT.fnDraw();
                        } else {
                            alert('something Went Wrong');
                        }
                    }
                });
            }
        } else {
            $('#stock_msg').html('Please Enter valid stock value');
        }
    })
    /* Active Inactive */
    $('#table3').on('click', '.product_status', function (e) {
        e.preventDefault();
        var msg = '';
        url = $(this).attr('href');
        status = $(this).attr('data');
        if (status == 1) {
            msg = 'Active';
        } else if (status == 0) {
            msg = 'Deactive';
        }
        if (confirm('Are you sure? You want to ' + msg + ' this Product?')) {
            $.ajax({
                data: {status: status},
                url: url,
                success: function (data) {
                    if (data.status == 'OK') {
                        $('body #message').html(data.msg);
                        DT.fnDraw();
                    } else {
                        $('#add_product_model #msg').modal(data.msg);
                    }
                }
            })
        } else {
            $('#err_msg').html('Please Enter Category');
        }
    });
    /* delete product */
    $('#table3').on('click', '.delete_btn', function (e) {
        e.preventDefault();
        var msg = '';
        url = $(this).attr('href');
        if (confirm('Are you sure Want to delete this Product')) {
            $.ajax({
                url: url,
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#alert-msg').html(data.msg);
                        DT.fnDraw();
                    } else {
                        $('#alert-msg').modal(data.msg);
                    }
                }
            })
        } else {
            $('#err_msg').html('Please Enter Category');
        }
    });
});
function checkformat(ele, doctypes, str) {
    txtext = ele;
    txtext = txtext.toString().toLowerCase();
    fformats = doctypes.split('|');
    if (fformats.indexOf(txtext) == - 1) {
        return false;
    } else {
        return true;
    }
}
