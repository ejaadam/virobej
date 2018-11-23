<div class="col-sm-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right">
                <button class="btn btn-danger btn-sm close_btn" >  <i class="fa fa-times"></i> {{trans('admin/general.close')}}</button>
            </div>
            <h3 class="box-title"><i class="fa fa-edit margin-r-5"></i> {{trans('admin/affiliate/admin.change_mobile')}}<b id="user_mobile_val"></b></h3>
			     
        </div>
        <div class="box-body">
            <form action="{{route('admin.account.update_mobile')}}" method="post" class="form-horizontal form-bordered" id="change_mobile_form" autocomplete="off"  novalidate="novalidate" >
                  <input type="hidden" class="form-control" id="uname_mobile" value="">
			<div class="form-group">
			<label class="col-sm-4 control-label" for="">{{trans('admin/affiliate/admin.current_mobile')}}</label>
                <div class="col-sm-5">
				<p class="text-muted" id="old_mobile"></p>
				</div>
				</div>
             
               <div class="form-group">
               <label class="control-label col-sm-4">{{trans('admin/affiliate/admin.new_mobile')}}</label>
			   <div class="col-sm-7">
			   <div class="input-group">
			   <input type="hidden" id="old_no" name="old_no" class="form-control" value="" required="">
			   <input type="text" id="mobile" name="mobile" class="form-control valid" value="">
			   <div class="input-group-btn">
			   <!--<button type="button" class="btn btn-info" id="check_mobile_no">{{trans('admin/account/user.check_mobile')}}</button>-->
			   </div>
			   </div>
			   </div>
			   </div>
			   
                <div class="form-group">
                    <label class="control-label col-sm-4">&nbsp;</label>
                    <div class="col-sm-7">
                        <button  id="update_member_mobile" class="btn btn-primary"> {{trans('admin/general.submit')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
