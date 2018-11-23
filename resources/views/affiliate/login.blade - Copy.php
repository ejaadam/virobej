@extends('affiliate.layout.loginlayout')
@section('title',"Login");
@section('content')
<!-- signin -->
<div class="panel signin">
    <div class="panel-heading">
      <h1><a href="<?php echo route('home');?>" >{{$siteConfig->site_name}}</a></h1>
      <h4 class="panel-title">Welcome! Please Sign In.</h4>
    </div>
    <div class="panel-body">    	
      <form id="loginfrm"  method="POST" action="{{route('auth.login')}}">
		  {!! csrf_field() !!}
        <div class="form-group mb10">
          <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" name="uname" id="uname"  class="form-control" placeholder="Enter Member ID">
          </div>
        </div>
        <div class="form-group nomargin">
          <div class="input-group" style="position:relative">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input type="password" name="password" id="password"  class="form-control" placeholder="Enter Password">
            <a href="forgot-pwd" class="btn-forgot">Forgot?</a>
          </div>
        </div>
        <div>&nbsp;</div>
        <div class="form-group">
          <button class="btn btn-success btn-quirk btn-block"  name="toploginBtn" id="toploginBtn">Sign In</button>
         
        </div>
      </form>
      <div class="form-group">
        <a href="<?php echo URL::to('/signup');?>" class="btn btn-default btn-quirk btn-stroke btn-stroke-thin btn-block btn-sign">Not a member? Sign up now!</a>
      </div>
  </div>
</div>
<!-- signin -->
<!-- forgot -->
<div class="panel forgot" style="display:none">
	<div class="panel-heading">
      	<h1><a href="<?php echo URL::to('/home');?>" >{{$siteConfig->site_name}}</a></h1>
      	<h4 class="panel-title">Forgot Password.</h4>
    </div>
    <div class="panel-body">    	
        <form id="forgotfrm"  method="POST" action="{{route('auth.forgot')}}">
        {!! csrf_field() !!}
        <div class="form-group mb10">
          <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" name="uname" id="uname"  class="form-control" placeholder="Enter Member ID">
          </div>
        </div>
        <div class="form-group">
          <button class="btn btn-success btn-quirk btn-block"  name="topForgotBtn" id="topForgotBtn">Submit</button>         
        </div>
        </form>
        <div class="form-group">
        <a href="login" class="btn btn-default btn-quirk btn-stroke btn-stroke-thin btn-block btn-sign backtoLogin">CLICK HERE TO SIGN IN</a>
        </div>
    </div>
</div>
<!-- forgot -->
@stop
@section('scripts')
<script type="text/javascript" src="{{asset('js/providers/login.js')}}"  ></script>
@stop
