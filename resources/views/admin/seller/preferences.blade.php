@extends('admin.common.layout')
@section('pagetitle')
Preferences
@stop
@section('layoutContent')
<form class="form" id="preferences-form" action="{{URL::to('admin/seller/preferences/save')}}">
    <input type="hidden" name="supplier_id" id="supplier_id" value="{{$preferences->supplier_id or ''}}"/>
    <h2 class="title">Preferences</h2>
    <div class="row">
        <div class="col-sm-12">
            <div class="checkbox">
                <label class="control-label" for="is_ownshipment"><input type="checkbox" name="preferences[is_ownshipment]" id="is_ownshipment" value="1" {{ isset($preferences->is_ownshipment) && !empty($preferences->is_ownshipment)?'checked="checked"':''}}/>Self Shipment</label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-action col-sm-12 text-right">
            <input type="submit" class="btn btn-sm btn-success" value="Save"/>
        </div>
    </div>
</form>
@include('admin.common.assets')
<script src="{{asset('resources/supports/admin/seller/preferences.js')}}"></script>	
@stop
