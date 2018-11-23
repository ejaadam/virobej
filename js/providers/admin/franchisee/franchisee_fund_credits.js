function checkformat(ele, doctypes, str) {
    txtext = ele;
    txtext = txtext.toLowerCase();
    fformats = doctypes.split('|');
    if (fformats.indexOf(txtext) == -1) {
        return false;
    } else {
        return true;
    }
}
$(document).ready(function () {
    $("#payment_confirm_form").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            pg_id: {
                required: true
            }
        },
        messages: {
            pg_id: {
                required: "Please enter payment gateway transaction id"
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                $("#addfund_payment_confirm_submit").attr('disabled', 'disabled');
                $("#addfund_payment_confirm_submit").val('Processing....');
                $.post($(form).attr('action'), $(form).serialize(), function (data) {
                    if (data.status == "ok") {
                        $("#addfund_payment_confirm .modal-body").html(data.msg);
                        setTimeout(function () {
                            $("#addfund_payment_confirm").modal('hide');
                            $('#search').trigger('click');
                            window.location.reload(true);
                        }, 2000);
                    } else {
                        alert(data.msg);
                        return false;
                    }
                    $("#addfund_payment_confirm_submit").removeAttr('disabled', 'disabled');
                }, 'json');
            }
        }
    });
    $("#payment_refund_form").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            remarks: {
                required: true
            }
        },
        messages: {
            remarks: {
                required: "Please enter reason to refund"
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                $("#addfund_payment_confirm_submit").attr('disabled', 'disabled');
                $("#addfund_payment_confirm_submit").val('Processing....');
                $.post($(form).attr('action'), $(form).serialize(), function (data) {
                    if (data.status == "ok") {
                        $("#addfund_payment_refund .modal-body").html(data.msg);
                        setTimeout(function () {
                            $("#addfund_payment_refund").modal('hide');
                            $('#search').trigger('click');
                            window.location.reload(true);
                        }, 2000);
                    } else {
                        alert(data.msg);
                        return false;
                    }
                    $("#addfund_payment_confirm_submit").removeAttr('disabled', 'disabled');
                }, 'json');
            }
        }
    });
    $("#addfund_verify_form").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            remarks: {
                required: true
            },
            verification_status: {
                required: true
            }
        },
        messages: {
            remarks: {
                required: "Please enter remarks"
            },
            verification_status: {
                required: "Please choose verification status"
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                $("#addfund_verify_submit").attr('disabled', 'disabled');
                $("#addfund_verify_submit").val('Processing....');
                $.post($(form).attr('action'), $(form).serialize(), function (data) {
                    if (data.status == "ok") {
                        $("#addfund_verify .modal-body #addfund_verify_form").before('<div class = "verify_msg">' + data.msg + '</div>');
                        $("#addfund_verify_form").hide();
                        setTimeout(function () {
                            $("#addfund_verify").modal('hide');
                            var relation_id = $("#relation_id").val();
                            $("#verification_status_" + relation_id + " .verify").attr('data-verification_status_id', data.verification_status_id);
                            $("#verification_status_" + relation_id + " .verify").hide();
                            $("#verification_status_" + relation_id + " .status_col").html(data.verification_status_label);
                        }, 2000);
                    } else {
                        alert(data.msg);
                        return false;
                    }
                    $("#addfund_verify_submit").removeAttr('disabled', 'disabled');
                }, 'json');
            }
        }
    });
    $("select[name='statusupdate']").change(function (e) {
        e.preventDefault();
        var cnt = $('tbody td input[type=checkbox]:checked').length;
        if (cnt > 0) {
            $('#order_statusupdate').val($(this).val());
            $('#orderFrm').submit();
        }
        else {
            alert("Records not selected");
        }
        return false;
    });
    $('#user_addfund_table').on('click', '.payment_confirm', function (e) {
        e.preventDefault();
        var uaf_id = $(this).data("userfund_id");
        $("#addfund_payment_confirm").modal();
        $("#uaf_id").val(uaf_id);
        $("#addfund_payment_confirm_submit").removeAttr('disabled');
        $("#addfund_payment_confirm_submit").val('Make Payment');
        $(".remarks").val('');
        $("#pg_id").val('');
    });
    $('#user_addfund_table').on('click', '.payment_refund', function (e) {
        e.preventDefault();
        var uaf_id = $(this).data("userfund_id");
        var update_status = $(this).data("status");
        $("#addfund_payment_refund").modal();
        $("#uaf_id1").val(uaf_id);
        $("#update_status1").val(update_status);
        $("#addfund_payment_refund_submit").removeAttr('disabled');
        $("#addfund_payment_refund_submit").val('Refund Payment');
        $(".remarks").val('');
    });
    $('#orderFrm').submit(function (e) {
        e.preventDefault();
        $('.msgSuccess').remove();
        var status = $('option:selected', $('#statusupdate')).text();
        if (confirm('Are you sure, You wants to move to ' + status + '?')) {
            $.post($(this).attr('action'), $(this).serialize(), function (data) {
                if (data.status == 'OK') {
                    $('#example_wrapper').before(unescape(data.msg));
                    location.reload(true);
                }
            }, 'json');
        }
    });
    $('#check_all').on('ifChecked', function (event) {
        $('.check').iCheck('check');
    });
    $('#check_all').on('ifUnchecked', function (event) {
        $('.check').iCheck('uncheck');
    });
    $('#check_all').on('ifChanged', function (event) {
        if (!this.changed) {
            this.changed = true;
            $('#check_all').iCheck('check');
        } else {
            this.changed = false;
            $('#check_all').iCheck('uncheck');
        }
        $('#check_all').iCheck('update');
    });
    $('.search-panel .dropdown-menu').find('a').click(function (e) {
        e.preventDefault();
        var param = $(this).data('value');
        var concept = $(this).text();
        $('.search-panel span#search_concept').text(concept);
        $('.input-group #search_param').val(param);
    });
    $('#search').click(function () {
        $('#user_addfund_table').dataTable({
            "bPaginate": true,
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
                    "uname": $('#uname', $('#user_add_fund')).val(),
                    "from": $('#from', $('#user_add_fund')).val(),
                    "to": $('#to', $('#user_add_fund')).val(),
                    "payment_type": $('#payment_type', $('#user_add_fund')).val(),
                    "payment_status": $('#payment_status', $('#user_add_fund')).val(),
                    "status": $('#status', $('#user_add_fund')).val(),
                    "search_param": $('#search_param', $('#user_add_fund')).val(),
                    "transaction_id": $('#transaction_id', $('#user_add_fund')).val(),
                    "currency_id": $('#currency_id', $('#user_add_fund')).val(),
                    "search": $('#search', $('#user_add_fund')).val()
                }
            },
            "columns": [
                {
                    "data": "requested_date",
                    "name": "requested_date",
                    "class": "text-center",
                    "render": function (data, type, row, meta) {
                        return new String(row.requested_date).dateFormat("dd-mmm-yyyy HH:mm:ss");
                    }
                },
                {
                    "data": "transaction_id",
                    "name": "transaction_id",
                    "render": function (data, type, row, meta) {
                        var pay_types = row.payout_types;
                        pay_types = pay_types.replace('Direct', '').replace('Transfer', '').replace('Wallet', '').replace('Credit/Debit Card', '').replace('/Net Banking', '').replace('-', '').replace(/\s/g, "");
                        pay_types = pay_types.toLowerCase();
                        pay_type_var = pay_types + "_trans_id";
                        pay_type_var = pay_type_var.replace(/\s/g, "");
                        var transaction = row.transaction_id + '<br />';
                        if (eval("row." + pay_type_var) != "Not-Set" && eval("row." + pay_type_var) != null) {
                            transaction += 'PG : ';
                            if (pay_types == 'paypal') {
                                transaction += 'AF' + row.transaction_id;
                            }
                            else {
                                if (eval("row." + pay_type_var) != '')
                                    transaction += eval("row." + pay_type_var);
                            }
                        }
                        return new String('<b>' + transaction + '</b><br />User Name: ' + row.uname + '<br />Full Name: ' + row.full_name + '<br />Country: ' + row.country);
                    }
                },
                {
                    "data": "amount",
                    "name": "amount",
                    "render": function (data, type, row, meta) {
                        var amount = parseFloat(row.amount);
                        var decimal_places = get_decimal_value(amount)
                        return new String(row.currency + ' ' + parseFloat(amount).toFixed(decimal_places));
                    }
                },
                {
                    "data": "payout_types",
                    "name": "payout_types",
                    "render": function (data, type, row, meta) {
                        var payout_types = row.payout_types.replace('Direct', '').replace('Transfer', '');
                        return new String(payout_types);
                    }
                },
                {
                    "data": "payment_status",
                    "name": "payment_status",
                    "render": function (data, type, row, meta) {
                        var payment_status_arr = new Array('<span class="label label-info">Initial</span>', '<span class="label label-success">Confirmed</span>', '<span class="label label-danger">Failed</span>', '<span class="label label-danger">Cancelled</span>', '<span class="label label-warning">Pending</span>');
                        return new String(payment_status_arr[row.payment_status]);
                    }
                },
                {
                    "data": "status",
                    "name": "status",
                    "render": function (data, type, row, meta) {
                        var status_arr = new Array('<span class="label label-warning">Pending</span>', '<span class="label label-success">Confirmed</span>', '<span class="label label-warning">Processing</span>', '<span class="label label-danger">Cancelled</span>', '<span class="label label-danger">Failed</span>', '<span class="label label-danger">Refund</span>');
                        var status_var = '-';
                        if (row.payment_status != 0) {
                            status_var = status_arr[row.status];
                        }
                        status_var = new String('<div class ="status_col">' + status_var + '</div><br />');
                        return status_var;
                    }
                },
                {
                    "data": "verification_status",
                    "name": "verification_status",
                    "render": function (data, type, row, meta) {
                        var status_var = '-';
                        status_var = new String('<div class ="status_col"><span class="label ' + row.verification_label_cls + '">' + row.verification_status + '</span></div>');
                        var json = $.parseJSON(meta.settings.jqXHR.responseText);
                        var status_url = $('#action_url').val();
                        var json = $.parseJSON(meta.settings.jqXHR.responseText);
                        var action_buttons = '';
                        if (row.verification_status_id == 2) {
                            action_buttons = "<p><a href='#' class = 'verify' data-relation_id = '" + row.uaf_id + "' data-verification_status_id = '" + row.verification_status_id + "' data-purpose = '" + row.purpose + "' >Verify</a></p>";
                        }
                        var remarks = '';
                        if (row.remarks !== null)
                            remarks = '<p>Remarks : ' + row.remarks + '</p>';
                        return '<div id ="verification_status_' + row.uaf_id + '">' + status_var + action_buttons + remarks + '</div>';
                    }
                },
                {
                    "data": "updated_date",
                    "name": "updated_date",
                    "render": function (data, type, row, meta) {
                        var update_date;
                        if (row.status == 0) {
                            if (row.released_date != null)
                                update_date = row.released_date;
                            else
                                update_date = row.requested_date;
                        } else if (row.status == 1) {
                            update_date = row.approved_date;
                        } else if (row.status == 3) {
                            update_date = row.cancelled_date;
                        } else if (row.status == 5) {
                            update_date = row.refund_date;
                        }
                        else {
                            update_date = row.updated_date;
                        }
                        return new String(update_date).dateFormat("dd-mmm-yyyy HH:mm:ss");
                    }
                },
                {
                    "class": "text-center",
                    "orderable": false,
                    "render": function (data, type, row, meta) {
                        var json = $.parseJSON(meta.settings.jqXHR.responseText);
                        var status_url = $('#action_url').val();
                        var json = $.parseJSON(meta.settings.jqXHR.responseText);
                        var action_buttons = '';
                        var action_buttons2 = '';
                        if (row.status != 1 && row.status != 5 && (row.payment_status == 0 || row.payment_status == 2 || row.payment_status == 1)) {
                            if ((row.payment_status == 0 || row.payment_status == 2) && (row.verification_status_id != 5 && row.verification_status_id != 6)) {
                                action_buttons2 += "<li><a href='#' data-status='1' class='payment_confirm' data-userfund_id='" + row.uaf_id + "'>Paid</a></li>";
                            }
                            if (row.payment_status == 1 && row.status != 1 && row.status != 5) {
                                //action_buttons += "<li><a href='"+ json.url + "/admin/change_franchisee_fund_status' data-status='2' class='status_btn' data-userfund_id='"+row.uaf_id+"'>Processing</a></li>";
                                action_buttons2 += "<li><a href='" + json.url + "/admin/change_franchisee_fund_status' data-status='1' class='status_btn' data-userfund_id='" + row.uaf_id + "'>Confirmed</a></li>";
                                action_buttons2 += "<li><a href='#' data-status='5' class='payment_refund' data-userfund_id='" + row.uaf_id + "'>Refunded</a></li>";
                            }
                            if (action_buttons2 != '') {
                                action_buttons = '<div class="btn-group action_col"><button type="button"  class="btn btn-xs btn-primary details">Action</button><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                                action_buttons = action_buttons + ' ' + action_buttons2 + '</ul></div>';
                            }
                        }
                        return action_buttons;
                    }
                },
            ],
            "order": [[0, 'desc']]
        });
    });
    $('#search').trigger('click');
    $('#user_addfund_table').on('click', '.verify', function (e) {
        e.preventDefault();
        var uaf_id = $(this).data("relation_id");
        var purpose = $(this).data("purpose");
        var verification_status_id = $(this).data("verification_status_id");
        $("#addfund_verify").modal();
        $("#addfund_verify_form").show();
        $(".verify_msg").remove();
        $("#relation_id").val(uaf_id);
        $("#purpose").val(purpose);
        $("select option[value=" + verification_status_id + "]").css('display', 'none');
        $("#addfund_verify_submit").removeAttr('disabled');
        $("#addfund_verify_submit").val('Verify Payment');
        $(".remarks").val('');
        $("#verification_status").val('');
    });
    $('#user_addfund_table').on('click', '.status_btn', function (event) {
        event.preventDefault();
        $('.alert').remove();
        var curLink = $(this);
        var status_msg = "pending";
        if ($(curLink).data('status') == 1) {
            status_msg = "confirm"
        } else if ($(curLink).data('status') == 3) {
            status_msg = "cancel"
        }
        if (confirm("Do you want to " + status_msg + " this user fund request ?")) {
            $.ajax({
                url: $(curLink).attr('href'),
                type: "POST",
                dataType: "json",
                data: {order_statusupdate: $(curLink).data('status'), confirm_list: $(curLink).data('userfund_id')},
                async: true,
                beforeSend: function () {
                    $("#user_fund_details .modal-title").html('Details');
                    $("#user_fund_details .modal-body").html('<div class="text-center">Loading in progress...</div>');
                    $("#user_fund_details").modal();
                },
                success: function (data) {
                    $('#parent_panel').prepend(data.msg);
                    if (data.status == 'OK') {
                        /* $(curLink).parents('tr').children('.status_col').html(data.span);
                         $(curLink).parents('ul').remove();*/
                        /*$(curLink).parents('td').children('.status_col').html('<span class="label label-success">Confirmed</span>');
                         $(curLink).parents('td').children('.action_col').remove();
                         $(curLink).parents('td').children('.check_box').remove();*/
                        $("#user_fund_details .modal-body").html("Fund " + data.content + " successfully");
                        $('#status_' + $(curLink).attr('data-userfund_id')).text(data.content);
                        setTimeout(function () {
                            $("#user_fund_details").modal('hide');
                            $('#search').trigger('click');
                            // window.location.reload(true);
                        }, 2000);
                    }
                },
                error: function () {
                    alert('Something went wrong');
                }
            });
        }
    });
});
