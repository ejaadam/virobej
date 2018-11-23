  <form class="form-horizontal form-bordered" action="{{route('seller.manage_users.reset-password')}}" id="update_user_pwdfrm" method="post" novalidate="novalidate" autocomplete="off">
			  <input type="hidden" id="reset_user_account_id" name="reset_user_account_id">
			  <input type="hidden" id="full_name" name="full_name">
                <div class="form-group">
                    <label class="control-label col-sm-4">Email :</label>
                    <div class="col-sm-4">
                        <label class="text-muted" id="uname_label"></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">Full Name :</label>
                    <div class="col-sm-4">
                        <label class="text-muted" id="fullname_label" ></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">Enter New Password :</label>
                    <div class="col-sm-6">
                        <input type="password" name="new_pwd" id="new_pwd" class="form-control" value=""  placeholder="Enter New Password" onkeypress="return RestrictSpace(event)">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">&nbsp;</label>
                    <div class="col-sm-7">
                        <button  id="update_member_pwd" class="btn btn-primary"> {{trans('admin/general.submit')}}
                        </button>
                    </div>
                </div>
            </form>
