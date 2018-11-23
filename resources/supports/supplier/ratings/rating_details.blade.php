<ul class="media-list comment-list" id="commentlist">
  <?php
    if (!empty($rating_detail))
		   {
		   ?>
  <li><small class="text-muted">Product Details</small>
  <p class="text-info"><strong>{{$rating_detail ->product_name}}</strong> ({{$rating_detail ->description}})</p>
  </li>
</ul>
 <h5>User Ratings </h5>
  <ul class="media-list feedbacklist">
   <li><small class="text-muted">Rating User Details</small> 
   <p class="text-info"><strong>{{$rating_detail ->full_name}}</strong>
        {{$rating_detail ->descriptions}}</p> 
        <div class="rateit" data-rateit-value="{{$rating_detail->rating}}" data-rateit-ispreset="true" data-rateit-readonly="true"></div>
         </li>   <hr />
</ul>
 {{ HTML::script('supports/member/rating/scripts/jquery.rateit.js') }}
 {{ HTML::style('supports/member/rating/scripts/rateit.css') }}
 <?php
 }?>