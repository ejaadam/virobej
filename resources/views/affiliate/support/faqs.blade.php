@extends('affiliate.layout.dashboard')
@section('title',"FAQs")
@section('content')
<style>
dl.list1 {
padding-left:0;
}
dl.list1 dd{
margin-left:0;
margin-bottom:10px;
border:1px solid #ddd;
background:#eee;
padding:10px 15px;
border-radius:5px;
list-style:none;
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-home"></i> FAQs</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li>Support</li>
    <li class="active">FAQs</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <!-- Small boxes (Stat box) -->
  <div class="row">
    <!-- ./col -->
    <div class="col-md-3">
    	<div class="box box-primary">
        	<div class="box-header with-border">
                <h3 class="box-title"><span>Frequently Ask Questions</span><br /><small class="text-muted">You've got questions. We've got answers.</small></h3>
            </div>
			<div class="box-body">
            	@if(!empty($faq_categories))
                <ul class="list-group list-group-bordered" id="faqcats" >
              @foreach($faq_categories as $category)
              <li class="list-group-item"><a href="{{URL::to('account/support/faqs').'/'.$category->link}}">{{$category->faq_category}}</a></li>
              @endforeach
              </ul>
              @endif
            </div>
        </div>  
    </div>
    <div class="col-md-9">
    	<div class="box box-primary">
        	<div class="box-header with-border">
            	<div class="col-md-6  pull-right">
            	<form id="faqfrm" name="faq" method="post" action="">
                   {!! csrf_field() !!} 
                  <div class="input-group">
				<input type="text" name="term" id="term" class="form-control" placeholder="Enter Search Keywords" value="{{(isset($faq_word))?$faq_word:''}}">
                <span class="input-group-btn">
                  <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-search"></i> Search</button>
                </span>
             </div>
                </form>
                </div>
                <h3 class="box-title"><span>Search Your Quories</span><br /><small class="text-muted">You've got questions. We've got answers.</small></h3>
            </div>
			<div class="box-body">
            	<p><strong>Search for:</strong> "<span id="searct_label"></span>"</p>
            	<dl  id="search_list" class="list1">                                
                </dl>            
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
<script>
$(function(){	
	$('ul#faqcats li a').click(function (e) {
		e.preventDefault();
		$('#searct_label').text($(this).text());
		$('#faqfrm').attr('action',$(this).attr('href'));
		$('#faqfrm #term').val('');
		$('#faqfrm').submit();
	});
	
	
	$('#faqfrm').on('submit',function (e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			dataType: 'json',
			data: $(this).serialize(),
			type: "post",
			cache: false,
			beforeSend:function(){
				$('#search_list').empty();
				$('body').toggleClass('loaded');
			},
			success: function (res) {
				if(res.list!='undefind' && res.list!=null){					
					$(res.list).each(function(key,elm){
						$('#search_list').append('<dd><h4>'+elm.questions+'</h4><p>'+elm.answers+'</p></dd>')
					});					
				}	
				$('body').toggleClass('loaded');		   
			},
			error:function(){
			
			}	   
		});
	});
	
	$('ul#faqcats li:nth(1) a').trigger('click');
});
</script>
@stop