@extends('emails.maillayout')
@section('content')
<tr>
	<td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$full_name}},</td>
</tr>
<tr>
	<td align="center" style="font-size:15px;font-weight:bold;color:#000">User Name - {{$uname}},</td>
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
	<td align="center" style="font-size:30px;color:#000">Security PIN Reset!</td>
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
    <td style="font-size:14px;color:#929292"><p>Hey, {{$full_name}}! </p>
    <p>Did you forget your Security PIN? That's okay, you can reset your Security PIN here. </p>
	<p>OTP code is {{$code}}</p>
    <p>In order to reset your Security PIN please click on the link below:</p>
    <p><a href="{{$resetlink}}" target="_blank">{{$resetlink}}</a></p>
    <p>If you did not request a Security PIN reset, please contact Customer Support.</p>
    <p>If you have any questions or need assistance, please e-mail us at   </p>
    </td>
</tr>
@stop