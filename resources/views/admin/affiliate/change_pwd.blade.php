<div class="col-sm-6">
        <div class="box-header with-border">
            <div class="pull-right">
                <button class="btn btn-danger btn-sm close_btn" >  <i class="fa fa-times"></i> {{trans('admin/general.close')}}</button>
            </div>
            <h3 class="box-title"><i class="fa fa-edit margin-r-5"></i> {{trans('admin/affiliate/settings/changepwd.login_password')}}</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal form-bordered" action="{{route('admin.account.updatepwd')}}" id="update_member_pwdfrm" method="post" novalidate="novalidate" autocomplete="off">
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/changepwd.account_id')}} :</label>
                    <div class="col-sm-4">
                        <label class="control-label text-muted" id="uname_label" ></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/changepwd.fullname')}} :</label>
                    <div class="col-sm-4">
                        <label class="control-label text-muted" id="fullname_label" ></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/changepwd.new_password')}} :</label>
                    <div class="col-sm-4">
                        <input type="password" name="new_pwd" id="new_pwd" class="form-control" value=""  placeholder="Enter the New Password">
                    </div>
                </div>
               <!-- <div class="form-group">
                    <label class="control-label col-sm-4">{{\trans('admin/account/settings/changepwd.confirm_password')}} :</label>
                    <div class="col-sm-7">
                        <input type="text" name="cnfm_pwd" id="cnfm_pwd" class="form-control" value=""  placeholder="{{trans('user/settings/changepwd.confirm_password')}}">
                    </div>
                </div>-->
                <div class="form-group">
                    <label class="control-label col-sm-4">&nbsp;</label>
                    <div class="col-sm-7">
                        <button  id="update_member_pwd" class="btn btn-primary"> {{trans('admin/general.submit')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>

</div>
