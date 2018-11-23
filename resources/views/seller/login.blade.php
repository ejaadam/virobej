@extends('seller.layouts.login')
@section('contents')
<div class="login_wrapper">
	<!--  Login Form  -->
    <div class="login_panel" id="login-panel">
        <div class="text-center">
            <img  style="width:60%" height="80" src="{{$pagesettings->site_logo}}" alt="{{$pagesettings->site_name}}">
            <div class="login_head">
                <h1><strong>Login</strong></h1>
            </div>
        </div>
        <form id="login_form" method="POST" action="{{route('seller.login')}}">
            <div class="form-group">
                <label for="username">{{$lfields['username']['label']}}</label>
				<!--span id="err_msg"></span-->
                <input id="username" class="form-control input-lg" {!!build_attribute($lfields['username']['attr'])!!} onkeypress="return RestrictSpace(event)">
            </div>
            <div class="form-group">
				
                <label for="password">{{$lfields['password']['label']}}</label>
                <input id="password" class="form-control input-lg" {!!build_attribute($lfields['password']['attr'])!!} onkeypress="return RestrictSpace(event)"/>
                
				<label class="checkbox"><a class="form_toggle pull-right" id="forgot-password-btn" href="#"> Forgot password?</a><input type="checkbox" name="login_remember" id="login_remember"> Remember me</label>
            </div>
            <div class="login_submit">
                <button class="btn btn-primary btn-block btn-lg" type="submit"  name="signmebutton" id="login_button">LOGIN</button>
            </div>
			<hr/>
            <div class="text-center">
                <p><a  id="" href="seller/sign-up" class="text-primary">New Seller? Signup Now</a></p>
            </div>
        </form>
    </div>
	<!--  Forgot Password  -->
    <div class="login_panel" id="forgot-pwd-panel" {{isset($forgot_password) && $forgot_password ? '' : 'style=display:none'}}>
        <div class="text-center">
            <img src="{{$pagesettings->site_logo}}"  style="width:60%" height="80" alt="{{$pagesettings->site_name}}">
            <div class="login_head">
                <h3 id="forgot_title">Recovery Password</h3>
				<h5>Enter your Email and instructions will be sent to you!</h5>
		    </div>
        </div>		
        <div id="forgot-password">
            <form id="forgotfrm" action="{{route('seller.forgot-password')}}" method="post"> <!-- lost-form  class="step-1"-->
                <div class="form-group">
                    <!--label for="username">Enter Your Email</label-->
                    <input {!!build_attribute($ffields['uname']['attr'])!!} id="username" class="form-control input-lg" value="" onkeypress="return RestrictSpace(event)"/>
                </div>
                <div class="login_submit">
                   <!-- <button class="btn btn-primary btn-block btn-lg">Next</button> -->
					<button class="btn button btn-block btn-primary"  type="submit">Reset</button>
                </div>
            </form>
			<!--form id="forgot_optfrm"  method="POST" action="" style="display: none;" autocomplete="off"> //'seller.forgot_opt'
                <h4 class=""></h4>
                <div id="options" class="well"></div>
                <span id="options-err"></span>
                <div class="row">
                    <div class="col-xs-4 col-xs-offset-4 login_submit">
                        <button type="submit" class="btn btn-block btn-flat btn-primary" disabled="disabled">Continue <i class="fa fa-angle-double-right"></i></button>
                    </div>
                </div>
            </form>
			<form id="resetfrm"  method="POST" action="" style="display:none;" autocomplete="off">    // seller.resetpwd           
                <div id="verify_code">
                    <div class="form-group has-feedback">
                        <label>Verification Code</label>
                        <input {!!build_attribute($rfields['code']['attr'])!!} id="code"  class="form-control" onkeypress="return isNumberKey(event)"/>
                        <span id="errmsg"></span>
                        <a href="#" class="text-blue block" id="resend-forgot-otp">Resend Verification Code</a>
                        <span class="fa fa-key form-control-feedback"></span>
                    </div>
                </div>
                <div id="new_password" style="display:none;">
                    <div class="form-group has-feedback">
                        <label>New Password</label>
                        <input {!!build_attribute($rfields['newpwd']['attr'])!!} id="newpwd"  class="form-control" onkeypress="return RestrictSpace(event)"/>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
					<div class="form-group has-feedback">
                        <label>Retype Password</label>
                        <input {!!build_attribute($rfields['confirm_pwd']['attr'])!!} id="confirm_pwd"  class="form-control" onkeypress="return RestrictSpace(event)"/>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-4 login_submit">
                            <button type="submit" class="btn btn-primary btn-block btn-flat" disabled="disabled">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
			<div class="text-center" id="resetpwd_success" style="display:none;">
                <p class="text-success"><i class="icon-ok-sign" style="font-size: 70px;"></i></p>
				<p id="success_msg">Password has been  updated successfully..</p>
            </div-->
            <div class="text-center">
                <!--small>Never mind, <a class="form_toggle" id="login-btn" href="#">send me back to the Login screen</a></small-->
				<a href="{{route('seller.login')}}" class="backtoLogin">Go to <b>Sign In Now!</b></a>
            </div>
        </div>
    </div>	
</div>
<script src="{{asset('js/providers/seller/login.js')}}"></script>
@stop
