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
                    "data": "from_uname",
                    "name": "from_uname"
                },   
				{
                    "data": "to_uname",
                    "name": "to_uname"
                },   
				{
                    "name": "transaction_details",
                    "class": "text-left",
                    "data": function (row, type, set) {
                        return row.remark;
                    }
            	},               
                {
                    "name": "amount",
                    "class": "text-right",
                    "data": function (row, type, set) {
                        return parseFloat(row.amount).toFixed(2)+ ' ' + row.code;
                    }
                },
                {
                    "name": "commission_amount",
                    "class": "text-right",
                    "data": function (row, type, set) {
                        return '<b class="text-success">' + parseFloat(row.commission_amount).toFixed(2)  + ' ' + row.code + '</b> ';
                    }
                },
                {
                    "data": "status",
                    "name": "status",
                    "class": "status",
                    "render": function (data, type, row, meta) {
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
				/*{
                    "class": "text-center",
                    "orderable": false,
                    "render": function (data, type, row, meta) {
                        var json = $.parseJSON(meta.settings.jqXHR.responseText);
                        var action_buttons = '';
                        action_buttons = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        if ((row.status == 2 || row.status == 3)) {
                            action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-bonus text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="1" data-transaction_id="' + row.transaction_id + '">Confirm</a></li>';
                            action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-bonus text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="4" data-transaction_id="' + row.transaction_id + '">Cancel</a></li>';
                        }else if(row.status == 1){
							action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-bonus text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="4" data-transaction_id="' + row.transaction_id + '">Cancel</a></li>';
						}
                        action_buttons += '</ul></div>';
                        return action_buttons;
                    }
                }
*/            
			],
            "order": [[0, 'desc']]
        });
    });
    $('#commission_table').on('click', '.confirm-bonus', function (e) {
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
	$("#reset_btn").click(function (event) {
        event.preventDefault();
        $('#commission-form').trigger('reset');
        $('#search').trigger('click');
    });  
});


/*$(document).ready(function () {		
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
                    "name": "created_date"
                },   
				{
                    "data": "from_uname",
                    "name": "from_uname"
                },   
				{
                    "name": "transaction_details",
                    "class": "text-left",
                    "data": function (row, type, set) {
                        return row.remark;
                    }
            	},
                {
                    "data": "code",
                    "name": "code"
                },
                {
                    "name": "amount",
                    "class": "text-right",
                    "data": function (row, type, set) {
                        return parseFloat(row.amount).toFixed(2);
                    }
                },
                {
                    "name": "commission_amount",
                    "class": "text-right",
                    "data": function (row, type, set) {
                        return '<b class="text-success">' + parseFloat(row.commission_amount).toFixed(2) + '</b> ';
                    }
                },
                {
                    "data": "status",
                    "name": "status",
                    "class": "status",
                    "render": function (data, type, row, meta) {
                        if (row.status == 1) {
                            return ' <span class="label label-success">Confirmed</span>'
                        }
                        if (row.status == 2) {
                            return ' <span class="label label-warning">Pending</span>'
                        }
                        if (row.status == 3) {
                            return ' <span class="label label-primary">Waiting</span>'
                        }
                        if (row.status == 4) {
                            return ' <span class="label label-danger">Cancelled</span>'
                        }
                    }
                },
                {
                    "data": "confirmed_date",
                    "name": "confirmed_date"
                },
            
			],
            "order": [[0, 'desc']]
        });
    });	 
   
    $('#search').trigger('click');
    $("#reset_btn").click(function (event) {
        event.preventDefault();
        $('#commission-form').trigger('reset');
        $('#search').trigger('click');
    });   
}); */
