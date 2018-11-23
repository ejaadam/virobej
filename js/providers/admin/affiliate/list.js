var category = '';
var categoryTree = '';
var Scategory = [];
var treeNodes = '';
var treeObj = Object();
 var selectNodes = [];
$(document).ready(function () {

	treeConfig = {
        selectMode: 2,
        icons: false,
        checkbox: true,
        blurTree: function (event, data) {
            //logEvent(event, data);
        },
        create: function (event, data) {
            //logEvent(event, data);
        },
        init: function (event, data, flag) {
            Scategory = [];
            var Input = '';
            var Snodes = data.tree.getSelectedNodes();
            $.each(Snodes, function (index, val) {

                Scategory.push(val.key);
                Input = Input + '<input type="hidden" name="affiliate[category_id][]" value="' + val.key + '">';

            });
            $('#categorySel').html(Input);
        },
        focusTree: function (event, data) {
            //logEvent(event, data);
        },
        activate: function (event, data) {
            var node = data.node;
            $('#echoActive').text(node.title);
            if (! $.isEmptyObject(node.data)) {
            }
        },
        beforeActivate: function (event, data) {
            //logEvent(event, data, 'current state=' + data.node.isActive());
        },
        beforeExpand: function (event, data) {
            //logEvent(event, data, 'current state=' + data.node.isExpanded());
        },
        beforeSelect: function (event, data) {
            logEvent(event, data, 'current state=' + data.node.isSelected());
        },
        blur: function (event, data) {
            //logEvent(event, data);
            $('#echoFocused').text('-');
        },
        click: function (event, data) {
            //logEvent(event, data, ', targetType=' + data.targetType);
        },
        collapse: function (event, data) {
            //logEvent(event, data);
        },
        createNode: function (event, data) {
            //logEvent(event, data);
        },
        dblclick: function (event, data) {
            //logEvent(event, data);
        },
        deactivate: function (event, data) {
            //logEvent(event, data);
            $('#echoActive').text('-');
        },
        expand: function (event, data) {
            //logEvent(event, data);
        },
        focus: function (event, data) {
            //logEvent(event, data);
            //$('#echoFocused').text(data.node.title);
        },
        keydown: function (event, data) {
            //logEvent(event, data);
            switch (event.which) {
                case 32:
                    data.node.toggleSelected();
                    return false;
            }
        },
        keypress: function (event, data) {
            //logEvent(event, data);
        },
        lazyload: function (event, data) {
            //logEvent(event, data);
            data.result = {url: 'ajax-sub2.json'};

        },
        loadChildren: function (event, data) {
            //logEvent(event, data);
        },
        postProcess: function (event, data) {
            //logEvent(event, data);
            data.response[0].title += ' - hello from postProcess';
        },
        removeNode: function (event, data) {
            //logEvent(event, data);
        },
        renderNode: function (event, data) {
            //logEvent(event, data);
        },
        renderTitle: function (event, data) {
            //logEvent(event, data);
        },
        select: function (event, data) {
            Scategory = [];
            var Input = '';
            //var s = data.tree.getSelectedNodes().join(', ');
            var Snodes = data.tree.getSelectedNodes();
            $.each(Snodes, function (index, val) {
                Scategory.push(val.key);
                Input = Input + '<input type="hidden" name="affiliate[category_id][]" value="' + val.key + '">';
            });
            $('#categorySel').html(Input);
        }
    }
	 $.ui.fancytree.debugLevel = 1;
    function logEvent(event, data, msg) {
        msg = msg ? ': ' + msg : '';
        $.ui.fancytree.info("Event('" + event.type + "', node=" + data.node + ")" + msg);
    }
      $('#affiliate-form #image_url-preview').attr('src', $('#affiliate-form #image_url').attr('data-default'));
            $.ajax({
                url:  'admin/online/signup-categories',
                dataType: 'JSON',
                success: function (op) {
                    catArr_resource = op;
                    $(':ui-fancytree').fancytree('destroy');
                    obj = buildTree(filterCategory(0));
                    treeConfig.source = obj.list;
                    treeObj = $('#tree').fancytree(treeConfig);
                }
            });
			$('#tree_country').html('');
           loadCountry($store_id = '', country = []);

	 $('#affiliate-form').submit(function (e) {
        e.preventDefault();
        CURFORM = $(this);
        var data = new FormData();
       if (CROPPED) {
           data.append('image_url', uploadImageFormat($('#image_url-preview').attr('src')));
        } 
        $.each(CURFORM.serializeObject(), function (k, v) {
            data.append(k, v);
        });
        $.ajax({
            url: ID !== null ? $('#affiliate-form').attr('action') + '/' + ID : $('#affiliate-form').attr('action'),
            data: data,
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            success: function (op) {
               
                DT.fnDraw();
                $('#affiliate-form').resetForm();
            }
		
        });
    });
		
		
	
	
	function filterCategory(pid) {
        var resultAarray = jQuery.grep(catArr_resource, function (elm, i) {
            return (elm.parent_id == pid);
        });
        return resultAarray;
    }
	
 $('#form-panel').on('click', '.imgCls', function (e) {
        if ($("input[name='img_type']:checked").val() == 1) {
            $('input:radio[name=img_type]').filter('[value=1]').prop('checked', true)
            $('#image_type1').fadeIn();
            $('#image_type2').fadeOut();
        }
        if ($("input[name='img_type']:checked").val() == 2) {
            $('#image_type1').fadeOut();
            $('#image_type2').fadeIn();
        }
    })
	 $('#form-panel').on('change', '.desCls', function (e) {
        e.preventDefault();
        if ($('.desCls:checked').length > 0) {
            if ($(this).val() == 2) {
                $('#descFld').css('display', 'none');
                $('#cbFld').css('display', 'block');
                if ($('#addFld').length == 0) {
                    $('#multiFld').append($('<div>', {class: 'form-group cbInput'}).append($('<div>', {class: 'col-sm-6'}).append([$('<input>', {type: 'text', class: 'form-control', name: 'affiliate[cb_name][]'})])).append($('<div>', {class: 'col-sm-2'}).append([$('<input>', {type: 'text', class: 'form-control', name: 'affiliate[cb_val][]'})])).append($('<div>', {class: 'col-sm-1'}).append('<i class="fa fa-plus fa-lg" id="addFld" aria-hidden="true" title="add"></i>')));
                }
            } else {
                $('#cbFld').css('display', 'none');
                $('#descFld').css('display', 'block');
            }
        }
    })
	 $('#form-panel').on('click', '.removFld', function (e) {
        e.preventDefault();
        $(this).closest('.form-group').remove();
        $('#addFld').css('display', 'block');
    })
    $('#form-panel').on('click', '#addFld', function (e) {
        e.preventDefault();
        if ($('.cbInput').length < 6) {
            $('#multiFld').append($('<div>', {class: 'form-group cbInput'}).append($('<div>', {class: 'col-sm-6'}).append([$('<input>', {type: 'text', class: 'form-control', name: 'affiliate[cb_name][]'})])).append($('<div>', {class: 'col-sm-2'}).append([$('<input>', {type: 'text', class: 'form-control', name: 'affiliate[cb_val][]'})])).append($('<div>', {class: 'col-sm-1'}).append('<i class="fa fa-minus fa-lg removFld" aria-hidden="true" title="add"></i>')));
        } else {
            $(this).css('display', 'none');
        }
    })
	 function loadCountry(store_id, countries) {
        $.ajax({
            url: 'admin/online/countries/list',
            dataType: 'JSON',
            data: {store_id: store_id},
            type: 'POST',
            success: function (op) {
                $.each(op, function (key, val) {
                    if (countries.length == 0) {
                        $('#tree_country').append('<br><input type="checkbox" name="country" value="' + val.country_id + '"> ' + val.country);
                    } else {
                        if (jQuery.inArray(val.country_id, countries) != - 1) {
                            console.log(val.country_id + "is in array");
                            $('#tree_country').append('<br><input type="checkbox" name="country" checked value="' + val.country_id + '"> ' + val.country);
                        } else {
                            console.log(val.country_id + "is NOT in array");
                            $('#tree_country').append('<br><input type="checkbox" name="country" value="' + val.country_id + '"> ' + val.country);
                        }
                    }
                });
            }
        });
    }
	$('img.editableImg').on('click', function (e) {
        e.preventDefault();
        $($(this).data('input')).trigger('click');
    });
	
	/* Table Listing */
	    var t = $('#listtbl');
	    var ID = null;
	   // var catArr_resource = new Array();
	    var DT = t.dataTable({
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
            url: $('#listfrm').attr('action'),
            type: 'POST',
            data: function (d) {
		
                return $.extend({}, d, $('input,select', '#listfrm').serializeObject());
            },
        },
        columns: [
            {
                name: 'store_name',
                class: 'text-left',
                data: function (row, type, set) {
                    return '<strong>' + row.store_name + '</strong>(' + row.store_code + ')';
                }
            },
            {
                name: 'logo_url',
                data: function (row, type, set) {
                    if (row.logo_url != null) {
                        return '<img width="100" height="80" class="img-responsive" src="' + row.logo_url + '">';
                    } else {
                        return '<img width="100" height="80" class="img-responsive" src="' + row.store_logo + '">';
                    }
                },
            },
            {
                name: 'country',
                data: 'country',
            },
            {
                name: 'logo_url',
                data: function (row, type, set) {
                    return row.company_name;
                },
            },
			{
                data: 'created_on',
                name: 'created_on'
            },
            {
                name: 'status',
                data: function (row, type, set) {
	                return '<span class="label label-' + row.status_class + '">' + row.status + '</span><span class="label label-success">' + row.is_featured + '</span>';
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
	  $('#listfrm').on('submit', function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#searchbtn').click(function (e) {
        DT.fnDraw();
    });
    $('#resetbtn').click(function (e) {
        $('input,select,input:checkbox', $(this).closest('form')).val('');
        $('input:checkbox').removeAttr('checked');
        DT.fnDraw();
    });
    $('#form-panel').on('click','.close-btn', function (e) {
        e.preventDefault();
        $('#form-panel').hide();
        $('#list-panel').show();
    });
	
	$('.box-body').on('change','#store_banner',function(e){
		e.preventDefault();
		var img = $('#store_banner option:selected').data('thumbnail');
		var id  = $('#store_banner').val();
		if(id !=''){
			$('#banner-preview').html('<img class="img-thumbnail" style="height:130px" src="'+img+'">');
			$('#bannerFld').css('display','block');
		}else{
			$('#bannerFld').css('display','none');
		}
		
	});
	
    $('body').on('click', '.dtr-details .actions,td .actions', function (e) {
        e.preventDefault();
        if ($(this).closest('.dtr-bs-modal')) {
            $(this).closest('.dtr-bs-modal').modal('hide');
        }
        addDropDownMenuActions($(this), function (data) {
            CURFORM = $('#affiliate-form');
           if (data.details != undefined) {

                $('#affiliate-form').resetForm();
                $('#form-panel').show();
                $('#list-panel').hide();
				$(':ui-fancytree').fancytree('destroy');
                $('#form-panel .panel-title').text('Edit Online Store Details');
                ID        = data.details.store_id;
                category  = data.details.category_id;
                   $('#category_id').append('<option value="' + data.details.category_id + '">' + data.details.category_id + '</option>');
                   $('#affiliate-form #store_name').val(data.details.store_name);
                   $('#affiliate-form #program_id').val(data.details.program_id);
			 if(data.details.banner_id !=0){
					$('#store_banner').val(data.details.banner_id);
					var img = $('#store_banner option:selected').data('thumbnail');
				$('#banner-preview').html('<img class="img-thumbnail" style="height:130px" src="'+img+'">');
					   $('#bannerFld').css('display','block');
    			}else{
    					$('#bannerFld').css('display','none');
    			}
                if (data.details.logo_url != null && data.details.logo_url != '') {
                    $('#affiliate-form #logo_url').val(data.details.logo_url);
                    $("input[name=img_type][value='1']").prop('checked', true);
                    $('#image_type1').fadeIn();
                    $('#image_type2').fadeOut();
                } else {
                    $("input[name=img_type][value='2']").prop('checked', true);
                    $('#image_type1').fadeOut();
                    $('#image_type2').fadeIn();
                    $('#affiliate-form #image_url-preview').attr('src', data.details.store_logo);
                }
              if (data.details.is_featured) {
                    $('#affiliate-form #is_featured').val(data.details.is_featured);
                    $('#affiliate-form #is_featured').attr('checked', true);
                } else {
                    $('#affiliate-form #is_featured').attr('checked', false);
                }
                    $('#affiliate-form #url').val(data.details.url);
                     $('#affiliate-form #aff_netwrk').val(data.details.supplier_id);
                 $('#affiliate-form #old_cashback').val(data.details.old_cashback);
                $('#affiliate-form #cashback').val(data.details.new_cashback);
                $('#affiliate-form #old_cashback_type').val(data.details.cashback_type);
                $('#affiliate-form #cashback_type').val(data.details.cashback_type);
                $('#affiliate-form #status').val(data.details.status);
                $('#affiliate-form #dont_desc').val(data.details.dont_desc);
                $('#affiliate-form #dos_desc').val(data.details.dos_desc);
                $('#affiliate-form #cb_notes').val(data.details.cb_notes);
                $('#affiliate-form #conditions').val(data.details.conditions);
                $('#affiliate-form #from_date').val(data.details.expired_on);
               $('#affiliate-form #affiliate_image_url-preview').attr('src', data.details.store_logo);
                $('#affiliate-form #website_url').val(data.details.website);
                if (data.details.desc_type == 2) {
                    $('#descFld').css('display', 'none');
                    $('#cbFld').css('display', 'block');
                    $('#multiFld input').remove();
                    $("input[name=desc_type][value=" + data.details.desc_type + "]").prop('checked', 'checked');
                    var arr = data.details.description;
                    $.each(arr, function (index, val) {
                        $('#multiFld').append($('<div>', {class: 'form-group'}).append($('<div>', {class: 'col-sm-6'}).append([$('<input>', {type: 'text', class: 'form-control', name: 'affiliate[cb_name][]', value: val.desc})])).append($('<div>', {class: 'col-sm-2'}).append([$('<input>', {type: 'text', class: 'form-control', name: 'affiliate[cb_val][]', value: val.val})])).append($('<div>', {class: 'col-sm-1'}).append('<i class="fa fa-minus fa-lg removFld" aria-hidden="true" title="add"></i>')));
                    })
                } else {
                    $('#multiFld input').remove();
                    $('#descFld').css('display', 'block');
                    $('#cbFld').css('display', 'none');
                    $('#affiliate-form #description').val(data.details.description);
                    $("input[name=desc_type][value=" + data.details.desc_type + "]").prop('checked', 'checked');
                }
               $('#affiliate-form #tags').val(data.details.tags);
                $('#affiliate-form #meta_title').val(data.details.meta_title);
                $('#affiliate-form #meta_keyword').val(data.details.meta_keyword);
                $('#affiliate-form #meta_desc').val(data.details.meta_desc);
                $('#affiliate-form #cb_waiting_period').val(data.details.cb_waiting_days);
                $('#affiliate-form #cb_traking_period').val(data.details.cb_tracking_days);
               
	            if((category != undefined) && (category != '')) {
                    //selectNodes = category.split(',');
                     selectNodes = category;
                }

             	
			/*	var editor = CKEDITOR.instances.description;
				if(editor) {
					editor.destroy(true); 
				}   
				CKEDITOR.replace('description');
				CKEDITOR.instances['description'].setData(data.details.description);
				
				var editor = CKEDITOR.instances.dos_desc;
				if (editor) {
					editor.destroy(true); 
				}   
				CKEDITOR.replace('dos_desc');
				CKEDITOR.instances['dos_desc'].setData(data.details.dos_desc);
				
				var editor = CKEDITOR.instances.dont_desc;
				if (editor) {
					editor.destroy(true); 
				}   
				CKEDITOR.replace('dont_desc');
				CKEDITOR.instances['dont_desc'].setData(data.details.dont_desc);
				
				var editor = CKEDITOR.instances.cb_notes;
				if (editor) {
					editor.destroy(true); 
				}   
				CKEDITOR.replace('cb_notes');
				CKEDITOR.instances['cb_notes'].setData(data.details.cb_notes); */
				//console.log(catArr_resource.length);
                if (catArr_resource.length == 0) {
                    $.ajax({
                        url: window.location.ADMIN + 'online/signup-categories',
                        dataType: 'JSON',
                        success: function (op) {
                            catArr_resource = op;
					        $(':ui-fancytree').fancytree('destroy');
                            obj = buildTree(filterCategory(1487));
                            treeConfig.source = obj.list;
                            treeObj = $('#tree').fancytree(treeConfig);
                            $('#tree').fancytree('getRootNode').visit(function (node) {
                                node.setExpanded(true);
                            });
                        }
                    });
                }else {
                    $(':ui-fancytree').fancytree('destroy');
                   
                    obj = buildTree(filterCategory(0));
			        treeConfig.source = obj.list;
                    treeObj = $('#tree').fancytree(treeConfig);
                    $('#tree').fancytree('getRootNode').visit(function (node) {
                        node.setExpanded(true);
                    });
                }
                $('#tree_country').html('');
                loadCountry(data.details.store_id, data.details.country_ids); 
            }
            else {
                DT.fnDraw();
            }
        });
 
   });

function buildTree(pCats) {
        var treeNodeObj = [];
        var preSelected = false;
        $.each(pCats, function (k, elm) {
            var itemObj = Object();
            itemObj.title = elm.name;
            itemObj.key = elm.id;
              console.log(elm.id);  
            if (selectNodes.indexOf(elm.id) !== -1) {
                itemObj.selected = true;
            }
            if (preSelected == false && itemObj.selected != undefined && itemObj.selected == true) {
                preSelected = true;
            }
            sCats = filterCategory(elm.id);
            if (sCats.length > 0) {
                obj = buildTree(sCats);
                itemObj.children = obj.list;
                if (obj.preSelected == true) {
                    itemObj.expand = true;
                    itemObj.activeVisible = true
                }
            }
            else {
                itemObj.children = [];
            }
            treeNodeObj.push(itemObj);
        });
        return {preSelected: preSelected, list: treeNodeObj};
    }

    $('#list-panel,#form-panel').on('click', '.back-to-list', function (e) {
        e.preventDefault();
        CODE = null;
        $('#affiliate-form').resetForm();
        $('#affiliate-form .select2').select2();
        $('#store_logo-preview', '#affiliate-form').attr('src', $('#store_logo', '#affiliate-form').data('default'));
        $('#mobile', '#affiliate-form').removeAttr('readonly');
		$('#form-panel').css('display','none');
		$('#list-panel').css('display','block');
    });

});
	