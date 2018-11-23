$(document).ready(function () {
    var PF = $('#preferences-form');
    PF.on('submit', function (e) {
        e.preventDefault();
        $('.alert').remove();
        $.ajax({
            url: PF.attr('action'),
            data: PF.serialize(),
            beforeSend: function () {
                $('input[type="submit"]', PF).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
                PF.before(OP.msg);
                $('input[type="submit"]', PF).removeAttr('disabled', true).val('Save');
            },
            error: function (jqXhr) {
                $('input[type="submit"]', PF).removeAttr('disabled', true).val('Save');
                if (jqXhr.status == 422) {
                    var data = jqXhr.responseJSON;
                    PF.appendLaravelError(data.error);
                }
                else {
                    alert('Something went wrong');
                }
            }
        });
    });
});
