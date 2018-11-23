@extends('affiliate.layout.dashboard')
@section('title',"Referral Bonus")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Referral Bonus</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li>Bonus</li>
        <li class="active">Referral Bonus</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12">
             <div class="box box-primary">
					<div class="box-header with-border">
	                   <form id="form_referral_bonus" class="form form-bordered" action="{{URL::to('account/bonus/referral')}}" method="post">
                       		{!! csrf_field() !!}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="from">{{\trans('affiliate/fund_transfer_history.username')}}</label>
									   <div class="input-group">
                                    <input type="text" id="search_term" name="search_term" class="form-control col-xs-12"  value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" placeholder="{{trans('affiliate/withdrawal/history.search_term_phn')}}">
									<div class="input-group-btn ">
                               <button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('affiliate/general.filter')}} <span class="caret"></span></button>
                                     <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" id="chkbox">
									  <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="FromUser" type="checkbox" >{{trans('affiliate/bonus/referral_bonus.from_user')}}</label></li>
                                      <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Referral" type="checkbox">{{trans('affiliate/bonus/referral_bonus.referrer')}}</label></li>
                                      </ul>
                                    </div>
									</div>
                                </div>
                            </div>
                              <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="bonus_referral_label"> {{trans('affiliate/bonus/referral_bonus.package')}} </label>
                                    <select name="type_of_package" id="type_of_package" class="form-control">
                                      <option value="">{{trans('affiliate/general.select_package')}}</option>
                                      @if(!empty($package_list))
                                      @foreach($package_list as $pkg_list)
                                      <option value="{{$pkg_list->package_id}}">{{$pkg_list->package_name}}</option>
                                      @endforeach
                                      @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="referral_bouns_list" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                                    
                                <th class="text-center">{{trans('affiliate/general.create_date')}}</th>
                                <th>{{trans('affiliate/bonus/referral_bonus.from_uname')}}</th>
                                <th>{{trans('affiliate/bonus/referral_bonus.referrer')}}</th>
								 <th>{{trans('affiliate/bonus/referral_bonus.referred_group')}}</th>
                                <th>{{trans('affiliate/bonus/referral_bonus.package')}}</th>
                                <th>{{trans('affiliate/bonus/referral_bonus.pay_mode')}}</th> 
                                <th>{{trans('affiliate/bonus/referral_bonus.net_amt')}}</th> 
                                <th>{{trans('affiliate/general.status')}}</th>                                                       
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
					</div>
				</div>
			</div>
			<!-- ./col -->			
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('user.common.datatable_js')
<script src="{{asset('js/providers/affiliate/bonus/referral_bonus.js')}}"></script>
@stop