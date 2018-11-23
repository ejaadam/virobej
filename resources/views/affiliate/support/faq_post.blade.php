@extends('affiliate.layout.dashboard')
@section('title',"FAQs")
@section('content')
<section class="inner-banner">
  <div class="thm-container">
    <h2>FAQ</h2>
    <ul class="breadcumb">
      <li><a href="index.html"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="faq.html">FAQ</a></li>
      <li><span>My First Drop2Wash</span></li>
    </ul>
  </div>
</section>
<section class="innercont-page faq-page">
  <div class="thm-container">
    
    <div class="row">
    <div class="sec-title m-b30">
          <h2><span><a href="{{URL::to('faq')}}">{{$title}}</a></span></h2>
        </div>
     <div class="col-md-12 col-sm-12">
        <form class="faq-form form form-bordered" name="faq" method="post" action="{{URL::to('account/support/faqs/search-term')}}">
           	{!! csrf_field() !!} 
			<div class="input-group">
				<input type="text" name="faq_word" placeholder="Enter Search Keywords" value="{{(isset($faq_word))?$faq_word:''}}">
                <span class="input-group-btn">
                  <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-search"></i> Search</button>
                </span>
             </div>
        </form>
        
        <div data-grp-name="faq-accrodion" class="accrodion-grp faq-accrodion">
		@if(!empty($faq_list))
			@foreach($faq_list as $key=>$val)
          <div class="accrodion ">
            <div class="accrodion-title">
              <h4>{{$key}}</h4>
            </div>
            <div class="accrodion-content" >
			{{$val}}
            </div>
          </div>
		  @endforeach
		  @endif
        </div>
        </div>
    
    </div>
  </div>
</section>
@stop