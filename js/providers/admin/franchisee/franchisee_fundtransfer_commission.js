$(document).ready(function () {
    $('#search').click(function () {
        $('#commission_table').dataTable({
            "bPagenation": true,
            "bProcessing": true,
            "bFilter": false,
            "bAutoWidth": false,
            "oLanguage": {
                "sSearch": "<span>Search:</span> ",
                "sInfo": "Showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries",
                "sLengthMenu": "_MENU_ <span>entries per page</span>"
            },
            "bDestroy": true,
            "bSort": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "type": "POST",
                "data": {
                    "search_term": $('#search_term', $('#commission-form')).val(),
                    "status": $('#status', $('#commission-form')).val(),
                    "from": $('#from', $('#commission-form')).val(),
                    "to": $('#to', $('#commission-form')).val()
                }
            },
            "columns": [
                {
                    "data": "created_date",
                    "name": "created_date",
                    "class": "text-center",
                    "render": function (data, type, row, meta) {
                        return new String(row.created_date).dateFormat("dd-mmm-yyyy HH:MM:ss");
                    }
                },
				 {
                    "data": "receiver",
                    "name": "receiver",
                    "render": function (data, type, row, meta) {
                        return row.receiver_uname+' - '+row.franchisee_location+' '+row.franchisee_type_name;
                    }
                },
                {
                    "name": "from_uname",
                    "data": function (row, type, set) {
                        var uname = row.from_full_name;
                        if (row.from_uname !== null && row.from_uname != '') {
                            uname += '<br />(' + row.from_uname + ')';
                        }
                        if (row.district_name !== undefined && row.district_name !== null && row.district_name != '') {
                            uname += '<br />District : ' + row.district_name;
                        }
                        return new String(uname);
                    }
                },
                {
                    "data": "to_uname",
                    "name": "to_uname",
                    "render": function (data, type, row, meta) {
                        return '<b>' + row.to_full_name + '</b><br /> (' + row.to_uname + ')';
                    }
                },
				
                {
                    "data": "remarks",
                    "name": "remarks",
                    "render": function (data, type, row, meta) {
                        var transaction_details = '';
                        if (row.remark !== undefined && row.remark != '') {
                            transaction_details = row.remark + '<br />';
                        }
                        transaction_details += '<b>Transaction Id: ' + row.transaction_id + '<b />';
                        return new String(transaction_details);
                    }
                },
                {
                    "data": "amount",
                    "name": "amount",
                    "class": "text-right no-wrap",
                    "render": function (data, type, row, meta) {
                        return '<b class="text-success">' + row.currency_symbol + ' ' + parseFloat(row.amount).toFixed(2) + ' ' + row.currency + '</b> ';
                    }
                },
                {
                    "data": "commission_amount",
                    "name": "commission_amount",
                    "class": "text-right no-wrap",
                    "render": function (data, type, row, meta) {
                        return '<b class="text-success">' + row.currency_symbol + ' ' + parseFloat(row.commission_amount).toFixed(2) + ' ' + row.currency + '</b> ';
                    }
                },
                {                    
                    "name": "status_name",
                    "class": "text-center",
					"data": "status_name",
					"render" : function(data, type, row, meta){
						return '<div class = "label '+row.status_label+'">'+row.status_name+'</div>';
					}
                },
                {
                    "data": "confirmed_date",
                    "name": "confirmed_date",
                    "class": "text-center",
                    "render": function (data, type, row, meta) {
                        return (row.confirmed_date != null && row.confirmed_date != '') ? new String(row.confirmed_date).dateFormat("dd-mmm-yyyy HH:MM:ss") : '-';
                    }
                },
               /* {
                    "class": "text-center",
                    "orderable": false,
                    "render": function (data, type, row, meta) {
                        var json = $.parseJSON(meta.settings.jqXHR.responseText);
                        var action_buttons = '';
                        action_buttons = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        if ((row.commission_type == 1 || row.commission_type == 6) && (row.status == 2 || row.status == 3)) {
                            action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-commission text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="1" data-transaction_id="' + row.transaction_id + '">Confirm</a></li>';
                            action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-commission text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="4" data-transaction_id="' + row.transaction_id + '">Cancel</a></li>';
                        }else if(row.status == 1){
							action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-commission text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="4" data-transaction_id="' + row.transaction_id + '">Cancel</a></li>';
						}
                        action_buttons += '</ul></div>';
                        return action_buttons;
                    }
                }*/
            ],
            "order": [[0, 'desc']]
        });
    });
    $('#commission_table').on('click', '.confirm-commission', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('.alert').remove();
        if (confirm('Are you wants to ' + CurEle.text() + ' ' + CurEle.data('transaction_id') + ' this transaction?')) {
            $.ajax({
                url: CurEle.attr('href'),
                type: "POST",
                data: {fr_com_id: CurEle.data('fr_com_id'), status: CurEle.data('status')},
                datatype: "JSON",
                success: function (data) {
                    $('#commission-form').after(data.msg);
                    if (data.status == 'OK') {
                        //$('#search').trigger('click');
                    }
                },
                error: function () {
                    alert('Something went wrong');
                }
            });
        }
    });
    $('#search').trigger('click');
});
