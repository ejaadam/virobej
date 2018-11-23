$(document).ready(function () {

    $(document).on("click", "#theme_setting", function () {

        var photo = document.getElementById("fileUpload");
        var file = photo.files[0];
        var data = new FormData();
        var logo = $('#fileUpload').val();
        var filename = $('#fileUpload').val().replace(/C:\\fakepath\\/i, '');
        var tname = $('#theme_name').val();
        var aname = $('#app_name').val();
        var uid = $('#userid').val();
        data.append('file', file);
        data.append('t_name', tname);
        data.append('ap_name', aname);
        data.append('logo_name', filename);
        data.append('user_id', uid);
        if (logo == '') {
            $("#fileUpload").css("border", "1px solid red");
            $("#logoerr").html("{{trans('create_company.select_img_file')}}").show();
        } else if (tname == '') {
            $("#theme_name").css({'border': "1px solid red"}).attr("placeholder", "Please select theme");
            $("#themeerr").html("{{trans('create_company.select_theme_name')}}").show();
        } else if (aname == '') {
            $("#app_name").css({'border': "1px solid red"}).attr("placeholder", "Enter your App Name");
            $("#apperr").html("{{trans('create_company.enter_app_name')}}").show();
        } else {

            $.ajax({
                url: "<?php echo URL::to('admin/logo_upload'); ?>",
                type: 'POST',
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#theme_setting').html('Processing...').attr('disabled', 'disabled');
                },
                success: function (opp) {
                    if (opp.status = "ok")
                    {
                        $('#user_theme_settings').hide();
                        $("#success").html("Data Successfully Added").show();
                    }
                    else
                    {
                        alert('failure');
                    }
                },
            });
        }
    });
    $(document).on("change", "#fileUpload", function () {
//Get count of selected files
        var countFiles = $(this)[0].files.length;
        var imgPath = $(this)[0].value;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        var image_holder = $("#image-holder");
        image_holder.empty();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {

//loop for each file selected for uploaded.
                for (var i = 0; i < countFiles; i++) {

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $("<img />", {
                            "src": e.target.result,
                            "class": "thumb-image"
                        }).appendTo(image_holder);
                    }

                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[i]);
                }

            } else {
                alert("This browser does not support FileReader.");
            }
        } else {
            alert("Pls select only images");
        }
    });
    $(document).on("click", "#company_register", function () {
        $("#gamification_form_register").validate({
            rules: {
                Account_name: "required",
                acc_mail: {
                    required: true,
                    email: true
                },
                industry: "required",
                employees: {
                    required: true,
                    number: true,
                    minlength: 1
                },
                officePhone: {
                    required: true,
                    number: true,
                    minlength: 10
                },
                officeFax: {
                    required: true,
                    number: true,
                    minlength: 10
                },
                Account_website: {
                    required: true,
                    url: true
                },
                country_phone_code: "required",
                country_fax_code: "required",
                annualRevenue: "required",
                Account_type: "required",
                billing_street1: "required",
                billing_street2: "required",
                billing_city: "required",
                billing_state: "required",
                billing_code: "required",
                billing_country: "required",
                shipping_street1: "required",
                shipping_street2: "required",
                shipping_city: "required",
                shipping_state: "required",
                shipping_code: "required",
                shipping_country: "required",
                Account_description: "required"
            },
            messages: {
                Account_name: "{{trans('create_company.enter_your_gami_name')}}",
                acc_mail: "{{trans('create_company.enter_your_gami_email')}}",
                industry: "{{trans('create_company.select_industry')}}",
                employees: "{{trans('create_company.total_employee')}}",
                officePhone: "{{trans('create_company.phone_number')}}",
                officeFax: "{{trans('create_company.fax_number')}}",
                annualRevenue: "{{trans('create_company.company_revenue')}}",
                Account_type: "{{trans('create_company.acc_type')}}",
                Account_website: "{{trans('create_company.your_website')}}",
                billing_street1: "{{trans('create_company.billing_street1')}}",
                billing_street2: "{{trans('create_company.billing_street2')}}",
                billing_city: "{{trans('create_company.billing_city')}}",
                billing_state: "{{trans('create_company.billing_state')}}",
                billing_code: "{{trans('create_company.billing_code')}}",
                billing_country: "{{trans('create_company.billing_country')}}",
                shipping_street1: "{{trans('create_company.shipping_street1')}}",
                shipping_street2: "{{trans('create_company.shipping_street2')}}",
                shipping_city: "{{trans('create_company.shipping_city')}}",
                shipping_state: "{{trans('create_company.shipping_state')}}",
                shipping_code: "{{trans('create_company.shipping_code')}}",
                shipping_country: "{{trans('create_company.shipping_country')}}",
                Account_description: "{{trans('create_company.about_your_company')}}",
                country_phone_code: "{{trans('create_company.country_code')}}",
                country_fax_code: "{{trans('create_company.country_code')}}"
            },
            submitHandler: function () {
                //form.submit();
                var echeck = $('#emailcheck').val();
                var pcheck = $('#phonecheck').val();
                if ((echeck != 'error') && (pcheck != 'perror')) {
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo URL::to('admin / company_registration'); ?>",
                        data: {account_name: $('#Account_name').val(), off_phone: $('#officePhone').val(), industry: $('#industry').val(), officeFax: $('#officeFax').val(), employees: $('#employees').val(), annualRevenue: $('#annualRevenue').val(), Account_type: $('#Account_type').val(), Account_website: $('#Account_website').val(), billing_street1: $('#billing_street1').val(), billing_street2: $('#billing_street2').val(), billing_city: $('#billing_city').val(), billing_state: $('#billing_state').val(), billing_code: $('#billing_code').val(), billing_country: $('#billing_country').val(), shipping_street1: $('#shipping_street1').val(), shipping_street2: $('#shipping_street2').val(), shipping_city: $('#shipping_city').val(), shipping_state: $('#shipping_state').val(), shipping_code: $('#shipping_code').val(), shipping_country: $('#shipping_country').val(), Account_description: $('#Account_description').val(), acc_m: $('#acc_mail').val(), cfax_code: $('#country_fax_code').val(), cphone_code: $('#country_phone_code').val(), req: 'ajax'},
                        dataType: 'json',
                        error: function () {
                            $('#login_mess').before('<div id="loginsg" style="color:red">Please try later. Something went wrong</div>');
                        },
                        beforeSend: function () {
                            $('#company_register').html('Processing...').attr('disabled', 'disabled');
                            $('.errmsg_yellow').text('');
                        },
                        success: function (op) {
                            //$('#login_button').html('LOG IN').attr('disabled', false);
                            if (op.status == 'ok') {
                                $('#gamification_form_register').hide();
                                $('#login_mess').text(op.msg);
                                //window.location.href = op.url;
                            }
                        }
                    })
                }
                else {

                    if (pcheck == 'perror') {
                        $('#officePhone').focus();
                    } else {
                        $('#acc_mail').focus();
                    }
                }
            }
        })
    });
    $(document).on("change", "#checkid", function () {
        if ($(this).is(':checked')) {
            var bs1 = $('#billing_street1').val();
            $('#shipping_street1').val(bs1);
            var bs1 = $('#billing_street2').val();
            $('#shipping_street2').val(bs1);
            var bs1 = $('#billing_city').val();
            $('#shipping_city').val(bs1);
            var bs1 = $('#billing_state').val();
            $('#shipping_state').val(bs1);
            var bs1 = $('#billing_code').val();
            $('#shipping_code').val(bs1);
            var bs1 = $('#billing_country').val();
            $('#shipping_country').val(bs1);
        } else {
            $('#shipping_street1').val('');
            $('#shipping_street2').val('');
            $('#shipping_city').val('');
            $('#shipping_state').val('');
            $('#shipping_code').val('');
            $('#shipping_country').val('');
        }
    });
    $(document).on("keypress", "#officePhone", function (e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#phoneerrmsg").html("{{trans('create_company.digits_only')}}").show().fadeOut("slow");
            return false;
        }
    });
    $("#officeFax").keypress(function (e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#faxerrmsg").html("{{trans('create_company.digits_only')}}").show().fadeOut("slow");
            return false;
        }
    });
    $("#employees").keypress(function (e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#employeeserrmsg").html("{{trans('create_company.digits_only')}}").show().fadeOut("slow");
            return false;
        }
    });
    $("#annualRevenue").keypress(function (e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $("#revenueerrmsg").html("{{trans('create_company.digits_only')}}").show().fadeOut("slow");
            return false;
        }
    });
    $('input, textarea').on('keyup', function () {
        var my_txt = $(this).val();
        var len = my_txt.length;
        if (len > 0)
        {
            var valid = $(this).attr('id');
            $("#" + $(this).attr('id')).css({'border': "1px solid green"});
        }
    });
    $('select').on('change', function () {
        var my_txt = $(this).val();
        var len = my_txt.length;
        if (len > 0)
        {
            var valid = $(this).attr('id');
            $("#" + $(this).attr('id')).css({'border': "1px solid green"});
        }
    });
    $("#billing_country").change(function () {
        $country_id = $("#billing_country").val();
        $("#country_phone_code").val($("#country_phone_code #c_" + $country_id).text());
        $("#country_fax_code").val($("#country_fax_code #c_" + $country_id).text());
    });
    $("#acc_mail").blur(function () {
        user_email_check();
    });
    function user_email_check() {
        //alert('hi');
        emailcheck = 0;
        var email = $("#acc_mail").val();
        if (email && isValidEmailAddress(email)) {
            //alert('valid');
            $.ajax({
                url: "<?php echo URL::to('admin / gem_email_check'); ?>",
                type: "POST",
                data: {email: $("#acc_mail").val()},
                datatype: "application/json",
                success: function (data) {
                    data = JSON.parse(data);
                    if (data.status == 'ok') {
                        $("#email_avail_status").text(data.msg).addClass('text-success');
                        $('#emailcheck').val('');
                        emailcheck = 1;
                        return true;
                    } else if (data.status == 'error') {
                        $("#email_avail_status").text(data.msg).addClass('text-danger');
                        $('#emailcheck').val('error');
                        return false;
                    }
                },
                error: function () {
                    alert('Something went wrong');
                    return false;
                }
            });
        } else {

            $('#email_avail_status').text('Email Address Not Valid').show().addClass('text-danger');
        }
    }

    function isValidEmailAddress(emailAddress) {
        var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
        // alert( pattern.test(emailAddress) );
        return pattern.test(emailAddress);
    }


    $("#officePhone").blur(function () {
        if (checkULength($("#officePhone").val())) {
            mobileno_check();
        } else {
            $('#phoneerrmsg').text('Enter Valid Phone Number').show().addClass('text-danger');
        }
    });
    function mobileno_check() {
        //alert('hi');
        mobilecheck = 0;
        var officePhone = $("#officePhone").val();
        $("#phoneerrmsg").text('');
        if (officePhone) {
            $.ajax({
                url: "<?php echo URL::to('admin / gem_phone_check'); ?>",
                type: "POST",
                data: {off_phone: $("#officePhone").val()},
                datatype: "application/json",
                success: function (data) {
                    data = JSON.parse(data);
                    if (data.status == 'ok') {
                        //$("#mobile_avail_status").text(data.msg);
                        //$("#mobile_avail_status").removeClass("help-block");
                        //$("#user_register_submit").removeAttr('disabled');
                        mobilecheck = 1;
                        $("#phoneerrmsg").text(data.msg).addClass('text-success');
                        $('#phonecheck').val('');
                        return true;
                    } else if (data.status == 'error') {
                        $("#phoneerrmsg").text(data.msg);
                        $("#phoneerrmsg").text(data.msg).addClass('text-danger');
                        $('#phonecheck').val('perror');
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

    function checkULength(phone) {
        if (phone.length < 6) {
            return false;
        } else if (phone.length > 15) {
            return false;
        }
        return true;
    }
});
