$(document).ready(function () {
    var MIF = $('#meta_info_form');
    $('#meta_keys', MIF).select2({
        tags: true,
        minimumInputLength: 3,
        tokenSeparators: [',', ' ']
    });
    MIF.validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        rules: {
            'meta_info[url_slug]': 'required'
        },
        messages: {
            'meta_info[url_slug]': 'Please enter the URL Slug'
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            $.ajax({
                data: $(form).serialize(),
                url: $(form).attr('action'),
                success: function (data) {
                    $(form).before(data.msg);
                    if (data.status == 'OK') {
                        setTimeout(function () {
                            $('.back-btn', $(form)).trigger('click');
                        }, 1000);
                    }
                }
            });
        }
    });
    $(document.body).on('click', '.meta-info', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            data: {post_type_id: CurEle.data('post_type_id'), relative_post_id: CurEle.data('relative_post_id')},
            url: window.location.BASE + 'admin/seller/meta-info',
            success: function (data) {
                MIF.trigger('reset');
                $('.alert').remove();
                $('#meta_form .panel-title').html(CurEle.attr('title'));
                $('#relative_post_id', MIF).val(CurEle.data('relative_post_id'));
                $('#post_type_id', MIF).val(CurEle.data('post_type_id'));
                if (data != []) {
                    $('#url_slug', MIF).val(data.url_slug);
                    $('#description', MIF).val(data.description);
                    $('#meta_keys', MIF).val(data.meta_keys);
                    $('#tags', MIF).val(data.tags);
                }
                $('#meta_keys', MIF).select2({
                    tags: true,
                    minimumInputLength: 3,
                    tokenSeparators: [',', ' ']
                });
                $('#list').hide();
                $('#meta_form').show();
            }
        });
    });
    MIF.on('click', '.back-btn', function (e) {
        e.preventDefault();
        $('#list').show();
        $('#meta_form').hide();
    });
});
