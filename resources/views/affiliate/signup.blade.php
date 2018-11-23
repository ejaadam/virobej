@extends('affiliate.layout.signuplayout')
@section('page-title','Affiliate Registration')
@section('contents')
<header class="header">
  <h1>{{trans('affiliate/signup.page_title',['site_name'=>$pagesettings->site_name])}}</h1>
</header>
<div class="row regbox">
	<div class="panel panel-warning text-center">
	  <h2>{{trans('affiliate/signup.join_bonus_title')}}</h2>
	  <h4>{{trans('affiliate/signup.join_today_forbonus',['site_name'=>$pagesettings->site_name])}}</h4>
	</div>      
</div>    
<div class="row regbox">
    <div class="medium-5 columns text-center">	
        <div class="row learnmore">
			<div class="medium-5 columns text-center">
              <a href="/0.0/free" target="_blank"><i class="fa fa-lightbulb-o fa-2x"></i>{{trans('affiliate/signup.learne_more_about',['site_name'=>$pagesettings->site_name])}}</a>
			</div>    
			<div class="medium-5 columns text-center">
              <a href="/0.0/saying" target="_blank"><i class="fa fa-comment fa-2x"></i>{{trans('affiliate/signup.people_saying')}}</a>
			</div>
        </div>        
        <div class="spacer"></div>          
        <img src="{{asset('resources/assets/themes/affiliate/img/i-register_woman.jpg')}}" class="register_woman" alt="{{trans('affiliate/signup.business_support',['site_name'=>$pagesettings->site_name])}}">        
    </div>      
    <div class="medium-7 columns regbox signup"> 
		<p>{{trans('affiliate/signup.shorttxt')}}</p>
        @if(isset($errmsg))
		<div class="alert alert-danger">{{$errmsg}}</div>
		@elseif(isset($sponsor_info))			
		<form id="signupFrm" action="{{route('aff.signup.save')}}" method="post">
			<div class="row" >
				<input type='hidden' name="sponser_account_id" value="{{$sponsor_info->sponser_account_id}}">		
			</div>
			<div class="row">
				<div class="small-6 medium-6 columns">
					<p>{{trans('affiliate/signup.Sponser_name')}}:<br>
					<b class="text-danger">{{$sponsor_info->sponser_name }}</b></p>				
				</div>
				<div class="small-6 medium-6 columns">
					<p>{{trans('affiliate/signup.sponser_country')}}:<br>
					<b class="text-danger">{{ $sponsor_info->sponser_country}}</b></p> 
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<div class="small-6 medium-6 columns">
							 <label>{{trans('affiliate/signup.firstname')}}
							 <input type="text" name="firstname" id="firstname"  placeholder="First name" value=""/>
							 </label>
					</div>
					<div class="small-6 medium-6 columns">
						 <label>{{trans('affiliate/signup.lastname')}}
						 <input type="text" name="lastname"  id="lastname" placeholder="Last name" value=""/>
						  </label>
					</div>
				</div>
			</div>   
			<div class="row">
			   <div class="small-6 medium-6 columns">
					<label>{{trans('affiliate/signup.username')}}
					<input type="text" name="username"  id="username" placeholder="Username" value=""/>
					</label>
			  </div>
			  <div class="small-6 medium-6 columns">
					<label>{{trans('affiliate/signup.email')}}
					<input type="text" name="email"  id="email" placeholder="you@email.com" value=""/>
					</label>
			  </div> 
			</div>
			<div class="row">            
				<div class="small-6 medium-6 columns">
				   <label>{{trans('affiliate/signup.password')}}
				  <input type="password" name="password" id="password"  placeholder="Choose a password" />
				  </label>
				</div>				
				<div class="small-6 medium-6 columns">
					<label>{{trans('affiliate/signup.confirm_password')}}
					<input type="password" name="confirm_password"  id="confirm_password" placeholder="Confirm password" />
					</label>
				</div> 
			</div>           
			<div class="row">			
				<div class="small-6 medium-6 columns">
					<label>{{trans('affiliate/signup.country')}}				
						<select name="country">
							<option value="">Select Country</option>
							@if(!empty($countries))
							@foreach ($countries as $country_val)
							<option value="{{$country_val->iso2}}">{{$country_val->country_name}}</option>
							@endforeach
							@endif
						</select>
					</label>
				</div>
				<div class="small-6 medium-6 columns">
					<label>{{trans('affiliate/signup.zipcode')}}
					<input type="text" name="postcode"  id="postcode" placeholder="Zipcode/Postal code password" />
					</label>
				</div>		   
			</div>  
			<div class="row">
				<div class="col-sm-12 columns">
					<input type="submit" id="submit_button" class="button large expand" value="I Want To Start Earning Money NOW!">
					<p><?php echo trans('affiliate/signup.privacy_txt',['site_name'=>$pagesettings->site_name,'link'=>url('privacy-policy')])?>
					 <p><em><?php echo trans('affiliate/signup.terms_txt',['site_name'=>$pagesettings->site_name,'link'=>url('terms-and-policy')])?> .</em></p>
				</div>
			</div>                             
		</form>
		@endif
	</div>   
</div>
<div class="regconfirm hidden">
	<div class="row">
		<div class="medium-12 columns">
			<div class="panel text-center">
				<h2 class='text-success'><i class="fa fa-check-circle-o"></i> Congratulations!</h2>
				<p>Your account created successfully, To activate your account we've sent an email to verify your email address. Please check your inbox.</p>
				<button class="btn btn-succes btn-sm" id="gotoLoginBtn" data-url="{{route('aff.login')}}">Login Now</button>
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/signup.js')}}"></script>
@stop