$(document).ready(function () {
    $('#new_product').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: window.location.BASE + 'supplier/products/add_new_products',
            beforeSend: function () {
                $('#new_product').attr('disabled', true);
            },
            success: function (data) {
                $('#new_product').attr('disabled', false);
                if (data.status == 'OK') {
                    $('.modal-title').html('Add Product');
                    $('#add_product_model #new_product_form').html(data.content);
                    crop_js();
                    $('#add_product_model').modal('show');
                } else {
                    alert('something Went Wrong');
                }
            }
        })
    });
    $('#add_form').on('click', '#cancel', function (e) {
        e.preventDefault();
        $('#add_form')[0].reset();
        $('#add_product_model').modal('hide');
    })
    $('#add_form').on('change', '#product_img', function (e) {
        var val = $(this).val();
        doctypes = 'jpg|png|jpeg';
        if (val != 'undefined' && val != '') {
            txtext = val.split('.')[1];
            txtext = txtext.toString().toLowerCase();
            fformats = doctypes.split('|');
            if (fformats.indexOf(txtext) == - 1) {
                alert('Please choose Valid filer formet ie: jpg,jpeg,png');
                val = $(this).val('');
            }
        }
    });
    /* Validations */
    $('#add_form').validate({
        rules: {
            product: 'required',
            brand: 'required',
            category: 'required',
            product_img: {
                required: function (element) {
                    var img_status = $('#add_form #prevoius_img').val();
                    return (img_status != '1');
                }
            },
            currency_id: 'required',
            price: 'required',
            stock: 'required',
            in_stock: 'required',
            sku_id: 'required',
            description: {
                required: true,
                minlength: 10,
                maxlength: 300
            }
        },
        messages: {
            product: 'Please Enter Product Name',
            brand: 'Select Brand Name',
            category: 'Select Category',
            product_img: 'Please Choose Product Image',
            currency_id: 'Select Currency type',
            price: 'Please Enter Price',
            stock: 'Enter Stock of this product',
            in_stock: 'Please Select Stock',
            sku_id: 'Please Enter Sku Name',
            description: {
                required: 'Please Enter product description',
                minlength: 'Please Enter description more then 10 letters',
                maxlength: 'Please Enter description less then 300 letters'
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                add_form = $(form);
                $('#add_form').ajaxSubmit({
                    beforeSend: function () {
                        $('#submit_product').attr('disabled', true);
                        $('.alert-msg').empty();
                        $('.alert-msg').show();
                    },
                    success: function (data) {
                        $('#submit_product').attr('disabled', false);
                        if (data.status == 'OK') {
                            $('#submit_product').val('Add Product');
                            $('#submit_product').attr('disabled', 'disabled');
                            $('#add_product_model').modal('hide');
                            $('#add_form')[0].reset();
                            $('#image_resize .cropper-container').remove();
                            $('.alert-msg').html(data.msg).fadeOut(4000);
                            $('#search').trigger('click');
                        } else {
                            $('#alert-msg').html(data.msg);
                        }
                    },
                });
            }
        }
    })
});
