@extends('admin.common.layout')
@section('title','Create Support Center User')
@section('layoutContent')
<section class="content">
    <!--Main row -->
    <div class="row">
        <!--Left col -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Update Support Center User Access Location</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div id="msg"></div>
                <div id="access_form" >
                    <form name="search_user" id="search_user" action="{{URL::to('admin/check_franchisee')}}" class='form-horizontal form-validate'>
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
                        <!--<div class="form-group">
              <label for="textfield" class="control-label col-sm-2">Franchisee Current Type:</label>
              <div class="col-sm-6">
                                      <h5><div id="franchi_name"><strong></strong></div></h5>
                              </div>
           </div>-->

                    </form>
                </div>
            </div>
            <div class="box-body">
                <div id="access_edit">

                </div>
            </div>
        </div>
    </div><!-- /.box -->

</section>

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
                    beforeSend: function (data) {
                        $('#check').text('Processing..');
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            $('#uname_status').html('');
                            $('#access_edit #user_id').val(data.user_id);
                            $('#access_edit').css('display', 'block');
                            $('.fld').css('display', 'block');
                            $('#check').text('Check Franchisee');
                            $('#access_edit').html(data.content);
                            $('#edit_access' + data.scope).css('display', 'block');
                            $('#access_form').hide();
                            $("#state").change();
                        } else if (data.status == 'not_avail') {
                            $('#uname_status').html('Franchisee Not Avaliable');
                            $('#access_edit').css('display', 'none');
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
            $('#access_edit').html('');
        }).on('mouseup', function () {
            $('#access_edit').html('');
        });

    });

</script>
@stop
