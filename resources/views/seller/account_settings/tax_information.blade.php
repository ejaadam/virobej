
<div class="row">
	<div class="col-sm-12">
		<!-- div class="panel panel-default" -->
			<div class="">
				<div class="row">
					<div class="col-sm-12">
						<div>
							<div class="panel panel-info">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a>
											Tax Informations
										</a>
									</h4>
								</div>
								<div id="acc2_collapseFive" class="panel-collapse collapse in">
									<div class="panel-body">
									<!-- PAN Card Details -->		
									<div class="col-sm-12 well">
										<form class="form-horizontal" id="tax_info" action="{{route('seller.account-settings.tax-information')}}" enctype="multipart/form-data">
											<fieldset>	
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">{!!$fields['pan_name']['label']!!}</label>
													<div class="col-sm-4">
														<?php if(!empty($details->pan_card_status) && $details->pan_card_status==1) { ?>
														<span class="span_clss"><b>: {{$details->pan_card_name or ''}}</b></span>
													   <?php } else { ?>	
															<input type="text" class="form-control" id="pan_name" {!!build_attribute($fields['pan_name']['attr'])!!}  value="{{$details->pan_card_name or ''}}"  placeholder="Enter Name on Pan Card" onkeypress="return alpha_checkwithspace(event)">
													   <?php } ?>
													</div>
												</div>
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">{!!$fields['pan_number']['label']!!}</label>
													<div class="col-sm-4">
														<?php if(!empty($details->pan_card_status) && $details->pan_card_status==1) { ?>
															<span class="span_clss" style=""><b>: {{$details->pan_card_no or ''}}</b></span>
														<?php } else { ?>		
															<input type="text" class="form-control" id="pan_number" {!!build_attribute($fields['pan_number']['attr'])!!} placeholder="Enter Pan Card Number" value="{{$details->pan_card_no or ''}}"  onkeypress="return alphaNumeric_withoutspace(event)">
														<?php } ?>
													</div>
													<?php if(!empty($details->pan_card_image) && $details->pan_card_status!=1) { ?>
													<span>
														<?php  if(!empty($details->pan_card_image)  && !empty($path)){ ?> <a  href="{{$path.$details->pan_card_image}}" target="_blank" class="btn btn-xs btn-info pull-left"><u><i class="fa fa-download"> Download</i></u></a> <?php } ?>
													</span>
													<?php } ?>
												</div>
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">Upload Pan Card</label>
													<div class="col-sm-6">
														<?php if(!empty($details->pan_card_status) && $details->pan_card_status==1) { ?>
														<span>
															<?php  if(!empty($details->pan_card_image)  && !empty($path)){ ?> <a  href="{{$path.$details->pan_card_image}}" target="_blank" class="btn btn-xs btn-info pull-left"><u><i class="fa fa-download"> Download</i></u></a> <?php } ?>
														</span>
														<?php } else { ?>
															<div data-provides="fileupload" class="fileupload fileupload-new ">
																<span class="btn btn-default btn-file">
																	<span class="fileupload-new">upload</span><span class="fileupload-exists">Change</span><input type="file" id="pan_card_upload" name="pan_card_upload" data-formatallowd="jpg|jpeg|png|pdf" data-error="Please select valid formet(*.jpg, *.jpeg,*.pdf, *.png)" class="file_upload">
																</span>
																<span class="fileupload-preview"></span>
																<button type="button" class="close fileupload-exists" data-dismiss="fileupload" style="float:none">×</button><br><br>
																<span class="text-muted"><b>Accepted File Formats:png,jpg & pdf</b></span>
															</div>	
														<?php } ?>
													</div>
												</div>
											</fieldset>      
											<?php if(empty($details->pan_card_status)) { ?>
											<div class="form-group">
												<label  class="col-sm-2 control-label"> </label>
												<div class="col-sm-10"  >
													<input type="submit" name="submit" id="submit" class="btn btn-primary btn-md" value="Save">
												</div>
											</div>	<?php } ?> 
										</form>										
									</div>	
									<!-- PAN Card Details End-->
									<hr>
					   <!-- GST Details-->
					   <div class="col-sm-12 well">
							<form class="form-horizontal" id="gstin_form" action="{{route('seller.account-settings.gst-information')}}" enctype="multipart/form-data">
									
											<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label"></label>
													<div class="col-sm-10">
														<div class="checkbox">
														<?php  if(!empty($details->gst_status) && ($details->gst_status==1)) {
																 if(!empty($details->is_registered) && ($details->is_registered==1)){
																echo " ";
																 }
																else{ ?>
													 <input type="checkbox" id="gstin" value="0" name="no_gstin" class="flat-red gstin" @if(isset($details->is_registered)) {{($details->is_registered)==0 ?' checked' : ''}} @endif >			
															<label><b>I dont have GSTIN</b></label>
															<?php	}																
																 }
																 else if(!empty($details->is_registered) && ($details->is_registered==1)){ 
																			 echo "";
																 } else { ?>
															<input type="checkbox" id="no_gstin" value="0" name="no_gstin" class="flat-red gstin" @if(isset($details->is_registered)) {{($details->is_registered)==0 ?' checked' : ''}} @endif >			
															<label><b>I dont have GSTIN</b></label>
																 <?php }?>
														</div>
													</div>
												</div>
											 <?php if(!isset($details->is_registered) || $details->is_registered==1) { ?>
											 
										<span class="form-horizontal" id="gstin_image_block">		
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">{!!$gst_fields['gstin_no']['label']!!}</label>
													<div class="col-sm-4">
													<?php if(!empty($details->gst_status) && $details->gst_status==1){ ?>
														
														<span class="span_clss"><b>: {{$details->gstin_no or ''}}</b></span>
													<?php }  else { ?>
											<input type="text" class="form-control" id="gstin_no" {!!build_attribute($gst_fields['gstin_no']['attr'])!!} value="{{$details->gstin_no or ''}}" placeholder="Enter GSTIN Number" onkeypress="return alphaNumeric_withoutspace(event)"> 
													<?php } ?>
													
													</div>
												<?php 	if(!empty($details->tax_document_path) && $details->gst_status!=1){ ?>
													
													<span>
														  <?php  if(!empty($details->tax_document_path)  && !empty($path)){ ?> <a  href="{{$path.$details->tax_document_path}}" target="_blank" class="btn btn-xs btn-info "><u><i class="fa fa-download"> Download</i></u></a> <?php } ?>
													</span> <?php } ?>
													
													
												</div>
													<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">&nbsp;</label>
														<div class="col-sm-6">
														
														@if(!empty($details->gst_status) && $details->gst_status==1)
															<a href="{{$path.$details->tax_document_path}}" target="_blank" class="btn btn-xs btn-info"><u><i class="fa fa-download"> Download</i></u></a>
														@else
														 
														 <div data-provides="fileupload" class="fileupload fileupload-new ">
														  <span class="btn btn-default btn-file">
															 <span class="fileupload-new">upload</span><span class="fileupload-exists">Change</span><input type="file" id="gstin_image" name="gstin_image" data-formatallowd="jpg|jpeg|png|pdf" data-error="Please select valid formet(*.jpg, *.jpeg,*.pdf, *.png)" class="file_upload"> 
															</span>
															<span class="fileupload-preview"></span>
														  <button type="button" class="close fileupload-exists" data-dismiss="fileupload" style="float:none">×</button><br><br>
														  <span class="text-muted"><b>Accepted File Formats:png,jpg & pdf</b></span>
															</div>	@endif
													</div> 
													</div>
											 </span>  <?php } ?>
										
										 <hr>
											 <?php if(!isset($details->is_registered) || $details->is_registered==1) { ?>
											<span class="form-horizontal" id="tan_image_block">		
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">{!!$gst_fields['tan_no']['label']!!}  </label>
													<div class="col-sm-4">
														<?php if(!empty($details->tan_status) && $details->tan_status==1){ ?>
														 <span class="span_clss"><b>: {{$details->tan_no or ''}}</b></span>
														<?php } else { ?>
														 <input type="text" class="form-control" id="tan_no" {!!build_attribute($gst_fields['tan_no']['attr'])!!} value="{{$details->tan_no or ''}}" placeholder="Enter TAN Number"  onkeypress="return alphaNumeric_withoutspace(event)"> 
														<?php } ?>
													</div>
												<?php if(!empty($details->tan_path) && $details->tan_status!=1){ ?>
													   <span>
														  <?php  if(!empty($details->tan_path)  && !empty($path)){ ?> <a  href="{{$path.$details->tan_path}}" target="_blank" class="btn btn-xs btn-info"><u><i class="fa fa-download"> Download</i></u></a> <?php } ?>
												</span> <?php } ?>
												
												</div>
											<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">&nbsp;</label>
													<div class="col-sm-6">
														@if(!empty($details->tan_status) && $details->tan_status==1)
															<a href="{{$path.$details->tax_document_path}}" target="_blank" class="btn btn-xs btn-info"><u><i class="fa fa-download"> Download</i></u></a>
														@else
														 <div data-provides="fileupload" class="fileupload fileupload-new ">
														  <span class="btn btn-default btn-file">
															 <span class="fileupload-new">upload</span><span class="fileupload-exists">Change</span><input type="file" id="tan_image" name="tan_image" data-formatallowd="jpg|jpeg|png|pdf" data-error="Please select valid formet(*.jpg, *.jpeg,*.pdf, *.png)" class="file_upload"> 
															</span>
															<span class="fileupload-preview"></span>
														  <button type="button" class="close fileupload-exists" data-dismiss="fileupload" style="float:none">×</button><br><br>
														  <span class="text-muted"><b>Accepted File Formats:png,jpg & pdf</b></span>
															</div>	
															@endif
													</div>
												</div>
											 </span> <?php }
								if(empty($details->gst_status)) { ?>
									   <div class="form-group">
										 <label  class="col-sm-2 control-label"> </label>
										  <div class="col-sm-10"  >
										  <input type="submit" name="submit" id="gst_submit" class="btn btn-primary btn-md" value="Save">
										 </div>
										</div>	
												<?php } ?>
									  </form>
										  </div> <!-- GST Details END -->
							
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<!-- /div -->
	</div>
</div>
<br><br>   
<!-- ID Proof Details -->
<form class="form-horizontal" id="id_proof_details" action="{{route('seller.account-settings.proof-details')}}" enctype="multipart/form-data">
<div class="panel panel-info">
   <div class="panel-heading">
   <div class="form-group">
				<div class="col-sm-3" >
				<b>Upload Your Documents</b> 
			 </div>
			 
	<div class="col-sm-3 pull-right" style="padding-top:4px;">
			
				  <label for="inputEmail" class="col-sm-8 control-label"></label>
				   <input type="submit" id="proof_details" class="btn btn-success btn-md" value="Save">
					  </div> </div>
			</div>
   <div class="box-body table-responsive">
		<table id="mange_center" class="table table-bordered table-striped" >
			 <thead>
					<tr>
						<th>Information</th>
						<th>Details</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
			   </thead>
		   <tbody>
				  <tr>
							<td>
								   <h5><b>ID Proof No</b></h5></br>
									<h5><b>ID Proof</b></h5></td>
							<td>
							 <div class="col-sm-12">
									  <div class="form-group">
										 <div class="col-sm-8">
										 <?php if(!empty($details->id_proof_status) && $details->id_proof_status==1) { ?>
											 <span><b>{{$details->id_proof_no or ''}}</b></span>
										<?php } else { ?>
											 <input type="text" class="form-control" name="proof_no" id="proof_no" placeholder="Enter ID Proof No" value="{{$details->id_proof_no or ''}}" >
										<?php } ?>
										  </div>
									   </div>
									  
								  <div class="form-group">
										<div class="col-sm-8">
										
									@if(!empty($details->id_proof_status) && $details->id_proof_status==1)
										<br> <span><b>{{$proof_name->id_proof_name or ''}}</b></span>
									 @else
										   <select name="id_proof_type" class="form-control" id="id_proof_type">
											<option value="">Select ID Proof</option>
										   @if(isset($id_proof) && !empty($id_proof))
											@foreach($id_proof as $key=>$filed)
											<option value="{{$filed->document_type_id}}" @if(!empty($details->id_proof_document_type_id)) {{($details->id_proof_document_type_id == $filed->document_type_id)?' selected ':''}} @endif >{{ucfirst($filed->type)}}</option>
											@endforeach
											@endif
											   </select>
											  @endif
												  </div>
												  </div>
									   </td>
								 <td>
									  <div class="form-group">
								  <div class="col-sm-6">
								  <?php  if(isset($details->address_proof_status) && ($details->address_proof_status==1)) { ?>
									<label class="text-success"><b>VERIFIED</b></label> <?php  } 
									 if(isset($details->address_proof_status) && ($details->address_proof_status==0))  { ?>
									  <label class="errmsg"><b>Not VERIFIED</b></label> <?php } 
									if(isset($details->address_proof_status) && ($details->address_proof_status==2))  { ?>
									  <label class="errmsg"><b>REJECTED</b></label> 
									<?php }		?>	
										</div>
										 </div>
									 </td>
							   <td>     
							   
							  <div class="form-group">
								<div class="col-sm-10">
								@if(!empty($details->id_proof_status) && $details->id_proof_status==1)
										 <a href="{{$path.$details->id_proof_path}}" target="_blank"><i class="fa fa-download"> Download</i></a>
									 @else
								   <input type="file" id="id_image" name="id_image" data-formatallowd="jpg|jpeg|png|pdf" data-error="Please select valid formet(*.jpg, *.jpeg,*.pdf, *.png)" class="file_upload" > 
								
								<br>
								
								<?php if(!empty($details->id_proof_path)  && !empty($path)){ ?> <a href="{{$path.$details->id_proof_path}}" target="_blank" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"> Download</i></a> <?php }?>
								@endif
								</div>
								 </div>
								  </td>
								 </div>
							  
						   </tr>
						 <tr>
						<td>
								 <h5><b>Address Proof No</b></h5></br>
								 <h5><b>Address Proof</b></h5>
							  </td>
					  <td>
					   <div class="col-sm-12">
								<div class="form-group">
									 <div class="col-sm-8">
									@if(!empty($details->address_proof_status) && $details->address_proof_status==1)
									   <span><b>{{$details->address_proof_no or ''}}</b></span>
									 @else
									<input type="text" class="form-control" name="address_proof_no" id="address_proof_no"  value="{{$details->address_proof_no or ''}}" placeholder="Enter Address Proof No">
									@endif		
											</div>
												 </div>
											
							  <div class="form-group">
									  <div class="col-sm-8">
									  @if(!empty($details->address_proof_status) && $details->address_proof_status==1)
									<br>  <span><b>{{$proof_name->address_proof_name or ''}}</b></span> 
										 @else
										   <select name="address_proof_type" class="form-control" id="address_proof_type">
											   <option value="">Select Address Proof</option>
												@if(isset($address_proof) && !empty($address_proof))
														@foreach($address_proof as $key=>$filed)
														<option value="{{$filed->document_type_id}}" @if(!empty($details->address_proof_document_type_id)) {{($details->address_proof_document_type_id== $filed->document_type_id)?' selected ':''}} @endif >{{ucfirst($filed->type)}}</option>
														@endforeach
														@endif
													  </select>
													  @endif
												  </div>
											</div>
							  </td>
						 <td>
								  <div class="form-group">
								  <div class="col-sm-6">
								  <?php  if(isset($details->address_proof_status) && ($details->address_proof_status==1)) { ?>
									<label class="errmsg"><b>VERIFIED</b></label> <?php  } 
									else if(isset($details->address_proof_status) && ($details->address_proof_status==0))  { ?>
									  <label class="errmsg"><b>Not VERIFIED</b></label> 
									<?php } 
									else if(isset($details->address_proof_status) && ($details->address_proof_status==2))  { ?>
									  <label class="errmsg"><b>REJECTED</b></label> 
									<?php } ?>
										</div>
										 </div>
						  </td>
						<td>
									<div class="form-group">
									   <div class="col-sm-10">
									   @if(!empty($details->address_proof_status) && $details->address_proof_status==1)
									  <a href="{{$path.$details->id_proof_path}}" target="_blank"><i class="fa fa-download"> Download</i></a> 
									 @else
										 <input type="file" id="address_image" name="address_image" data-formatallowd="jpg|jpeg|png|pdf" data-error="Please select valid formet(*.jpg, *.jpeg,*.pdf, *.png)" class="file_upload">
										<br>
									   <?php if(!empty($details->address_proof_path) && !empty($path)){ ?> <a href="{{$path.$details->id_proof_path}}" target="_blank" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"> Download</i></a> <?php }?>
											@endif												 
											 </div>
											   </div>
											   <br><br>
					</td>
				  </div>
				</tr>
		 </tbody>
	</table>
  </div>
</div>
</form>

<script src="{{asset('js/providers/seller/tax_info.js')}}"></script>
<script src="{{asset('js/providers/file_upload.js')}}"></script>
<style>
.span_clss{
padding-top: 5px !important; 
display: inline-block;
}
</style>