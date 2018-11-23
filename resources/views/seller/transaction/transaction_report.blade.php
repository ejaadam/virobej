@extends('seller.common.layout')
@section('pagetitle')
Transaction Report
@stop

@section('layoutContent')

    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Transaction Report</h4>
                </div>
                <div class="panel_controls">                    
					<form class="form-horizontal" action="#" method="post" id="transaction_report">
						<div class="row">
						<div class="col-sm-3">
							<input type="text"  placeholder="Search Term" name="term" id="term" class="form-control" >
						</div>
						<div class="col-sm-2">
							<select name="ewallet_id" id="ewallet_id" class="form-control">
								<option value="">Select Wallet</option>
								@if(isset($wallet_list) && !empty($wallet_list))
								@foreach ($wallet_list as $value)
								<option value="{{$value->wallet_id}}">{{$value->wallet}}</option>
								@endforeach
								@endif
							</select>
						</div>
						<div class="col-sm-2">
							<input type="text"  placeholder="From Date" name="from" id="from" class="form-control" />
						</div>
						<div class="col-sm-2">
							<input type="text"  placeholder="To Date" name="to" id="to" class="form-control" />
						</div>
						<div class="col-sm-3">
							<button id ="search" type="button" class="btn btn-sm btn-primary" title="Search">Search</button>
							<!--button name ="submit" type="submit" class="btn btn-sm btn-primary" title="Export" value="Export"/>Export</button>
							<button name ="submit" type="submit" class="btn btn-sm btn-primary" title="Print" value="Print"/>Print</button-->
						</div>
						 </div>
					</form>                   
                </div>
                <table id="transaction_report_datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>From / To Name (Username)</th>
                            <th>Description</th>
                            <th>Wallet</th>
                            <th>Amount</th>
                            <th>Paid Amount</th>
                            <th>Charges</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
@section('scripts')
<script src="{{asset('resources/supports/supplier/transaction_report.js')}}"></script>
@stop
