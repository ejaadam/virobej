

<div class="row">
<div class="col-sm-12">
	<div class="panel panel-default">
		
		<div class="">
			<div class="row">
				<!--div class="col-sm-1">
				</div-->							
				<div class="col-sm-12">
				<form class="form-horizontal" id="update_status" action="{{route('admin.seller.update-proof-status')}}" enctype="multipart/form-data">
					<input type="hidden" id="tax_id" name="tax_id" value="<?php  echo (isset($user_details->tax_id)) ? $user_details->tax_id	 : '';?>">
					<div class="" id="accordion2">
					
							<div id="acc2_collapseFive" class="panel-collapse collapse in">
								<div class="panel-body">
									<div class="col-sm-12">
										<fieldset>		
										
											<div class="form-group">
												<label for="inputEmail" class="col-sm-4 control-label">Seller Name :</label>
												<div class="col-sm-8">
											   <div class="span_clss"><strong><?php echo (isset($user_details->company_name)) ? $user_details->company_name. ' ('.$user_details->supplier_code.')' : '';?></strong></div>
												</div>
											</div>
											<div class="form-group">
												<label for="inputEmail" class="col-sm-4 control-label">Contact	 Name :</label>
												<div class="col-sm-8">
											   <div class="span_clss"><strong><?php echo (isset($user_details->full_name)) ? $user_details->full_name	 : '';?></strong></div>
												</div>
											</div>
												<?php if(!empty($user_details->pan_card_no)) { ?>
											<div class="form-group">
												<label for="inputEmail" class="col-sm-4 control-label">Pan Card Details :</label>
												<div class="col-sm-8">
											   <div class="span_clss"><strong><?php echo (isset($user_details->pan_card_no)) ? $user_details->pan_card_no	 : '';?>(<?php echo (isset($user_details->pan_card_name)) ? $user_details->pan_card_name : '-';?>)</strong></div>
												</div>
											</div>  <?php } ?>

											<!-- <div class="form-group">
												<label for="inputEmail" class="col-sm-4 control-label">Pan Card Name :</label>
												<div class="col-sm-8">
											   <div class="span_clss"><strong><?php //echo (isset($user_details->pan_card_name)) ? $user_details->pan_card_name : '';?></strong></div>
												</div>
											</div>-->
											
											<div class="form-group">
												<label for="inputEmail" class="col-sm-4 control-label">Pan Card Status:</label>
											<div class="col-sm-4">
											
												 <select  name="pan_status"  id="pan_status" class="form-control">
											<option value="">Select Status</option>
											<option value="0" {{isset($user_details->status_id) && $user_details->status_id==0 ? 'selected="selected"' : ''}}>Pending</option>
											<option value="1" {{isset($user_details->status_id) && $user_details->status_id==1 ? 'selected="selected"' : ''}}>Approved</option>
											<option value="2" {{isset($user_details->status_id) && $user_details->status_id==2 ? 'selected="selected"' : ''}}>Rejected</option>
											</select> 
											</div>
								</div>
								
								<?php if(!empty($user_details->id_proof_type)){ ?>
								<hr>
								<div class="form-group">
												<label for="inputEmail" class="col-sm-4 control-label">ID Proof :</label>
														<div class="col-sm-4">
														 <div class="span_clss"><strong><?php echo (isset($user_details->id_proof_type)) ? $user_details->id_proof_type : '';?></strong></div>
														<select  name="id_proof_status"  id="id_proof_status" class="form-control">
														<option value="">Select Status</option>
														<option value="0" {{isset($user_details->id_proof_status) && $user_details->id_proof_status==0 ? 'selected="selected"' : ''}}>Pending</option>
														<option value="1" {{isset($user_details->id_proof_status) && $user_details->id_proof_status==1 ? 'selected="selected"' : ''}}>Approved</option>
														<option value="2" {{isset($user_details->id_proof_status) && $user_details->id_proof_status==2 ? 'selected="selected"' : ''}}>Rejected</option>
														</select>
														</div>
												<div class="col-sm-3">
												<br> <a  href="{{$path.$user_details->id_proof_path}}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-download"> Download</i></a>
												</div>
										 
								</div> <?php } ?>
					
					<?php if(!empty($user_details->address_proof_type)){ ?>
					<hr>
								<div class="form-group">

									<label for="inputEmail" class="col-sm-4 control-label">Adress Proof :</label>
											<div class="col-sm-4">
											<div class="span_clss"><strong><?php echo (isset($user_details->address_proof_type)) ? $user_details->address_proof_type : '';?></strong></div>
											<select  name="addres_status"  id="addres_status" class="form-control">
											<option value="">Select Status</option>
											<option value="0" {{isset($user_details->address_proof_status) && $user_details->address_proof_status==0 ? 'selected="selected"' : ''}}>Pending</option>
											<option value="1" {{isset($user_details->address_proof_status) && $user_details->address_proof_status==1 ? 'selected="selected"' : ''}}>Approved</option>
											<option value="2" {{isset($user_details->address_proof_status) && $user_details->address_proof_status==2 ? 'selected="selected"' : ''}}>Rejected</option>
											</select>
											</div>
										<div class="col-sm-4">
											 
										      <br> <a  href="{{$path.$user_details->address_proof_path}}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-download"> Download</i></a>
											</div>
											</div> <?php } ?>
											<hr>
										<?php if(!empty($user_details->gstin_no)){ ?>
											<div class="form-group">
												<label for="inputEmail" class="col-sm-4 control-label">GSTIN :</label>
														<div class="col-sm-4">
														 <div class="span_clss"><strong><?php echo (isset($user_details->gstin_no)) ? $user_details->gstin_no : '';?></strong></div>
														<select  name="gst_status"  id="gst_status" class="form-control">
														<option value="">Select Status</option>
														<option value="0" {{isset($user_details->gst_status) && $user_details->gst_status==0 ? 'selected="selected"' : ''}}>Pending</option>
														<option value="1" {{isset($user_details->gst_status) && $user_details->gst_status==1 ? 'selected="selected"' : ''}}>Approved</option>
														<option value="2" {{isset($user_details->gst_status) && $user_details->gst_status==2 ? 'selected="selected"' : ''}}>Rejected</option>
														</select>
														</div>
												<div class="col-sm-3">
												<br> <a  href="{{$path.$user_details->tax_document_path}}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-download"> Download</i></a>
												</div>
										 
											</div> <?php } ?>
											
												<?php if(!empty($user_details->tan_no)){ ?>
												<hr>
											<div class="form-group">
												<label for="inputEmail" class="col-sm-4 control-label">TAN :</label>
														<div class="col-sm-4">
														 <div class="span_clss"><strong><?php echo (isset($user_details->tan_no)) ? $user_details->tan_no : '';?></strong></div>
														<select  name="tan_status"  id="tan_status" class="form-control">
														<option value="">Select Status</option>
														<option value="0" {{isset($user_details->tan_status) && $user_details->tan_status==0 ? 'selected="selected"' : ''}}>Pending</option>
														<option value="1" {{isset($user_details->tan_status) && $user_details->tan_status==1 ? 'selected="selected"' : ''}}>Approved</option>
														<option value="2" {{isset($user_details->tan_status) && $user_details->tan_status==2 ? 'selected="selected"' : ''}}>Rejected</option>
														</select>
														</div>
												<div class="col-sm-3">
												<br> <a  href="{{$path.$user_details->tan_path}}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-download"> Download</i></a>
												</div>
										 
											</div> <hr> <?php } ?>
											
								  <!--   <div class="form-group">
										   <label for="inputEmail" class="col-sm-4 control-label">Verification Status:</label>
											<div class="col-sm-6">
											   <select  name="status"  id="status" class="form-control">
											<option value="">Select Status</option>
											<option value="0" {{isset($status) && $status==0 ? 'selected="selected"' : ''}}>Pending</option>
											<option value="1" {{isset($status) && $status==1 ? 'selected="selected"' : ''}}>Approved</option>
											<option value="2" {{isset($status) && $status==2 ? 'selected="selected"' : ''}}>Rejected</option>
											</select>
											</div>
								</div>-->
						
							<div class="form-group">
									 <label  class="col-sm-4 control-label"> </label>
									  <div class="col-sm-8">
									  <input type="submit" name="submit" id="upload_form" class="btn btn-primary btn-md" value="Submit">
									 </div>
								</div>	
							</div>
					
					</div>
				
				</form>	
				</div>
				
			</div>
		</div>
		
		
	</div>
</div>
</div>


<script src="{{asset('resources/supports/admin/seller/proof_status.js')}}"></script>	
<style>
.span_clss{
padding-top: 5px !important; 
display: inline-block;
}
</style>