$(function () {

    var t = $('#hist_table');
    var DT = t.dataTable({
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
        "sDom": "t" + "<'col-sm-6 bottom info 'li>r<'col-sm-6 info bottom text-right'p>",
        "ajax": {
            url: $('#form').attr('action'),
            type: 'POST',
            data: function (d) {
                return $.extend({}, d, $('input,select', '#form').serializeObject());
            },
        },
        "columns": [
            {
                "data": "created_on",
                "name": "created_on",
                "class": "text-center",
                "render": function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat("dd-mmm-yyyy H:M:s");
                }
            },
            {
                "data": "transaction_id",
                "name": "transaction_id",
                "class": "text-right"

            },
            {
                "data": "trans_from",
                "name": "trans_from",
                "class": "text-left",
				"render":function (data, type, row, meta){
					return new String(row.trans_from+'('+row.funame+')'+'<br><span class="small text-muted">'+row.from_acc_roll+'</span>');
				}
            },
            {
                "data": "trans_to",
                "name": "trans_to",
                "class": "text-left",
				"render":function (data, type, row, meta){
					return new String(row.trans_to+'('+row.tuname+')'+'<br><span class="small text-muted">'+row.to_acc_roll+'</span>');
				}
            },
            /* {
             "data": "rating",
             "name": "rating",
             "class": "text-left",
             "render": function (data, type, row, meta) {
             var comments = 'Rating: '+ row.rating+'<p>'+row.feedback+'</p>';
             return new String(comments);
             }
             }, */
            {
                "data": "wallet_name",
                "name": "wallet_name",
                "class": "text-left",
            },
            /*   {
             "data": "to_wallet_name",
             "name": "to_wallet_name",
             "class": "text-left",
             }, */
            {
                "data": "amount",
                "name": "amount",
                "class": "text-right",
            },
            {
                "data": "handleamt",
                "name": "handleamt",
                "class": "text-right",
            },
            {
                "data": "paidamt",
                "name": "paidamt",
                "class": "text-right",
            },
            /* {
             "data": "added_by",
             "name": "added_by",
             "class": "text-right",
             }, */
            {
                "data": "status_id",
                "name": "status_id",
                "class": "text-center",
                "render": function (data, type, row, meta) {
                    status = '<span class="label label-' + row.statusCls + '">' + row.status + '</span>';
                    return new String(status);
                }
            },
            /*  {
             "class": "text-center",
             "orderable": false,
             "render": function (data, type, row, meta) {
             var json = $.parseJSON(meta.settings.jqXHR.responseText);
             var url = $('#action_url').val();
             var json = $.parseJSON(meta.settings.jqXHR.responseText);
             var action_buttons = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear" aria-hidden="true"></i> <span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right text-left" role="menu">';

             if (row.status_id != 1)
             {
             action_buttons = action_buttons+'<li><a href="' + json.url + '/reviews/change_review_status" class="change_status text-left" data-status="1" rel="'+row.ft_id+'">Confirm</a></li>';
             }
             if(row.status_id != 2){
             action_buttons = action_buttons+'<li><a href="' + json.url + '/reviews/change_review_status" class="change_status text-left" data-status="2" rel="'+row.ft_id+'">pending</a></li>';
             }
             action_buttons = action_buttons + '</ul></div>';
             return action_buttons;
             }
             } */
        ],
    });
    $('#form').on('submit', function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#searchbtn').click(function (e) {
        DT.fnDraw();
    });
    $('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        DT.fnDraw();
    });
    $('#review_table').on('click', '.change_status', function (e) {
        e.preventDefault();
        curLine = $(this);
        $.ajax({
            url: curLine.attr('href'),
            data: {id: curLine.attr('rel'), status: curLine.attr('data-status')},
            type: "POST",
            dataType: 'JSON',
            success: function (res) {
                if (res.status == 'ok')
                {
                    curLine.closest('tr').hide();
                    DT.fnDraw();
                    $('#status_msg').html('<div class="alert alert-success">' + res.contents + '</div>').fadeOut(9000);
                } else {
                    $('#status_msg').html('<div class="alert alert-success">' + res.contents + '</div>').fadeOut(9000);
                }
            },
            error: function () {
                alert('Something went wrong');
                return false;
            }
        });
    });

});
