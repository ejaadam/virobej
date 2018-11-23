@extends('affiliate.layout.loginlayout')
@section('title',"Recover Password")
@section('content')
<div class="login-box recoverPwd">
  <div class="login-logo">
    <a href="{{url('/')}}"><b>Admin</b>LTE</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    @if(isset($errmsg))
    <div class="alert alert-danger text-center">{{$errmsg}}</div>
    <div class="login-box-footer">
    <a href="{{url('login')}}">Go to Sign In</a>    
	</div>
  	@elseif(isset($usrtoken))
  	<h4 class="text-center text-info">Choose a new password</h4>
    <p class="login-box-msg">A strong password is a combination of letters and punctuation marks. It must be at least 6 characters long.</p>
    <form id="recoverfrm"  method="POST" action="{{route('auth.update_newpwd')}}">
	    <input type="hidden" name="usrtoken" id="usrtoken" value="{{$usrtoken}}" />
		{!! csrf_field() !!}
      <div class="form-group has-feedback">
        <input type="password" name="newpassword" id="newpassword"  class="form-control" placeholder="Enter new password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">   
        <!-- /.col -->
        <div class="col-xs-4 col-xs-offset-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat" id="continueBtn">Continue</button>
        </div>
        <!-- /.col -->
      </div>
    </form>	
    <div class="login-box-footer">
    <a href="{{url('login')}}">Go to Sign In</a>    
	</div>
    @else
    <div class="alert alert-danger text-center">{{\Lang::get('user/forgotpwd.validate_msg.reset_sess_expireds')}}</div>
    <div class="login-box-footer">
    <a href="{{url('login')}}">Go to Sign In</a>    
	</div>
    @endif
    
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
@stop
@section('scripts')
<script type="text/javascript" src="{{asset('js/providers/affiliate/recoverpwd.js')}}"  ></script>
@stop