$(document).ready(function () {
	
    var catInfo = catPath = catList = subList = exlink = '';
    //temp data
    var Constants = {ACTIVE: 1, INACTIVE: 2};
    /* Category List */
    var DT = $('#category_listtbl').dataTable({
        bPagenation: true,
        bProcessing: true,
        bFilter: false,
        bAutoWidth: false,
        oLanguage: {
            sSearch: "<span>Search:</span> ",
            sInfo: "Showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries",
            sLengthMenu: "_MENU_ <span>entries per page</span>"
        },
        ajax: {
            url: $('#cat_listfrm').attr('action'),
            type: 'POST',
            data: function (d) {
                return $.extend({}, d, $('input,select', '#cat_listfrm').serializeObject());
            },
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                class: 'text-left',
            },
            {
                data: 'category_name',
                name: 'category_name',
                render: function (data, type, row, meta) {
                    return '<span><b>' + row.bcategory_name + '</b></span><br><span>' + row.parent_name_lbl + ' : ' + row.parent_name + '</span>';
                }
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return '<span class="label label-'+row.status_dispCls+'">' + row.status_name + '</span>';
                }
            },
			{
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '';
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear" aria-hidden="true"></i> <span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                    action_buttons += '<li><a href="' + document.location.BASE + 'admin/category/products/edit"  class="edit_btn" data-category_id="' + row.bcategory_id + '" data-parent_category_id="' + row.parent_bcategory_id + '" data-category_name="' + row.bcategory_name + '">Edit</a></li>';
                    action_buttons += (row.status == Constants.ACTIVE)
                            ? '<li><a data-status="' + Constants.INACTIVE + '" data-category_id="' + row.bcategory_id + '" href="' + document.location.BASE + 'admin/category/products/change-status" class="change_status">Inactive</a></li>'
                            : '<li><a data-status="' + Constants.ACTIVE + '" data-category_id="' + row.bcategory_id + '"  href="' + document.location.BASE + 'admin/category/products/change-status" class="change_status">Active</a></li>';
					action_buttons += '</ul></div>';
                    return action_buttons;
                }
            }
        ],       
    });
	
	/* Category Change Status */
    $('#category_listtbl').on('click', '.change_status', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        if (confirm('Are you sure to ' + CurEle.text() + ' this category')) {
            $.ajax({
                data: {category_id: CurEle.data('category_id'), status: CurEle.data('status')},
                url: CurEle.attr('href'),
                beforeSend: function () {
                    $('.alert,div.help-block ,span.errmsg').remove();
                },
                success: function (data) {
                    if (data.status == 'OK') {
                        $('#category_listtbl').before('<div class="alert alert alert-success">' + data.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                        $('.alert').fadeOut(6000);
                        DT.fnDraw();
                    } else {
                        $('#category_listtbl').before('<div class="alert alert-danger">' + data.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                        $('.alert').fadeOut(6000);
                    }
                }
            });
        }
    });
	
	/* Add New Category */
    $('#cat_listfrm').on('click', '.add_btn', function (e) {
        e.preventDefault();
        $('#category_editfrm').trigger("reset");
        var CurEle = $(this);
        catInfo = '';
		$('#category_editfrm #choose_category').fadeIn('fast');
		$('#category_editfrm #category_linage').empty();
        $('.alert,div.help-block ,span.errmsg').remove();
        $('#Category_edit .panel-title').html('Add Category');
        $('#Category_edit #update_category').text('Save Category');
        $('#choose_category').parents('.form-group').show();
        $('#category_editfrm #category_id').val('');
        $('#category_editfrm #bcategory_name').val('');
        $('#category_editfrm #bcategory_slug').attr('readonly', true);
        //$('#category_editfrm i').removeClass().addClass('icon-edit');
        $('#category_editfrm i').attr('class','').attr('class','icon-edit');
        $('#category_editfrm #bcategory_slug').val('');
        $('#category_editfrm #catSelect').val('');
        $('#category_editfrm #meta_title').val('');
        $('#category_editfrm #meta_desc').val('');
        $('#category_editfrm #meta_keywords').val('');
        $('#image_url-preview').attr('src', document.location.BASE + 'resources/uploads/bcategories/default_img.jpg');
        $('#update_category').attr('disabled', false);
        $('#Category_list').fadeOut('fast', function () {
            $('#Category_edit').fadeIn('slow');
        });
    });
	

	   /* Category Edit Details */
    $('#category_listtbl').on('click', '.edit_btn', function (e) {
        e.preventDefault();
        catInfo = '';
        var CurEle = $(this);
		$('#category_editfrm #choose_category').fadeOut('fast'); //new
        $('#Category_edit .box-title').html('Edit Category');
        $('#Category_edit #update_category').text('Update Category');
        
        $('#category_editfrm #category_id').val(CurEle.attr('data-category_id'));
        $('#category_editfrm #bcategory_name').val(CurEle.attr('data-category_name'));
        $.ajax({
            url: CurEle.attr('href'),
            data: {bcategory_id: CurEle.attr('data-category_id')},
            dataType: "json",
            type: 'POST',
            beforeSend: function () {
                $('.alert,div.help-block ,span.errmsg').remove();
            },
            success: function (res) {
                if (res.status == 200) {
                    catInfo = res.data.category;
                    catPath = res.data.category_path;
                    $('#Category_list').fadeOut('fast', function () {
                        $('#Category_edit').fadeIn('slow');
                    });
					
					if (catPath) {	
						var txt = '';
						$.each(catPath, function (k, v) {	
							txt += v.bcategory_name + ' <i class="icon-arrow-right margin-r-5"></i> ';	
						});
						//txt += '<span class="text-success">' + catInfo.bcategory_name + '</span>';
						console.log(txt);
						$('#category_editfrm #category_linage').html(txt);
					}	
                    if (parseInt(catInfo.parent_bcategory_id) > 0) {
                        $('#choose_category').parents('.form-group').show();
                    } else {
                        $('#choose_category').parents('.form-group').hide();
                    }
                    if (catInfo.category_img === null || catInfo.category_img === undefined) {
                        $('#image_url-preview').attr('src', document.location.BASE + 'resources/uploads/bcategories/default_img.jpg');
                    } else {
                        $('#image_url-preview').attr('src', catInfo.category_img);
                    }
                    var bcat_name = CurEle.attr('data-category_name');
                    str1 = bcat_name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '').replace(/[-]+/g, '-');
                    if (str1 != catInfo.slug) {
                        $('#category_editfrm #bcategory_slug').attr('readonly', false);
                        //$('#category_editfrm #editslugBtn i').removeClass().addClass('icon-remove');
                        $('#category_editfrm #editslugBtn i').attr('class','').attr('class','icon-remove');
                    } else {
                        $('#category_editfrm #bcategory_slug').attr('readonly', true);
                        //$('#category_editfrm #editslugBtn i').removeClass().addClass('icon-edit');
                        $('#category_editfrm #editslugBtn i').attr('class','').attr('class','icon-edit');
                    }
                    $('#category_editfrm #bcategory_slug').val(catInfo.slug);
                    $('#category_editfrm #current_cat_id').val(catInfo.parent_bcategory_id);
                    $('#category_editfrm #meta_title').val(catInfo.meta_title);
                    $('#category_editfrm #meta_desc').val(catInfo.meta_desc);
                    $('#category_editfrm #meta_keywords').val(catInfo.meta_keywords);
                }
            }
        });
    });
	
	$('#bcategory_name').keyup(function () {	
        if ($('#category_editfrm #bcategory_slug').attr('readonly')) {
            var bcat_name = $('#bcategory_name').val();
            str = bcat_name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '').replace(/[-]+/g, '-');
            $('#bcategory_slug').val(str);
        }
    });
	
	/* Slug Edit Cancel */
    $('#category_editfrm').on('click', '#editslugBtn', function (e) {
        e.preventDefault();
        if (! $('#category_editfrm #bcategory_slug').attr('readonly')) {		
            $('#category_editfrm #bcategory_slug').attr('readonly', true);
            //$('i', $(this)).removeClass().addClass('icon-edit');
            $('i', $(this)).attr('class','').attr('class','icon-edit');
            $('#bcategory_name').trigger('keyup');
            $('#bcategory_name').trigger('blur');
        } else {		
            $('#category_editfrm #bcategory_slug').attr('readonly', false);
            //$('i', $(this)).removeClass().addClass('icon-remove');
            $('i', $(this)).attr('class','').attr('class','icon-remove');
        }
    });
	
	/* Category Name slug_checking */
    $('#bcategory_slug, #bcategory_name').blur(function (e) {
        e.preventDefault();
        $('#update_category').attr('disabled', true);
        $.ajax({
            url: document.location.BASE + 'admin/category/products/check-slug',
            data: {bcategory_slug: $('#bcategory_slug').val(), bcategory_id: $('#category_id').val()},
            dataType: "json",
            type: 'POST',
            beforeSend: function () {
                $('.alert,div.help-block').remove();
            },
            success: function (data) {
                if (data.status == 200) {
                    $('#update_category').attr('disabled', false);
                } else {
                    $('#catslug-err').addClass('errmsg').text(data.msg);
                }
                setTimeout(function () {
                    $('#catslug-err').removeClass('errmsg').empty();
                    //$('#catslug-err').attr('class','').empty();
                }, 3000);
            }
        });
    });  
	
	/* Choose Category */
    $('#category_editfrm').on('click', '#choose_category', function (e) {
        e.preventDefault();	
		catPath='';
        $('#pcat_btn').remove();
		//console.log('h = '+catInfo);
        if (catInfo.parent_bcategory_id != 0 && catList == '') 
		{ 
            if (!$.isEmptyObject(catInfo)) 
			{ 
                $.ajax({
                    url: document.location.BASE + 'admin/category/products/getcategory',
                    //data: {excbcat_id: 171, pbcat_id: 171},
                    //data: {excbcat_id: catInfo.bcategory_id, pbcat_id: catInfo.gparent_bcategory_id},
                    dataType: "json",
                    type: 'POST',
                    beforeSend: function () {
                        $('#catSelect').empty();
                        $('#catSelect').append('<option value="">Loding...</option>');
                    },
                    success: function (data) {						
                        $.each(catPath, function (k, e) {
                            $('#cat_link').append("<a href='#'  class='pcat_btn' data-bcategory_id = \"" + e.bcategory_id + "\" >" + e.bcategory_name + ' <i class="icon-arrow-right margin-r-5"></i> ' + "</a>");
                        });
                        $('#catSelect').empty();
                        var options = [];
						$('#catSelect').append('<option value="">-- Select --</option>');
                        $.each(data, function (k, e) {
                            $str = '<option value="' + e.bcategory_id + '" data-haschild="' + e.haschild + '"';
                            if (e.bcategory_id == catInfo.parent_bcategory_id) {
                                $str += ' selected="selected" ';
                            }
                            $str += '>' + e.bcategory_name + '</option>';
                            $('#catSelect').append($str);
                        });
                    }
                });
            } else {			
                $.ajax({
                    url: document.location.BASE + 'admin/category/products/getcategory',
                    dataType: "json",
                    type: 'POST',
                    beforeSend: function () {
                        $('#catSelect').empty();
                        $('#catSelect').append('<option value="">Loding...</option>');
                    },
                    success: function (data) {					
                        $('#catSelect').empty();
                        $('#catSelect').append('<option value="">-- Select --</option>');
                        $.each(data, function (k, e) {
                            $('#catSelect').append('<option value="' + e.bcategory_id + '" data-haschild="' + e.haschild + '">' + e.bcategory_name + '</option>');
                        }); 
                    }
                });
            }
        } else if(catList != ''){
			$('#catSelect').empty();
			$('#catSelect').append('<option value="">-- Select --</option>');
			var options = [];
			cat_id = $('#category_editfrm #current_cat_id').val();
			$.each(catList, function (k, e) {
		    	console.log(e.bcategory_id + ' = ' + cat_id);				
				//$('#catSelect').append('<option value="' + e.bcategory_id + '" data-haschild="' + e.haschild + '" selected="'+(e.bcategory_id == cat_id ? true:false)+'">' + e.bcategory_name + '</option>');				
				$('#catSelect').append('<option value="' + e.bcategory_id + '" data-haschild="' + e.haschild + '" '+(e.bcategory_id == cat_id ? 'selected="selected"':'')+'>' + e.bcategory_name + '</option>');				
			});
		}
		if(exlink != ''){
		    $('#cat_link').html(exlink);
		}else {
		    $('#cat_link').empty();
		}		
        $('#parent_categoryModal').modal('show');
    });
		
    /* Categories Load On-change*/
    $('#parent_categoryModal').on('change', '#catSelect', function (e) {
        e.preventDefault();	
		var links = '';
        //$('#current_cat_id').val($('#catSelect option:selected').val());
        $('#catSelect').attr('data-parent-id',$('#catSelect option:selected').val());
	    var z = $('#catSelect').attr('data-parent-id');  console.log('z =' + z);  // ues checking
		
	    if ($('#catSelect option:selected').data('haschild') >=1) {		   
         	//links = links+$('#catSelect option:selected').text()+'/ ';
			$('#cat_link').append("<a href='#' class='pcat_btn' data-bcategory_id = \"" + $('#catSelect').val() + "\" >" + $('#catSelect option:selected').text() + ' <i class="icon-arrow-right margin-r-5"></i> ' + "</a>");
				
            $.ajax({
                //url: document.location.BASE + 'admin/category/online-store/getcategory',
                url: document.location.BASE + 'admin/category/products/getcategory',
                data: {excbcat_id: $('#catSelect').val(), pbcat_id: $('#catSelect').val()},
                dataType: "json",
                type: 'POST',
                beforeSend: function () {
                    $('#catSelect').empty();
                    $('#catSelect').append('<option value="">Loding...</option>');
                },
                success: function (data) {
				    subList = data;
                    $('#catSelect').empty();
                    $('#catSelect').append('<option value="">-- Select --</option>');
                    var options = [];
                    $.each(data, function (k, e) {
                        $('#catSelect').append('<option value="' + e.bcategory_id + '" data-haschild="' + e.haschild + '">' + e.bcategory_name + '</option>');
                        //$('#catSelect option:selected').attr('selected', true);
                    });
                }
            });
        }
		
		
		/*else{		
			$('#category_linage').text('');
			$('#category_linage').append('<a href="#">'+links+$("#catSelect option:selected").text()+'</a>');
		}*/
    });
	
	/* New Modal Finish Btn */
	$('#parent_categoryModal .modal-finish_btn').click(function (e) {
        e.preventDefault();
		
		/*var z = $('#catSelect').attr('data-parent-id');
		console.log('z =' + z);*/
				
        var txt = parent_id = pcat_name = '';
        if ($('#catSelect').attr('data-parent-id') != '')
		{
            parent_id = $('#catSelect').attr('data-parent-id');
			$('#category_editfrm #current_cat_id').val(parent_id);   // current_cat_id to parent_bcategory_id
			
            $('#cat_link .pcat_btn').each(function () { 
                txt += $(this).text() + ' <i class="icon-arrow-right margin-r-5"></i> ';
            });
            pcat_name = $('#catSelect option:selected').text();
			if(pcat_name != '' && pcat_name !='-- Select --'){
			    txt += '<span class="">' + pcat_name + '</span>';
			}
            $('#category_linage').html(txt);
			catList = subList;			
			exlink = $('#cat_link').html();
            //$('#category_linage').html('All <i class="fa fa-arrow-right margin-r-5"></i>'+txt);
			//$('#deal-form #bcategory_id').siblings('.errmsg').attr({for:'',class:''}).empty(); 
        }	
    });	
	
	/*................*/
	
    $('#parent_categoryModal').on('click', '.pcat_btn', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            url: document.location.BASE + 'admin/category/products/getcategory',
            data: {pbcat_id: CurEle.data('bcategory_id'), excbcat_id: catInfo.bcategory_id},
            dataType: "json",
            type: 'POST',
            beforeSend: function () {
                $('#catSelect').append('<option value="">Loading...</option>');
                CurEle.nextAll('a').remove();
            },
            success: function (data) {
			    catList = exlink ='';
			    subList = data;
                $('#catSelect').empty();
                $('#catSelect').append('<option value="">-- Select --</option>');
                var options = [];
                $.each(data, function (k, e) {
                    $('#catSelect').append('<option value="' + e.bcategory_id + '" data-haschild="' + e.haschild + '">' + e.bcategory_name + '</option>');
                });
            }
        });
    });
	/* Update Category */
 /*  $('#category_editfrm').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,
        rules: {
            bcategory_name: {required: true, pattern: /^[a-zA-Z0-9\s\&\-\_']+$/},
            bcategory_slug: {required: true, pattern: /^[a-z0-9\-]+$/},
        },
		 messages: $online_category_valmsg,
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {

                CURFORM = $(form);
                var data = new FormData();
                if (CROPPED) {
                    CROPPED = false;
                    data.append('image_url', uploadImageFormat($('#image_url-preview').attr('src')));
                }
                $.each(CURFORM.serializeObject(), function (k, v) {
                    data.append(k, v);
                });
                $.ajax({
                    url: $('#category_editfrm').attr('action'),
                    data: data,
                    dataType: "json",
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block ,span.errmsg').remove();
                    },
                    success: function (data) {
                        $('#Category_editModal').modal('hide');
                        DT.fnDraw();
                        if (data.status == "OK") {
                            $('#Category_edit').fadeOut('fast', function () {
                                $('#Category_list').fadeIn('slow');
                            });
                            $('#category_listtbl').before('<div class="alert alert-success">' + data.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                            $('.alert').fadeOut(6000);
                        }
                        else {
                            $('#Category_edit').before('<div class="alert alert-danger">' + data.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                            $('.alert').fadeOut(6000);
                        }
                    }
                });
            }
        }
    }); */
	
    $('#category_editfrm').submit(function (e) {
        e.preventDefault();
        CURFORM = $(this);
        var data = new FormData();
          if (CROPPED) {
               CROPPED = false;
               data.append('image_url', uploadImageFormat($('#image_url-preview').attr('src')));
            }
            $.each(CURFORM.serializeObject(), function (k, v) {
              data.append(k, v);
              });
              $.ajax({
                    url: $('#category_editfrm').attr('action'),
                    data: data,
                    dataType: "json",
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block ,span.errmsg').remove();
                    },
                    success: function (data) {
					    catInfo = catPath = catList = exlink = '';
					    $('#category_editfrm #current_cat_id').val('');
						$('#category_editfrm #category_linage').empty();
                        $('#parent_categoryModal #cat_link').empty();
                        $('#parent_categoryModal #catSelect').val('');
                        $('#parent_categoryModal #catSelect').attr('data-parent-id');
                        $('#Category_editModal').modal('hide');
                        DT.fnDraw();
                        if (data.status == "OK") {
                            $('#Category_edit').fadeOut('fast', function () {
                                $('#Category_list').fadeIn('slow');
                            });
                            $('#category_listtbl').before('<div class="alert alert-success">' + data.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                            $('.alert').fadeOut(6000);
                        }
                        else {
                            $('#Category_edit').before('<div class="alert alert-danger">' + data.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                            $('.alert').fadeOut(6000);
                        } 
                    }
                });
    });
	
	 $('#searchbtn').click(function (e) {
			DT.fnDraw();
		});
		
	 $('#resetbtn').click(function (e) {
			$('input,select', $(this).closest('form')).val('');
			DT.fnDraw();
		});
		
	/*  $('#cat_listfrm').on('submit', function (e) {
        e.preventDefault();
        DT.fnDraw();
       }); */
	   
	  $('.modal-close_btn').click(function (e) {
        e.preventDefault();
        $('.pcat_btn').remove();
       });
	   
	  $('.close_btn').click(function (e) {
        e.preventDefault();
        $('#Category_edit').fadeOut('fast', function () {
            $('#Category_list').fadeIn('slow');
        });
        $('#current_cat_id').val('');
		$('#cat_link').val('');
      });
	
	$('img.editable-img').on('click', function (e) {
        e.preventDefault();
        $($(this).data('input')).trigger('click');
    });
});
