$(document).ready(function () {
    var DT = $('#dt_basic').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('input,select', '.panel_controls').serializeObject());
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy H:m:s');
                }
            },
            {
                name: 'order_code',
                class: 'text-left',
                data: function (row, type) {
                    return row.sub_order_code;
                }
            },
            {
                name: 'product_name',
                class: 'text-left',
                data: function (row, type) {
                    return row.product_name;
                }
            },
            {
                name: 'fullname',
                class: 'text-left',
                data: function (row, type, data, meta) {
                    if (row.fullname != '')
                    {
                        return row.fullname;
                    }
                    else
                    {
                        return null;
                    }
                }
            },
            {
                name: 'reason',
                class: 'text-center',
                data: function (row, type) {
                    return row.category;
                }
            },
            {
                class: 'text-center',
                name: 'return_type',
                data: function (row, type) {
                    var type = '';
                    return row.return_type;
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '<div class="btn-group">';
                    action_buttons = action_buttons + '<button type="button" class="btn btn-xs btn-primary ">Action</button>';
                    action_buttons = action_buttons + '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    action_buttons = action_buttons + '<ul class="dropdown-menu pull-right text-left" role="menu">';
                    action_buttons = action_buttons + '<li><a href="" class="view"  data-remote="false" data-toggle="modal"">View Details</a></li>';
                    action_buttons = action_buttons + '</ul></div>';
                    return action_buttons;
                }
            }
        ]
    });
    $('#search').click(function () {
        $('#full_name,#code_order').prop('checked', true);
        DT.fnDraw();
    });
    $(document).on('click', '#clear_filter', function (e) {
        $('#start_date').val('');
        $('#end_date').val('');
        $('#search_term').val('');
    });
    $(document).on('click', '.view', function (e) {
        $('#details').show();
        $('#list').hide();
    });
    $('#shipment_details').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        rules: {
            'shipment[courier_id]': 'required',
            'shipment[mode_id]': 'required',
            'shipment[bill_number]': 'required',
            'shipment[remarks]': 'required',
            'shipment[weight]': 'required'
        },
        messages: {
            'shipment[courier_id]': 'Please Select your Courier',
            'shipment[mode_id]': 'Please select your mode of Shipment',
            'shipment[bill_number]': 'Please enter your bill number',
            'shipment[remarks]': 'Please enter your remaks',
            'shipment[weight]': 'Please enter the parsel weight'
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $.ajax({
                url: $(form).attr('action'),
                data: $(form).serialize(),
                success: function (res) {
                    $('.message').html(res.msg);
                    if (res['status'] == 'OK') {
                        DT.fnDraw();
                        $('#shipment_details_modal').modal('hide');
                    }
                }
            });
        }
    });
});
