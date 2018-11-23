@extends('emails.user.maillayout')
@section('content')
<tr>
	<tr>
		<td>		
		<h1>Reset Your Password</h1>
		<p>Hello,</p>
		<!--p>We're sorry you forgot your password. Don't worry, it happens to everyone.</p-->
		<p>Your have request to reset password of your <?php echo $sitename; ?> account on <?php echo date('l, F jS, Y').' at '.date('h:i:s A'); ?>.</p>
		</td>
	</tr>	
	<tr> 
		<td>		
			<p style="color:#990000;">Your Verification code to reset Password is </p>
			<h1 style="text-align:center;color:#90f;">{{$code}}</h1>
			
			<p>After setting your password if you continue to have trouble logging in,</p>
			<p>Here are a few helpful hints:</p>
			<ol>
			<li>Make sure your "caps lock" is not on.</li>
			<li>Remember that your password is "CaSe SeNsItiVe".</li>
			<li>The security setting on your browser should be set to "medium".</li>
			<li>You may want to clear your temporary internet files (cache), then try to log in again.</li>
			</ol>
			<p>PayGyft support teams will never ask you to send or confirm your password or personal information via email. If you believe an unauthorized person accessed your account, please contact help@paygyft.com.</p> 

		</td>	
	</tr>
</tr>
@stop
