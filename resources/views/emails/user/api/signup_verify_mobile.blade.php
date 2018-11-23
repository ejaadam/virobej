@extends('emails.user.maillayout')
@section('content')
<tr>
	<tr>
		<td>		
		<h1>Mobile Verification</h1>
		<p>Hello,</p>
		<p style="color:#990000;">{{$code}}  is your One Time Password for {{$sitename}}. Please enter OTP to complete your registration and it will expire after use or after 5 minutes.</p>
		</td>
	</tr>	
	<tr> 
		<td><p>Virob support teams will never ask you to send or confirm your password or personal information via email. If you believe an unauthorized person accessed your account, please contact help@Virob.com.</p> 

		</td>	
	</tr>
</tr>
@stop
