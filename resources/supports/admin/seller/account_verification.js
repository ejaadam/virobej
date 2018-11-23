$(document).ready(function () {
    var DT = $('#image_verify_list').dataTable({
        ajax: {
            url: $('#verification_docs_list').attr('action'),
            data: function (d) {
                return $.extend({}, d, {
                    type_filer: $('#verification_docs_list #type_filer').val(),
                    search_term: $('#verification_docs_list #search_term').val(),
                    status: $('#verification_docs_list #status').val(),
                    from: $('#verification_docs_list #from').val(),
                    to: $('#verification_docs_list #to').val(),
                    uname: $('#verification_docs_list #uname').val(),
                    account_id: $('#verification_docs_list #account_id').val()
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'spi.created_on',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat('dd-mmm-yyyy HH:MM:ss');
                }
            },
            {
                data: 'name',
                name: 'name',
                render: function (data, type, row, meta) {
                    var html = '<a target="_blank" href="' + window.location.BASE + 'admin/seller/details/' + row.uname + '">' + row.full_name + ' (' + row.uname + ')<a>';
                    return html;
                }
            },
            {
                data: 'type',
                name: 'type',
                render: function (data, type, row, meta) {
                    return $('<ul>').attr({class: 'list-unstyled'}).append(function () {
                        var elments = [];
                        elments.push($('<li>').html(row.type));
                        elments.push($('<li>').append([$('<b>').html('Type'), ': ', row.content_type]));
                        $.each(row.other_fields, function (k, e) {
                            elments.push($('<li>').append([$('<b>').html(e.label), ': ', e.value]));
                        });
                        return elments;
                    })[0].outerHTML;
                }
            },
            {
                data: 'path',
                name: 'path',
                class: 'text-center col-sm-2',
                render: function (data, type, row, meta) {
                    var url = '';
                    var content = '';
                    url += window.location.BASE + 'assets/uploads/supplier_verify_doc/' + row.path;
                    if (row.content_type == 'Image') {
                        content = '<a class = "pull-left" href = "' + url + '"><img src = "' + url + '" alt = "" class = "media-object img img-thumbnail"></a>';
                    }
                    else {
                        content = '<a class = "btn btn-xs btn-info pull-left" target="_blank" href = "' + url + '"><i class="fa fa-download"></i></a>';
                    }
                    return content;
                }
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center status',
                render: function (data, type, row, meta) {
                    var content = '';
                    if (row.status_id == 1)
                    {
                        content = '<span class="label label-success">Approved</span>';
                    }
                    if (row.status_id == 0)
                    {
                        content = '<span class="label label-danger">Pending</span>';
                    }
                    if (row.status_id == 2)
                    {
                        content = '<span class="label label-danger">Rejected</span>';
                    }
                    return content;
                }
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                    if (row.status_id != 1) {
                        action_buttons += '<li><a data-id="' + row.uv_id + '" data-status="1" class="change_status" href="javascript:void(0)">Verified</a></li>';
                    }
                    if (row.status_id != 1) {
                        action_buttons += '<li><a data-id="' + row.uv_id + '" data-status="2" class="change_status" href="javascript:void(0)">Rejected</a></li>';
                    }
                    action_buttons += '<li><a data-id="' + row.uv_id + '" class="delete" href="javascript:void(0)">Delete</a></li>';
                    action_buttons += '</ul></div>';
                    return action_buttons;
                }
            }
        ]
    });
    $('#type_filer').loadSelect({
        url: window.location.BASE + 'admin/seller/doc-list',
        key: 'document_type_id',
        value: 'type',
    });
    $('.upload').change(function () {
        var uploadFile = $(this).val();
        var valArr = uploadFile.split('.');
        txtext = uploadFile.split('.')[(valArr.length) - 1];
        txtext = txtext.toLowerCase();
        doctypes = $(this).data('format');
        fformats = doctypes.split('|');
        if (fformats.indexOf(txtext) == - 1) {
            $(this).val('');
            alert('Invalide! - Available file types are ' + '(' + doctypes + ').');
            return false;
        }
        $(this).closest('.form-group').find('.file_err').empty();
        var doc_type = $(this).closest('.row').find('.select_type');
        if (uploadFile != '') {
            if (doc_type.val() != '') {
                $('#sent_doc').attr('disabled', false);
            } else {
                $(this).closest('.row').find('.doc_err').html('Plese select the Document Type')
                $('#sent_doc').attr('disabled', true);
                return false;
            }
        } else {
            $('#sent_doc').attr('disabled', true);
            return false;
        }
    });
    $('#upload_form').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            data: new FormData(this),
            url: window.location.BASE + 'seller/save_document',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                DT.fnDraw();
            }
        });
    });
    $('#search').click(function (e) {
        DT.fnDraw();
    });
    $(document).on('click', '.change_status', function (event) {
        event.preventDefault();
        if (confirm('Are you Sure?')) {
            $.ajax({
                data: {status: $(this).data('status'), uv_id: $(this).data('id')},
                url: window.location.BASE + 'admin/seller/change_status',
                success: function (data) {
                    DT.fnDraw();
                }
            });
        }
    });
    $(document).on('click', '.delete', function (event) {
        event.preventDefault();
        if (confirm('Are you Sure, You wants to delete it?')) {
            $.ajax({
                data: {uv_id: $(this).data('id')},
                url: window.location.BASE + 'admin/seller/delete_doc',
                success: function (data) {
                    DT.fnDraw();
                }
            });
        }
    });
});
