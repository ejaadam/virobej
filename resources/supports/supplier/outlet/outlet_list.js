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
                d.status = $('#status').val();
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
                    str = '<a href="' + row.actions.edit.url + '" class="actions"><b>' + row.store_name + '</b>' + ' (#' + row.store_code + ')</a><br><i class="fa fa-list-alt margin-r-5"></i>' + row.category;
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
                bcategoryId = (op.data.bcategory_id != undefined) ? op.data.bcategory_id : '';
               /*  $.ajax({
                    url: window.location.BASE + 'seller-categories',
                    type: "post",
                    dataType: "json",
                    success: function (op) {
                        if (op.data != '') {
                            var data = op.data;
                            catArr_resource = data;
                            Tree = buildTree(filterCategory(1485), 1);
                            Tree = '<li data-value="" data-level="0" class="level-0"><a href="#">- Select -</a></li>' + Tree;
                            $('.dropdown-menu .inner').append(Tree);
                            $('#example-one').hierarchySelect('setValue', bcategoryId);                            
                        }
                        if (op.countries != '') {
                            $('#country option').remove();
                            $.each(op.countries, function (index, val) {
                                $('#country').append('<option value="' + val.country_id + '">' + val.country + '</option>');
                            })
                        }
                    }
                }); */
                checkPincodeMR = function () {
                    $('#mr_city_id', '#store-form').val(op.data.mr_city_id);
                }
                checkPincodeACC = function () {
                    $('#acc_city_id', '#store-form').val(op.data.acc_city_id);
                }
                $('#autocomplete', '#store-form').val(op.data.address);
                $("input[name=specify_working_hrs][value=" + op.data.has_specific_hrs + "]").iCheck('check');
                if (op.data.has_specific_hrs == 3) {
                    $(".working_hrs").fadeIn();
                }
                $("input[name=split_working_hrs][value=" + op.data.isSplit + "]").iCheck('check');
                if (op.data.isSplit == 1) {
                    $(".session-2").fadeIn();
                }
                $.each(op.data.operating_hrs, function (key, value) {
                    $.each(value, function (k, val) {
				       k = parseInt(k);
                        if (k == 1) {
                            if (val['is_closed'] == 1) {
                                $('input[id="operating_hrs[' + key + ']"][value="' + val['is_closed'] + '"]').iCheck('check');
                            }/*  else{
						    	$('input[id="operating_hrs[' + key + ']"][value="' + val['is_closed'] + '"]').iCheck('uncheck');
							}   */
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
                $('#logo-preview').attr('src', op.data.store_logo);
                $('#old_logo').val(op.data.store_logo);
                // $('#email,#mobile,#uname', '#store-form').attr({readonly: true});
                $('#store-form').attr('action', window.location.RETAILER + 'store/update-web');
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
			    $('#store_view_details #outlet_address').html(res.flatno_street);
			    $('#store_view_details #outlet_category').html(res.category);
			 	$('#store_view_details #outlet_phone').html(res.landline_no);				
			    $('#store_view_details #outlet_approval').html('<span class="label label-'+ res.is_approved_class +'">'+res.is_approved+'</span>');
				if(res.rating != 0){
				   $('#store_view_details #outlet_rating').closest('.form-group').show();
					$('#store_view_details #outlet_rating').html('<span class="text-success">'+res.rating+' <i class="fa fa-star"></i></span>');					
				}else{
				    $('#store_view_details #outlet_rating').closest('.form-group').hide();
				}
				if(res.likes != 0){
				    $('#store_view_details #outlet_likes').closest('.form-group').show();
				    $('#store_view_details #outlet_likes').html('<span class="text-success"><i class="fa fa-thumbs-o-up"></i>'+res.likes+'</span>');
				}else{
				    $('#store_view_details #outlet_likes').closest('.form-group').hide();
				}
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
});
