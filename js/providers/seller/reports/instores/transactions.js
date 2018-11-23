$(function () {
	var balance = '';
    var WALLET = {};
    WALLET.TRANS = {};
    WALLET.TRANS.FORM = $('#filter_form');
    WALLET.TRANS.REPORT = $('#trans_list');
    WALLET.TRANS.TABLE = $('#list_table');
	console.log(WALLET.TRANS.TABLE);
    var DT = WALLET.TRANS.TABLE.dataTable({
        
        columnDefs: [
            {className: 'text-center', targets: [0]},
            {className: 'text-center', targets: [1]},
            {className: 'text-left', targets: [2]},
            {className: 'text-center', targets: [3]},
            
        ],
        ajax: {
            url: WALLET.TRANS.REPORT.attr('action'),
            type: "POST",
            data: function (d) {
                return $.extend({}, d, $('input,select', '#filter_form').serializeObject());
            },
			dataSrc: function (res) {
				$('#balance-fields h4').each(function (k, v) {
                    $(this).html(res.balance[$(this).attr('id')]);
                });
                return res.data;
			},	
        },
        columns: [                    
            {
				name: 'remark',
				data: 'remark',
				class: 'text-left',
				render: function (data, type, row, meta) {
					return  '<strong>'+ row.statementline + '</strong><br>' + row.remark + '<br>' + row.created_on;
				}
            },  
            {
                data: 'status',
                name: 'status',
                render: function (data, type, row, meta) {
                    return '<span class="label label-' + row.status_class + '">' + row.status + '</span>';
                }
            },
            {
                name: 'amount',
                data: 'amount',
                class: 'text-right',
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
    /* WALLET.TRANS.FORM.on('submit', function (e) {
     e.preventDefault();
     DT.fnDraw();
     }); */
    WALLET.TRANS.FORM.on('click', '#searchbtn', function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    WALLET.TRANS.FORM.on('click', '#resetbtn', function (e) {
        $('input,select,input:checkbox', $(this).closest('form')).val('');
        $('input:checkbox').removeAttr('checked');
        e.preventDefault();
        DT.fnDraw();
    });
	WALLET.TRANS.TABLE.on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (op) {
            if (op.details != undefined && op.details != null) {
                /*  $('#transactions-details table').empty();
                 $.each(op.details, function (k, e) {
                 $('#transactions-details table').append($('<tr>').append([$('<th>').append(e.label), $('<td>').append(e.value)]));
                 }); */
                var details = op.details;
                var payments = op.details.payment_details;
                $('#ord-details').html('<span><strong>' + details.remark + '</strong></span><br><div class="row"><div class="col-md-6">Order Number<br><strong>' + details.order_code.value + '</strong><br>' + details.created_on + '</div><div class="col-md-6">Amount<br><strong>' + details.amount.value + '</strong><br><span class="label label-' + details.status_class + '">' + details.status + '</span></div></div>');

                $('#mr-details').html('<div class="row"><div class="col-md-6">Customer : <strong>' + details.customer.value + '</strong><br>Customer Code: ' + details.account_code.value + '<br>' + details.mobile.label + ' : ' + details.mobile.value + '<br>' + details.email.label + ' : ' + details.email.value + '</div><div class="col-md-6"></div></div>');

                var row = label = '';
                if (payments != '') {
                    $.each(payments, function (index, fld) {
                        row = row + '<tr><td class=".' + fld.label + '_cls">' + fld.label + '</td><td>' + fld.value + '</td></tr>';
                        if (fld.label == '') {
                            $('.' + fld.label + '_cls').css('display', 'none');
                        }
                    });
                    $('#pay-details').html('<p><strong>Payment Details</strong></p><div class="row"><div class="col-md-12"><table class="table">' + row + '</table></div></div>');
                }

                //$('.faq').attr('href',details.support)
                $('#transactions-list').hide();
                $('#transactions-details').show();
            }
        });
    });
	$('#transactions-details').on('click', '#back', function (e) {
        e.preventDefault();
        $('#transactions-details').hide();
        $('#transactions-list').show();
    });
});