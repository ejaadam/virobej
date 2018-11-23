$(function () {
    var t = $('#log_table');
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
        "bStateSave": true,
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
                "render": function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat("dd-mmm-yyyy H:M:s");
                }
            },
            {
                "data": "id",
                "name": "id",
                "render": function (data, type, row, meta) {
                    return new String('#' + row.id);
                }
            },
            {
                "data": "fullname",
                "name": "fullname",
                "class": "text-left",
                "render": function (data, type, row, meta) {
                    return new String('<strong>' + row.fullname + '</strong>') + '<br> (' + row.uname + ')';
                }
            },
            /* {
             "data": "remark",
             "name": "remark",
             "class": "text-left",
             "render": function (data, type, row, meta) {
             console.log(row);
             var description = row.statementline + ' (' + row.remark + ')</br>#' + row.transaction_id + '<br/><b>Wallet</b>: ' + row.wallet;
             return new String(description);
             }

             }, */

            {
                "data": "payment_type_name",
                "name": "payment_type_name",
            },
            {
                "data": "amount",
                "name": "amount",
                "class": "text-right trans",
            },
            {
                "data": "purpose",
                "name": "purpose",
                /* "render": function (data, type, row, meta) {
                 if((row.order_code != null) && (row.order_code !=undefined) && (row.purpose != null) && (row.purpose !=undefined))
                 return status = row.purpose+' ('+row.order_code+')';
                 else
                 //return order_code = '-- ';
                 return status = row.purpose;

                 } */
            },
            {
                "data": "payment_status",
                "name": "payment_status",
                "class": "text-center",
                "render": function (data, type, row, meta) {
                    payment_status = '<span class="label label-' + row.payment_statusCls + '">' + row.payment_statusLbl + '</span>';
                    return new String(payment_status);
                }
            },
            {
                "data": "status",
                "name": "status",
                "class": "text-center",
                "render": function (data, type, row, meta) {
                    status = '<span class="label label-' + row.statusCls + '">' + row.statusLbl + '</span>';
                    return new String(status);
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.action, true);
                }
            },
        ],
        drawCallback: function (settings) {
            if (settings.json.filters !== undefined) {
                if (settings.json.filters.country) {
                    $('#country', MERCHANT.LIST.FORM).addOptions(settings.json.filters.country, null, true);
                }
                if (settings.json.filters.category) {
                    $('#category', MERCHANT.LIST.FORM).addOptions(settings.json.filters.category, null, true);
                }
            }
        },
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
    $('#xbp-content').on('click', '#back', function (e) {
        e.preventDefault();
        $('#list').css('display', 'block');
        $('#details').css('display', 'none');
    })
    $('#log_table').on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (op) {
            if (op.content != undefined && op.content != null) {
                $('#details').html(op.content);
                $('#list').css('display', 'none');
                $('#details').css('display', 'block');
            }
            else if (op.details != undefined && op.details != null) {
                $('#merchant-list').pageSwapTo('#merchant-form-panel');
                CODE = op.details.code;
                $.each(op.details, function (k, v) {
                    if (k != 'mrlogo') {
                        $('#' + k, '#merchant-form').val(v);
                        if (k == 'mr_postcode' || k == 'acc_postcode') {
                            $('#' + k, '#merchant-form').trigger('change');
                        }
                    }
                });
                checkPincodeMR = function () {
                    $('#mr_city_id', '#merchant-form').val(op.details.mr_city_id);
                }
                checkPincodeACC = function () {
                    $('#acc_city_id', '#merchant-form').val(op.details.acc_city_id);
                }
                $('#mrlogo-preview').attr('src', op.details.logo);
                $('#email,#mobile,#uname', '#merchant-form').attr({readonly: true});
            }
            else {
                DT.fnDraw();
            }
        });
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
