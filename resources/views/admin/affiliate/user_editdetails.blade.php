<div class="col-sm-6">

        <div class="box-header with-border">
            <div class="pull-right">
                <button class="btn btn-danger btn-sm close_btn" >  <i class="fa fa-times"></i> {{trans('admin/general.close')}}</button>
            </div>
            <h3 class="box-title"><i class="fa fa-edit margin-r-5"></i> {{trans('admin/affiliate/settings/user_edit.edit_details')}}</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal form-bordered" id="user_updatefrm" action="{{route('admin.account.update_details')}}" method="post" novalidate="novalidate" autocomplete="off">
                <div class="form-group">
                    <input type="hidden" name="uname" id="uname" class="form-control" value="">
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/user_edit.first_name')}} :</label>
                    <div class="col-sm-7">
                        <input type="text" name="first_name" id="first_name" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/user_edit.last_name')}} :</label>
                    <div class="col-sm-7">
                        <input type="text" name="last_name" id="last_name" class="form-control" value="">
                    </div>
                </div>
                
           <div class="form-group">
                        <label for="textfield" class="control-label col-sm-4">{{trans('admin/affiliate/settings/user_edit.dob')}} :</label>
                        <div class="col-sm-7">
                            <input type="text" name="dob" id="dob" class="form-control datepicker"  placeholder="DOB"   value="">
                        </div>
                    </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">&nbsp;</label>
                    <div class="col-sm-7">
                        <button  id="update_member_details" class="btn btn-primary"> <i class="fa fa-arrow-right"></i>{{trans('admin/general.submit')}}
                        </button>&nbsp;
                        <button type="button" id="member_reset" class="btn btn-warning"><i class="fa fa-repeat"></i>  {{trans('admin/general.reset')}}
                        </button>

                    </div>
                </div>
            </form>
        </div>

</div>
