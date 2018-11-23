$(document).ready(function () {
    $(document.body).on('click', '#close_cancel', function (e) {
        e.preventDefault();
        $('#withdraw_block').show();
        $('#cancel_info').empty();
        if ($('#status option:selected').val() != '') {
            $('.panel-title').text($('#status option:selected').text() + ' Withdrawal');
        }
    });
    var DT = $('#withdraw_list_tbl').dataTable({
        ajax: {
            url: window.location.API.SUPPLIER + 'withdraw/' + $('#withdraw_block').data('status') + '/list',
            data: function (d) {
                return $.extend({}, d, $('input,select', '.panel_controls').serializeObject());
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                class: 'text-center'
            },
            {
                name: 'transaction_id',
                data: 'transaction_id',
                class: 'text-center'
            },
            {
                name: 'amount',
                data: 'amount',
                class: 'text-right'
            },
            {
                name: 'handleamt',
                data: 'handleamt',
                class: 'text-right'
            },
            {
                name: 'paidamt',
                data: 'paidamt',
                class: 'text-right'
            },
            {
                data: 'payment_type',
                name: 'payment_type',
            }, {
                data: 'confirmed_on',
                name: 'confirmed_on',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    meta.settings.aoColumns[meta.col].bVisible = (row.status_id == 1 ? true : false);
                    return  row.confirmed_on;
                }
            },
            {
                data: 'cancelled_on',
                name: 'cancelled_on',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    meta.settings.aoColumns[meta.col].bVisible = (row.status_id == 3 ? true : false);
                    return  row.cancelled_on;
                }
            },
            {
                data: 'expected_on',
                name: 'expected_on',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    meta.settings.aoColumns[meta.col].bVisible = (row.status_id == 0 || row.status_id == 2 ? true : false);
                    return  row.expected_on;
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);
                }
            }
        ],
        drawCallback: function (settings) {
            if (settings.json.filters !== undefined) {
                if (settings.json.filters.payment_types) {
                    $('#withdrawal_list_frm #payout_type').addOptions(settings.json.filters.payment_types);
                }
                if (settings.json.filters.currencies) {
                    $('#withdrawal_list_frm #currency_id').addOptions(settings.json.filters.currencies);
                }
            }
        }
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#withdraw_list_tbl,#withdrawal_details').on('click', '.actions', function (e) {
        e.preventDefault();
        $('.alert').remove();
        var CurEle = $(this), data = CurEle.data();
        if (CurEle.data('confirm') == undefined || (CurEle.data('confirm') != null && CurEle.data('confirm') != '' && confirm(CurEle.data('confirm')))) {
            if (data.confirm != undefined) {
                delete data.confirm;
            }
            $.ajax({
                url: CurEle.attr('href'),
                data: data,
                success: function (data) {
                    DT.fnDraw();
                }
            });
        }
    });
    $('#withdraw_list_tbl').on('click', '.withdraw-details', function (e) {
        e.preventDefault();
        $('.alert').remove();
        var CurEle = $(this), data = CurEle.data();
        if (CurEle.data('confirm') == undefined || (CurEle.data('confirm') != null && CurEle.data('confirm') != '' && confirm(CurEle.data('confirm')))) {
            if (data.confirm != undefined) {
                delete data.confirm;
            }
            $.ajax({
                url: CurEle.attr('href'),
                data: data,
                success: function (data) {
                    $('#withdraw_block').hide();
                    $('#withdrawal_details').show();
                    $('.panel-title', $('#withdrawal_details')).html(data.details.transaction_id);
                    $('.options', $('#withdrawal_details')).html(addDropDownMenu(data.actions, true));
                    $.each($('p.form-control-static', $('#withdrawal_details')), function (k, e) {
                        $('#' + $(e).attr('id')).html(data.details[$(e).attr('id')]);
                    });
                    if (data.details.conversion_details != undefined && data.details.conversion_details != null) {
                        $('#conversion_details').show();
                        $('tbody', $('#conversion_details')).empty();
                        $.each(data.details.conversion_details, function (k, e) {
                            $('tbody', $('#conversion_details')).append([
                                $('<tr>').append([
                                    $('<td>').html(e.wallet),
                                    $('<td>', {class: 'text-right'}).html(e.from_amount),
                                    $('<td>', {class: 'text-right'}).html(e.to_amount),
                                    $('<td>', {class: 'text-center'}).html(e.debit_transaction_id),
                                    $('<td>', {class: 'text-center'}).html(e.credit_transaction_id)
                                ])
                            ])
                        });
                    }
                    else {
                        $('#conversion_details').hide();
                    }
                    if (data.details.account_info != undefined && data.details.account_info != null) {
                        $('#account_info').show();
                        $('tbody', $('#account_info')).empty();
                        $.each(data.details.account_info, function (k, e) {
                            $('tbody', $('#account_info')).append([
                                $('<tr>').append([
                                    $('<td>').html(k),
                                    $('<td>').html(e)
                                ])
                            ])
                        });
                    }
                    else {
                        $('#account_info').hide();
                    }
                }
            });
        }
    });
    $('.close-withdrawal_details').on('click', function (e) {
        e.preventDefault();
        $('#withdraw_block').show();
        $('#withdrawal_details').hide();
    });
});
