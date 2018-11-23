@extends('seller.common.layout')

@section('layoutContent')
<div id="main_content">
    <!-- main content -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Store Info</h4>
                </div>
                <div class="panel-body">
                    <fieldset class="col-sm-12">
                        <form class="form" id="supplier-update-form" action="{{Route('api.v1.seller.setup.store-info')}}">
							<input type="hidden" name="address_id" value="{{$supplier_account_details->address_id or ''}}"/>
							<input type="hidden" name="account_id" value="{{$supplier_account_details->account_id or ''}}"/>
							<input type="hidden" name="user_id" value="{{$supplier_account_details->user_id or ''}}"/>
							<input type="hidden" name="supplier_id" value="{{$supplier_account_details->supplier_id or ''}}"/>
							<input type="hidden" name="store_id" value="{{$supplier_account_details->store_id or ''}}"/>
							<div class="row">								
								<fieldset class="col-sm-4">		
									<legend class="title">Contact Details</legend>
								   
									 <div class="form-group">
										<label class="control-label" for="">{!!$fields['store_extras.store_name']['label']!!}</label>
										<input id="storename" value="{!!$supplier_account_details->store_name or ''!!}" class="form-control" {!!build_attribute($fields['store_extras.store_name']['attr'])!!}/>
									</div>									
									<div class="form-group">
										<label class="control-label" for="{!!$fields['store_extras.email']['attr']['name']!!}">{!!$fields['store_extras.email']['label']!!}</label>
										<input id="email" value="{!!$supplier_account_details->store_email or ''!!}" class="form-control" {!!build_attribute($fields['store_extras.email']['attr'])!!}/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['store_extras.mobile_no']['attr']['name']!!}">{!!$fields['store_extras.mobile_no']['label']!!}</label>               
										<input id="mobile_no" value="{!!$supplier_account_details->store_mobile or ''!!}" class="form-control" {!!build_attribute($fields['store_extras.mobile_no']['attr'])!!}/>                
										<span id="mobile_no-error"></span>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['store_extras.landline_no']['attr']['name']!!}">{!!$fields['store_extras.landline_no']['label']!!}</label>
										<input id="landline_no" value="{!!$supplier_account_details->landline_no or ''!!}" class="form-control" {!!build_attribute($fields['store_extras.landline_no']['attr'])!!}/>
									</div>
								</fieldset>
								<fieldset class="col-sm-4">
									<legend class="title">Pickup Address</legend>
									<div class="checkbox">
										<label for="same-as-given">Same as Company Address</label>
										<input type="checkbox" id="same-as-given"/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['address.postal_code']['attr']['name']!!}">{!!$fields['address.postal_code']['label']!!}</label>
										<input id="postal_code" data-selected="{!!$supplier_account_details->account_postal_code or ''!!}" value="{!!$supplier_account_details->account_postal_code or ''!!}" class="form-control same-as-given-field" {!!build_attribute($fields['address.postal_code']['attr'])!!}/>
									</div>

									<div class="form-group">
										<label class="control-label" for="{!!$fields['address.city_id']['attr']['name']!!}">{!!$fields['address.city_id']['label']!!}</label>
										<select data-selected="{!!$supplier_account_details->city_id or ''!!}" id="city_id" class="form-control same-as-given-field" {!!build_attribute($fields['address.city_id']['attr'])!!}>
										</select>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['address.state_id']['attr']['name']!!}">{!!$fields['address.state_id']['label']!!}</label>
										<select data-selected="{!!$supplier_account_details->state_id or ''!!}" id="state_id" class="form-control same-as-given-field" {!!build_attribute($fields['address.state_id']['attr'])!!}>
										</select>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['address.country_id']['attr']['name']!!}">{!!$fields['address.country_id']['label']!!}</label>
										<select data-selected="{!!$supplier_account_details->country_id or ''!!}" id="country_id" class="form-control same-as-given-field" {!!build_attribute($fields['address.country_id']['attr'])!!}>
										</select>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['address.flatno_street']['attr']['name']!!}">{!!$fields['address.flatno_street']['label']!!}</label>
										<input id="address1" data-selected="{!!$supplier_account_details->account_address1 or ''!!}" value="{!!$supplier_account_details->account_address1 or ''!!}" class="form-control same-as-given-field" {!!build_attribute($fields['address.flatno_street']['attr'])!!}/>
									</div>
									<div class="form-group">
										<label class="control-label" for="{!!$fields['address.address']['attr']['name']!!}">{!!$fields['address.address']['label']!!}</label>
										<input id="address2" data-selected="{!!$supplier_account_details->account_address2 or ''!!}" value="{!!$supplier_account_details->account_address2 or ''!!}" class="form-control same-as-given-field" {!!build_attribute($fields['address.address']['attr'])!!}/>
									</div>
									
									<div class="form-group">
										<label class="control-label" for="{!!$fields['store_extras.website']['attr']['name']!!}">{!!$fields['store_extras.website']['label']!!}</label>
										<input id="website" value="{!!$supplier_account_details->store_website or 'http://'!!}" class="form-control" {!!build_attribute($fields['store_extras.website']['attr'])!!}/>
									</div>
								</fieldset>
								<fieldset class="col-sm-4">
									<legend class="title">Working Details</legend>
									
									<div class="form-group">
										<label class="control-label" for="{!!$fields['store_extras.working_hours_from']['attr']['name']!!}">{!!$fields['store_extras.working_hours_from']['label']!!}</label>
										<div class="input-group">
											<input data-err-msg-to="#working_hours_from-error" id="working_hours_from" value="{!!$supplier_account_details->working_hours_from or ''!!}" {!!build_attribute($fields['store_extras.working_hours_from']['attr'])!!} class="form-control"/>
											<span class="input-group-addon">-</span>
											<input data-err-msg-to="#working_hours_to-error" id="working_hours_to" value="{!!$supplier_account_details->working_hours_to or ''!!}" {!!build_attribute($fields['store_extras.working_hours_to']['attr'])!!} class="form-control"/>
										</div>
										<span id="working_hours_from-error"></span>
										<span id="working_hours_to-error"></span>
									</div>
								</fieldset>
							</div>
							<div class="clearfix"></div>
							<div class="row">
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
@if (!empty($supplier_account_details->state_id))
<script>
    var state_id = <?php echo $supplier_account_details->state_id;?>;
    var city_id = <?php echo $supplier_account_details->city_id;?>;
</script>
@endif
@stop
@section('scripts')
<script src="{{asset('resources/supports/Jquery-loadselect.js')}}"></script>
<script src="{{asset('resources/supports/app.js')}}"></script>
<script src="{{asset('resources/supports/supplier/account-update.js')}}"></script>
@stop
