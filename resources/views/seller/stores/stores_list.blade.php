@extends('seller.common.layout')
@section('pagetitle')
Suppliers List
@stop

@section('layoutContent')
<div id="stores_list">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default" id="list">
                <div class="panel-heading">
                    <a href="{{URL::to('admin/suppliers/stores/add')}}" id="add_stores" class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>{{trans('stores_list.add_stores_btn')}}</a>
                    <h4 class="panel-title">{{trans('stores_list.page_title')}} </h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form id="stores_list_form" action="{{URL::route('api.v1.seller.stores.list-data')}}" method="post">
                            <input type="hidden" class="form-control" id="status_col"  value ="status_value">
                            <div class="input-group col-sm-3">
                                <input type="text" id="search_text" name="search_text" class="form-control">
                                <div class="input-group-btn">
                                    <button data-toggle="dropdown" class="btn btn-default ">{{trans('stores_list.filter_dropdown')}} <span class="caret"></span></button>
                                    <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right">
                                        <li><label class="col-sm-12"><input type="checkbox"  name="filterTerms[]" id="uname" value="uname"/>  {{trans('stores_list.store_name_filter')}}</label></li>
                                        <li><label class="col-sm-12"><input type="checkbox"  name="filterTerms[]" id="phone"  value="phone"/>{{trans('stores_list.phone_filter')}}</label></li>
                                        <li><label class="col-sm-12"><input type="checkbox" name="filterTerms[]" id="code" value="code"/> {{trans('stores_list.store_code_filter')}}</label></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                    <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                    <span class="input-group-addon">-</span>
                                    <input class="form-control" type="text" id="to" name="to" placeholder="To">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button id="search" type="button" class="btn btn-primary btn-sm">{{trans('general.search_btn')}}</button>
                                <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="{{trans('general.export_btn')}}" formtarget="_new"/>
                                <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="{{trans('general.print_btn')}}" formtarget="_new"/>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="msg"></div>
                <table id="stores_list_table" class="table table-striped">
                    <thead>
                        <tr>

                            <th>{{trans('stores_list.store_name_filter')}}</th>
                            <th>{{trans('stores_list.company_name_fld')}}</th>
                            <th>{{trans('stores_list.store_code_filter')}}</th>
                            <th>{{trans('stores_list.phone_filter')}}</th>
                            <th>{{trans('stores_list.address_th')}}</th>
                            <th>{{trans('stores_list.city_th')}}</th>
                            <th>Status</th>
                            <th>{{trans('stores_list.updated_on_th')}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>
<div id="store_create" style="display:none">
    <div class="pageheader">
        <div class="row">
            <div id="alert-msg" class="alert-msg"></div>
            <div class="col-sm-12">
                <div class="panel panel-default" id="list">
                    <div class="panel-heading">
                        <h4 class="panel-title col-sm-6">{{trans('stores_list.div_panel_title')}}</h4>
                    </div>
                    <div id="successmsg">
                    </div>
                    <div class='panel-body'>
                        <form name="create_stores" class="form-horizontal" id="create_stores" action="">
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['create.store_name']['attr']['name']!!}">{!!$fields['create.store_name']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="store_name" {!!build_attribute($fields['create.store_name']['attr'])!!} />
                                </div>
                            </div>
							<!--<div class="form-group">
								<label class="control-label col-md-2" for="test_url">{!!trans('stores_list.store_logo_fld')!!}<span class="error">*</span></label>
								<div class="col-md-8">
									<input type="text" class="form-control" id="store_logo" name="create[store_logo]" value="" />
								</div>
							</div>-->
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.firstname']['attr']['name']!!}">{!!$fields['store_extras.firstname']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="firstname"  {!!build_attribute($fields['store_extras.firstname']['attr'])!!} />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.lastname']['attr']['name']!!}">{!!$fields['store_extras.lastname']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="lastname"  {!!build_attribute($fields['store_extras.lastname']['attr'])!!}/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.mobile_no']['attr']['name']!!}">{!!$fields['store_extras.mobile_no']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="mobile_no"  {!!build_attribute($fields['store_extras.mobile_no']['attr'])!!}/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.landline_no']['attr']['name']!!}">{!!$fields['store_extras.landline_no']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="landline_no"  {!!build_attribute($fields['store_extras.landline_no']['attr'])!!}/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.email']['attr']['name']!!}">{!!$fields['store_extras.email']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="email"  {!!build_attribute($fields['store_extras.email']['attr'])!!} />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.address1']['attr']['name']!!}">{!!$fields['store_extras.address1']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="address1"  {!!build_attribute($fields['store_extras.address1']['attr'])!!} />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.address2']['attr']['name']!!}">{!!$fields['store_extras.address2']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="address2"  {!!build_attribute($fields['store_extras.address2']['attr'])!!}/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.postal_code']['attr']['name']!!}">{!!$fields['store_extras.postal_code']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="postal_code"  {!!build_attribute($fields['store_extras.postal_code']['attr'])!!} />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.city_id']['attr']['name']!!}">{!!$fields['store_extras.city_id']['label']!!}<span class="error"></span></label>
                                <div class="col-md-8">
                                    <select class="form-control" id="city_id"  {!!build_attribute($fields['store_extras.city_id']['attr'])!!}></select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.state_id']['attr']['name']!!}">{!!$fields['store_extras.state_id']['label']!!}<span class="error"></span></label>
                                <div class="col-md-8">
                                    <select class="form-control" id="state_id"  {!!build_attribute($fields['store_extras.state_id']['attr'])!!}></select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.country_id']['attr']['name']!!}">{!!$fields['store_extras.country_id']['label']!!}<span class="error"></span></label>
                                <div class="col-md-8">
                                    <select class="form-control" id="country_id"  {!!build_attribute($fields['store_extras.country_id']['attr'])!!}></select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['store_extras.website']['attr']['name']!!}">{!!$fields['store_extras.website']['label']!!}<span class="error">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" id="website"  {!!build_attribute($fields['store_extras.website']['attr'])!!} value="http://" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="{!!$fields['create.status']['attr']['name']!!}">{!!$fields['create.status']['label']!!}<span class="error"></span></label>
                                <div class="col-md-8">
                                    <select class="form-control" id="status" name="create[status]">
                                        <option value="1">{!!trans('stores_list.enable_fld')!!}</option>
                                        <option value="0">{!!trans('stores_list.disable_fld')!!}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
								<label class="control-label col-md-2">Working Hours</label>
								<div class="col-md-8">
								@if(!empty($fields['store_extras.working_days']['options']))
									@foreach($fields['store_extras.working_days']['options'] as $option)
									<label class="checkbox"><input {{build_attribute(array_merge($fields['store_extras.working_days']['attr'],$option['attr']))}} {{isset($logged_userinfo->working_days) && in_array($option['value'], $logged_userinfo->working_days)?'checked="checked"':''}}>{{$option['label']}}</label>
									@endforeach
								@endif
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-2" for="">{!!$fields['store_extras.working_hours_from']['label']!!}</label>
								<div class="col-md-8">
									<div class="input-group">
										<input data-err-msg-to="#working_hours_from-error" id="working_hours_from" value="{!!$logged_userinfo->working_hours_from or ''!!}" class="form-control" {!!build_attribute($fields['store_extras.working_hours_from']['attr'])!!}/>
										<span class="input-group-addon">-</span>
										<input data-err-msg-to="#working_hours_to-error" id="working_hours_to" value="{!!$logged_userinfo->working_hours_to or ''!!}" class="form-control" {!!build_attribute($fields['store_extras.working_hours_to']['attr'])!!}/>
									</div>
									<span id="working_hours_from-error"></span>
									<span id="working_hours_to-error"></span>
								</div>
							</div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="email"></label>
                                <div class="col-md-8">
                                    <button id="save_manage" type="submit" class="btn btn-success btn-sm">{!!trans('general.create_btn')!!}</button>
                                    <button id="cancel_btn" class="btn btn-danger btn-sm">{!!trans('general.cancel_btn')!!}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('seller.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/supports/supplier/stores/store_list.js')}}"></script>
<!--script src="{{asset('resources/supports/admin/meta-info.js')}}"></script-->
@stop
