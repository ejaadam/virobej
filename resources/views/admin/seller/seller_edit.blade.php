@extends('admin.common.layout')
@section('pagetitle')
Suppliers Edit
@stop
@section('layoutContent')
@if(!empty($supplier_det))
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <form class="form-horizontal" id="edit_suppliers" method="post" enctype="multipart/form-data" action="<?php echo URL::to('admin/seller/update');?>">
                <div class="col-sm-12">
                    <table class="table table-bordered  table-default" id="account-details">
                        <tbody >
                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{$supplier_det->supplier_id or ""}}">
                        <input type="hidden" id="account_id" name="supplier_account_id" value="{{$supplier_det->account_id or ""}}">
                         <input type="hidden" id="address_id" name="address_id" value="{{$supplier_det->address_id or ""}}">
                        <tr>
                            <th colspan="2"><h3>Account Details</h3></th>
                        </tr>
                        <tr>
                            <th>Supplier Name</th>
                            <td> <input id="supplier_name" name="supplier_name" type="text" class="form-control" value="{{$supplier_det->uname or ''}}" disabled="disabled" ></td>
                        </tr>
                        <tr>
                            <th>Company Name </th>
                            <td><input id="company_name" name="company_name" type="text" class="form-control" data-required="true" value="{{$supplier_det->company_name or ''}}" disabled="disabled"></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><input id="street1" name="street1" type="text" class="form-control" Placeholder="{{trans('create_suppliers.street_1')}}" data-required="true" value="{{$supplier_det->flatno_street or ''}}"></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td><input id="street2" name="street2" type="text" class="form-control" Placeholder="{{trans('create_suppliers.street_2')}}" data-required="true" value="{{$supplier_det->address or ''}}"></td>
                        </tr>
						<tr>
                            <th></th>
                            <td><input id="Postcode" name="Postcode" type="text" class="form-control" Placeholder="{{trans('create_company.Postcode')}}" data-required="true" value="{{$supplier_det->postal_code or ''}}"></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
								<select name="city" id="city" class="form-control" data-required="true"></select>
							</td>
                        </tr>
                        <tr>
                            <th></th>
                            <td><select name="state" id="state" class="form-control selectfont" data-selected="{{$supplier_det->state_id}}" data-required="true"></select></td>
                        </tr>						
                        <tr>
                            <th></th>
                            <td>
                                <select name="country" id="country" data-selected="{{$supplier_det->country_id or ''}}" class="form-control" data-required="true"></select>
                            </td>
                        </tr>                        
                        <tr>
                            <th>Email</th>
                            <td><input id="email" name="email" type="text" class="form-control"data-required="true" data-type="email" value="{{$supplier_det->email or ''}}"></td>
                        </tr>
                         <tr>
                            <th>Mobile</th>
                            <td><input id="mobile" name="mobile" type="text" class="form-control" data-required="true" value="{{$supplier_det->mobile or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Fax</th>
                            <td><input type="text" class="form-control" id="officeFax" name="officeFax" maxlength="10" data-required="true" value="{{$supplier_det->office_fax or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td><input type="text" class="form-control" id="officePhone" name="officePhone" maxlength="10" data-required="true" value="{{$supplier_det->office_phone or ''}}"></td>
                        </tr>
                        </tbody>
                    </table>
                    <br/>
                    <table class="table table-bordered  table-default" id="store-details">
                        <tbody>
							 <input type="hidden" value="{{$supplier_det->store_details->store_id or ''}}" name="store_id">
                            <tr>
                                <th colspan="2"><h3>Store Details</h3></th>
								
							</tr>
                        
						@if(isset($supplier_det->store_details->store_code))
						<tr>
                            <th>Store Name</th>
                            <td>                               
                                <input type="text" class="form-control" id="store_name" readonly name="store_details[store_name]" value="{{$supplier_det->store_details->store_name or ''}}">
							</td>
                        </tr>
                        <tr>
                            <th>Store Code</th>
                            <td><input type="text" class="form-control" id="store_code" readonly name="store_details[store_code]" value="{{$supplier_det->store_details->store_code or ''}}"></td>
                        </tr>
						@else 
						<tr>
                            <th>Store Name</th>
                            <td>                               
                                <input type="text" class="form-control" id="store_name"  name="store_details[store_name]" value="{{$supplier_det->store_details->store_name or ''}}">
							</td>
                        </tr>
						@endif
                        <tr>
                            <th>Email</th>
                            <td><input type="text" class="form-control" id="email" name="store_extra[email]" value="{{$supplier_det->store_details->email or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td><input type="text" class="form-control" id="mobile_no" name="store_extra[mobile_no]" value="{{$supplier_det->store_details->mobile_no or ''}}"></td>
                        </tr>
						<tr>
                            <th>Address 1</th>
                            <td><input type="text" class="form-control" id="address1" name="store_extra[address1]" value="{{$supplier_det->store_details->address1 or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Address 2</th>
                            <td><input type="text" class="form-control" id="address2" name="store_extra[address2]" value="{{$supplier_det->store_details->address2 or ''}}"></td>
                        </tr>
						<tr>
                            <th>Postal Code</th>
                            <td><input type="text" class="form-control" id="postal_code" name="store_extra[postal_code]" value="{{$supplier_det->store_details->postal_code or ''}}"></td>
                        </tr>
						<tr>
                            <th>City</th>
                            <td><select class="form-control" id="store_city" name="store_extra[city_id]" ></select></td>
                        </tr>
						<tr>
                            <th>State</th>
                            <td>
                               <select class="form-control" id="store_state" name="store_extra[state_id]" data-selected="{{$supplier_det->store_details->state_id or ''}}"></select>
                            </td>
                        </tr>
                        <tr>
                            <th>Country</th>
                            <td>
                                <select  class="form-control" id="store_country" name="store_extra[country_id]" data-selected="{{$supplier_det->store_details->country_id or ''}}">
                                </select>
                            </td>
                        </tr>						
                        <tr>
                            <th>Website</th>
                            <td><input type="url" class="form-control" id="website" name="store_extra[website]" value="{{$supplier_det->store_details->website or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Working Hours</th>
                            <td>
                                <div class="input-group">
                                    <input type="time" class="form-control" id="working_hours_from" name="store_extra[working_hours_from]" value="{{$supplier_det->store_details->working_hours_from or ''}}">
                                    <span class="input-group-addon">-</span>
                                    <input type="time" class="form-control" id="working_hours_to" name="store_extra[working_hours_to]" value="{{$supplier_det->store_details->working_hours_to or ''}}">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br/>
                    <table class="table table-bordered  table-default" id="payment-settings">
                        <tbody>
                            <tr>
                                <th colspan="2"><h3>Payout Details</h3></th>
								<input type="hidden" value="{{$supplier_det->payment_details->payment_settings->setting_id or ''}}" name="setting_id">
                        </tr>
                        <tr>
                            <th>Bank Name</th>
                            <td><input type="text" class="form-control" id="bank_name" name="payment_settings[bank_name]" value="{{$supplier_det->payment_details->payment_settings->bank_name or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Account Holder Name</th>
                            <td><input type="text" class="form-control" id="account_holder_name" name="payment_settings[account_holder_name]" value="{{$supplier_det->payment_details->payment_settings->account_holder_name or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Account No</th>
                            <td><input type="text" class="form-control" id="account_no" name="payment_settings[account_no]" value="{{$supplier_det->payment_details->payment_settings->account_no or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Account Type</th>
                            <td>
							<!--input type="text" class="form-control" id="account_type" name="payment_settings[account_type]" value="{{$supplier_det->payment_details->payment_settings->account_type or ''}}"-->
							<select  class="form-control" id="account_type" name="payment_settings[account_type]" data-selected="{{$supplier_det->payment_details->payment_settings->account_type or ''}}">		
								@if (isset($supplier_det->payment_details->payment_settings->account_type))
									@if ($supplier_det->payment_details->payment_settings->account_type == 'Current')
									<option value="Current" selected>Current</option>
									<option value="Savings">Savings</option>
									@endif
								@endif
								<option value="Current" >Current</option>
								<option value="Savings" selected>Savings</option>
								
                            </select>
							</td>
                        </tr>
                        <tr>
                            <th>IFSC Code</th>
                            <td><input type="text" class="form-control" id="ifsc_code" name="payment_settings[ifsc_code]" value="{{$supplier_det->payment_details->payment_settings->ifsc_code or ''}}"></td>
                        </tr>
						<tr>
                            <th>Address 1</th>
                            <td><input type="text" class="form-control" id="address1" name="payment_settings[address1]" value="{{$supplier_det->payment_details->payment_settings->address1 or ''}}"></td>
                        </tr>
                        <tr>
                            <th>Address 2</th>
                            <td><input type="text" class="form-control" id="address2" name="payment_settings[address2]" value="{{$supplier_det->payment_details->payment_settings->address2 or ''}}"></td>
                        </tr>
						<tr>
                            <th>Postal Code</th>
                            <td><input type="text" class="form-control" id="post_code" name="payment_settings[postal_code]" value="{{$supplier_det->payment_details->payment_settings->postal_code or ''}}"></td>
                        </tr>
						<tr>
                            <th>City</th>
                            <td>
								<select  class="form-control" id="city_id" name="payment_settings[city_id]" data-selected="{{$supplier_det->payment_details->payment_settings->city_id or ''}}">
								</select>
							</td>
                        </tr>
						<tr>
                            <th>State</th>
                            <td>
                                <select  class="form-control" id="bank_state" name="payment_settings[state_id]" data-selected="{{$supplier_det->payment_details->payment_settings->state_id or ''}}">
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Country</th>
                            <td>
                                <select  class="form-control" id="bank_country" name="payment_settings[country_id]" data-selected="{{$supplier_det->payment_details->payment_settings->country_id or ''}}">
                                </select>
                            </td>
                        </tr>
                        
                        
                        
                        
                        </tbody>
                    </table>
                    <div class="text-right" style="padding-top: 10px;">
                        <input class="btn btn-md btn-primary" type="submit" name="supp_edit" id="supp_edit" value="{{trans('create_company.submit')}}">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@include('admin.common.assets')
@stop
@section('scripts')
<script>
var city = 0;
var store_city = 0;
var bank_city = 0;
<?php if (isset($supplier_det->city_id) && !empty($supplier_det->city_id)) { ?>
	city = <?php echo $supplier_det->city_id; ?>;
<?php } ?>
<?php if (isset($supplier_det->store_details->city_id) && !empty($supplier_det->store_details->city_id)) { ?>
	store_city = <?php echo $supplier_det->store_details->city_id ; ?>;
<?php } ?>
<?php if (isset($supplier_det->payment_details->payment_settings->city_id) && !empty($supplier_det->payment_details->payment_settings->city_id)) { ?>
	bank_city = <?php echo $supplier_det->payment_details->payment_settings->city_id; ?>;
<?php } ?>

</script>
<script src="{{asset('resources/supports/admin/seller/seller_edit.js')}}"></script>	
@stop
