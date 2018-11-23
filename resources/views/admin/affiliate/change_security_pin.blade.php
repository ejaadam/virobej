<div class="col-sm-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right">
                <button class="btn btn-danger btn-sm close_btn" >  <i class="fa fa-times"></i> {{trans('admin/general.close')}}</button>
            </div>
            <h3 class="box-title"><i class="fa fa-edit margin-r-5"></i> {{trans('admin/affiliate/admin.reset_pin')}}</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal form-bordered" action="{{route('admin.account.updatepin')}}" id="update_member_pinfrm" method="post" novalidate="novalidate" autocomplete="off">
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/changepwd.account_id')}} :</label>
                    <div class="col-sm-7">
                        <label class="control-label text-muted" id="uname_pin" ></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/changepwd.fullname')}} :</label>
                    <div class="col-sm-7">
                        <label class="control-label text-muted" id="fullname_pin" ></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/changepwd.new_pin')}} :</label>
                    <div class="col-sm-7">
                        <input type="password" name="new_pin" id="new_pin" class="form-control" value=""  placeholder="Enter the New Pin">
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
        </div>
    </div>
</div>
