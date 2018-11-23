
            <form class="form-horizontal form-bordered" action="{{route('admin.franchisee.change_pin')}}" id="update_memberpin" method="post" novalidate="novalidate" autocomplete="off">
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/changepwd.support_name')}} :</label>
                    <div class="col-sm-7">
					<label class="control-label" id="support_pin" style="text-align:left"></label>
                        <input type="hidden" class="control-label" id="uname_pin"  name="account_id">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/changepwd.account')}} :</label>
                    <div class="col-sm-7">
                        <label class="control-label" id="fullname_pin" style="text-align:left"></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/changepwd.new_pin')}} :</label>
                    <div class="col-sm-7">
                      <input type="password" class="form-control" name="new_tpin" id="new_tpin" placeholder="Enter new PIN"  required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/changepwd.confirm_pin')}} :</label>
                    <div class="col-sm-7">
                      <input type="password" class="form-control" name="confirm_tpin" id="confirm_tpin" placeholder="Confirm new PIN" required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">&nbsp;</label>
                    <div class="col-sm-7">
                        <button  id="update_member_pin" class="btn btn-primary"> {{trans('admin/general.submit')}}
                        </button>
                    </div>
                </div>
            </form>
