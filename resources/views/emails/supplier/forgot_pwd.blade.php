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
					<!--h3>Reset Your Password</h3-->
					<p>Hello,</p>
					<p>We're sorry you forgot your password. Don't worry, it happens to everyone.</p>
					<p>Simply click the button below and create a new password. Keep in mind, the link will expire in 8 hours.</p>
				</td>
			</tr>
			<tr>
				<td style="padding:0px 0px 0px 200px;">
					<h3><a href="{{$forgotpwd_link}}" style="background-color:#007fcb;padding:10px 17px;color:white;text-decoration: none; border-radius:5px">Reset Password</a></h3>
				</td>
			</tr>
			<tr> 
				<td style="padding: 15px; padding-top: -20px !important;">
					<!--p>Alternatively, you can create a new password by entering OTP (One Time Password)</p>
					<h1 style="text-align:center; color:#90f;">{{$code}}</h1-->			
					<p>If you didn't try to reset your password, <a href="{{$remove_forgotSess}}"><b>click here</b></a> and we'll forget this ever happened.</p>
					<p>{{$siteConfig->site_name}} support teams will never ask you to send or confirm your password or personal information via email. If you believe an unauthorized person accessed your account, please contact <a href="mailto:{{$siteConfig->merchant_care_emailid}}" title="{{$siteConfig->merchant_care_emailid}}">{{$siteConfig->merchant_care_emailid}}</a>.</p> 
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
						<li>Always keep your account details safe.</li>
						<li>Never disclose your login details to anyone.</li>
						<li>Change your password regularly.</li>
						<li>Should you suspect someone is using your account illegally, please notify us immediately.</li>
					</ul>
				</td>
			</tr>
			
			<!--OLD -->
			<!--tr>
				<td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear  <?php echo $uname;?>,<br /><br />
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:15px;font-weight:bold;color:#000">
				User Name - <?php echo $uname;?></td>
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
				<td align="center" style="font-size:30px;color:#000">Password has been updated successfully!</td>
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
					<p> Hey (<?php echo $uname;?>)!</p> 
					<p>As requested, your password has been changed.</p>
					<p>Your new password is:<?php echo '';?></p>
					<p>You can login using this password.</p>
					<p>Your username is - <?php echo $uname;?>.</p>
					<p><strong>Important Security Tips:</strong></p>	
					<ul style="margin: 0px; padding: -0px 0px 0px 16px; font-size: 14px; line-height:25px">
						<li> Always keep your account details safe.</li>
						<li>Never disclose your login details to anyone.</li>
						<li>Change your password regularly.</li>
						<li>Should you suspect someone is using your account illegally, please notify us immediately.</li>
					</ul>
			   </td>
			</tr-->
        </tbody>
    </table>
</div>
@stop