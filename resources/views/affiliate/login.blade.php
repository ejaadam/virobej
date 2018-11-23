@extends('affiliate.layout.loginlayout')
@section('title',"Login")
@section('content')
<div class="login-box signin">
  <div class="login-logo">
    <a href="{{url('/')}}"><b>{{$pagesettings->site_name}}</b> Affiliate</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"><b>Welcome!</b> Please Sign In.</p>
    <form id="loginfrm"  method="POST" action="{{route('aff.checklogin')}}">
      <div class="form-group has-feedback">
        <input type="text" name="uname" id="uname" class="form-control" placeholder="Enter Account ID/Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" id="password"  class="form-control" placeholder="Enter Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat" id="toploginBtn">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
	<div class="login-box-footer">
    <a href="" class="btn-forgot">Forgot your password?</a>
    <a href="{{url('signup')}}">Not a Affiliate? <b>Sign Up Now!</b></a>
	</div>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<div class="login-box forgot"  style="display:none">
  <div class="login-logo">
    <a href="{{url('/')}}"><b>{{$pagesettings->site_name}}</b> Affiliate</a>
  </div>
  <!-- /.forgot-logo -->
  <div class="login-box-body">
	<h4 class="">Enter Account ID/Email</h4>    	
    <form id="forgotfrm"  method="POST" action="{{route('aff.forgotpwd')}}">		
      <div class="form-group has-feedback">
        <input type="text" name="uname_id" id="uname_id"  class="form-control">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="row">        
        <div class="col-xs-4 col-xs-offset-8">
          <button type="submit" class="btn btn-primary btn-block btn-flat"  name="topForgotBtn"  id="topForgotBtn">Continue</button>
        </div>        
      </div>
    </form>
	<div class="login-box-footer">
    <a href="{{url('forgotpwd')}}" class="backtoLogin">Go to Sign In</a>    
	</div>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.forgot-box -->
@stop
@section('scripts')
<script type="text/javascript" src="{{asset('js/providers/affiliate/login.js')}}"  ></script>
@stop