
            <form class="form-horizontal form-bordered" action="{{route('admin.franchisee.change_password')}}" id="update_member_pwdfrm" method="post" novalidate="novalidate" autocomplete="off">
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/changepwd.support_name')}} :</label>
                    <div class="col-sm-7">
                        <label class="control-label " id="support_label" style="text-align:left"></label>
                        <input type="hidden" class="control-label text-muted" id="uname_label" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/changepwd.account')}} :</label>
                    <div class="col-sm-7">
                        <label class="control-label" id="fullname_label" style="text-align:left"></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/changepwd.new_password')}} :</label>
                    <div class="col-sm-7">
                        <input type="text" name="new_pwd" id="new_pwd" class="form-control" value=""  placeholder="{{trans('admin/changepwd.new_password')}}">
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
