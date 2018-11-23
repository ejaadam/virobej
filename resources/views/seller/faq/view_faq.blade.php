@extends('supplier.common.layout')
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<div id="main_content ">
    <!-- main content -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">FAQ's </h4>
                </div>
                <div class="panel-body" >
                    <div class="col-sm-12">
                        <div class="panel-group" id="accordion1"> @if(!empty($faqs))
                            @foreach($faqs as $faq)
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"> <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#acc1_collapse_{{$faq->id}}"> {{$faq->title}} <span class="icon-angle-left"></span></a> </h4>
                                </div>
                                <div id="acc1_collapse_{{$faq->id}}" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body"> {{$faq->description}} </div>
                                </div>
                            </div>
                            @endforeach
                            @endif </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
