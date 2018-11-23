
$(document).ready(function () {
    var DT = $('#feedback_list').dataTable({
        ajax: {
            //'url': window.location.BASE + 'admin/feedback/lists',
            data: function (d) {
                return $.extend({}, d, {
                    term: $('#search_term').val(),
                    from: $('#from_date').val(),
                    to: $('#to_date').val(),
                    feedback_type: $('#feedback_type').val(),
                    //status_id: $('#status_id', $('#training_form')).val()
                });
            }
        },
        columns: [
            {
                data: 'subject',
                name: 'subject',
                orderable: false,
                render: function (data, type, row, meta) {
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var content = '';
                    content += '<div class="col-sm-12 " title="' + row.subject + '">';
                    content += '<div class="panel panel-default box">';
                    content += '<div class="col-sm-12">';
                    content += '<h5 class="text-left" style="padding-left:10px"><a href="' + json.url + '/admin/feedback/details/' + row.feedback_id + '" data="' + row.feedback_id + '"  class="details1">' + row.subject + '</a>';
                    content += '<div class="btn-group pull-right"><button type="button" class="btn btn-xs btn-primary details"><span class="processing' + row.subscription_id + '"></span>Action</button><button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (row.status == 0)
                    {
                        content += '<li><a href="' + json.url + '/supplier/feedback/replied/' + row.feedback_id + '"class="replied" id="add-feedback-' + row.feedback_id + '" data-id="' + row.feedback_id + '" data-account="' + row.account_id + '"  id="' + row.company_id + '"> View Replies </a></li>';
                    }
                    if (row.account_type_id == 1)
                    {
                        content += '<li><a href="' + json.url + '/supplier/feedback/reply/save/' + row.feedback_id + '"class="reply" data="' + row.feedback_id + '" data-description="' + row.description.substring(0, 200) + '"; data-account="' + row.account_id + '"  id="' + row.company_id + '">Reply</a></li>';
                    }
                    content += '</ul></div>';
                    content += '<div class="pull-right text-align"><span class="text-muted ">Created on : ' + new String(row.created_on).dateFormat("dd-mmm-yyyy  HH:MM:ss") + '</span>';
                    if (row.status == 0)
                    {
                        content += '&nbsp;<span class="label label-info"> Replied </span>';
                    }
                    if (row.status == 1)
                    {
                        content += '&nbsp;<span class="label label-success"> New </span>';
                    }
                    content += '</div></h5>';
                    content += '</div>';
                    content += '<div class="panel-footer"><div class="row">';
                    content += '<div class="col-sm-12"><p><span class="mR10"><b>Company:</b> ' + row.company_name + '</span><span class="mR10"><b>Member:</b> ' + (row.full_name) + ' (' + (row.uname) + ')</span></p><p>' + row.description + '</p></div>';
                    content += '<div class="col-sm-12" id="feedback_comment_list"></div></div>';
                    content += '</div></div>';
                    return content;
                }
            }
        ],
        initComplete: function (settings, json) {
            $('thead', $('#feedback_list')).remove();
        },
        drawCallback: function (e, settings) {
            var content = '';
            $.each($('tr td', $('#feedback_list')), function () {
                content += $(this).html();
                $(this).parent('tr').remove();
            });
            $('tbody', $('#feedback_list')).html('<tr><td>' + content + '</td></tr>');
        }
    });
    $('#search').on('click', function () {
        DT.fnDraw();
    });
    $(document.body).on('click', '.details1', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        //if (confirm('Are you sure you want to ' + CurEle.text() + '?')) {
        $.ajax({
            url: CurEle.attr('href'),
            success: function (res) {
                if (res['status'] == 'OK') {
                    $('#edit_data1 .modal-body').empty();
                    $('#edit_data1 .modal-body').html(res.contents);
                    $('#edit_data1').modal();
                }
            }
        });
    });
    $(document.body).on('click', '.reply', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#feedback_comment_list', CurEle.closest('.panel')).html('<hr/><h4>Leave Your Comments</h4><form method="post" class="form-horizontal form-validate" name="reply_user" id="reply_user" enctype="multipart/form-data"><div class="row"><div class="col-md-12"><label></label><textarea class="form-control" name = "description" id="description" rows="3"></textarea> <label></label><input type="submit" name="reply" id="reply_btn" class="btn btn-primary mR10" value="Reply" ><input type="button" id="cancel_btn" class="btn btn-danger mR10" value="cancel" ></div></div></form>');
        $(document.body).on('click', '#reply_btn', function (e) {
            e.preventDefault();
            var val = $('#description').val();
            if (val == '')
            {
                $('#description').after('<span class="danger">Please enter your Comments</span>');
            }
            else
            {
                var account = CurEle.data('account');
                //$('#company_id', $('#reply_user')).val(CurEle.attr('id'))
                //$('#account_id', $('#reply_user')).val(account)
                var datastring = $('#reply_user').serialize();
                $('.alert').remove();
                $.ajax({
                    url: CurEle.attr('href'),
                    data: datastring,
                    success: function (res) {
                        DT.fnDraw();
                        $('#feedback_comment_list', CurEle.closest('.panel')).empty();
                        $('#edit_data1').modal('hide');
                        $('#main_content').before(res.msg);
                    }
                });
            }
        });
        $(document.body).on('click', '#cancel_btn', function (e) {
            e.preventDefault();
            var CurEle = $(this);
            $('#feedback_comment_list', CurEle.closest('.panel')).empty();
        });
        //if (confirm('Are you sure you want to ' + CurEle.text() + '?')) {
    });
    $(document.body).on('click', '.replied', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        var f_id = CurEle.data('id')
        //if (confirm('Are you sure you want to ' + CurEle.text() + '?')) {
        $.ajax({
            url: CurEle.attr('href'),
            success: function (res) {
                if (res['status'] == 'OK') {
                    $('#replied_msg').html(res.contents);
                    $('#submit_new_feedbck').attr('class', 'btn btn-sm btn-danger pull-right close_btn').html('X');
                    $('#close_btn').prop('type', 'button');
                    $('#fbck_list').hide();
                    $('#submit_reply_feedback').on('click', function (e) {
                        e.preventDefault();
                        var val = $('#description').val();
                        if (val == '')
                        {
                            $('#description').after('<span class="danger">Please enter your Comments</span>');
                        }
                        else
                        {
                            var account = CurEle.data('account');
                            //$('#company_id', $('#reply_user')).val(CurEle.attr('id'))
                            //$('#account_id', $('#reply_user')).val(account)
                            var datastring = $('#replier_supplier').serialize();
                            $('.alert').remove();
                            $.ajax({
                                url: window.location.BASE + 'supplier/feedback/reply/save/' + f_id,
                                data: datastring,
                                success: function (res) {
                                    // $('#msg').before(res.msg);
                                    $('#replier_supplier').trigger('reset');
                                    $('#add-feedback-' + f_id).trigger('click');
                                    /*								$('input[type=text], textarea').val('');
                                     $('#replier_supplier #description').empty();
                                     $('#feedback_comment_list').empty();
                                     $('#edit_data1').modal('hide');
                                     $('#main_content').before(res.msg);
                                     DT.fnDraw();
                                     */                            }
                            });
                        }
                    });
                }
            }
        });
        $(document.body).on('click', '#close_btn', function (e) {
            e.preventDefault();
            var CurEle = $(this);
            $('#replied_msg').empty();
            $('#close_btn').prop('type', 'hidden');
            $('#fbck_list').show();
        });
    });
    $(document.body).on('click', '#submit_new_feedbck', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $('#fbck_list').hide();
        $('#create_feedback').show();
        $('#submit_new_feedbck').attr('class', 'btn btn-sm btn-danger pull-right close_btn').html('X');
        $('#create_feedback').html('<div class="panel-body"><form method="post" class="form-horizontal form-validate" name="reply_user" id="submit_feedback_supp" enctype="multipart/form-data"><div class="row"><label class="col-md-2">Subject</label><div class="col-md-8"><input type="text" class="form-control" name="subject" id="subject"></div></div><div class="row"><label class="col-md-2">Description</label><div class="col-md-8"><textarea class="form-control" name = "description" id="description" rows="3"></textarea></div></div><div class="row"><label class="col-md-2"></label><div class="col-md-8"><input type="submit" name="submit" id="submit_btn" class="btn btn-primary" value="Submit"></div></div></form></div>');
        /*$('#submit_feedback_supp').validate({
         errorElement: 'div',
         errorClass: 'error',
         focusInvalid: false,
         rules: {
         subject: {
         required: true,
         },
         description: {
         required: true,
         },
         },
         messages: {
         subject: {
         required: 'Please Enter Your  Subject',
         },
         description: {
         required: 'Please Enter Your Description',
         },
         },
         submitHandler: function (form, event) {
         event.preventDefault();
         if ($(form).valid()) {
         $('.alert').remove();
         var datastring = $(form).serialize();*/
        $(document.body).on('click', '#submit_btn', function (e) {
            e.preventDefault();
            $.ajax({
                url: window.location.BASE + 'supplier/feedback/submit',
                data: $('#submit_feedback_supp').serialize(),
                beforeSend: function () {
                    $('button[type="submit"]', $('#submit_feedback_supp')).val('Processing...').attr('disabled', 'disabled');
                },
                success: function (data) {
                    if (data.status == 'OK')
                    {
                        $('#create_feedback').hide();
                        $("#submit_new_feedbck").attr('class', 'btn btn-success btn-sm pull-right').html('<span class="icon-plus"></span>Submit Your Feedback');
                        $('#fbck_list').show();
                        //$('#add_zone_modal').modal('hide');
                        DT.fnDraw();
                        $('input[type="text"],textarea', $(form)).val('');
                        $('#msg').html('<div class="alert alert-success">' + data.msg + '</div>');
                    }
                    $('button[type="submit"]', $(form)).removeAttr('disabled');
                },
                error: function (jqXhr) {
                    $('input[type="submit"]', $('submit_feedback_supp')).val('Submit').attr('disabled', false);
                    if (jqXhr.status === 422) {
                        var data = jqXhr.responseJSON;
                        $('#submit_feedback_supp').appendLaravelError(data.error);
                    }
                    else {
                        alert('Something went wrong');
                    }
                }
            })
        });
        /* }
         }
         });*/
        $(document.body).on('click', '.close_btn', function (e) {
            e.preventDefault();
            var CurEle = $(this);
            $('#create_feedback').hide();
            $('#submit_new_feedbck').attr('class', 'btn btn-success btn-sm pull-right').html('<span class="icon-plus"></span>Submit Your Feedback');
            $('#fbck_list').show();
            $('#replied_msg').empty()
        });
    });
});
