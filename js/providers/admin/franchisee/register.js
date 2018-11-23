// When the browser is ready...
var err_franchi = 1;
$(document).ready(function () {    
    $("#save_user").click(function () {
        var check = 0;
        // Setup form validation on the #create_user element
        $("#create_user").validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            // Specify the validation rules
            rules: {
                first_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 15
                },
                last_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 15
                },
                uname: {
                    required: true,
                    minlength: 6,
                    maxlength: 15
                },
                email: {
                    required: true,
                    email: true,
                },
                mobile: {
                    digits: true,
                    minlength: 3,
                    maxlength: 13,
                },
                password: {
                    required: true,
                    minlength: 5,
                    maxlength: 20
                },
                tpin: {
                    required: true,
                    minlength: 5,
                    maxlength: 20
                },
                zipcode: {
                    required: true,
                    maxlength: 8,
                    minlength: 6
                },
                currency: "required",
                country: "required",
                state: "required",
                district: "required",
                district_others: {
                    required: function (element) {
                        return ($("#district option:selected").val() == 0)
                    }
                },
                city: "required",
                city_others: {
                    required: function (element) {
                        return ($("#district option:selected").val() == 0)
                    }
                },
                fran_type: "required",
                sex: "required",
                dob: "required",
                isdeposited: "required",
                company_name: {
                    required: true,
                    minlength: 6,
                    maxlength: 50
                },
                company_address: "required",
                office_available: "required",
            },
            errorPlacement: function (error, element) {
                var name = $(element).attr("name");
                if (name == "isdeposited" || name == "office_available") {
                    error.appendTo($("#" + name + "_err"));
                }
                else {
                    error.insertAfter(element);
                }
            },
            // Specify the validation error messages
            messages: {
                first_name: {
                    required: "Please Enter First Name .",
                    minlength: "First name must be greater than 3 characters",
                    maxlength: "First name must be less than 16 characters"
                },
                last_name: {
                    required: "Please Enter Last Name .",
                    minlength: "User name must be greater than 3 characters",
                    maxlength: "Last name nust be less than 15 characters"
                },
                uname: {
                    required: "Please Enter User Name .",
                    minlength: "User name must be greater than 6 characters",
                    maxlength: "User name must be less than 16 characters"
                },
                email: {
                    required: "Please provide a valid email.",
                    email: "Please provide a valid email."
                },
                mobile: {
                    digits: "Invalid phone number ! Only numbers allowed",
                    required: "Please provide a valid Phone Number .",
                    maxlength: "Please provide a valid Phone Number",
                    minlength: "Please provide a valid Phone Number"
                },
                password: {
                    required: "Please enter the Password",
                    minlength: "Password must be greater than 5 characters",
                    maxlength: "Password must be less than 20 characters"
                },
                tpin: {
                    required: "Please enter Security Pin",
                    minlength: "Secutiry Pin must be greater than 5 characters",
                    maxlength: "Secutiry Pin be less than 20 characters"
                },
                zipcode: {
                    required: "Please enter zip code",
                    minlength: "Please Provide Valide zipcode",
                    maxlength: "Please Provide Valide zipcode"
                },
                currency: "Please select currency",
                country: "Please select country",
                state: "Please select state",
                district: "Please select district",
                city: "Please select city",
                district_others: {
                    required: "Enter the District"
                },
                city_others: {
                    required: "Enter the City"
                },
                fran_type: "Please select status",
                dob: "Please select Date of birth",
                isdeposited: "Please choose deposited or not",
                company_name: {
                    required: "Please enter company or firm name",
                    minlength: "Company or Firm name must be greater than 6 characters",
                    maxlength: "Company or Firm name must be less than 50 characters"
                },
                company_address: "Please enter company or frim address",
                office_available: "Please choose office available or not"
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                    var datastring = $(form).serialize();
                    var user_name = $('#uname').val();
                    if (confirm('Are you sure, You wants to create proceed?')) {
                        $.ajax({
                            url: $(form).attr('action'),
                            type: "POST",
                            data: datastring,
                            dataType: "json",
                            beforeSend: function () {
                                $('#save_user').val("Processing..");
                            },
                            success: function (data)
                            {
                                if (data.status == 'ok') {
                                    $('#create_user').hide();
                                    $('.box-title').html("Support Center Access");
                                    $("html, body").animate({scrollTop: 0}, "slow");
                                    $('#access_form').css('display', 'block');
                                    $('#franchisee_access #franchi_type').val(data.franchisee_type);
                                    $('#franchisee_access #user_id').val(data.user_id);
                                    $('#franchisee_access #pwd').val(data.pwd);
                                    $('#franchisee_access #tpin').val(data.tpin);
                                    $('#franchisee_access #franchi_name').html(data.type_name + ' Support Center');
                                    $('#franchisee_access #franchi_uname').html(user_name);
                                    $('#franchisee_access #country').val(data.country_id);
                                    franchisee_access_set(data.franchisee_type);
                                    $('#msg').html('<div class="alert alert-success "><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Support Center Details Successfully added.Please Give ' + user_name + ' Support Center access</div>');
                                    var franchi_type = data.franchisee_type;
                                    var franchi_name = data.type_name + ' Support Center';
                                    var relation_id = '';
                                    switch (franchi_type) {
                                        case '1':
                                            relation_id = $("#franchisee_access #country option:selected").val();
                                            break;
                                        case '2':
                                            relation_id = $("#franchisee_access #region option:selected").val();
                                            break;
                                        case '3':
                                            relation_id = $("#franchisee_access #state option:selected").val();
                                            break;
                                        case '4':
                                            relation_id = $("#franchisee_access #district option:selected").val();
                                            break;
                                        case '5':
                                            relation_id = $("#franchisee_access #city option:selected").val();
                                            break;
                                    }
                                    if (relation_id != '')
                                        check_existing_frachise_access(franchi_type, relation_id, franchi_name);
                                    $(data.access_type).css('display', 'block');
                                }
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
    });
	
});
    /* Franchisee access form validation */
    $("#submit_access").click(function () {
        var franchi_type = $('#franchisee_access #franchi_type').val();
        $("#franchisee_access").validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            // Specify the validation rules
            rules: {
                country: "required",
                region: {
                    required: function (element) {
                        return (franchi_type != '1' && franchi_type != '5' && franchi_type != '3' && franchi_type != '4')
                    }
                },
                state: {
                    required: function (element) {
                        return (franchi_type != '1' && franchi_type != '2')
                    }
                },
                district: {
                    required: function (element) {
                        return (franchi_type != '1' && franchi_type != '2' && franchi_type != '3')
                    }
                },
                city: {
                    required: function (element) {
                        return (franchi_type != '1' && franchi_type != '2' && franchi_type != '3' && franchi_type != '4')
                    }
                }
            },
            // Specify the validation error messages
            messages: {
                country: "Please select Country",
                region: "Please select Region",
                state: "Please select State",
                district: "Please select District",
                city: "Please select city"
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                    var franchi_type = $("#franchisee_access #franchi_type").val();
                    var franchi_name = $("#franchisee_access #franchi_name").text();
                    var relation_id = '';
                    switch (franchi_type) {
                        case '1':
                            relation_id = $("#franchisee_access #country option:selected").val();
                            break;
                        case '2':
                            relation_id = $("#franchisee_access #region option:selected").val();
                            break;
                        case '3':
                            relation_id = $("#franchisee_access #state option:selected").val();
                            break;
                        case '4':
                            relation_id = $("#franchisee_access #district option:selected").val();
                            break;
                        case '5':
                            relation_id = $("#franchisee_access #city option:selected").val();
                            break;
                    }
                    check_existing_frachise_access(franchi_type, relation_id, franchi_name);
                    if (!err_franchi) {
                        var datastring = $(form).serialize();
                        $.ajax({
                            url: $(form).attr('action'),
                            type: "POST",
                            data: datastring,
                            dataType: "json",
                            beforeSend: function () {
                                $('#franchisee_access #submit_access').val("Processing..");
                            },
                            success: function (data)
                            {
                                if (data.status == 'ok') {
                                    $('#msg').html('<div class="alert alert-success">' + data.msg + '</div>');
                                    $('#franchisee_access').hide();
                                }
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
    });
    $("#user_country").change(function () {
        var country_id = $("#user_country").val();
        $("#user_city").change();
        if (country_id != '') {
            $("#user_state").val('');
            $("#phonecode").val('');
            var stateOpt = "<option value=''>--Select State--</option>";
            var phoneOpt = '';
            var currencyOpt = '';
            $.post($(this).data('url'), {country_id: country_id}, function (data) {
                if (data.state_list != '' && data.state_list != null) {
                    var states = data.state_list;
                    $.each(states, function (key, elements) {
                        stateOpt += "<option value='" + elements.state_id + "'>" + elements.state + "</option>";
                    });
                }
                if (data.phone_code_list != '' && data.phone_code_list != null) {
                    var phone_code = data.phone_code_list;
                    $.each(phone_code, function (key, elements) {
                        phoneOpt += "<option value='" + elements.phonecode + "'>" + elements.phonecode + "</option>";
						currencyOpt += "<option value='" + elements.currency_id + "'>" + elements.currency_code + "</option>";
                    });
                }

                //stateOpt += "<option value='0'>Others</option>";
                $("#user_state").html(stateOpt);
                $("#phonecode").html(phoneOpt);			
                $("#currency").html(currencyOpt);				
            }, 'json');
        } else {
            $("#user_state").html("<option value=''>--Select State--</option>");
        }      
    });
	
    $("#user_state").change(function () {
        var state_id = $("#user_state").val();
        var district_id = $("#user_district").val();
        if (state_id>0) {
            $("#user_district").html('');
            var districtOpt = "<option value=''>--Select District--</option>";
            $.post($(this).data('url'), {state_id: state_id}, function (data) {
                if (data.district_list != '' && data.district_list != null) {
                    var districts = data.district_list;
                    $.each(districts, function (key, elements) {
                        districtOpt += "<option value='" + elements.district_id + "'>" + elements.district + "</option>";
                    });
                }
                districtOpt += "<option value='0'>Others</option>";
                $("#user_district").html(districtOpt);
            }, 'json');
        } else {
            $("#user_district").html("<option value=''>--Select City--</option>");
        }
      
    });

    $("#zipcode").blur(function(){
		$("#error").html('');
       var zipcode=$(this).val();
	   var district_id = $("#user_district").val();
	    if (zipcode != '' && district_id != '') {
			  var cityOpt = "<option value=''>--Select Locality--</option>";
               $.post($(this).data('url'), {district_id:district_id,zipcode: zipcode}, function (data) {
				    if (data.city_list != '' && data.city_list != null) {
                    var cities = data.city_list;
                    $.each(cities, function (key, elements) {
                        cityOpt += "<option value='" + elements.city_id + "'>" + elements.city + "</option>";
                    });
                }
			else if (data.error=='not_found')
			{
				$("#error").html('Pincode Not Found');
			}
				 cityOpt += "<option value='0'>Others</option>";
                $("#user_city").html(cityOpt); 
            }, 'json');
		}
    });
    $("#franchisee_access").on('change', "#country", function () {
        var country_id = $("#franchisee_access #country").val();
        //$('#submit_access').show();
        if (country_id != '') {
            $(".union_territory").css('display', 'none');
            $("#union_territory").html('');
            var franchi_type = $("#franchisee_access #franchi_type").val();
            $("#franchisee_access #region").val('');
            $("#franchisee_access #state").val('');
            var regionOpt = "<option value=''>--Select Region--</option>";
            var stateOpt = "<option value=''>--Select State--</option>";
            $.post($(this).data('url'), {country_id: country_id}, function (data) {
                if (data.region_list != '' && data.region_list != null) {
                    var regions = data.region_list;
                    $.each(regions, function (key, elements) {
                        regionOpt += "<option value='" + elements.region_id + "'>" + elements.region + "</option>";
                    });
                }
                if (data.state_list != '' && data.state_list != null) {
                    var states = data.state_list;
                    $.each(states, function (key, elements) {
                        stateOpt += "<option value='" + elements.state_id + "'>" + elements.state + "</option>";
                    });
                }
                $("#franchisee_access #region").html(regionOpt);
                $("#franchisee_access #state").html(stateOpt);
            }, 'json');
           
            var franchi_name = $("#franchisee_access #franchi_name").text();
            var relation_id = country_id;
            check_existing_frachise_access(franchi_type, relation_id, franchi_name);
            
         } else {
            $("#franchisee_access #region").html("<option value=''>--Select Region--</option>");
            $("#franchisee_access #state").html("<option value=''>--Select State--</option>");
        }
    });
    // district
    $("#franchisee_access").on('change', "#state", function () {
        var state_id = $("#franchisee_access #state").val();
        if (state_id != '' && state_id != null) {
            var territoryOpt = '';
            var franchi_type = $("#franchisee_access #franchi_type").val();
            $(".union_territory").css('display', 'none');
            $("#franchisee_access #union_territory").html('');
            $("#franchisee_access #district").html('');
            var districtOpt = "<option value=''>--Select District--</option>";
            $.post($(this).data('url'), {state_id: state_id}, function (data) {
                if (data.territory_list != '' && data.territory_list != null) {
                    var territory = data.territory_list;
                    $.each(territory, function (key, elements) {
                        territoryOpt += "<option value='" + elements.state_id + "' selected='selected'>" + elements.state_name + "</option>";
                    });
                }
                if (data.district_list != '' && data.district_list != null) {
                    var districts = data.district_list;
                    $.each(districts, function (key, elements) {
                        districtOpt += "<option value='" + elements.district_id + "'>" + elements.district + "</option>";
                    });
                }
                //districtOpt += "<option value='0'>Others</option>";
                //alert(territoryOpt);
                if (territoryOpt != '') {
                    $(".union_territory").css('display', 'block');
                    $("#franchisee_access #union_territory").html(territoryOpt);
                } else {
                    $(".union_territory").css('display', 'none');
                    $("#franchisee_access #union_territory").html('');
                }
                $("#franchisee_access #district").html(districtOpt);
            }, 'json');
            //if(franchi_type == 3){
            var franchi_name = $("#franchisee_access #franchi_name").text();
            var relation_id = $("#franchisee_access #state").val();
            check_existing_frachise_access(franchi_type, relation_id, franchi_name);
            //}
        } else {
            $("#franchisee_access #district").html("<option value=''>--Select City--</option>");
        }
        $("#franchisee_access #district").change();
        $("#franchisee_access #city").change();
    });
    // city
    $("#franchisee_access").on('change', "#district", function () {
        var state_id = $("#franchisee_access #state").val();
        var district_id = $("#franchisee_access #district").val();
        if (state_id != '' && district_id != '' && district_id != 0 && district_id != null) {
            var franchi_type = $("#franchisee_access #franchi_type").val();
            $("#franchisee_access #district_others").css('display', 'none');
            $("#franchisee_access #city").html('');
            var cityOpt = "<option value=''>--Select City--</option>";
            $.post($(this).data('url'),{state_id: state_id, district_id: district_id}, function (data) {
                if (district_id != '' && data.city_list != '' && data.city_list != null) {
                    var cities = data.city_list;
                    $.each(cities, function (key, elements) {
                        cityOpt += "<option value='" + elements.city_id + "'>" + elements.city + "</option>";
                    });
                }
                //cityOpt += "<option value='0'>Others</option>";
                $("#franchisee_access #city").html(cityOpt);
            }, 'json');
            //if(franchi_type == 4){
            var franchi_name = $("#franchisee_access #franchi_name").text();
            var relation_id = district_id;
            check_existing_frachise_access(franchi_type, relation_id, franchi_name);
            //}
        } else if (state_id != '' && district_id == 0) {
            $("#franchisee_access #district_others").css('display', 'block');
        } else {
            $("#franchisee_access #city").html("<option value=''>--Select City--</option>");
        }
        $("#franchisee_access #city").change();
    });
    $("#franchisee_access").on('change', "#city", function () {
        var franchi_type = $("#franchisee_access #franchi_type").val();
        if (franchi_type == 5) {
            var franchi_name = $("#franchisee_access #franchi_name").text();
            var relation_id = $("#franchisee_access #city").val();
            check_existing_frachise_access(franchi_type, relation_id, franchi_name);
        }
        if ($("#franchisee_access #city option:selected").text() == "Others") {
            $("#franchisee_access #city_others").css('display', 'block');
        } else {
            $("#franchisee_access #city_others").css('display', 'none');
        }
    });
    $("#franchisee_access").on('change', "#region", function () {
        var franchi_type = $("#franchisee_access #franchi_type").val();
        //if(franchi_type == 2){
        var franchi_name = $("#franchisee_access #franchi_name").text();
        var relation_id = $("#franchisee_access #region").val();
        check_existing_frachise_access(franchi_type, relation_id, franchi_name);
        //}
    });
    $('#email').change(function () {
        var email = $(this).val();
        $.ajax({
            dataType: "json",
            type: "post",
            data: {email: email},
            url: $(this).data('url'),
            success: function (data) {
                if (data.status == 'ok') {
                    $('#email_status').html(data.msg);
                    $('#save_user').show();
                } else if (data.status == 'error') {
                    $('#email_status').html(data.msg);
                    $('#save_user').hide();
                }
            }
        });
    });
    $('#uname').change(function () {
        var uname = $(this).val();
        $.ajax({
            dataType: "json",
            type: "post",
            data: {uname: uname},
            url: $(this).data('url'),
            success: function (data) {
                if (data.status == 'ok') {
                    $('#uname_status').html(data.msg);
                    $('#save_user').show();
                } else if (data.status == 'error') {
                    $('#uname_status').html(data.msg);
                    $('#save_user').hide();
                }
            }
        })
    });
    $(".isdeposited, #fran_type").on('change', function () {
        var package_details = $("#fran_type option:selected").data('val');
        var fran_type = $("#fran_type option:selected").val();
        if ($(".isdeposited:checked").val() == 1 && fran_type != 1) {
            if (fran_type == '') {
                alert('Choose Franchisee Type');
                return false;
            }
            $("#package_info").html(package_details);
            $("#package_view_label").css('display', 'block');
        } else {
            $("#package_view_label").css('display', 'none');
        }
    });
   /* var dt = new Date();
    dt.setFullYear(new Date().getFullYear() - 18);
    $('#dob').datepicker({
        viewMode: "years",
        endDate: dt
    }); 
});*/
function franchisee_access_set(franci_type) {
    if (franci_type == 2) {
        $('.country').css('display', 'block');
        $('.region1').css('display', 'block');
        $('.state').css('display', 'none');
        $('.district').css('display', 'none');
        $('.city').css('display', 'none');
    } else if (franci_type == 3) {
        $('.country').css('display', 'block');
        $('.region1').css('display', 'none');
        $('.state').css('display', 'block');
        $('.district').css('display', 'none');
        $('.city').css('display', 'none');
    } else if (franci_type == 4) {
        $('.country').css('display', 'block');
        $('.region1').css('display', 'none');
        $('.state').css('display', 'block');
        $('.district').css('display', 'block');
        $('.city').css('display', 'none');
    } else if (franci_type == 5) {
        $('.country').css('display', 'block');
        $('.region1').css('display', 'none');
        $('.state').css('display', 'block');
        $('.district').css('display', 'block');
        $('.city').css('display', 'block');
    } else {
        $('.country').css('display', 'block');
        $('.region1').css('display', 'none');
        $('.state').css('display', 'none');
        $('.district').css('display', 'none');
        $('.city').css('display', 'none');
    }
}
function check_existing_frachise_access(franchise_type, relation_id, franchi_name) {
    $.post('admin/franchisee/acc/check_franchise_access', {franchise_type: franchise_type, relation_id: relation_id, franchi_name: franchi_name}, function (data) {
		data.status='ok';
        if (data.status == 'ok') {
			
            err_franchi = 0;
            $('#franchisee_status').html('');
            var country_id = '';
            var state_id = '';
            var district_id = '';
            if ($("#country") !== undefined && $("#country option:selected").val() !== undefined) {
                country_id = $("#country option:selected").val();
            }
            else if ($("#state") !== undefined && $("#state option:selected").val() !== undefined)
                state_id = $("#state option:selected").val();
           else  if ($("#union_territory") !== undefined && $("#union_territory option:selected").val() !== undefined)
                union_territory = $("#union_territory option:selected").val();
            else if ($("#district") !== undefined && $("#district option:selected").val() !== undefined)
                district_id = $("#district option:selected").val();
			
            $.post('admin/franchisee/acc/check_franchise_mapped', {franchise_type: franchise_type, country_id: country_id, state_id: state_id, district_id: district_id}, function (data) {
                var franchisee_mapped_users = '';
                if (data.country_franchisee != '')
                    franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">Country Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">' + data.country_franchisee + ' </div></div></div></div>';
                if (data.region_franchisee != '')
                    franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">Regional Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">' + data.region_franchisee + ' </div></div></div></div>';
                if (data.state_franchisee != '')
                    franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">State Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">' + data.state_franchisee + ' </div></div></div></div>';
                if (data.district_franchisee != '')
                    franchisee_mapped_users += '<div class="col-lg-3"><div class="form-group fld"><label for="textfield" class="col-sm-12">District Support Center: </label> <div class="col-sm-12"><div id="franchi_typename">' + data.district_franchisee + ' </div></div></div></div>';
                if (franchisee_mapped_users === undefined)
                    franchisee_mapped_users = '';
                $("#franchisee_mapped_user").html(franchisee_mapped_users);
            });
            $('#franchisee_access #submit_access').show();
        } else if (data.status == 'error') {
            err_franchi = 1;
            $('#franchisee_status').html(data.msg);
            $('#franchisee_access #submit_access').hide();
        }
    }, 'json')
}

