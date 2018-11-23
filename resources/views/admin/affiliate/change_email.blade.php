<div class="col-sm-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right">
                <button class="btn btn-danger btn-sm close_btn" >  <i class="fa fa-times"></i> {{trans('admin/general.close')}}</button>
            </div>
            <h3 class="box-title"><i class="fa fa-edit margin-r-5"></i> {{trans('admin/affiliate/settings/changepwd.change_mail')}}<b id="user_value">       </b></h3>
			     
         </div>
         <div class="box-body">
            <form action="{{route('admin.account.email')}}" method="post" class="form-horizontal form-bordered" id="change_email_form" autocomplete="off"  novalidate="novalidate" >
               <input type="hidden" class="form-control" id="user_name" value="" >
			   <div class="form-group">
			   <label class="col-sm-4 control-label" for="">{{trans('admin/affiliate/settings/changepwd.current_email')}}</label>
                <div class="col-sm-5">
				<p class="text-muted" id="old_emails"></p>
				</div>
				</div>
               <div class="form-group">
               <label class="control-label col-sm-4">{{trans('admin/affiliate/settings/changepwd.new_email')}}</label>
			   <div class="col-sm-7">
			   <div class="input-group">
			   <input type="hidden" id="old_email" name="old_email" class="form-control" value="" required="">
			   <input type="text" id="email" name="email" class="form-control valid" value="">
			   <div class="input-group-btn">
			  <!-- <button type="button" class="btn btn-info" id="check_email">{{trans('admin/account/settings/changepwd.check_email')}}</button>-->
			   </div>
			   </div>
			   </div>
			   </div>
			   
                <div class="form-group">
                    <label class="control-label col-sm-4">&nbsp;</label>
                    <div class="col-sm-7">
                  
						<button  id="update_member_email" class="btn btn-primary"> {{trans('admin/general.submit')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
