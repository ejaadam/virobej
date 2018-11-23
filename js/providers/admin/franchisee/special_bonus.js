$(document).ready(function () {
    $('#search').click(function () {		 		
		//alert($('#check-all').is(':checked'));		
        $('#commission_table').dataTable({
            "bPagenation": true,
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
                    "search_term": $('#search_term', $('#commission-form')).val(),
                    "status": $('#status', $('#commission-form')).val(),
                    "from": $('#from', $('#commission-form')).val(),
                    "to": $('#to', $('#commission-form')).val()
                }
            },
            "columns": [
               {            
			   		"searchable": false,
			   		"orderable": false,
					'className': 'dt-body-center',
                    "data": function (row, type, set) {
                        var uname = row.root_username;                                              
                        return new String('<input type="checkbox" class="check" name="fr_com_id[]" value="'+row.fr_com_id+'"/>');
                    }
                },						
                {
                    "data": "created_date",
                    "name": "created_date",
                    "class": "text-center",
                    "render": function (data, type, row, meta) {
                        return new String(row.created_date).dateFormat("dd-mmm-yyyy HH:MM:ss");
                    }
                },				
                {
                    "name": "from_uname",
                    "data": function (row, type, set) {
                        var uname = row.root_username;                                              
                        return new String(uname);
                    }
                },
                {
                    "data": "to_uname",
                    "name": "to_uname",
                    "render": function (data, type, row, meta) {
                        return '<b>' + row.to_full_name + '</b><br /> (' + row.to_uname+' - '+row.franchisee_location+' '+row.franchisee_type_name+')';
                    }
                },
				
                {
                    "data": "remarks",
                    "name": "remarks",
                    "render": function (data, type, row, meta) {
                        var transaction_details = '';
                        if (row.remark !== undefined && row.remark != '') {
                            transaction_details = row.remark + '<br />';
                        }
                        transaction_details += '<b>Transaction Id: ' + row.transaction_id + '<b />';
                        return new String(transaction_details);
                    }
                },
                {
                    "data": "amount",
                    "name": "amount",
                    "class": "text-right no-wrap",
                    "render": function (data, type, row, meta) {
                        return '<b class="text-success">' + row.currency_symbol + ' ' + parseFloat(row.amount).toFixed(2) + ' ' + row.currency + '</b> ';
                    }
                },
                {
                    "data": "commission_amount",
                    "name": "commission_amount",
                    "class": "text-right no-wrap",
                    "render": function (data, type, row, meta) {
                        return '<b class="text-success">' + row.currency_symbol + ' ' + parseFloat(row.commission_amount).toFixed(2) + ' ' + row.currency + '</b> ';
                    }
                },
                {                    
                    "name": "status_name",
                    "class": "text-center",
					"data": "status_name",
					"render" : function(data, type, row, meta){
						return '<div class = "label '+row.status_label+'">'+row.status_name+'</div>';
					}
                },
                {
                    "data": "confirmed_date",
                    "name": "confirmed_date",
                    "class": "text-center",
                    "render": function (data, type, row, meta) {
                        return (row.confirmed_date != null && row.confirmed_date != '') ? new String(row.confirmed_date).dateFormat("dd-mmm-yyyy HH:MM:ss") : '-';
                    }
                },
                {
                    "class": "text-center",
                    "orderable": false,
                    "render": function (data, type, row, meta) {
                        var json = $.parseJSON(meta.settings.jqXHR.responseText);
                        var action_buttons = '';
                        action_buttons = '<div class="btn-group"><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        if (row.status == 2 || row.status == 3 || row.status == 4) {
                            action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-bonus text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="1" data-transaction_id="' + row.transaction_id + '">Confirm</a></li>';
							if (row.status == 2 || row.status == 3){
                            action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-bonus text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="4" data-transaction_id="' + row.transaction_id + '">Cancel</a></li>';
							}
                        }else if(row.status == 1){
							action_buttons += '<li><a href="' + json.url + '/update-status" class="confirm-bonus text-left"data-fr_com_id="' + row.fr_com_id + '" data-status="4" data-transaction_id="' + row.transaction_id + '">Cancel</a></li>';
						}
                        action_buttons += '</ul></div>';
                        return action_buttons;
                    }
                }
            ],
            "order": [[1, 'desc']],
			'fnDrawCallback': function(oSettings) {
				$('#check-all').prop({'indeterminate':false,'checked':false});
			}
        });
    });
    $('#commission_table').on('click', '.confirm-bonus', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('.alert').remove();
        if (confirm('Are you wants to ' + CurEle.text() + ' ' + CurEle.data('transaction_id') + ' this transaction?')) {
            $.ajax({
                url: CurEle.attr('href'),
                type: "POST",
                data: {fr_com_id: CurEle.data('fr_com_id'), status: CurEle.data('status')},
                datatype: "JSON",
                success: function (data) {
                    $('#commission-form').after(data.msg);
                    if (data.status == 'OK') {
                        $('#search').trigger('click');
                    }
                },
                error: function () {
                    alert('Something went wrong');
                }
            });
        }
    });
    $('#search').trigger('click');
	$("#reset_btn").click(function (event) {
        event.preventDefault();
        $('#commission-form').trigger('reset');
        $('#search').trigger('click');
    }); 
	
	 $('#check_all').on('change', function() {
		$('#commission_table').find('.check').prop('checked',this.checked);		
		statusoption_view();		
	});
	 
	 $('#commission_table').on('change','.check', function() {
		var $checkboxes = $('#commission_table').find('.check');
		var $checked = $checkboxes.filter(function() { return this.checked; });
		
		if($checked.length === 0) {
			$('#check-all').prop({'indeterminate':false,'checked':false});
		} else if($checked.length === $checkboxes.length) {
			$('#check-all').prop({'indeterminate':false,'checked':true});			
		} else {
			$('#check-all').prop('indeterminate',true);
		}
		statusoption_view();
	});
	 function statusoption_view(){
		 var $checkboxes = $('#commission_table').find('.check');
		 var $checked = $checkboxes.filter(function() { return this.checked; });
		 if($checked.length >0){
			 $(".dataTables_processing").html('Action <div class="btn-group btn-group-md">'
                        + '<select class="btn btn-default" name="status" id="update_status" style="height: 30px;">'
                        + '<option value="">Select Status</option>'
                        + '<option value="1">Confirm</option>'                       
                        + '<option value="4">Cancel</option>'
                        + '</select>'
                        + '<input type="button" class="btn btn-default" id="statusupdate" style="height: 30px;" value="Update"></div>').show();
		}else{
			$(".dataTables_processing").html('');
			$(".dataTables_processing").hide();
		}
	 }
	 
	 $(document.body).on('click', "#statusupdate", function (e) {
		e.preventDefault();
		var cnt = $('tbody td input[type=checkbox]:checked').length;
		if (cnt > 0) {
			if ($('#update_status').val() != '')
			{
				$('#specialBonusFrm').submit();
			}
			else
			{
				alert("Please Select the status");
			}
		}
		else {
			alert("Records not selected");
		}
		return false;
	});
	 $('#specialBonusFrm').submit(function (e) {
            e.preventDefault();
            $('.alert').remove();
            var status = $('option:selected', $('#update_status')).text();
            if (confirm('Are you sure, You wants to move to ' + status + '?')) {
                $.post($(this).attr('action'), $(this).serialize(), function (data) {
                    if (data.status == 'OK') {
                        $('#search').trigger('click');
                    }
                    $('#specialBonusFrm').prepend(data.msg);
                }, 'json');
            }
        });
});
