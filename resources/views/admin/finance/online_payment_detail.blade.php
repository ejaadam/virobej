@if(!empty($details))
@if($details->purpose == config('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY'))
<div class="col-md-12">
    <div class="box box-primary">
        <div class="box-header with-border">
		    <i class="fa fa-money"></i>
			<h3 class="box-title">Payment Details</h3>
		    <div class="box-tools">
			    <button class="btn btn-sm btn-danger pull-right" id="back"><i class="fa fa-lg fa-arrow-circle-left margin-r-5"></i>Back</button>
			</div>
		</div>
        <div class="box-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Created On </label>
                </div>
                <div class="col-md-6">
                    {{date('d-M-Y',strtotime($details->created_on))}}
                </div>
            </div>
            @if(($details->purpose == 1)||($details->purpose == 2))
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Order Code </label>
                </div>
                <div class="col-md-6">
                    #{{$details->order_code}}
                </div>
            </div>
            @else
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Addfund code </label>
                </div>
                <div class="col-md-6">
                    #{{$details->order_code}}
                </div>
            </div>
            @endif
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Bill Amt </label>
                </div>
                <div class="col-md-6">
                    {{$details->famt}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Paid Amt </label>
                </div>
                <div class="col-md-6">
                    {{$details->amount}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Payment Status </label>
                </div>
                <div class="col-md-6">
                    <span class="label label-{{$details->payment_statusCls}}">{{$details->payment_statusLbl}}</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Order Status </label>
                </div>
                <div class="col-md-6">
                    <span class="label label-{{$details->statusCls}}">{{$details->statusLbl}}</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Payment Type</label>
                </div>
                <div class="col-md-6">
                    {{$details->payment_type_name}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Description</label>
                </div>
                <div class="col-md-6">
                    {{$details->description}}
                </div>
            </div>
            @if(!empty($gateway_responce))
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Reference Id</label>
                </div>
                <div class="col-md-6">
                    {{$gateway_responce->referenceId}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Payment Mode</label>
                </div>
                <div class="col-md-6">
                    {{$gateway_responce->paymentMode}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Gateway Responce</label>
                </div>
                <div class="col-md-6">
                    {{$gateway_responce->txStatus}}
                </div>
            </div>
            @endif
            @if($details->released_date != null)
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Released On</label>
                </div>
                <div class="col-md-6">
                    {{date('d-M-Y',strtotime($details->released_date))}}
                </div>
            </div>
            @endif
            @if($details->cancelled_date != null)
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Released On</label>
                </div>
                <div class="col-md-6">
                    {{date('d-M-Y',strtotime($details->cancelled_date))}}
                </div>
            </div>
            @endif
            @if($details->refund_date != null)
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Released On</label>
                </div>
                <div class="col-md-6">
                    {{date('d-M-Y',strtotime($details->refund_date))}}
                </div>
            </div>
            @endif
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Paid By </label>
                </div>
                <div class="col-md-6">
                    {{$details->fullname}} ({{$details->uname}})
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Mobile </label>
                </div>
                <div class="col-md-6">
                    {{$details->user_mobile}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Email </label>
                </div>
                <div class="col-md-6">
                    {{$details->user_email}}
                </div>
            </div>
        </div>
        <div class="box-header with-border"><i class="fa fa-user"></i><h3 class="box-title">Merchant details</h3></div>
        <div class="box-body">
            <div class="col-md-12">
                <div class="col-md-offset-2">
                    <img width="100" class="img img-thumbnail" src="{{$details->merchant_logo}}">
                </div>
            </div>
            @if(isset($details->store_name))
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Merchant </label>
                </div>
                <div class="col-md-6">
                    {{$details->store_name}}
                </div>
            </div>
            @endif
            @if(isset($details->formated_address))
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Address</label>
                </div>
                <div class="col-md-6">
                    {{$details->formated_address}}
                </div>
            </div>
            @endif
            @if(isset($details->store_mobile))
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Store Mobile</label>
                </div>
                <div class="col-md-6">
                    {{$details->store_mobile}}
                </div>
            </div>
            @endif
            @if(isset($details->store_email))
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Store Email</label>
                </div>
                <div class="col-md-6">
                    {{$details->store_email}}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@elseif($details->purpose == config('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE'))
<div class="col-md-12">
    <div class="box box-primary">
        <div class="box-header with-border"><i class="fa fa-money"></i><h3 class="box-title">Payment Details</h3><div class="box-tools"><button class="btn btn-sm btn-danger pull-right" id="back"><i class="fa fa-lg fa-arrow-circle-left margin-r-5"></i>Back</button></div></div>
        <div class="box-body">
            <div class="col-md-8">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Created On </label>
                    </div>
                    <div class="col-md-6">
                        {{date('d-M-Y',strtotime($details->created_on))}}
                    </div>
                </div>
                @if(($details->purpose == 1)||($details->purpose == 2))
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Order Code </label>
                    </div>
                    <div class="col-md-6">
                        #{{$details->order_code}}
                    </div>
                </div>
                @else
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Addfund code </label>
                    </div>
                    <div class="col-md-6">
                        #{{$details->order_code}}
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Bill Amt </label>
                    </div>
                    <div class="col-md-6">
                        {{$details->famt}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Paid Amt </label>
                    </div>
                    <div class="col-md-6">
                        {{$details->famount}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Payment Status </label>
                    </div>
                    <div class="col-md-6">
                        <span class="label label-{{$details->payment_statusCls}}">{{$details->payment_statusLbl}}</span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Order Status </label>
                    </div>
                    <div class="col-md-6">
                        <span class="label label-{{$details->statusCls}}">{{$details->statusLbl}}</span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Payment Type</label>
                    </div>
                    <div class="col-md-6">
                        {{$details->payment_type_name}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Description</label>
                    </div>
                    <div class="col-md-6">
                        {{$details->description}}
                    </div>
                </div>
                @if(!empty($gateway_responce))
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Reference Id</label>
                    </div>
                    <div class="col-md-6">
                        {{$gateway_responce->referenceId}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Payment Mode</label>
                    </div>
                    <div class="col-md-6">
                        {{$gateway_responce->paymentMode}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Gateway Responce</label>
                    </div>
                    <div class="col-md-6">
                        {{$gateway_responce->txStatus}}
                    </div>
                </div>
                @endif
                @if($details->released_date != null)
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Released On</label>
                    </div>
                    <div class="col-md-6">
                        {{date('d-M-Y',strtotime($details->released_date))}}
                    </div>
                </div>
                @endif
                @if($details->cancelled_date != null)
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Released On</label>
                    </div>
                    <div class="col-md-6">
                        {{date('d-M-Y',strtotime($details->cancelled_date))}}
                    </div>
                </div>
                @endif
                @if($details->refund_date != null)
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Released On</label>
                    </div>
                    <div class="col-md-6">
                        {{date('d-M-Y',strtotime($details->refund_date))}}
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Paid By </label>
                    </div>
                    <div class="col-md-6">
                        {{$details->fullname}} ({{$details->uname}})
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Mobile </label>
                    </div>
                    <div class="col-md-6">
                        {{$details->user_mobile}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Email </label>
                    </div>
                    <div class="col-md-6">
                        {{$details->user_email}}
                    </div>
                </div>
            </div>
        </div>
		<div class="box-header with-border"><i class="fa fa-user"></i><h3 class="box-title">Merchant details</h3></div>
        <div class="box-body">
            <div class="col-md-8">               
                <div class="col-md-12">
                    <div class="col-md-offset-2">
                        <img width="100" class="img img-thumbnail" src="{{$details->merchant_logo}}">
                    </div>
                </div>
                @if(isset($details->store_name))
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Merchant </label>
                    </div>
                    <div class="col-md-6">
                        {{$details->store_name}}
                    </div>
                </div>
                @endif
                @if(isset($details->formated_address))
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Address</label>
                    </div>
                    <div class="col-md-6">
                        {{$details->formated_address}}
                    </div>
                </div>
                @endif
                @if(isset($details->store_mobile))
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Store Mobile</label>
                    </div>
                    <div class="col-md-6">
                        {{$details->store_mobile}}
                    </div>
                </div>
                @endif
                @if(isset($details->store_email))
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Store Email</label>
                    </div>
                    <div class="col-md-6">
                        {{$details->store_email}}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@elseif($details->purpose == config('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'))
<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header with-border"><i class="fa fa-money"></i><h3 class="box-title">Payment Details</h3><div class="box-tools"><button class="btn btn-sm btn-danger pull-right" id="back"><i class="fa fa-lg fa-arrow-circle-left margin-r-5"></i>Back</button></div></div>
        <div class="box-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Created On </label>
                </div>
                <div class="col-md-6">
                    {{date('d-M-Y',strtotime($details->created_on))}}
                </div>
            </div>
            @if(($details->purpose == 1)||($details->purpose == 2))
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Order Code </label>
                </div>
                <div class="col-md-6">
                    #{{$details->order_code}}
                </div>
            </div>
            @else
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Addfund code </label>
                </div>
                <div class="col-md-6">
                    #{{$details->order_code}}
                </div>
            </div>
            @endif
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Bill Amt </label>
                </div>
                <div class="col-md-6">
                    {{$details->famt}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Paid Amt </label>
                </div>
                <div class="col-md-6">
                    {{$details->famount}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Payment Status </label>
                </div>
                <div class="col-md-6">
                    <span class="label label-{{$details->payment_statusCls}}">{{$details->payment_statusLbl}}</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Order Status </label>
                </div>
                <div class="col-md-6">
                    <span class="label label-{{$details->statusCls}}">{{$details->statusLbl}}</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Payment Type</label>
                </div>
                <div class="col-md-6">
                    {{$details->payment_type_name}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Description</label>
                </div>
                <div class="col-md-6">
                    {{$details->description}}
                </div>
            </div>
            @if(!empty($gateway_responce))
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Reference Id</label>
                </div>
                <div class="col-md-6">
                    {{$gateway_responce->referenceId}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Payment Mode</label>
                </div>
                <div class="col-md-6">
                    {{$gateway_responce->paymentMode}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Gateway Responce</label>
                </div>
                <div class="col-md-6">
                    {{$gateway_responce->txStatus}}
                </div>
            </div>
            @endif
            @if($details->released_date != null)
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Released On</label>
                </div>
                <div class="col-md-6">
                    {{date('d-M-Y',strtotime($details->released_date))}}
                </div>
            </div>
            @endif
            @if($details->cancelled_date != null)
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Released On</label>
                </div>
                <div class="col-md-6">
                    {{date('d-M-Y',strtotime($details->cancelled_date))}}
                </div>
            </div>
            @endif
            @if($details->refund_date != null)
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Released On</label>
                </div>
                <div class="col-md-6">
                    {{date('d-M-Y',strtotime($details->refund_date))}}
                </div>
            </div>
            @endif
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Paid By </label>
                </div>
                <div class="col-md-6">
                    {{$details->fullname}} ({{$details->uname}})
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Mobile </label>
                </div>
                <div class="col-md-6">
                    {{$details->user_mobile}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <label>Email </label>
                </div>
                <div class="col-md-6">
                    {{$details->user_email}}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@else
<h4 class="text-muted">Details not available</h4>
@endif

