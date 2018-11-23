@extends('admin.common.layout')
@section('pagetitle','Dashboard')
@section('layoutContent')
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="box_stat box_ico">
            <span class="stat_ico stat_ico_1"><i class="li_shop"></i></span>
            <h4>{{$products->verified_products or '0'}} / {{$products->total_products or '0'}}</h4>
            <small>Verified / All</small>
            <p>Products</p>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="box_stat box_ico">
            <span class="stat_ico stat_ico_3"><i class="li_banknote"></i></span>
            <h4>{{$sales->stock_on_hand or '0'}} / {{$sales->sold_items or '0'}}</h4>
            <small>Stock / Sold</small>
            <p>Stock</p>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="box_stat box_ico">
            <span class="stat_ico stat_ico_4"><i class="li_vallet"></i></span>
            <h4>{{$orders->new or '0'}} / {{$orders->total or '0'}}</h4>
            <small>New / Total</small>
            <p>Orders</p>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="box_stat box_ico">
            <span class="stat_ico stat_ico_5"><i class="li_user"></i></span>
            <h4>{{$customers->active or '0'}} / {{$customers->total or '0'}}</h4>
            <small>Active / All</small>
            <p>Customers</p>
        </div>
    </div>
</div>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">Rencent Orders</h2>
        </div>
        <div class="panel-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Order Amount</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($recent_orders) &&!empty($recent_orders))
                    @foreach($recent_orders as $order)
                    <tr>
                        <td>{{$order->ordered_on}}</td>
                        <td><a href="">{{$order->order_code}}</a></td>
                        <td>{{$order->customer_name}}</td>
                        <td align="right">{{$order->qty}}</td>
                        <td align="right">{{$order->net_pay}}</td>
                        <td align="center"><span class="label label-{{$order->payment_status_class}}">{{$order->payment_status}}</span></td>
                        <td align="center"><span class="label label-{{$order->order_status_class}}">{{$order->order_status}}</span></td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <a class=" btn btn-link" href="">More...</a>
        </div>
    </div>
</div>
@stop
@section('scripts')
@stop
