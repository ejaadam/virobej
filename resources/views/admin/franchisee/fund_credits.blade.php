@extends('admin.common.layout')
@section('title','Support Center Fund Credits')
@section('layoutContent')
<style type='text/css'>.help-block{color:#f56954;}</style>
<section class="content">
    <div class = "row">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Support Center Fund Credits</h3>
            </div>
            <div class="block">
                @if(Session::has('fund_credit'))
                <div class="alert alert-success">{{Session::get('fund_credit')}}</div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-horizontal form-bordered" id="user_add_fund" action="#" method="post" >
                            <div class="form-group col-md-3 mg10">
                                <div class="col-md-12">
                                    <label class="form-label" for="uname">Username </label>
                                    <input type="text"  placeholder="Username" name="uname" id="uname" class="form-control" value="{{$uname or ''}}"/>
                                </div>
                            </div>
                            <div class="form-group col-md-4 mg10">
                                <div class="col-md-12">
                                    <label class="form-label" for="uname">Payment Type </label>
                                    <select name="payment_type" id="payment_type" class="form-control">
                                        <option value="">All</option>
                                        @if($payment_type_list)
                                        @foreach($payment_type_list as $row)
                                        <?php
                                        $search_replace = array(
                                            'Direct'=>'',
                                            'Transfer'=>'');
                                        $payout_name = strtr($row->payout_types, $search_replace);
                                        ?>
                                        <option value="{{$row->type_id}}" <?php
                                        if (!empty($payment_type))
                                        {
                                            echo ($row->type_id == $payment_type) ? "selected = 'selected'" : '';
                                        }
                                        elseif ($row->type_id == 16)
                                        {
                                            echo "selected = 'selected'";
                                        }
                                        ?>>{{$payout_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 mg10">
                                <div class="col-md-12">
                                    <label class="form-label" for="uname">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-control">
                                        <option value="">All</option>
                                        <option value="0" <?php
                                        if (($status != ''))
                                        {
                                            echo ($payment_status == 0) ? "selected = 'selected'" : '';
                                        }
                                        ?>>Initial</option>
                                        <option value="1" <?php
                                        if (!empty($status))
                                        {
                                            echo ($payment_status == 1) ? "selected = 'selected'" : '';
                                        }
                                        ?>>Confirmed</option>
                                        <option value="2" <?php
                                        if (!empty($status))
                                        {
                                            echo ($payment_status == 2) ? "selected = 'selected'" : '';
                                        }
                                        ?>>Failed</option>
                                        <option value="3" <?php
                                        if (!empty($status))
                                        {
                                            echo ($payment_status == 3) ? "selected = 'selected'" : '';
                                        }
                                        ?>>Cancelled</option>
                                        <option value="4" <?php
                                        if (!empty($status))
                                        {
                                            echo ($payment_status == 4) ? "selected = 'selected'" : '';
                                        }
                                        ?>>Pending/Processing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 mg10">
                                <div class="col-md-12">
                                    <label class="form-label" for="uname">Currency</label>
                                    <select name="currency_id" id="currency_id" class="form-control">
                                        <option value="">All</option>
                                        @if($currencies)
                                        @foreach($currencies as $currency)
                                        <option value="{{$row->type_id}}" {{(isset($currency_id) && $currency->id == $currency_id)?'selected = "selected"':''}}>{{$currency->code}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 mg10">
                                <div class="col-md-12">
                                    <label class="form-label" for="uname">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">All</option>
                                        <option value="0" <?php
                                        if (($status != ''))
                                        {
                                            echo ($status == 0) ? "selected = 'selected'" : '';
                                        }
                                        ?>>Pending</option>
                                        <option value="1" <?php
                                        if (!empty($status))
                                        {
                                            echo ($status == 1) ? "selected = 'selected'" : '';
                                        }
                                        ?>>Confirmed</option>
                                                <?php /* ?><option value="2" <?php if(!empty($status)) { echo ($status == 2) ? "selected = 'selected'" : '';}?>>Processing</option>
                                                  <option value="3" <?php if(!empty($status)) { echo ($status == 3) ? "selected = 'selected'" : '';}?>>Cancelled</option>
                                                  <option value="4" <?php if(!empty($status)) { echo ($status == 4) ? "selected = 'selected'" : '';}?>>Failed</option><?php */?>
                                        <option value="5" <?php
                                        if (!empty($status))
                                        {
                                            echo ($status == 5) ? "selected = 'selected'" : '';
                                        }
                                        ?>>Refund</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 mg10">
                                <div class="col-md-12">
                                    <label class="form-label" for="from">From Date </label>
                                    <input type="text"  placeholder="From Date" name="from" id="from" class="form-control" value="{{$from or ''}}"/>
                                </div>
                            </div>
                            <div class="form-group col-md-3 mg10">
                                <div class="col-md-12">
                                    <label class="form-label" for="to"> To Date </label>
                                    <input type="text"  placeholder="To Date" name="to" id="to" class="form-control" value="{{$to or ''}}"/>
                                </div>
                            </div>
                            <div class="form-group col-md-4 mg10">
                                <label class="form-label" for="to">Transaction Id</label>
                                <div class="input-group">
                                    <div class="input-group-btn search-panel">
                                        <button type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown">
                                            <span id="search_concept">Filter by</span> <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="">All</a></li>
                                            @if($payment_type_list)
                                            @foreach($payment_type_list as $row)
                                            <?php
                                            $search_replace = array(
                                                'Direct'=>'',
                                                'Transfer'=>'');
                                            $payout_name = strtr($row->payout_types, $search_replace);
                                            ?>
                                            <li><a href="#" data-value = "{{$row->type_id}}">{{$payout_name}}</a></li>
                                            @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                    <input type="hidden" name="search_param" value="{{$search_param}}" id="search_param" />
                                    <input type="text" class="form-control" name="transaction_id" id="transaction_id" class="form-control" placeholder="Transaction Id" />
                                   <!-- <span class="input-group-btn">
                                        <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button>
                                    </span>-->
                                </div>
                            </div>
                            <div class="form-group form-actions">
                                <div class="col-md-12">
                                    <input type="hidden" id="action_url" value="{{URL::to('/') . '/admin/change_user_fund_status/'}}" />
                                    <input  name ="search" type="button" class="btn btn-sm btn-primary" value="Search" id="search" />
                                    <input name ="submit" type="submit" class="btn btn-sm btn-primary" value="Export" />
                                    <input name ="submit" type="submit" class="btn btn-sm btn-primary" value="Print" />
                                    <button type="reset" class="btn btn-sm btn-warning"><i class="fa fa-repeat"></i> Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="box-body table-responsive">
                @if ($status==0)
                <!--                <div  class="form-inline form-group pull-right" >
                                    <label for="email">Update Status:</label>
                                    <select name="statusupdate" class="form-control"  id="statusupdate">
                                        <option value="">Select Status</option>
                                        <option value="1">Confirmed</option>
                                        <option value= "2">Processing</option>
                                        <option value= "3">cancel</option>
                                        <option value= "4">Failure</option>
                                    </select>
                                </div>-->
                @endif
                <form name="orderFrm" id="orderFrm" method="post" action="{{URL::to('admin/change_user_fund_status')}}">
                    <input type="hidden" id="order_statusupdate" name="order_statusupdate"/>
                    <table id="user_addfund_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Transaction Details</th>
                                <th nowrap="nowrap">Amount</th>
                                <th>Payment Type</th>
                                <th>Pay Status</th>
                                <th>Status</th>
                                <th>Verification Status</th>
                                <th>Updated On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="user_fund_details" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" style="width: 450px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Details</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">Loading in progress...</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addfund_payment_confirm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" style="width: 450px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Payment Confirm</h4>
                </div>
                <div class="modal-body">
                    <form id="payment_confirm_form" action="<?php echo URL::to('admin/addfund-payment-confirm');?>" onsubmit="return false;">
                        <input type="hidden" name="update_status" id="update_status" value="1">
                        <input type="hidden" name="uaf_id" id="uaf_id">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <td>Payment Gateway Transaction Id </td><td>:</td><td><input type="text" name="pg_id" id="pg_id" class="form-control" /></td>
                                </tr>
                                <tr>
                                    <td>Remarks</td><td>:</td><td><textarea cols="30" name="remarks" id="remarks" rows="5" class="form-control remarks"></textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center"><input type="submit" value="Make Payment" id="addfund_payment_confirm_submit" class="form-control"></td></tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addfund_payment_refund" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" style="width: 450px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Payment Refund</h4>
                </div>
                <div class="modal-body">
                    <form id="payment_refund_form" action="<?php echo URL::to('admin/addfund-payment-refund');?>" onsubmit="return false;">
                        <input type="hidden" name="update_status" id="update_status1" value="5">
                        <input type="hidden" name="uaf_id" id="uaf_id1">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <td>Remarks</td><td>:</td><td><textarea cols="30" name="remarks" id="remarks" rows="5" class="form-control remarks"></textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center"><input type="submit" value="Refund Payment" id="addfund_payment_refund_submit" class="form-control"></td></tr>
                            </tbody>
                        </table>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addfund_verify" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" style="width: 450px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Payment Verification</h4>
                </div>
                <div class="modal-body">
                    <form id="addfund_verify_form" action="<?php echo URL::to('admin/addfund-verify');?>" onsubmit="return false;">
                        <input type="hidden" name="relation_id" id="relation_id">
                        <input type="hidden" name="purpose" id="purpose">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <td>Remarks</td><td>:</td><td><textarea cols="30" name="remarks" id="remarks" rows="5" class="form-control remarks"></textarea></td>
                                </tr>
                                <tr>
                                    <td>Status</td><td>:</td><td>
                                        <select name="verification_status" id="verification_status" class="form-control">
                                            <option value="">-- Select Verification Status</option>
                                            @if(!empty($verification_status))
                                            @if(is_array($verification_status))
                                            @foreach($verification_status as $row)
                                            <option value="{{$row->verification_status_id}}">{{$row->verification_status}}</option>
                                            @endforeach
                                            @else
                                            <option value="{{$verification_status->verification_status_id}}">{{$verification_status->verification_status}}</option>
                                            @endif
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center"><input type="submit" value="Verify Payment" id="addfund_verify_submit" class="form-control"></td></tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
{{HTML::script('js/providers/admin/franchisee/franchisee_fund_credits.js')}}
<script type="text/javascript" src="https://rawgit.com/fronteed/iCheck/1.x/icheck.js"></script>
@stop
