$(document).ready(function () {
    $.ajax({
        url: $('#details-panel').data('url'),
        success: function (data) {
            //$('#supplier_order_code').text(data.sub_order_code);
            $('#created_on').text(data.sub_order_details.ordered_on);
            $('#status span').addClass(data.sub_order_details.sub_order_status_class).text(data.sub_order_details.sub_order_status);
            $('#qty').text(data.sub_order_details.qty);
            $('#net_pay').text(data.sub_order_details.net_pay);
            $('#shipping_details').html([$('<h3>').append(data.shipping_details.full_name), data.shipping_details.address]);
            if (data.order_particulars.length) {
                $('#sub_order_list_table tbody').empty();
                $.each(data.order_particulars, function (k, e) {
                    $('#sub_order_list_table tbody').append($('<tr>').append([
                        $('<td>', {class: 'stat_wide'}).append($('<div>', {class: 'media'}).append([
                            $('<img>', {alt: '', width: '100px', src: e.imgs[0].img_path, class: 'media-object img-thumbnail pull-left'}).append(),
                            $('<div>', {class: 'media-body'}).append([
                                $('<h3>').append($('<a>').html(e.product_name)),
                                $('<p>').append([$('<span>', {class: 'text-muted'}).html('MRP: '), e.mrp_price]),
                                $('<p>').append([$('<span>', {class: 'text-muted'}).html('Price: '), e.price]),
                                $('<p>').append([$('<span>', {class: 'text-muted'}).html('Discount: '), e.discount + '%'])
                            ])
                        ])),
                        $('<th>', {class: 'text-center'}).html(e.qty),
                        $('<th>', {class: 'text-center'}).html([$('<span>', {class: e.order_item_status_class}), e.order_item_status]),
                        $('<th>', {class: 'text-center'}).html(e.net_pay),
                        $('<td>').append(addDropDownMenu(e.actions))
                    ]));
                });
            }
        },
        error: function () {
            alert('Something went wrong');
        }
    });
    $(document.body).on('click', '.actions', function (e) {
        e.preventDefault();
        $('.alert').remove();
        var Ele = $(this), data = Ele.data();
        if (Ele.data('confirm') == undefined || (Ele.data('confirm') != null && Ele.data('confirm') != '' && confirm(Ele.data('confirm')))) {
            if (data.confirm != undefined) {
                delete data.confirm;
            }
            $.ajax({
                url: Ele.attr('href'),
                data: data,
                success: function (data) {
                    location.reload();
                }
            });
        }
    });
});
$('#shipment_details').validate({
    errorElement: 'div',
    errorClass: 'error',
    focusInvalid: false,
    rules: {
        'shipment[courier_id]': 'required',
        'shipment[mode_id]': 'required',
        'shipment[bill_number]': 'required',
        'shipment[remarks]': 'required',
        'shipment[weight]': 'required'
    },
    messages: {
        'shipment[courier_id]': 'Please Select your Courier',
        'shipment[mode_id]': 'Please select your mode of Shipment',
        'shipment[bill_number]': 'Please enter your bill number',
        'shipment[remarks]': 'Please enter your remaks',
        'shipment[weight]': 'Please enter the parsel weight'
    },
    submitHandler: function (form, event) {
        event.preventDefault();
        var order_item_id = $('.dispatch').data('order_item_id');
        var order_item_status_id = $('.dispatch').data('order_item_status_id');
        var data = $(form).serializeArray(); // convert form to array
        data.push({name: 'order_item_id', value: order_item_id});
        data.push({name: 'order_item_status_id', value: order_item_status_id});
        $.ajax({
            url: $(form).attr('action'),
            data: $.param(data),
            success: function (res) {
                $('.message').html(res.msg);
                if (res['status'] == 'OK') {
                    $('#search').trigger('click');
                    $('#shipment_details_modal').modal('hide');
                    location.reload();
                }
            },
            error: function () {
                alert('Something went wrong');
            }
        });
    }
});
