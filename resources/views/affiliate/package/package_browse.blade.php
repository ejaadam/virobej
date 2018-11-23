@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/package/purchase.buypackage_page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa fa-files-o"></i> {{\trans('affiliate/package/purchase.buypackage_page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>{{\trans('affiliate/package/purchase.package_page_title')}}</li>
        <li class="active">{{\trans('affiliate/package/purchase.buypackage_page_title')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content" id="package_purchase">   		
		<!-- /.row -->
		<div class="row" id="packagegrid">        
			<!-- ./packages list  -->
            @if(!empty($packages))
			<?php $colr=1;?>            
            @foreach($packages as $pack)
                <article class="pricing-column col-sm-3 col-md-4  col-lg-3">
                    <div class="inner-box card-box">
                        <?php if($colr>7){ $colr=1;}?>
                        <div class="plan-header header_color{{$colr++}} text-center">
                            <h3 class="plan-title">{{$pack->package_name}}</h3>
                            <h2 class="plan-price">{{$pack->price}}<span>{{$pack->currency_code}}</span></h2>
                            <div class="plan-duration">{{trans('affiliate/package/purchase.pack_validity')}} : {{ucwords($pack->package_name)}}(s)</div>                           
                        </div>
                        <ul class="plan-stats list-unstyled text-center">
                            <li>{{trans('affiliate/package/purchase.pack_qv')}} <b>{{$pack->package_qv}}</b></li>
                            <li>{{trans('affiliate/package/purchase.pack_capping_qv')}} <b>{{$pack->weekly_capping_qv}}</b></li>
                            <li><a href="javascript:void(0);" class="package_more_details" >{{trans('affiliate/package/purchase.buypack_morebtn')}}</a></li>
                        </ul>                
                        <div class="package_more_section"><a href="#" class="closeBtn">{{trans('affiliate/package/purchase.buypack_closebtn')}} <i class="fa fa-close"></i></a>
                            <p>{{$pack->description}}
                            <?php                             
                                $str = '<li>'.trans('affiliate/package/purchase.pack_shopping_points').': '.number_format($pack->shopping_points,2,',','.').'</li>';
								$str .= '<li>'.trans('affiliate/package/purchase.pack_refund').': '.$pack->refundable_days.'days</li>';	
                                echo '<div class="well"><ul class="list-unstyled ">'.$str.'</ul></div>';
                           ?></p>
                        </div>                                    
                        <div class="text-center">
                            <a href="{{route('aff.package.paymodes')}}" class="btn btn-sm btn-success buy_now" data-id="{{$pack->package_id}}" data-info="{{json_encode($pack)}}">{{trans('affiliate/package/purchase.buypack_buynowbtn')}}</a>
                        </div>
                    </div>
                </article>                
                @endforeach
             @endif
			<!-- ./packages list -->	
			
		</div>
		<!-- paymodes -->
			<div class="row" id="paymodes" style="display:none">
				<!-- ./col -->
				<div class="col-md-3">	
					<div class="box box-primary">
						<div class="box-header with-border">
							<i class="fa fa-edit"></i>
							<h3 class="box-title">{{trans('affiliate/package/purchase.package_page_title')}}</h3>    
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-default btn-sm backto_packagebtn"><i class="fa fa-arrow-left"></i> {{trans('affiliate/package/purchase.backbtn')}}</button>
							</div>	                    			
						</div>
						<div class="box-body">            
							<ul class="list-group list-group-bordered"  id="packInfo">
							<li class="list-group-item">
							  <b>{{trans('affiliate/package/purchase.package_name_label')}}</b> <span class="pull-right text-info pkname">1,322</span>
							</li>
							<li class="list-group-item">
							  <b>{{trans('affiliate/package/purchase.package_price')}}</b> <span class="pull-right text-success pkamt">543</span>
							</li>                
						  </ul>                 
						</div>
					</div>
				</div>
				<div class="col-md-9" id="paymentprocess">	
					<div class="box box-primary">
						<div class="box-header with-border">
							<i class="fa fa-edit"></i>
							<h3 class="box-title">{{trans('affiliate/package/purchase.paymodes')}}</h3>                        			
						</div>
						<div class="box-body">
							<div class="form-group">
							<ul class="selpaymode">                   
							</ul>
							</div>                
						</div>
					</div>
				</div>
			</div>   
			<!-- paymodes -->
		<!-- /.row -->        
    </section>
    <!-- /.content -->
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/package/purchase.js')}}"></script>
@stop