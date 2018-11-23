$(function () {

    $.ajax({
        url: window.location.SELLER + 'wallet/balance',
        success: function (op) {
			console.log(op);
            if (op.wallet) {
                var cont = '', wallets = [];
                var wallet = op.wallet;
                data = Object(op.transactions);
				$('#wallets').empty();				
                 cont += '<div class="row"><blockquote class="col-sm-3"><span class="label label-primary col-sm-2"><h4>' + wallet.currency_symbol + '</h4></span> <div class="col-sm-10 text-muted"><em> Current Balance <br>' + wallet.current_balance + '</em><br></div></blockquote></div><div class="clearfix"></div>';
                cont = '<div class="panel col-sm-12"><div class="panel-heading"><h4>' + (wallet.can_add_money ? '<a class="btn btn-primary pull-right load-content" href="' + document.location.SELLER + 'wallet/add-money">Add Money</a>' : '') + wallet.wallet_name + '</h4></div><div class="panel-body col-sm-12"><div id="' + wallet.wallet_code + '">' + cont + '</div></div></div>';
				console.log(cont); 
                $('#wallets').append(cont);
                wallets.push(wallet.wallet_code);
                var tbl = '';
                if (data != undefined && data != '') {
                    $.each(data, function (index, val) {
                        tbl += '<tr><td class="text-center">' + val.created_on + '</td><td>' + val.remark + '</td><td>' + val.wallet_code + '</td><td class="text-right">' + val.amount + '</td></tr>'
                    });
                }
                $('#flds').html(tbl);
            } else {
                $('#wallets').html('<h3 class="text-center">' + $no_records + '<h3>');
            }
        }
    });
});
