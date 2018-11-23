$(function () {
    var t = $('#listtbl');
    var DT = t.dataTable({
        ajax: {
            url: $('#listfrm').attr('action'),
            data: function (d) {
                return $.extend({}, d, $('input,select', '#listfrm').serializeObject());
            }
        },
        columnDefs: [
            {className: 'text-center', targets: [0]},
            {className: 'text-center', targets: [1]},
            {className: 'text-center', targets: [2]},
            {className: 'text-center', targets: [3]},
            {className: 'text-center', targets: [4]},
            {className: 'text-center', targets: [5]}
        ],
        columns: [
            {
                name: 'created_on',
                data: 'created_on',
                class: 'text-center'
            },
            {
                name: 'company_name',
                data: 'company_name',                
            },
            {
                name: 'profit_sharing',
                data: 'profit_sharing',
                class: 'text-right',
            },
            {
                name: 'status',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.status_class + '">' + row.status + '</span>';
                }
            },
            {
                data: 'updated_on',
                name: 'updated_on'
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);
                }
            },
        ],
        responsive: {
            /* details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return data.uname;
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }  */
        }
    });
    t.on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (data) {
            if (data.edit != undefined) {
                console.log(data);				
                $('#profit-sharing-form').resetForm();
                $('#form-panel').show();
                $('#list-panel').hide();
                $('#profit-sharing-form').attr('action', data.edit.details.action.url);
                $('#profit-sharing-form input[type=submit]').val(data.edit.details.action.label);
                $('#profit-sharing-form .profit_sharing_in_label').text(data.edit.details['profit_sharing_in_label']);
                $('#form-panel #supplier_name').text(data.edit.details['company_name']);
                $('#form-panel #supplier_code').text(data.edit.details['supplier_code']);
                $('#form-panel #store_name').text(data.edit.details['store_name']);
                $('#form-panel #store_code').text(data.edit.details['store_code']);
                $('#form-panel #mobile').text(data.edit.details['mobile']);
                $('#form-panel #email').text(data.edit.details['email']);
                $('#form-panel #contact').text(data.edit.details['uname']);
                $('#form-panel #formated_address').text(data.edit.details['address']);
                $('#profit-sharing-form #profit_sharing').text(data.edit.details['profit_sharing']);
                $('#profit-sharing-form #cashback_on_pay').val(data.edit.details['cashback_on_pay']);
                $('#profit-sharing-form #cashback_on_redeem').val(data.edit.details['cashback_on_redeem']);
                $('#profit-sharing-form #cashback_on_shop_and_earn').val(data.edit.details['cashback_on_shop_and_earn']);
            }
            else if (data.view != undefined) {
                console.log(data);
                $('#profit-sharing-form').resetForm();
                $('#form-panel').hide();
                $('#list-panel').hide();
                $('#view-panel').show();

                $('#view-panel #supplier_name').text(data.view.details['company_name']);
                $('#view-panel #supplier_code').text(data.view.details['supplier_code']);
                $('#view-panel #store_name').text(data.view.details['store_name']);
                $('#view-panel #store_code').text(data.view.details['store_code']);
                $('#view-panel #formated_address').text(data.view.details['address']);
                $('#view-panel #contact').text(data.view.details['uname']);
                $('#view-panel #mobile').text(data.view.details['mobile']);
                $('#view-panel #email').text(data.view.details['email']);

                if (data.view.new_request) {
                    $('#view-panel #profit_sharing').text(data.view.new_request['profit_sharing']);
                    $('#view-panel #cashback_on_pay').text(data.view.new_request['cashback_on_pay'] + '%');
                    $('#view-panel #cashback_on_redeem').text(data.view.new_request['cashback_on_redeem'] + '%');
                    $('#view-panel #cashback_on_shop_and_earn').text(data.view.new_request['cashback_on_shop_and_earn'] + '%');
                } else {
                    $('#new_request').hide();
                }

                if (data.view.current_details) {
                    $('#view-panel #current #profit_sharing').text(data.view.current_details['profit_sharing']);
                    $('#view-panel #cashback_on_pay').text(data.view.current_details['cashback_on_pay'] + '%');
                    $('#view-panel #cashback_on_redeem').text(data.view.current_details['cashback_on_redeem'] + '%');
                    $('#view-panel #cashback_on_shop_and_earn').text(data.view.current_details['cashback_on_shop_and_earn'] + '%');

                    if (parseInt(data.view.details['pay']) == 1) {
                        $('#accept_payment').prop('checked', data.view.details['pay']);
                    }
                    if (parseInt(data.view.details['offer_cashback']) == 1) {
                        $('#offer_cashback').prop('checked', data.view.details['offer_cashback']);
                    }

                } else {
                    $('#current').hide();
                }
            }
            else {
                DT.fnDraw();
            }
        });
    });
    $('#listfrm').submit(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#profit-sharing-form').submit(function (e) {
        e.preventDefault();
        CURFORM = $('#profit-sharing-form');
        $.ajax({
            url: $('#profit-sharing-form').attr('action'),
            data: $('#profit-sharing-form').serializeObject(),
            success: function (data) {
                $('#form-panel').hide();
                $('#list-panel').show();
                DT.fnDraw();
            }
        });
    });
    $('#searchbtn').click(function (e) {
        DT.fnDraw();
    });
    $('#resetbtn').click(function (e) {
        $('input,select,input:checkbox', $(this).closest('form')).val('');
        $('input:checkbox').removeAttr('checked');
        DT.fnDraw();
    });
    $('#form-panel,#view-panel').on('click', '.close-btn', function (e) {
        e.preventDefault();
        $('#view-panel,#form-panel').hide();
        $('#list-panel').show();
    });
});
