<div class="pageheader" >
    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">Shop Details
						<div class="box-tools pull-right">
							<a href="#" class="btn btn-danger btn-sm back-to-list"><i class="icon-remove"></i> Close</a>
						</div>
					</h4>
                </div>
                <div class="panel-body">				
					<div class="col-sm-6">
						<div class="box box-primary">
							
							<div class="box-body">		
								<div class="row">
									<div class="col-sm-6">
										<br><br>
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.outlets')}}</strong> 
										   <p class="text-muted" id="outlet_name"></p>
										</div>										
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.category_name')}}</strong>
										   <p class="text-muted" id="outlet_category"></p>
										</div>
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.phone_lbl')}}</strong>
										   <p class="text-muted" id="outlet_phone"></p>
										</div> 
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.status')}}</strong>
										   <p class="text-muted" id="outlet_status"></p>
										</div>									
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.no_outlet_images')}}</strong>
										   <p class="text-muted" id="image_count"></p>
										</div>		
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.location')}}</strong>
										   <p class="text-muted" id="outlet_address"></p>
										</div>	
									</div>
									<div class="col-sm-6">
										<br><br>
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.outlet_code')}}</strong>
										   <p class="text-muted" id="outlet_code"></p>
										</div>
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.email_lbl')}}</strong>
										   <p class="text-muted" id="outlet_email"></p>
										</div>
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.mobile_lbl')}}</strong>
										   <p class="text-muted" id="outlet_mobile"></p>
										</div>
										<div class="col-sm-12 form-group">
										   <strong>{{trans('general.approval')}}</strong>
										   <p class="text-muted" id="outlet_approval"></p>
										</div>											
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="box box-primary">
							
							<div class="box-body">
								<br>
								<div class="col-sm-12 "> 
									<b>Outlet Business Hours</b> 
									<br><br>
									<span id="business_hours"> </span>				
								</div>			
							</div>
						</div>
					</div>
				 </div>
               
            </div>
        </div>
    </div>
</div>