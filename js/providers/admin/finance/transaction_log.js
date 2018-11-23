$(function () {
    var t = $('#log_table');
    var DT = t.dataTable({
        ajax: {
            url: $('#form').attr('action'),
            data: function (d) {
                return $.extend({}, d, $('input,select', '#form').serializeObject());
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                width: '10%',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy H:M:s');
                }
            },
            {
                data: 'fullname',
                name: 'fullname',
                class: 'text-left'

            },
            {
                data: 'remark',
                name: 'remark',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    return row.statementline + ' (' + row.remark + ')</br><span class="text-muted"><b>Wallet</b>: ' + row.wallet + '</span><span class="text-muted"><b>Trans.ID</b>: #' + row.transaction_id + '</span>';
                }

            },
            {
                data: 'famount',
                name: 'amount',
                class: 'text-right trans'
            },
            {
                data: 'fcurrent_balance',
                name: 'current_balance',
                class: 'text-right'
            },
            {
                data: 'status_id',
                name: 'status_id',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return '<span class="label label-' + row.statusCls + '">' + row.status + '</span>';
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
        drawCallback: function () {
            $('td.trans:contains(' - ')').addClass('text-danger');
        }
    });
    t.on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (op) {
            if (op.details != undefined && op.details != null) {
                $('#transactions-details table').empty();
                $.each(op.details, function (k, e) {
                    $('#transactions-details table').append($('<tr>').append([$('<th>').append(e.label), $('<td>').append(e.value)]));
                });
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
            type: 'POST',
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
