$(document).ready(function () {
    var DT = $('#table3').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, {
                    'search_term': $('#search_term').val()
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'sc.created_on',
                class: 'text-center'
            },
            {
                data: 'courier',
                name: 'courier',
            },
            {
                class: 'text-center',
                orderable: false,
                data: 'parent_category_id',
                name: 'parent_category_id',
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '';
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button"  class="btn btn-xs btn-primary ">Action</button>';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                    action_buttons += '<li><a href="' + json.url + '/supplier/couriers/details" data-courier_id="' + row.courier_id + '" class="edit">Edit</a></li>';
                    action_buttons += '<li><a href="' + json.url + '/supplier/couriers/modes" data-courier_id="' + row.courier_id + '" data-courier="' + row.courier + '" class="modes">Modes</a></li>';
                    action_buttons += '<li><a href="' + json.url + '/supplier/couriers/delete" data-courier_id="' + row.courier_id + '" class="delete">Delete</a></li>';
                    action_buttons += '</ul></div>';
                    return action_buttons;
                }
            }
        ]
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#add_courier').on('click', function (e) {
        e.preventDefault();
        $('#courier_id', $('#add_courier_form')).parents('.form-group').show();
        $('#new_courier', $('#add_courier_form')).hide();
        $('select#courier_id').loadSelect({
            url: window.location.BASE + 'supplier/couriers/courier-list-to-add',
            key: 'courier_id',
            value: 'courier'
        });
        $('#courier_modal .modal-title').text('Add Courier');
        $('#add_courier_form').trigger('reset');
        $('#courier_modal').modal();
    });
    $('#new_courier_btn').click(function () {
        $('#country_id').loadSelect({
            url: window.location.BASE + 'countries-list',
            key: 'country_id',
            value: 'country'
        });
        $('#state_id').loadSelect({
            url: window.location.BASE + 'states-list',
            key: 'state_id',
            value: 'state',
            dependingSelector: ['#country_id']
        });
        $('#courier_id', $('#add_courier_form')).parents('.form-group').hide();
        $('#new_courier', $('#add_courier_form')).show();
    });
    $('#add_courier_form').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        rules: {
            'courier[courier]': 'required',
            'courier[address1]': 'required',
            'courier[city]': 'required',
            'courier[state_id]': 'required',
            'courier[country_id]': 'required',
            'courier[phone]': 'required',
            'courier[email]': 'required'
        },
        messages: {
            'courier[courier]': 'Please enter the Courier Name',
            'courier[address1]': 'Please enter the address',
            'courier[city]': 'Please enter ther city',
            'courier[state_id]': 'Please select the state',
            'courier[country_id]': 'Please select the Country',
            'courier[phone]': 'please enter the Phone no',
            'courier[email]': 'Please enter Email ID'
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $('#message').empty();
            $.ajax({
                data: $(form).serialize(),
                url: $(form).attr('action'),
                success: function (data) {
                    $('#message').html(data.msg);
                    $('#courier_modal').modal('hide');
                    if (data.status == 'OK') {
                        DT.fnDraw();
                    }
                }
            });
        }
    });
    $('#table3').on('click', '.edit', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('select#courier_id').loadSelect({
            url: window.location.BASE + 'supplier/couriers/courier-list-to-add',
            key: 'courier_id',
            value: 'courier'
        });
        $.ajax({
            data: {courier_id: CurEle.data('courier_id')},
            url: CurEle.attr('href'),
            success: function (data) {
                $('#message').html(data.msg);
                if (data.status == 'OK') {
                    $('#courier_id', $('#add_courier_form')).parents('form-group').hide();
                    $('#new_courier', $('#add_courier_form')).show();
                    $('#courier_id', $('#add_courier_form')).val(data.courier.courier_id);
                    $('#courier', $('#add_courier_form')).val(data.courier.courier);
                    $('#address1', $('#add_courier_form')).val(data.courier.address1);
                    $('#address2', $('#add_courier_form')).val(data.courier.address2);
                    $('#city', $('#add_courier_form')).val(data.courier.city);
                    $('#email', $('#add_courier_form')).val(data.courier.email);
                    $('#phone', $('#add_courier_form')).val(data.courier.phone);
                    $('#fax', $('#add_courier_form')).val(data.courier.fax);
                    $('#country_id').loadSelect({
                        url: window.location.BASE + 'countries-list',
                        key: 'country_id',
                        value: 'country',
                        selected: data.courier.country_id
                    });
                    $('#state_id').loadSelect({
                        url: window.location.BASE + 'states-list',
                        key: 'state_id',
                        value: 'state',
                        selected: data.courier.state_id,
                        dependingSelector: ['#country_id']
                    });
                    $('#courier_modal .modal-title').text('Edit Courier');
                    $('#courier_modal').modal();
                }
            }
        });
    });
    $('#table3').on('click', '.delete', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#message').empty();
        if (confirm('Are you sure Want to delete this Courier?')) {
            $.ajax({
                data: {courier_id: CurEle.data('courier_id')},
                url: CurEle.attr('href'),
                success: function (data) {
                    $('#message').html(data.msg);
                    if (data.status == 'OK') {
                        DT.fnDraw();
                    }
                }
            });
        }
    });
    $('#table3').on('click', '.modes', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#message').empty();
        $('#courier_id', $('#add_mode_form')).val(CurEle.data('courier_id'));
        $('#modes_table', $('#courier_modes_modal')).show();
        $('#add_mode_form', $('#courier_modes_modal')).hide();
        $.ajax({
            data: {courier_id: CurEle.data('courier_id')},
            url: CurEle.attr('href'),
            success: function (data) {
                if (data.status == 'OK') {
                    var content = '';
                    for (var i in data.modes) {
                        content += '<tr><td>' + data.modes[i].mode + '</td>';
                        content += '<td class="text-right">'
                        content += '<a href="#" class="btn btn-xs btn-info edit_mode" data-mode="' + data.modes[i].mode + '" data-mode_id="' + data.modes[i].mode_id + '" data-courier_id="' + CurEle.data('courier_id') + '"><span class="icon-edit"></span></a>';
                        content += '<a href="#" class="btn btn-xs btn-info delete_mode" data-mode="' + data.modes[i].mode + '" data-mode_id="' + data.modes[i].mode_id + '" data-courier_id="' + CurEle.data('courier_id') + '"><span class="icon-trash"></span></a>';
                        content += '</td></tr>';
                    }
                    $('#courier_modes_modal .modal-title').text(CurEle.data('courier') + '\'s Modes');
                    $('#modes_table tbody', $('#courier_modes_modal')).html(content);
                    $('#courier_modes_modal').modal();
                }
                else {
                    $('#message').html(data.msg);
                }
            }
        });
    });
    $('#add_mode_btn').click(function () {
        $('#add_mode').modal();
        $('#modes_table', $('#courier_modes_modal')).hide();
        $('#add_mode_form', $('#courier_modes_modal')).show();
        $('#add_mode_form', $('#courier_modes_modal')).trigger('reset');
    });
    $('#modes_table').on('click', '.edit_mode', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#add_mode_form', $('#courier_modes_modal')).trigger('reset');
        $('#courier_id', $('#add_mode_form')).val(CurEle.data('courier_id'));
        $('#mode_id', $('#add_mode_form')).val(CurEle.data('mode_id'));
        $('#mode', $('#add_mode_form')).val(CurEle.data('mode'));
        $('#modes_table', $('#courier_modes_modal')).hide();
        $('#add_mode_form', $('#courier_modes_modal')).show();
        $('#add_mode').modal();
    });
    $('#modes_table').on('click', '.delete_mode', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#message').empty();
        if (confirm('Are you sure Want to delete this ' + CurEle.data('mode') + '?')) {
            $.ajax({
                data: {mode_id: CurEle.data('mode_id')},
                url: window.location.BASE + 'admin/couriers/modes/delete',
                success: function (data) {
                    $('#message').html(data.msg);
                    if (data.status == 'OK') {
                        $('.modes[data-courier_id="' + CurEle.data('courier_id') + '"]').trigger('click');
                    }
                }
            });
        }
    });
    $('.back-to-mode-list').click(function (e) {
        e.preventDefault();
        $('#modes_table', $('#courier_modes_modal')).show();
        $('#add_mode_form', $('#courier_modes_modal')).hide();
    });
    $('#add_mode_form').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        rules: {
            'mode[mode]': 'required'
        },
        messages: {
            'mode[mode]': 'Please enter the Mode Name'
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $('#message').empty();
            $.ajax({
                data: $(form).serialize(),
                url: $(form).attr('action'),
                success: function (data) {
                    $('#message').html(data.msg);
                    if (data.status == 'OK') {
                        $('#add_mode_form', $('#courier_modes_modal')).hide();
                        $('a.modes[data-courier_id="' + $('#courier_id', $(form)).val() + '"]').trigger('click');
                        $('#modes_table', $('#courier_modes_modal')).show();
                    }
                }
            });
        }
    });
});
