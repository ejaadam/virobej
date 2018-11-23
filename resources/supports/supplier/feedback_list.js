
$(document).ready(function () {
    $('.alert').remove();
    $('#feedback123').on('click', function (e) {
        e.preventDefault();
        var curLine=$(this);
        $.ajax({
            url: 'description',
            beforeSend: function (res) {
                $('#edit_data2 .modal-body').html('<p>Loading in progress...</p>');
                $('#edit_data2').modal();
            },
            success: function (res) {
                $('#edit_data2 .modal-body').html(res.contents);
                $('#edit_data2').modal();
            }
        });
    });
});
