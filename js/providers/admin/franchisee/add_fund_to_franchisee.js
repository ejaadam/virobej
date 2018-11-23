$(function () {
    $("#add_funds").validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            username: {required: true},
            amount: {required: true, number: true},
            ewallet_id: {required: true},
            currency_id: {required: true},
            comment: {required: true,
                minlength: 8,
                maxlength: 100}
        },
        messages: {
            username: "Please enter User name",
            amount: {required: "Please Enter the Amount", digits: "Please Enter only Numeric values"},
            ewallet_id: {required: "Please Select the EWallet"},
            currency_id: {required: "Please Select the Currency"},
            comment: {required: "Please Give Admin Comments",
                minlength: "Comment length must be minimum 8 character and maximum 100",
                maxlength: "Comment length must be minimum 8 character and maximum 100"}
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                if (confirm('Are you sure, You wants to add fund?')) {
                    //$('input[type="submit"]', $(form)).val('Processing...').attr('disabled', true);
                    $.ajax({
                        url: $(form).attr('action'),
                        type: "POST",
                        data: $(form).serialize(),
                        dataType: "json",
                        success: function (data) {
                            var str = '';
                            if (data.status == 'ok') {
                                str = "<div class='alert alert-success'>" + data.msg + "<br></div>";
                            } else {
                                str = "<div class='alert alert-danger'>" + data.msg + "<br></div>";
                            }
                            $('#add_funds').before(str);
                            $("#add_funds").trigger('reset');
                            $("#user_details").hide();
                            $('#user_avail_status').html('');
                            $("#add_details").hide();
                            $('#uname_check_add').parents('.form-group').show();
                            $('input[type="submit"]', $(form)).val('Submit').removeAttr('disabled');
                        },
                        error: function () {
                            alert('Something went wrong');
                            return false;
                        }
                    });
                }
            }
        }
    });
    $("#username_add").keyup(function () {
        $(".alert").remove();
        $("#uname_check_add").parents('.form-group').show();
        $("#add_details").hide();
        $("#user_details").hide();
        $("#user_avail_status").hide();
    });
    /*------Username Checking Function--------*/
    $("#uname_check_add").click(function () {
        if ($("#username_add").val() == '') {
            $("#user_avail_status").html('Please Enter the username').addClass("help-block");

        } else {
            $("#username_add").empty();
            $("#user_details").hide();
            var username = $("#username_add").val();
            if (username) {
                $("#uname_check_add").val('Processing..').attr("disabled", "disabled");
                $.ajax({
                    url: $('#uname_check_add').data('url'),
                    type: "POST",
                    data: {username: $("#username_add").val()},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == 'notok') {
                            $("#user_id").val(data.user_id);
                            $("#add_details").show();
                            $("#user_details").hide();
                            $("#uname_check_add").parents('.form-group').hide();
                            $("#user_avail_status").html('').hide().addClass("help-block");
                            $("#Submit").removeAttr("disabled");
                            return true;
                        } else if (data.status == 'ok') {
                            $("#uname_check_add").parents('.form-group').hide();
                            $("#add_details").show();
                            $("#user_details").show();
                            $("#rec_email").val(data.email);
                            $("#rec_name").val(data.fullname);
                            $("#franchisee_type").val(data.franchisee_type);
                            $("#franchisee_type_name").val(data.franchisee_type_name);
                            $("#wallet_purchase_per").val(data.wallet_purchase_per);
                            $("#user_avail_status").html(data.msg).show().removeClass("help-block");
                            $("#Submit").removeAttr("disabled");
                            $("#user_id").val(data.user_id);
                            $("#uname_check_add").val('Check').removeAttr("disabled");
                            return true;
                        } else if (data.status == 'error') {
                            $("#user_avail_status").html(data.msg).show().addClass("help-block");
                            $("#Submit").attr("disabled", "disabled");
                            $("#uname_check_add").val('Check').removeAttr("disabled");
                            return false;
                        }
                    },
                    error: function () {
                        alert('Something went wrong');
                        return false;
                    }
                });

            }
        }
    });
    $('#amount').keyup(function () {
        var CurEle = $(this);
        var amount = parseFloat(CurEle.val());
        if (amount > 0) {
            var per = parseFloat($('#wallet_purchase_per').val());
            $('#net_amount').val(amount + ((amount / 100) * per)).parents('.form-group').show();
        }
        else {
            $('#net_amount').parents('.form-group').hide();
        }
    });
});
