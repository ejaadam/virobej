$(document).ready(function () {
    var categories = [], ACB = $('#add_category_btn'), add_category_section = $('#add_category_section'), categories_list = $('#categories_list');
    var DT = $('table', categories_list).dataTable({
        ajax: {
            url: window.location.API.SELLER + 'catalog/categories',
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
                data: 'category',
                name: 'category'
            },
            {
                data: 'parent_category',
                name: 'parent_category'
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
    $('table', categories_list).on('click', '.actions', function (e) {
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
    ACB.click(function (e) {
        e.preventDefault();
        $('.err_msg').empty();
        $('#msg').empty();
        $('#category_id').loadSelect({            
            url: window.location.API.SELLER + 'catalog/categories/available-categories',
            data: {supplier_id: $("#supplier_id").val()},
            key: 'id',
            notexistIn: categories,
            value: 'name',
            success: function () {
                categories_list.fadeOut(function () {
                    add_category_section.fadeIn();
                });
            }
        });
    });
    $('#add_existing_categories').validate({
        submitHandler: function (form, event) {
            event.preventDefault();
            if ($(form).valid()) {
                CURFORM = $('#brand_form');
                $.ajax({
                    url: $('#add_existing_categories').attr('action'),
                    data: $('#add_existing_categories').serialize(),
                    beforeSend: function () {
                        $('button[type="submi"]', $(form)).val('Processing...').attr('disabled', 'disabled');
                    },
                    success: function (data) {
                        DT.fnDraw();
                        $('#categories_list').show();
                        $('#add_category_section').hide();
                        $('.panel-title').html('Category List');
                        $('button[type="submit"]', $(form)).removeAttr('disabled');
                    }
                });
            }
        }
    });
    $('#close_category_list').on('click', function (e) {
        e.preventDefault();
        add_category_section.fadeOut(function () {
            categories_list.fadeIn();
        });
    });

});
