$(document).ready(function () {
    var RLF = $('#reviews_list_form'), RL = $('#reviews_list');
    var DT = RL.dataTable({
        ajax: {
            url: window.location.API.SUPPLIER + 'reviews',
            data: function (d) {
                return $.extend({}, d, {
                    search_term: $('#search_term', RLF).val()
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                class: 'text-center'
            },
            {
                data: 'full_name',
                name: 'full_name'
            }, {
                data: 'title',
                name: 'title'
            },
            {
                data: 'description',
                name: 'description',
                class: 'text-center',
            },
            {
                data: 'rating',
                name: 'rating',
                class: 'text-right',
            },
            {
                data: 'likes_count',
                name: 'likes_count',
                class: 'text-right',
            },
            {
                data: 'unlikes_count',
                name: 'unlikes_count',
                class: 'text-right',
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center',
            },
            {
                data: 'verification',
                name: 'verification',
                class: 'text-center',
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
    $('#search', RLF).click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    RL.on('click', '.actions', function (e) {
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
});
