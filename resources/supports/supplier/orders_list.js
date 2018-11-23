$(document).ready(function () {
    var DT = $('#dt_basic').dataTable({
        ajax: {
            url: window.location.API.SELLER + 'order/' + $('#dt_basic').data('status'),
            data: function (d) {
                return $.extend({}, d, {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    search_term: $('#search_term').val(),
                    status: $('#dt_basic').attr('data-status')
                });
            }
        },
        columns: [
            {
                data: 'ordered_on',
                name: 'ordered_on',
                lass: 'text-center no-wrap'
            },
            {
                name: 'order_code',
                data: 'order_code',
                class: 'text-left',
            },
            {
                name: 'customer_name',
                data: 'customer_name',
                class: 'text-left no-wrap'
            },
            {
                name: 'qty',
                data: 'qty',
                class: 'text-right',
            },
            {
                class: 'text-center no-wrap',
                name: 'net_pay',
                data: 'net_pay',
            },
            {
                name: 'order_status',
                data: 'order_status',
                class: 'text-center no-wrap',
                render: function (data, type, row, meta) {
                    return '<span class="' + row.order_status_class + '">' + row.order_status + '</span>'
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    if (row.actions != undefined) {
                        var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-xs btn-primary dropdown-toggle', 'data-toggle': 'dropdown'})
                                .append($('<span>').attr({class: 'caret'})),
                                $('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
                            var options = [];
                            if (row.actions != undefined && row.actions != null) {
                                $.each(row.actions, function (k, v) {
                                    var data = {};
                                    if (v.data != undefined) {
                                        $.each(v.data, function (key, val) {
                                            data['data-' + key] = val;
                                        });
                                    }
                                    if (v.url != undefined) {
                                        if (v.target != undefined) {
                                            data['target'] = v.target;
                                        }
                                        else if (v.class != undefined) {
                                            data['class'] = v.class;
                                        }
                                        else {
                                            data['class'] = 'actions';
                                        }
                                    }
                                    else {
                                        data['class'] = 'show-modal';
                                    }
                                    options.push($('<li>').append($('<a>').attr($.extend({href: v.url}, data)).text(v.title)));
                                });
                            }
                            return options;
                        }));
                        return content[0].outerHTML;
                    }
                    return '';
                }
            }
        ]
    });
    $('#search').click(function () {
        //$('#full_name,#code_order').prop('checked', true);
        DT.fnDraw();
    });
    $(document).on('click', '#clear_filter', function (e) {
        $('#start_date').val('');
        $('#end_date').val('');
        $('#search_term').val('');
    });
    $('#dt_basic').on('click', '.actions', function (e) {
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
                    DT.fnDraw();
                }
            });
        }
    });
    $(document.body).on('click', '.processing,.deliver,.complete,.cancel', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        //alert($(this).attr('id'));exit;
        if (confirm('Are you sure you want to ' + CurEle.text() + '?')) {
            $.ajax({
                url: CurEle.attr('href'),
                data: {order_account_id: CurEle.data('account_id'), company_id: CurEle.attr('id')},
                success: function (res) {
                    $('.message').empty();
                    if (res['status'] == 'OK') {
                        $('.message').html(res.msg);
                        DT.fnDraw();
                    }
                }
            });
        }
    });
    $(document).on('click', '.dispatch', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('.message').empty();
        $('#shipment_details').attr('action', CurEle.attr('href')).trigger('reset');
        $('#order_id', $('#shipment_details')).val(CurEle.data('order_id'));
        $('#sub_order_id', $('#shipment_details')).val(CurEle.data('sub_order_id'));
        $('#courier_id').loadSelect({
            url: window.location.BASE + 'courier-list',
            key: 'courier_id',
            value: 'courier',
        });
        $('#mode_id').loadSelect({
            url: window.location.BASE + 'courier-mode-list',
            key: 'mode_id',
            value: 'mode',
            dependingSelector: ['#courier_id']
        });
        $('#shipment_details_modal').modal();
    });
    $(document).on('click', '#cancelOrd', function (e) {
        e.preventDefault();
        var sub_order_id = $(this).attr('data-sub_order_id');
        var account_id = $(this).attr('data-account_id');
        var order_account_id = $(this).attr('name');
        if (confirm('Are you sure you want to Cancel Order?')) {
            $.ajax({
                url: window.location.BASE + 'supplier/order/cancel',
                data: {sub_order_id: sub_order_id, account_id: account_id, order_account_id: $(this).attr('name')},
                success: function (data) {
                    $('#cancel').attr('class', 'label label-sm label-danger').html('Cancelled');
                    //$('#cancelOrd').attr('class','btn btn-danger btn-sm mr').html('Cancelled');
//                    $('#c_order').html('<button type='button' class='btn btn-danger btn-sm mr'>Cancelled</button>');
                    // DT.fnDraw();
                }
            });
        }
    });
    $('#orderbox').on('click', '#close', function (e) {
        e.preventDefault();
        $('#orderinfo').remove();
        $('.message').empty();
        $('#orderreport').show();
        // DT.fnDraw();
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
            $.ajax({
                url: $(form).attr('action'),
                data: $(form).serialize(),
                success: function (res) {
                    $('.message').html(res.msg);
                    if (res['status'] == 'OK') {
                        DT.fnDraw();
                        $('#shipment_details_modal').modal('hide');
                    }
                }
            });
        }
    });
});
