$(document).ready(function () {

    var DT = $('#proof_verification_details').dataTable({
      ajax: {
            url: $('#proof_documents_details').attr('action'),
            data: function (d) {
                return $.extend({}, d, {
                    type_filer: $('#proof_documents_details #type_filer').val(),
                    search_term: $('#proof_documents_details #search_term').val(),
                    status: $('#proof_documents_details #status').val(),
                    from: $('#proof_documents_details #from').val(),
                    to: $('#proof_documents_details #to').val()
                   
                });
            }
        },
		
		/*ajax: {
            url: $('#proof_documents_details').attr('action'),
            type: 'POST',
            data: function (d) {
		
                return $.extend({}, d, $('input,select', '#proof_documents_details').serializeObject());
            },
        }, */
         columns: [
		          {
                data: 'created_on',
                name: 'spi.created_on',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy HH:MM:ss');
                }
            },
			 {
                data: 'full_name',
                name: 'full_name',
               
               
            },
			
			 {
                data: 'tax',
                name: 'tax',
                render: function (data, type, row, meta) {
					
					var url = '';
                    var content = '';
					if(row.is_registered==0){
						content = '-';
						return content;
					} else{
                    url += window.location.BASE + 'resources/uploads/seller/proof_details' + row.pan_card_image;
                    if (row.content_type == 'Image') {
                        content = '<a class = "pull-center" href = "' + url + '"><img src = "' + url + '" alt = "" class = "media-object img img-thumbnail"></a>';
                    }
                    else {
                        content = '<a class = "btn btn-xs btn-info" target="_blank" href = "' + url + '"><i class="fa fa-download"> Download</i></a>';
                    }
                    return content;
                   
                 }
		       }
              },
			
			  {
                data: 'status',
                name: 'status',
                class: 'text-center status',
                render: function (data, type, row, meta) {
                    var content = '';
                    if (row.status_id == 1)
                    {
                        content = '<span class="label label-success">Approved</span>';
                    }
                    if (row.status_id == 0)
                    {
                        content = '<span class="label label-danger">Pending</span>';
                    }
                    if (row.status_id == 2)
                    {
                        content = '<span class="label label-danger">Rejected</span>';
                    }
                    return content;
                   }
                 },
				
			     {
                data: 'updated_on',
                name: 'spi.updated_on',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy HH:MM:ss');
                }
             },
			    {
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            },
		 ]
    });
    $('#type_filer').loadSelect({
        url: window.location.BASE + 'admin/seller/doc-list',
        key: 'document_type_id',
        value: 'type',
    });
   
   
    

    $('#search').click(function (e) {
        DT.fnDraw();
    });
	
	 $('#proof_verification_details').on('click', '.edit_info', function (e) {
             e.preventDefault();
           $("#view_user_profile .modal-title").html("View Details");
            $("#view_user_profile .modal-body").html("Loading....");
            $("#view_user_profile").modal();
            $.post($(this).data('url'), {tax_id: $(this).data('tax_id')}, function (data) {
            $("#view_user_profile .modal-body").html(data.content); 
            }, 'json');  
		
        });
		
	
   function addDropDownMenu(arr, text) {
    arr = arr || [];
    text = text || false;
    var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-xs btn-primary dropdown-toggle', 'data-toggle': 'dropdown'})
            .append([$('<i>', {class: 'fa fa-gear'}), $('<span>').attr({class: 'caret'})]),
            $('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
        var options = [], data = {};
        $.each(arr, function (k, v) {
            data = {};
            if (! v.redirect) {
                v.class = v.class || (v.url ? 'actions' : 'show-modal');
            }
            else {
                data['target'] = v.target || '_blank';
            }
            v.url = v.url || '#';
            v.data = v.data || {};
            $.each(v.data, function (key, val) {
                data['data-' + key] = val;
            });
            options.push($('<li>').append($('<a>', {class: v.class}).attr($.extend({href: v.url}, data)).text(v.label)));
        });
        return options;
    }));
    return text ? content[0].outerHTML : content;
}
function addDropDownMenuActions(e, callback) {
    var Ele = e, data = Ele.data();
    callback = callback || null;
    if (Ele.data('confirm') == undefined || (Ele.data('confirm') != null && Ele.data('confirm') != '' && confirm(Ele.data('confirm')))) {
        if (data.confirm != undefined) {
            delete data.confirm;
        }
        $.ajax({
            url: Ele.attr('href'),
            data: data,
            success: function (data) {
                if (callback !== null) {
                    callback(data);
                }
            }
        });
    }
}
   
});
