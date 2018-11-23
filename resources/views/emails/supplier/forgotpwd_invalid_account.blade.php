@extends('emails.maillayout')
@section('content')
<div>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center" style="padding:20px;">
        <tbody>
			<tr>
				<td>
					<center><h3>Reset Your Password</h3></center>
				</td>
			</tr>
			<tr>
				<td width="40">&nbsp;</td>
			</tr>
			<tr>
				<td bgcolor="#d7d7d7" height="1"></td>
			</tr>
			<tr>
				<td width="40">&nbsp;</td>
			</tr>
			<tr>
				<td style="padding: 15px;">				
					<p>Dear Seller,</p>
					<p>It seems that you are trying to reset your password, but we are sorry that there is no account on file with this email address.</p>
					<p>It could be that you have an account under a different email address, or you might need to create a new account by clicking the button below.</p>
				</td>
			</tr>
			<tr>
				<td style="padding:0px 0px 0px 200px;">
					<h3><a href="" style="background-color:#007fcb;padding:10px 17px;color:white;text-decoration: none; border-radius:5px">Click Here to Sign Up</a></h3>
				</td>
			</tr>
			<tr>
				<td width="40">&nbsp;</td>
			</tr>
			<tr>
				<td bgcolor="#d7d7d7" height="1"></td>
			</tr>
			<tr>
				<td width="40">&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:14px;color:#929292">					
					<p><strong>Important Security Tips:</strong></p>	
					<ul style="margin: 0px; padding: -0px 0px 0px 16px; font-size: 14px; line-height:25px">
						<li> Always keep your account details safe.</li>
						<li>Never disclose your login details to anyone.</li>
						<li>Change your password regularly.</li>
						<li>Should you suspect someone is using your account illegally, please notify us immediately.</li>
					</ul>
				</td>
			</tr>
        </tbody>
    </table>
</div>
@stop

@extends('emails.retailer.layout.layout2')
@section('user-content')
<tr>
	<td style="padding: 15px;">				
		<p>Dear Merchant,</p>
		<p>It seems that you are trying to reset your password, but we are sorry that there is no account on file with this email address.</p>
		<p>It could be that you have an account under a different email address, or you might need to create a new account by clicking the button below.</p>
	</td>
</tr>
<tr>
	<td style="padding:0px 0px 0px 200px;">
		<h3><a href="" style="background-color:#007fcb;padding:10px 17px;color:white;text-decoration: none; border-radius:5px">Click Here to Sign Up</a></h3>
	</td>
</tr>
@stop
