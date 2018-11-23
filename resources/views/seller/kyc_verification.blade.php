@extends('seller.common.layout')
@section('layoutContent')
<div id="main_content">
    <!-- main content -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">KYC Verification</h4>
                </div>
                <div class="panel-body">
                    <fieldset class="col-sm-12">
                        <form class="form" id="kyc-verifiaction" action="{{Route('api.v1.seller.setup.update-kycc')}}" enctype="multipart/form-data">
							<input type="hidden" name="supplier_id" id="supplier_id" value="{{$supplier_id}}"/>
							<input type="hidden" name="kyc_verifiacation[pan_card_image]" id="pan_card_image" value="{{$kyc_verifiacation->pan_card_image or ''}}"/>
							<input type="hidden" name="kyc_verifiacation[auth_person_id_proof]" id="auth_person_id_proof" value="{{$kyc_verifiacation->auth_person_id_proof or ''}}"/>
							<div class="row">
								<div class="col-sm-6">
									<h4 class="title"> &nbsp; </h4>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['kyc_verifiacation.pan_card_no']['attr']['name']!!}">{!!$fields['kyc_verifiacation.pan_card_no']['label']!!}</label>
										<input id="pan_card_no" value="{!!$kyc_verifiacation->pan_card_no or ''!!}" class="form-control" style="text-transform:uppercase" {!!build_attribute($fields['kyc_verifiacation.pan_card_no']['attr'])!!}/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['kyc_verifiacation.pan_card_name']['attr']['name']!!}">{!!$fields['kyc_verifiacation.pan_card_name']['label']!!}</label>
										<input id="pan_card_name" value="{!!$kyc_verifiacation->pan_card_name or ''!!}" class="form-control" {!!build_attribute($fields['kyc_verifiacation.pan_card_name']['attr'])!!}/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['kyc_verifiacation.dob']['attr']['name']!!}">{!!$fields['kyc_verifiacation.dob']['label']!!}</label>
										<input id="dob" value="{!!$kyc_verifiacation->dob or ''!!}" class="form-control" {!!build_attribute($fields['kyc_verifiacation.dob']['attr'])!!}/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['pan_card_image']['attr']['name']!!}">{!!$fields['pan_card_image']['label']!!}</label>
										@if(!empty($kyc_verifiacation->pan_card_image))
										<div class="row">
											<div class="col-sm-4">
												<img class="img img-thumbnail" id="pan_card_image_file_preview" src="{!!URL::asset($kyc_verifiacation->pan_card_image.'?t='.date('YmdHis'))!!}"/>
											</div>
											<div class="col-sm-8">
												<input {!!build_attribute($fields['pan_card_image']['attr'])!!} id="pan_card_image_file" class="form-control"/>
											</div>
										</div>
										@else
										<input {!!build_attribute($fields['pan_card_image']['attr'])!!}  id="pan_card_image_file" class="form-control"/>
										@endif
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['kyc_verifiacation.vat_no']['attr']['name']!!}">{!!$fields['kyc_verifiacation.vat_no']['label']!!}</label>
										<input id="vat_no" value="{!!$kyc_verifiacation->vat_no or ''!!}" class="form-control" {!!build_attribute($fields['kyc_verifiacation.vat_no']['attr'])!!}/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['kyc_verifiacation.cst_no']['attr']['name']!!}">{!!$fields['kyc_verifiacation.cst_no']['label']!!}</label>
										<input id="cst_no" value="{!!$kyc_verifiacation->cst_no or ''!!}" class="form-control" {!!build_attribute($fields['kyc_verifiacation.cst_no']['attr'])!!}/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['kyc_verifiacation.gstin']['attr']['name']!!}">{!!$fields['kyc_verifiacation.gstin']['label']!!}</label>
										<input id="gstin" value="{!!$kyc_verifiacation->gstin or ''!!}" class="form-control" style="text-transform:uppercase"  {!!build_attribute($fields['kyc_verifiacation.gstin']['attr'])!!}/>
									</div>
								</div>
								<div class="col-sm-6">
									<h4 class="title">Authorize Person Details:</h4>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['kyc_verifiacation.auth_person_name']['attr']['name']!!}">{!!$fields['kyc_verifiacation.auth_person_name']['label']!!}</label>
										<input id="auth_person_name" value="{!!$kyc_verifiacation->auth_person_name or ''!!}" class="form-control" {!!build_attribute($fields['kyc_verifiacation.auth_person_name']['attr'])!!}/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['kyc_verifiacation.id_proof_document_type_id']['attr']['name']!!}">{!!$fields['kyc_verifiacation.id_proof_document_type_id']['label']!!}</label>
										<select name="kyc_verifiacation[id_proof_document_type_id]" id="id_proof_document_type_id" class="form-control" {!!build_attribute($fields['kyc_verifiacation.id_proof_document_type_id']['attr'])!!}>
											<option value="" hidden="hidden">-Select Proof Type-</option>
											@foreach($document_types as $doc)
											<option value="{!!$doc->document_type_id!!}" {!!(isset($kyc_verifiacation->id_proof_document_type_id) && $kyc_verifiacation->id_proof_document_type_id == $doc->document_type_id)?'selected="selected"':''!!}>{!!$doc->type!!}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['auth_person_id_proof']['attr']['name']!!}">{!!$fields['auth_person_id_proof']['label']!!}</label>
										@if(!empty($kyc_verifiacation->auth_person_id_proof))
										<div class="row">
											<div class="col-sm-4">
												<img class="img img-thumbnail" id="auth_person_id_proof_file_preview" src="{!!URL::asset($kyc_verifiacation->auth_person_id_proof.'?t='.date('YmdHis'))!!}"/>
											</div>
											<div class="col-sm-8">
												<input {!!build_attribute($fields['auth_person_id_proof']['attr'])!!} id="auth_person_id_proof_file" class="form-control"/>
											</div>
										</div>
										@else
										<input {!!build_attribute($fields['auth_person_id_proof']['attr'])!!} id="auth_person_id_proof_file" class="form-control"/>
										@endif
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="form-action col-sm-12 text-right">
									<input type="submit" class="btn btn-sm btn-success" value="Save"/>
								</div>
							</div>
						</form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
@include('seller.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/supports/supplier/kyc-verifiaction.js')}}"></script>
@stop
