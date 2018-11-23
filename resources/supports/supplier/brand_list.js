$(document).ready(function () {
    var DT = $('#brands-table').dataTable({
        ajax: {
            url: window.location.API.SELLER + 'catalog/brands',
            data: function (d) {
                return $.extend({}, d, {
                    search_term: $('#search_term').val()
                });
            }
        },
        columns: [
            {
                data: 'updated_on',
                name: 'updated_on',
                class: 'text-center'
            },
            {
                data: 'brand_name',
                name: 'brand_name',
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return $('<span>', {class: row.status_class}).text(row.status)[0].outerHTML;
                }
            },
            {
                data: 'verification',
                name: 'verification',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return $('<span>', {class: 'label label-' + row.verification_class}).text(row.verification)[0].outerHTML;
                }
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
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    $('#brands-table').on('click', '.actions', function (e) {
        e.preventDefault();
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
    $('#create_brand').click(function (e) {
        e.preventDefault();
        $('#add_brand_section').show();
        $('#brand_list').hide();
        $('.err_msg').empty();
    });
    $('#close-add-brand-btn').on('click', function (e) {
        e.preventDefault();
        $('#add_brand_section').hide();
        $('#brand_list').show();
    });
    $('#brand_form').on('submit', function (e) {
        e.preventDefault()
        CURFORM = $('#brand_form');
        $.ajax({
            url: window.location.API.SELLER + 'catalog/brands/new',
            data: CURFORM.serialize(),
            success: function (data) {
                $('#add_brand_section').hide();
                $('#brand_list').show();
                DT.fnDraw();
            }
        });
    });
});
