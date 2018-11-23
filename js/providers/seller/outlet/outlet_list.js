$(document).ready(function () {
	var treeList = '';
    var STORE = {}, CODE = null, checkPincodeACC = null, checkPincodeMR = null;
    STORE.LIST = {};
	
	STORE.LIST.TABLE = $('#store-list-table');
    STORE.LIST.FORM = $('#store-list-form');
    STORE.FORM = $('#store-form');
	
	var DT = STORE.LIST.TABLE.dataTable({
        ajax: {
            url: $('#store-list-form').attr('action'),
            data: function (d) {
                d.category_id = $('#category_id').val();
                d.search_term = $('#search_term').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                //d.status = $('#status').val();
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                class: 'text-center',
            },
            {
                name: 'logo',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<img class="img img-responsive img-thumbnail" src="' + row.logo + '" alt="' + row.store_name + '"/> ';
                }
            },
            {
                name: 'store_name',
                data: 'store_name',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    var str = '';
                    str = '<b>' + row.store_name + '</b>' + ' (#' + row.store_code + ')<br><i class="fa fa-list-alt margin-r-5"></i>' + row.category;
                    return str;
                }
            },
            {
                name: 'country',
                data: 'country',
                class: 'text-left',
            },
            {
                name: 'status_code',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.status_class + '">' + row.status_code + '</span> ';
                }
            },
            {
                name: 'is_approved',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.is_approved_class + '">' + row.is_approved + '</span> ';
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
    });
	
	STORE.LIST.TABLE.on('click', '.actions', function (e) {
        e.preventDefault();
        $('#store-form span[class="errmsg"]').attr({for : '', class: ''}).empty();
        addDropDownMenuActions($(this), function (op) { 
            console.log(op);			
            $('#acc_postcode ,#geolongitute, #geolatitute', '#store-form').val('');
            if (op.data != undefined && op.data != null)
			{					
                $('#store-list').pageSwapTo('#store-form-panel');
                CODE = op.data.code;
				$('#store-form div.nav-tabs-custom ul').find('li.active').removeClass();
				$('#store-form div.nav-tabs-custom div.tab-content').find('div.tab-pane.active').removeClass('active');
				$('#store-form div.nav-tabs-custom ul').find('li:eq(0)').addClass('active');
				$('#store-form div.nav-tabs-custom div.tab-content').find('div.tab-pane:eq(0)').addClass('active');
				$('#country_id').val(op.data.country_id);
                $.each(op.data, function (k, v) {
                    if (k != 'store_logo' && k != 'phone_code') {
                        $('#' + k, '#store-form').val(v);
                        if (k == 'mr_postcode' || k == 'acc_postcode') {
                            //$('#' + k, '#store-form').trigger('change');
                            $('#' + k, '#store-form').val(v);
                        } else if (k == 'postcode')
                        {
                            $('#acc_postcode', '#store-form').val(v);
                        }
                    }
                    if (k == 'phone_code') {
                        $('#mob_phonecode, #phonecode', '#store-form').text(v);
                    }
                });
				var postcode = $('#postal_code').val();
				if (postcode) {
					$.ajax({
						url: window.location.BASE + 'check-pincode',
						data: {pincode: postcode, country_id: $('#country_id').val()},
						success: function (OP) {
							$('#country_id, #state_id, #city_id, #district_id').prop('disabled', false).empty();
							$('#country_id').val(OP.country_id);
							$('#district_id').val(OP.district_id);
							//$('#country_id').append($('<option>', {value: OP.country_id}).text(OP.country));
							$('#country').append($('<option>', {value: OP.country_id}).text(OP.country));
							$('#country').attr('disabled',true);
							$('#state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
							$.each(OP.cities, function (k, e) {
								$('#city_id').append($('<option>', {value: e.id}).text(e.text));
							});
							$('#city_id option[value=' + op.data.city_id + ']').attr('selected', true);
						},
						error: function () {
							$('#country_id').val('').prop('disabled', true);
							$('#state_id').val('').prop('disabled', true);
							$('#city_id').val('').prop('disabled', true);
						}
					});
				}
                bcategoryId = (op.data.bcategory_id != undefined) ? op.data.bcategory_id : '';
               
                checkPincodeMR = function () {
                    $('#mr_city_id', '#store-form').val(op.data.mr_city_id);
                }
                checkPincodeACC = function () {
                    $('#acc_city_id', '#store-form').val(op.data.acc_city_id);
                }
				
                $('#autocomplete', '#store-form').val(op.data.address);				                		
				$('input[name="specify_working_hrs"][value="' + op.data.has_specific_hrs + '"]').prop("checked", true);
                if (op.data.has_specific_hrs == 3) {
                    $(".working_hrs").fadeIn();
                }			
				$('input[name="split_working_hrs"][value="' + op.data.isSplit + '"]').prop("checked", true);
                if (op.data.isSplit === 1) {
                    $(".session-2").fadeIn();
                }
				/* if (op.data.is_primary === 1) {
                   // $('#no_time').css("display", 'none');
                    //$('#global_time').css("display", 'none');
					$('input[name="specify_working_hrs"][value="3"]').prop("checked", true);
					$(".working_hrs").fadeIn();
                } else {
					$('#no_time').css("display", 'block');
                    $('#global_time').css("display", 'block');					
				} */
                $.each(op.data.operating_hrs, function (key, value) {
                    $.each(value, function (k, val) {
				       k = parseInt(k);
                        if (k == 1) {
                            if (val['is_closed'] == 1) {                                
                                $('input[id="operating_hrs[' + key + ']"][value="' + val['is_closed'] + '"]').prop("checked", true);								
								
								$('input[id="operating_hrs[' + key + ']"][value="1"]').parents('.form-group' + key).find('input[type="time"]').attr('disabled', 'disabled').val('');
								$($('input[id="operating_hrs_' + key + '_0_from"]')).next("span").remove();
								$($('input[id="operating_hrs_' + key + '_1_from"]')).next("span").remove();
								$($('input[id="operating_hrs_' + key + '_0_to"]')).next("span").remove();
								$($('input[id="operating_hrs_' + key + '_1_to"]')).next("span").remove();
                            }
                        }
                        if (val['is_closed'] == 0) {
                            k = k - 1;
                            if (val['from'] != undefined) {
                                $('#operating_hrs_' + key + '_' + k + '_from').val(convertTimeFrom12To24(val['from']));
                                $('#operating_hrs_' + key + '_' + k + '_to').val(convertTimeFrom12To24(val['to']));
                            }
                        }
                    });
                });
                //console.log( op.data.formatted_address  );
                $('input[name="formatted_address"]', '#store-form').val(op.data.formatted_address);
				if (op.data.store_logo) {
					$('#store-form #logo-preview').attr('src', op.data.store_logo);
				}
                $('#old_logo').val(op.data.store_logo);
                // $('#email,#mobile,#uname', '#store-form').attr({readonly: true});
                $('#store-form').attr('action', window.location.SELLER + 'outlet/update-web');
            }
			else if(op.view != undefined && op.view != null){
			   $('#store-list').pageSwapTo('#store_view_details');
			    var res = op.view;
			    $('#store_view_details #outlet_logo').attr('src',res.store_logo);
			    $('#store_view_details #outlet_name').html(res.store_name);
			    $('#store_view_details #outlet_code').html('<b>#'+ res.store_code+'</b>');
			    $('#store_view_details #outlet_email').html(res.email);
		
			    $('#store_view_details #outlet_mobile').html(res.mobile_no);
			    $('#store_view_details #outlet_status').html('<span class="label label-'+ res.status_class +'">'+res.status+'</span>');
			    $('#store_view_details #outlet_address').html(res.address);
			    $('#store_view_details #outlet_category').html(res.bcategory_name);
			 	$('#store_view_details #outlet_phone').html(res.landline_no);				
			    $('#store_view_details #outlet_approval').html('<span class="label label-'+ res.is_approved_class +'">'+res.is_approved+'</span>');
				
				
				if(res.image_cunt != 0){
				    $('#store_view_details #image_count').closest('.form-group').show();
				    $('#store_view_details #image_count').html('<a href="'+ res.image_link+'" target="_blank" >'+ res.image_cunt +'</a>');
				}else{
				    $('#store_view_details #image_count').closest('.form-group').hide();
				}
				$('#store_view_details #business_hours').html(res.timing);
			}
            else {
                DT.fnDraw();
            }
        });
    });
	
	
	
	function convertTimeFrom12To24(timeStr) {
        var colon = timeStr.indexOf(':');
        var hours = timeStr.substr(0, colon),
                minutes = timeStr.substr(colon + 1, 2),
                meridian = timeStr.substr(colon + 4, 2).toUpperCase();

        var hoursInt = parseInt(hours, 10), offset = meridian == 'PM' ? 12 : 0;

        if (hoursInt === 12) {
            hoursInt = offset;
        } else {
            hoursInt += offset;
        }
        var hrs = hoursInt.toString();
        if (hrs.length == 1) {
            hoursInt = '0' + hoursInt;
        }
        return hoursInt + ":" + minutes;
    }
	
	 $('#store-form-panel').on('submit', '#store-form', function (e) {
        e.preventDefault();		
        CURFORM = $('#store-form');
		var formData = new FormData(CURFORM);
		
		if(CROPPED) {
		   CROPPED = false;
		   formData.append('store_logo', uploadImageFormat($('#logo-preview').attr('src')));
		}
		$.each(CURFORM.serializeObject(), function (k, v) {
			formData.append(k, v);
		});		
	    $.ajax({
            type: 'POST',
            url: CURFORM.attr('action') + (CODE != null ? '/' + CODE : ''),            
            dataType: 'json',
			data:formData,
			processData:false,
			contentType: false,
            success: function (op,textStatus,xhr) {
		        $('#store-form-panel').pageSwapTo('#store-list');	
				$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert" area-label="close">&times;</a>'+op.msg+'</div>');
                DT.fnDraw();  
            },
            error: function (jqXHR, textStatus, errorThrown) {
				if(jqXHR.state.status == 422){
					$('#alt-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert alert-success" area-label="close">&times;</a>'+jqXHR.responseJSON.msg+'</div>');
				}
            }
        });
    });
	
	function buildTree(pCats, level) {
		level = level || 1;
		$.each(pCats, function (k, elm) {
			treeList = treeList + '<li  data-value="' + elm.id + '"  data-level="' + level + '" class="level-' + level + ' ' + activeCls + '"><a href="#">' + elm.name + '</a></li>';
			sCats = filterCategory(elm.id);
			if (sCats.length > 0) {
				treeList = buildTree(sCats, parseInt(level) + 1);
			}
		});
		return treeList;
	}
	
	function filterCategory(pid) {
		var resultAarray = jQuery.grep(catArr_resource, function (elm, i) {
			return (elm.parent_id == pid);
		});
		return resultAarray;
	}
	
	$('#postal_code').on('change', function () {
        var pincode = $('#postal_code').val();
        if (pincode != '' && pincode != null)
		{
			$.ajax({
				url: window.location.BASE + 'check-pincode',
				data: {pincode: pincode, country_id: $('#country_id').val()},
				success: function (OP) {
					console.log(OP);
					$('#country_id, #state_id, #city_id, #district_id').prop('disabled', false).empty();					
					$('#district_id').val(OP.district_id);
					$('#state_id').append($('<option>', {value: OP.state_id}).text(OP.state));
					$('#country').append($('<option>', {value: OP.country_id}).text(OP.country));
					$('#country').attr('disabled',true);
					$.each(OP.cities, function (k, e) {
						$('#city_id').append($('<option>', {value: e.id}).text(e.text));
					});
					$('#country_id, #state_id, #city_id').trigger('change');
				},
				error: function () {
					$('#state_id, #city_id').empty();					
					$('#state_id').val('').prop('disabled', true);
					$('#city_id').val('').prop('disabled', true);
					$('#country').val('').prop('disabled', true);
				}
			});
		}	
    });
	
	$('#store-form-panel #store-form').on('change', '.specify_working_hrs', function (evt) {	
        if ($('.specify_working_hrs:checked').val() == '3') {			
           $('.working_hrs').fadeIn();
        } else {			
            $('.working_hrs').fadeOut();
            //$('input[id="split_working_hrs"][value="1"]').iCheck('uncheck');
        }
    });
	
	$('#store-form-panel').on('change', '#split_working_hrs', function (evt) {
        if ($('#split_working_hrs').is(':checked')) {
            //$('.is-closed:not(:checked)').parents('.is-closed-div').siblings('.session-2').fadeIn();
            $('.session-2').fadeIn();
        } else {
            $('.session-2').fadeOut();
        }
    });
	
	//$('.closed').on('change', function () {
	$('#store-form-panel').on('change', '.closed', function (evt) { 
        var key = $(this).attr('data');
        console.log(key);        
		if ($(this).is(':checked')) {			
			$('input[id="operating_hrs[' + key + ']"][value="1"]').prop("checked", true);
			$('input[id="operating_hrs[' + key + ']"][value="1"]').parents('.form-group' + key).find('input[type="time"]').attr('disabled', 'disabled').val('');
			$($('input[id="operating_hrs_' + key + '_0_from"]')).next("span").remove();
			$($('input[id="operating_hrs_' + key + '_1_from"]')).next("span").remove();
			$($('input[id="operating_hrs_' + key + '_0_to"]')).next("span").remove();
			$($('input[id="operating_hrs_' + key + '_1_to"]')).next("span").remove();
		} else {
			$('input[id="operating_hrs[' + key + ']"][value="1"]').parents('.form-group' + key).find('input[type="time"]').removeAttr('disabled');
		}        
    });
	
	$('#store-form-panel, #store_view_details').on('click', '.back-to-list', function (evt) {
        evt.preventDefault();		
        CODE = null; 
		/* $('#store-form input[type="checkbox"]').iCheck('uncheck');
		$('#store-form input[type="radio"]').iCheck('uncheck'); 
		$('#store-form input[type="time"]').val('');
        $('#store-form').resetForm();	 */           
        $('#store-form-panel,#store_view_details').pageSwapTo('#store-list');
    });
	
	$('#store-list').on('click', '#add-store', function (evt) {
        evt.preventDefault();	 
		$('#store-form div.hierarchy-select #category').val('');
		$('#store-form .dropdown-toggle .selected-label').html('-- select --');
		$('#store-form div.hierarchy-select ul li.active').removeClass('active');
		$('#store-form div.nav-tabs-custom ul').find('li.active').removeClass();
		$('#store-form div.nav-tabs-custom div.tab-content').find('div.tab-pane.active').removeClass('active');
		$('#store-form div.nav-tabs-custom ul').find('li:eq(0)').addClass('active');
		$('#store-form div.nav-tabs-custom div.tab-content').find('div.tab-pane:eq(0)').addClass('active');
        $("input[name=specify_working_hrs][value='1']").iCheck('check');
        $('#store-form span[class="errmsg"]').attr({for : '', class: ''}).empty();
        $('#store-form').attr('action', window.location.SELLER + 'outlet/save-web');
		$('#store-form input[type=text]').val('');
		$('#store-form input[type=radio]').prop('checked',false);
		//businessCategories();
        $('#store-list').pageSwapTo('#store-form-panel');
    });
	
	
});

var loadFile = function(event) {	
	var output = document.getElementById('store_logo');
	output.src = URL.createObjectURL(event.target.files[0]);
};