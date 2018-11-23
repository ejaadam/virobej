$(document).ready(function () {
    var DT = $('#dt_basic').dataTable({
        ajax: {
            //'url': '{{URL::to('admin/company/list')}}'
            data: function (d) {
                return $.extend({}, d, {
                    'search_term': $('#search_term').val()
                });
            }
        },
        columns: [
            {
                name: 'full_name',
                data: 'full_name'
            },
            {
                name: 'email',
                data: 'email'
            },
            {
                name: 'mobile',
                data: 'mobile'
            },
            {
                name: 'p_order',
                data: 'p_order'
            },
        ]
    });
    $('#search').click(function () {
        DT.fnDraw();
    });
    $(document).on('click', '#clear_filter', function (e) {
        $('#start_date').val('');
        $('#end_date').val('');
        $('#search_term').val('');
    });
    $(document.body).on('click', '.inprogress,.dispatched,.delivered,.completed,.cancelled', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        if (confirm('Are you sure you want to ' + CurEle.text() + '?')) {
            $.ajax({
                url: CurEle.attr('href'),
                data: {status: CurEle.data('status')},
                success: function (res) {
                    if (res['status'] == 'OK') {
                        DT.fnDraw();
                    }
                }
            });
        }
    });
    $(document).on('click', '.view', function (e) {
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            beforeSend: function () {
                $('#order_details').modal();
            },
            success: function (res) {
                $('.modal-body').html(res.contents);
            }
        });
    });
});
