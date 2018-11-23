$(document).ready(function () {
    $('#uname,#phone,#code').prop('checked', true);
    var DT = $('#stores_list_table').dataTable({
        ajax: {
            url: $('#stores_list_form').attr('action'),
            data: function (d) {
                return $.extend({}, d, $('#stores_list_form input').serializeObject());
            }
        },
        columns: [
            {
                data: 'store_name',
                name: 'store_name',
            },
            {
                data: 'company_name',
                name: 'company_name',
            },
            {
                data: 'store_code',
                name: 'store_code',
                class: 'text-left',
            },
            {
                data: 'mobile_no',
                name: 'mobile_no',
                class: 'text-left'
            },
            {
                data: 'address',
                name: 'address',
                class: 'text-left'
            },
            {
                name: 'city',
                data: 'city',
                class: 'text-center',
            },
			{
                name: 'status',
                data: 'status',
                class: 'text-center',
            },
            {
                data: 'updated_on',
                name: 'updated_on',
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
    $('#stores_list_table').on('click', '.actions', function (e) {
        e.preventDefault();
        $('.alert').remove();
        var Ele = $(this), data = Ele.data();
        if (Ele.data('confirm') == undefined || (Ele.data('confirm') != null && Ele.data('confirm') != '' && confirm(Ele.data('confirm')))) {
            if (data.confirm != undefined) {
                delete data.confirm;
            }
            $.ajax({
                url: Ele.attr('href'),
                data: data,
                success: function (data) {
                    DT.fnDraw();
                }
            });
        }
    });
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
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
                $('#stores_list').hide();
                $('.panel-title').text('Update Store');
                $('#save_manage').text('Update');
                $('#save_manage').attr('id', 'update');
                $.each(res.data, function (key, val) {
                    $('#' + key).val(val);
                    if (key == 'working_days') {
                        if (val) {
                            var days = val.split(',');
                            $.each(days, function (k, v) {
                                $('#working_days' + v).prop('checked', true);
                            });
                        }
                    }
                    $('#store_create').show();
                });
                $(document.body).on('click', '#update', function (event) {
                    event.preventDefault();
                    CURFORM = $('#create_stores');
                    $.ajax({
                        url: window.location.BASE + 'supplier/stores/save/' + res.data.store_code,
                        data: $('#create_stores').serialize(),
                        success: function (data) {
                            if (data.status == 'OK')
                            {
                                $('#msg').append(data.msg);
                                DT.fnDraw();
                                $('#cancel_btn').trigger('click');
                            }
                        }
                    })
                });
            }
        });
    });
    $(document.body).on('submit', '#create_stores', function (event) {
        event.preventDefault();
        var datastring = $('#create_stores').serialize();
        CURFORM = $('#create_stores');
        var url = $(this).attr('href');
        $.ajax({
            url: window.location.API.SELLER + 'stores/save',
            data: datastring,
            success: function (data) {
                if (data.status == 'OK')
                {
                    $('#msg').append(data.msg);
                    DT.fnDraw();
                    $('#cancel_btn').trigger('click');
                }
            }
        });
    });
    $(document.body).on('click', '#cancel_btn', function (event) {
        event.preventDefault();
        $('#store_create').hide();
        $('#stores_list').show();
    });
    $(document.body).on('click', '#add_stores', function (event) {
        event.preventDefault();
        $('#create_stores')[0].reset();
        $('#store_create').show();
        $('#stores_list').hide();
        /*   $('#country_id').loadSelect({
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
         $('#city_id').loadSelect({
         url: window.location.BASE + 'city-list',
         key: 'city_id',
         value: 'city_name',
         dependingSelector: ['#state_id']
         }); */
    });
    var CS = $('#create_stores');
    $('#postal_code', CS).on('change', function () {
        var pincode = $('#postal_code', CS).val();
        if (pincode != '' && pincode != null) {
            $.ajax({
                url: window.location.BASE + 'check-pincode',
                data: {pincode: pincode},
                success: function (OP) {
                    $('#country_id, #state_id, #city_id', CS).prop('disabled', false).empty();
                    $('#country_id', CS).append($('<option>', {value: OP.country_id}).text(OP.country)).trigger('change');
                    $('#state_id', CS).append($('<option>', {value: OP.state_id}).text(OP.state)).trigger('change');
                    $.each(OP.cities, function (k, e) {
                        $('#city_id', CS).append($('<option>', {value: e.id}).text(e.text));
                    });
                    $('#city_id option:last').attr('selected', true);
                    $('#country_id, #state_id, #city_id', CS).trigger('change');
                },
                error: function () {
                    $('#country_id, #state_id, #city_id', CS).val('').prop('disabled', true).empty();
                }
            });
        }
    });
    function makeInitialTextReadOnly(input) {
        var readOnlyLength = input.value.length;
        input.addEventListener('keydown', function (event) {
            var which = event.which;
            if (((which == 8) && (input.selectionStart <= readOnlyLength))
                    || ((which == 46) && (input.selectionStart < readOnlyLength))) {
                event.preventDefault();
            }
        });
        input.addEventListener('keypress', function (event) {
            var which = event.which;
            if ((event.which != 0) && (input.selectionStart < readOnlyLength)) {
                event.preventDefault();
            }
        });
        input.addEventListener('cut', function (event) {
            if (input.selectionStart < readOnlyLength) {
                event.preventDefault();
            }
        });
        input.addEventListener('paste', function (event) {
            if (input.selectionStart < readOnlyLength) {
                event.preventDefault();
            }
        });
    }
    makeInitialTextReadOnly(document.getElementById('website'));
    $('#firstname, #lastname', CS).on('keypress', function (e) {
        var code = e.charCode ? e.charCode : e.keyCode;
        if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 116 || code == 32 || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
            return true;
        }
        return false;
    });
});
