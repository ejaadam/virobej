$(document).ready(function () {
    var PT=$('#payment-types');
    $.ajax({
        url: PT.data('url'),
        success: function (data) {
            PT.append(function () {
                var types=[];
                $.each(data.payment_types, function (k, e) {
                    types.push($('<a>', {class: 'btn col-sm-12 btn-default payments', 'data-payment_key': e.payment_key, }).append([
                        $('<div>', {class: 'col-sm-2'}).append([
                            $('<img>', {class: 'img col-sm-12 img-thumbnail ', src: e.image_name})
                        ]),
                        $('<div>', {class: 'col-sm-10 text-left'}).append([
                            $('<h4>').append([
                                e.payment_type,
                                $('<small>', {class: 'pull-right'}).append(e.charges)
                            ]),
                            $('<small>').append(e.description)
                        ]),
                    ]));
                });
                return types;
            });
        }
    });
    var F=$('#withdrawal-form');
    PT.on('click', '.payments', function (e) {
        e.preventDefault();
        var CurEle=$(this);
        $.ajax({
            url: F.data('url'),
            data: {payment_key: CurEle.data('payment_key')},
            success: function (data) {
                updateWithdrawForm(data);
            }
        });
    });
    $('.close-withdraw').on('click', function (e) {
        e.preventDefault();
        $('#payment-list').show();
        $('#withdrawal-form-panel').hide();
    });
    function updateWithdrawForm(data) {
        if (data.payment_type_details!=undefined) {
            $('#payment_type').html(data.payment_type_details.payment_type);
            $('#payment_key', F).val(data.payment_type_details.payment_key);
            $('#balance', F).html([data.currency_symbol, ' ', data.balance, ' ', data.currency_code]);
            $('#charge', F).html([data.currency_symbol, ' ', data.charge, ' ', data.currency_code]);
            $('#withdrawable_amount', F).html([data.currency_symbol, ' ', data.amount-data.charge, ' ', data.currency_code]);
            $('#amount', F).val(data.amount).attr({min: data.min, max: data.max});
            var options=[], breakdowns=[];
            $.each(data.payment_type_details.currency_allowed, function (k, e) {
                options.push($('<option>', {value: k}).html(e));
            });
            $('#currency_id', F).html(options);
            $.each(data.breakdowns, function (k, e) {
                breakdowns.push($('<div>', {class: 'row'}).append([
                    $('<div>', {class: 'col-sm-4'}).html(e.wallet),
                    $('<div>', {class: 'col-sm-2'}).html([e.currency_symbol, ' ', e.currency]),
                    $('<div>', {class: 'col-sm-2'}).html([e.currency_symbol, ' ', e.current_balance, ' ', e.currency]),
                    $('<div>', {class: 'col-sm-2'}).html([data.currency_symbol, ' ', e.equivalent, ' ', data.currency_code]),
                    $('<div>', {class: 'col-sm-2'}).append($('<input>', {class: 'form-control breakdown', type: 'number', name: 'breakdowns['+e.wallet_id+']['+e.currency_id+']', min: e.min, max: e.max}).val(e.breakdown))
                ]));
            });
            $('#breakdowns', F).html(breakdowns);
            $('#payment-list').hide();
            $('#withdrawal-form-panel').show();
            $('.account-details', $('#account-details')).hide();
            $('.'+data.payment_type_details.payment_key+'.'+data.currency_code, $('#account-details')).show();
            $.each(data.account_details, function (k, e) {
                $('#'+k, $('#account-details')).val(e);
            });
        }
    }
    F.on('change', '#currency_id,#amount', function () {
        $.ajax({
            url: F.data('url'),
            data: F.serialize(),
            success: function (data) {
                $('#amount,#withdrawable_amount,#charge,#breakdowns,#account-details', F).show();
                updateWithdrawForm(data);
            },
            error: function () {
                $('#amount,#withdrawable_amount,#charge,#breakdowns,#account-details', F).hide();
            }
        });
    });
    F.on('change', '.breakdown', function () {
        var TA=0.0;
        $.each($('.breakdown', F), function (k, e) {
            TA+=parseFloat($(e).val());
        });
        $('#amount', F).val(parseInt(TA));
        $('#amount', F).trigger('change');
    });
    F.on('submit', function (e) {
        e.preventDefault();
        CURFORM=F;
        $.ajax({
            url: F.attr('action'),
            data: F.serialize(),
            success: function (data) {
                $('.close-withdraw').trigger('click');
            }
        });
    });
    var PDT=$('#payment_list').dataTable({
        ajax: {
            'url': 'supplier/withdrawal/payment_type/list',
        },
        columns: [
            {
                render: function (data, type, row, meta) {
                    var content='';
                    content+='<div class="col-sm-3"><div class="row"><img src="'+row.img_path+'" id="img_'+row.payment_type_id+'" class="paymeny_type_details img img-thumbnail"  ><h5 class="text-center">'+row.payment_type+'</h5></div></div>';
                    return content;
                }
            }
        ],
        /*		initComplete: function (settings, json) {
         $('thead', $('#payment_list')).remove();
         },
         drawCallback: function (e, settings) {
         var content = '';
         $.each($('tr td', $('#payment_list')), function () {
         content += $(this).html();
         $(this).parent('tr').remove();
         });
         $('tbody', $('#payment_list')).html('<tr><td width="100%">' + content + '</td></tr>');
         }*/
    });
    var BLDT=$('#balance_list').dataTable({
        ajax: {
            url: 'supplier/withdrawal/balance/list',
        },
        columns: [
            {
                data: 'current_balance',
                name: 'current_balance',
            },
            {
                data: 'currency',
                name: 'currency',
            },
        ]
    });
    $(document.body).on('mouse', 'paymeny_type_details', function ()
    {
    });
});
