@extends('emails.maillayout')
@section('content')
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$userSess->full_name}},</td>
</tr>
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">User Name - {{$userSess->uname}} </td>
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
    <td align="center" style="font-size:30px;color:#000">Mobile Update Notification!!
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
<tr> <td style="font-size:14px;color:#929292"><p>Hey, {{$userSess->full_name}}! </p>
        <p>
		    As per your request your account mobile number has been changed.
        </p>
        <p>
            If you did not request a Change Mobile Number , please contact Customer Support.
        </p>
        <p>
           
        </p>
    </td>
</tr>
@stop
