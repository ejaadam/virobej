@extends('emails.maillayout')
@section('content')
<div>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center" style="padding:20px;">
        <tbody>	
		    <tr>
				<td>
					<center><h3>Password has been changed successfully!</h3></center>
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
					<p>Dear {{$name}},</p>
					
					<p>You have successfully changed your password. You can now login using the updated password.</p>
					
					<p>If this was not done by you, please contact our support team, we will help you resolve this issue.</p>
					
					<p>{{$siteConfig->site_name}} will never contact you to ask for your password and we advise using a unique password for every site. If you need any help setting up your account, please feel free to contact our Customer Support team at <a href="mailto:{{$siteConfig->merchant_care_emailid}}" title="{{$siteConfig->merchant_care_emailid}}">{{$siteConfig->merchant_care_emailid}}</a>.</p>
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
