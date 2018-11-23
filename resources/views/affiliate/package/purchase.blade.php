@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/package.buypackage_page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{\trans('affiliate/package.buypackage_page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>{{\trans('affiliate/package.package_page_title')}}</li>
        <li class="active">{{\trans('affiliate/package.buypackage_page_title')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
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
                            <div class="plan-duration">Validity : {{ucwords($pack->package_name)}}(s)</div>                           
                        </div>
                        <ul class="plan-stats list-unstyled text-center">
                            <li>QV <b>{{$pack->package_qv}}</b></li>
                            <li>Capping Weekly QV <b>{{$pack->weekly_capping_qv}}</b></li>
                            <li><a href="javascript:void(0);" class="package_more_details" >{{trans('affiliate/package.buypack_morebtn')}}</a></li>
                        </ul>                
                        <div class="package_more_section"><a href="#" class="closeBtn">{{trans('affiliate/package.buypack_closebtn')}} <i class="fa fa-close"></i></a>
                            <p>{{$pack->description}}
                            <?php                             
                                $str = '<li> Shopping Points: '.number_format($pack->shopping_points,2,',','.').'</li>';
								$str .= '<li>Refundable in: '.$pack->refundable_days.'days</li>';	
                                echo '<div class="well"><ul class="list-unstyled ">'.$str.'</ul></div>';
                           ?></p>
                        </div>                                    
                        <div class="text-center">
                            <a rel="{{$pack->package_id}}" class="btn btn-success btn-bordred btn-rounded waves-effect waves-light buy_now">{{trans('affiliate/package.buypack_buynowbtn')}}</a>
                        </div>
                    </div>
                </article>                
                @endforeach
             @endif
			<!-- ./packages list -->			
		</div>
		<!-- /.row -->        
    </section>
    <!-- /.content -->
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/package/purchase.js')}}"></script>
<script>$('body').toggleClass('loaded');</script>
@stop