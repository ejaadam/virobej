$(document).ready(function () {
							
	$('#country').loadSelect({
        url: window.location.BASE + 'countries-list',
        key: 'id',
        value: 'text'        
    });
    
    var DT = $('#retailer').dataTable({
        ajax: {
			url: $('#retailers_listfrm').attr('action'),
            type: 'POST',
            data: function (d) {
                d.search_term = $('#search_term').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.country = $('#country').val();
                d.bcategory = $('#bcategory').val();
                d.search_term = $('#search_term').val();
                var filterTerms = [];
                $('#chkbox :checked').each(function () {
                    filterTerms.push($(this).val());
                });
                d.filterTerms = filterTerms;
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
				class: 'text-center'
                /* render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy H:m:s');
                } */
            },
            {
                data: 'mrcode',
                name: 'mrcode',                
            },
			{
                name: 'mrbusiness_name',
                data: function (row, type, set) {
                    return '<b>' + row.mrbusiness_name + '</b><br>Acc.ID: ' + row.uname;
                }
            },
            {
                name: 'bcategory_name',
                data: 'bcategory_name',
                class: 'no-wrap'
            },
            {
                data: 'country',
                name: 'country',
                class: 'no-wrap'
            },
            {
                name: 'status',
                class: 'text-center',
                data: function (row, type, set) {
                    return'<span class="label label-' + row.status_class + '">' + row.status + '</span>&nbsp;<span class="label label-' + row.is_verified_class + '">' + row.is_verified + '</span>&nbsp;<span class="label label-' + row.block_class + '">' + row.block + '</span>'
                }
            },
            {
                data: 'activated_on',
                name: 'activated_on',
                class: 'text-center'
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);
                }
            }
        ]
    });
	
    $('#search').click(function () {
        DT.fnDraw();
    });
	
	$('#retailer').on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (data) {
			console.log(data);
			if((data.view != undefined) && (data.view != '')){
				
					$('#retailer_listf').hide();
					$('#view-panel #merchant_name').text(data.view.details['merchant_name']);
					$('#view-panel #merchant_code').text(data.view.details['merchant_code']);
					$('#view-panel #store_name').text(data.view.details['store_name']);
					$('#view-panel #store_code').text(data.view.details['store_code']);
					$('#view-panel #formated_address').text(data.view.details['formated_address']);
					$('#view-panel #contact').text(data.view.details['uname']);
					$('#view-panel #mobile').text(data.view.details['mobile']);
					$('#view-panel #email').text(data.view.details['email']);

					if (data.view.new_request) {
						$('#view-panel #profit_sharing').text(data.view.new_request['profit_sharing']+ '%');
						$('#view-panel #cashback_on_pay').text(data.view.new_request['cashback_on_pay'] + '%');
						$('#view-panel #cashback_on_redeem').text(data.view.new_request['cashback_on_redeem'] + '%');
						$('#view-panel #cashback_on_shop_and_earn').text(data.view.new_request['cashback_on_shop_and_earn'] + '%');
					} else {
						$('#new_request').hide();
					}

					if (data.view.current_details) {
						$('#view-panel .profit_sharing').text(data.view.current_details['profit_sharing']+ '%');
						$('#view-panel .cashback_on_pay').text(data.view.current_details['cashback_on_pay'] + '%');
						$('#view-panel .cashback_on_redeem').text(data.view.current_details['cashback_on_redeem'] + '%');
						$('#view-panel .cashback_on_shop_and_earn').text(data.view.current_details['cashback_on_shop_and_earn'] + '%');

						if (parseInt(data.view.details['pay']) == 1) {
							$('#accept_payment').prop('checked', data.view.details['pay']);
						}
						if (parseInt(data.view.details['offer_cashback']) == 1) {
							$('#offer_cashback').prop('checked', data.view.details['offer_cashback']);
						}
			
					} 
					$('.close').css('display','block');
					$('#view-panel').show();

			}
	        DT.fnDraw();
        });
    });
	
	
	
    /* $(document).on('click', '.push', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var history_id = $(this).attr('id');
        var package_status = $('#status_col').val();
        $('#package_details_modal').modal();
        $.ajax({
            url: url,
            data: {supplier_id: $(this).attr('id')},
            beforeSend: function () {
                $('#suppliers_details .modal-body').empty();
                $('#suppliers_details .modal-body').html('Loading..');
                $('#suppliers_details').modal();
            },
            success: function (res) {
                $('#suppliers_details .modal-body').empty();
                if (res.status == 'OK') {
                    $('#suppliers_details .modal-body').html(res.contents);
                }
                else {
                    $('#suppliers_details .modal-body').html('Details Not Avaliable');
                }
            }
        });
    });
    $(document).on('click', '.change_pwd', function (e) {
        e.preventDefault();
        var UserName = $(this).attr('data-uname'), Name = $(this).attr('data-company_name'), url = $(this).attr('href'), supplier_id = $(this).attr('id');
        $('#sid', $('#suppliers_reset_pwd')).html(UserName);
        $('#uname', $('#suppliers_reset_pwd')).html(Name);
        $('#suppliers_rpwd').modal();
        $('#suppliers_reset_pwd').trigger('reset');
        $('#suppliers_reset_pwd').validate({
            errorElement: 'div',
            errorClass: 'error',
            focusInvalid: false,
            rules: {
                login_password: {
                    required: true,
                },
                confirm_login_password: {
                    required: true,
                    equalTo: '#login_password',
                },
            },
            messages: {
                login_password: {
                    required: 'Please enter your Password',
                },
                confirm_login_password: {
                    required: 'Please Retype your Password',
                },
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                    var datastring = $(form).serialize();
                    var url = $(this).attr('href');
                    $.ajax({
                        url: window.location.BASE + 'admin/seller/reset_pwd/' + supplier_id,
                        data: datastring,
                        beforeSend: function () {
                            $('input[type="submit"]', $(form)).val('Processing..').attr('disabled', true);
                        },
                        success: function (data) {
                            $('#suppliers_rpwd').modal('hide');
                            $('#msg').html(data.msg);
                            $('#confirm_login_password').val('');
                            $('#login_password').val('');
                            $('input[type="submit"]', $(form)).val('Submit').attr('disabled', false);
                        },
                        error: function () {
                            $('input[type="submit"]', $(form)).val('Submit').attr('disabled', false);
                            alert('Something went wrong');
                        }
                    });
                }
            }
        });
    });
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var history_id = $(this).attr('id');
        var package_status = $('#status_col').val();
        $('#package_details_modal').modal();
        $.ajax({
            url: url,
            data: {supplier_id: $(this).attr('id')},
            beforeSend: function () {
                $('#edit_data .modal-body').empty();
                $('#edit_data .modal-body').html('Loading..');
                $('#edit_data').modal();
            },
            success: function (res) {
                $('#edit_data .modal-body').empty();
                $('#edit_data .modal-body').html(res.contents);
                $('#edit_data').modal();
            }
        });
    }); */
});
/* $(document).on('click', '.change_status', function (e) {
    e.preventDefault();
    var CurEle = $(this);
    if (confirm('Are you sure? You want to ' + CurEle.text() + '?')) {
        $.ajax({
            url: CurEle.attr('href'),
            data: {status: CurEle.data('status'), account_id: CurEle.attr('id')},
            success: function (res) {
                if (res.status == 'OK') {                       
					$("#dt_basic").dataTable().fnDraw();
                }
            }
        });
    }
}); */
