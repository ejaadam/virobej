$(document).ready(function () {
    var UF = $('#upload_form');
    $('#type_filer').loadSelect({
        url: window.location.BASE + 'seller/doc-list',
        key: 'document_type_id',
        value: 'type'
    });
    $('#document_type_id').loadSelect({
        url: window.location.BASE + 'seller/doc-list',
        key: 'document_type_id',
        value: 'type',
        optionData: [{key: 'other-fields', value: 'other_fields'}]
    });
    $('#document_type_id').on('change', function () {
        var CurEle = $(this);
        var other_fields = $('option:selected', CurEle).attr('data-other-fields');
        other_fields = other_fields != undefined ? $.parseJSON(other_fields) : null;
        $('.other-fields').remove();
        if (other_fields != undefined && ! other_fields != null) {
            $.each(other_fields, function (k, e) {
                CurEle.closest('.row').after($('<div>').attr({class: 'row other-fields'}).append([
                    $('<label>').attr({class: 'control-label col-md-2', for : e.id}).html(e.label),
                    $('<div>').attr({class: 'col-md-3'}).append(function () {
                        return $('<input>').attr({class: 'form-control', id: e.id, name: 'other_fields[' + e.id + ']', placeholder: e.label, type: e.type}).html(e.label);
                    })
                ]));
            });
        }
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
        var Curele = $(this);
        CURFORM = UF;
        $.ajax({
            data: new FormData(this),
            url: Curele.attr('action'),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                DT.fnDraw();
            }
        });
    });
    var DT = $('#image_verify_list').dataTable({
        ajax: {
            url: $('#product_list_form').attr('action'),
            data: function (d) {
                return $.extend({}, d, {
                    type_filer: $('#product_list_form #type_filer').val(),
                    from: $('#product_list_form #from').val(),
                    to: $('#product_list_form #to').val()
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'av.created_on',
                class: 'text-center'
            },
            {
                data: 'path',
                name: 'path',
                class: 'text-center col-xs-12 col-sm-1',
                render: function (data, type, row, meta) {
                    var url = '';
                    url = window.location.BASE + 'assets/uploads/supplier_verify_doc/' + row.path;
                    return (row.content_type == 'Image')
                            ? $('<a>', {class: 'pull-left', href: url}).append([$('<img>', {src: url, alt: '', class: 'img img-thumbnail media-object'})])[0].outerHTML
                            : $('<a>', {class: 'btn btn-sm btn-info pull-left', href: url}).append('Click To Dowload File')[0].outerHTML;
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
                data: 'status',
                name: 'status',
                class: 'text-center status',
                render: function (data, type, row, meta) {
                    var content;
                    return (row.status === 1)
                            ? $('<span>', {class: 'label label-success'}).append('Verified')[0].outerHTML
                            : (row.status === 0
                                    ? $('<span>', {class: 'label label-danger'}).append('Pending')[0].outerHTML
                                    : (row.status === 2
                                            ? $('<span>', {class: 'label label-danger'}).append('Cancelled')[0].outerHTML
                                            : ''));
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
        DT.fnDraw();
    });
    $(document).on('click', '.delete', function (event) {
        event.preventDefault();
        $.ajax({
            data: {uv_id: $(this).data('id')},
            url: window.location.BASE + 'seller/delete_doc',
            success: function (data) {
                DT.fnDraw();
            }
        });
    });
});
