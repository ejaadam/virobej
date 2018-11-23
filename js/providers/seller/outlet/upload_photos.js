$(function () {
    $('#upload-photos-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM = $(this);
        console.log(CURFORM);
        if ($('#upload-photos-form input[type="file"]')[0].files.length > 0)
        {

            CURFORM.ajaxSubmit({
                url: CURFORM.attr('action'),
                type: 'POST',
                dataType: "json",
                success: function (op) {
                    $('input,select', '#upload-photos-form').not('[type="hidden"]').val('');
                    $('#store-form-panel #close-form-panel').trigger('click');
                    DT.dataTable().fnDraw();
                }
            });
        }
        else
        {
            $('#upload-photos-form #file_err').attr('class', '').empty().addClass('errmsg').text('Please select file.');
        }
    });

    $('#upload-photos-form input[type="file"]').on('change', function (e) {
        if ($('#upload-photos-form input[type="file"]')[0].files.length > 0)
        {
            $('#upload-photos-form #file_err').attr('class', '').empty();
        }
    });

    var STORE = {}, CODE = null;
    STORE.IMAGE = {};
    STORE.IMAGE.LIST = $('#store-image-list-table');
    STORE.IMAGE.LIST.FORM = $('#store_image_list');

    var DT = STORE.IMAGE.LIST.dataTable({
        "ordering": false,
        ajax: {
            url: $('#store_image_list').attr('action'),
            data: function (d) {
                d.store_code = $('#store_code').val();
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                class: 'text-center',
            },
            {
                name: 'file_path',
                class: 'text-center col-sm-2',
                data: function (row, type, set) {
                    return '<img class="img img-responsive img-thumbnail" src="' + row.file_path + '" alt="' + row.store_name + '"/> ';
                }
            },
            {
                name: 'status',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.status_class + '">' + row.status + '</span> ';
                }
            },
            {
                name: 'is_verified',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<span class="label label-' + row.is_verified_class + '">' + row.is_verified + '</span> ';
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);
                }
            }
        ],
		
    });

    STORE.IMAGE.LIST.on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (op) {
            DT.dataTable().fnDraw();
        });
    });

    STORE.IMAGE.LIST.FORM.on('submit', function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
    STORE.IMAGE.LIST.FORM.on('click', '.remove_img', function (e) {
        $.post($(this).attr('data'),
                function (data, status) {
                    DT.fnDraw();
                });
    });
	
    $('#store-img-panel').on('click', '#add-store-imgs', function (e) {		
        e.preventDefault();
        $('#store-img-panel').pageSwapToRight('#store-form-panel');
        $('input,select', '#upload-photos-form').not('[type="hidden"]').val('');

    });
    $('#store-form-panel').on('click', '#close-form-panel', function (e) {
        e.preventDefault();
        $('input,select', '#upload-photos-form').not('[type="hidden"]').val('');
        $('#store-form-panel').pageSwapToRight('#store-img-panel');

    });
});
