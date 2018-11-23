@extends('admin.common.layout')
@section('title','Edit Support Center User\'s Profile')
@section('layoutContent')
<section class="content">
    <!--Main row -->
    <div class="row">
        <!--Left col -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Edit Support Center User's Profile</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div id="msg"></div>
                <div id="access_form" >
                    <form name="search_user" id="search_user" action="{{URL::to('admin/franchisee_edit_profile')}}" class='form-horizontal form-validate'>
                        <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">Support Center Username:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="uname" id="uname" value="">
                                <div id="uname_status"></div>
                            </div>
                        </div>
                        <div class="form-group check-btn">
                            <label for="textfield" class="control-label col-sm-2"></label>
                            <div class="col-sm-6">
                                <button id="check" class="btn btn-sm btn-primary">Check Support Center User</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body update_form">
                <div id="update_form">
                </div>
            </div>
        </div>
    </div><!-- /.box -->
</div><!--/.row (main row) -->
</section>
<style type="text/css">
    .help-block{
        color:#f56954;
    }
</style>
<script>
    $(document).ready(function () {
        $('#check').click(function (e) {
            e.preventDefault();
            var uname = $('#uname').val();
            if (uname != '') {
                $.ajax({
                    dataType: 'json',
                    type: 'post',
                    data: {uname: uname},
                    url: $('#search_user').attr('action'),
                    beforeSend: function () {
                        $('#check').text('Processing..');
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            /* $('.check-btn').hide();
                             $('#uname').attr('readonly','readonly'); */
                            $('#update_form').html(data.content);
                            $('#check').text('Check Franchisee');
                            $('#uname_status').html('');
                            $('#access_form').hide();
                        } else if (data.status == 'not_avail') {
                            $('#uname_status').html('Franchisee Not Avaliable');
                            $('#check').text('Check Franchisee');
                        }
                    },
                    error: function (data) {
                        alert("something Went Wrong");
                    }
                })
            } else {
                alert("Please Enter validate UserName");
            }
        })

        $('#uname').on('keyup', function () {
            $('#update_form').html('');
        }).on('mouseup', function () {
            $('#update_form').html('');
        });
    });
</script>

@stop
