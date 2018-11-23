
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
    <td align="center" style="font-size:30px;color:#000">Verify Your Email Now!!
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
		    
        <p>
            In order to process your request,please click on the link below:
        </p>
        <p style="text-align: center;">
            <a target="_blank" style="background:none repeat scroll 0 0 #00cf00;font-weight:bold;color:#fff;font-size:14px;text-decoration:none;min-height:40px;line-height:40px;padding:0px 20px;text-transform:uppercase;display:inline-block" href="<?php echo $email_verify_link;?>">CLICK HERE TO VERIFY YOUR EMAIL ID</a>
        </p>
		<p>
      		Alternatively, you can enter the following email reset code.
        </p>
		<p style="text-align: center;">
            <span style="background-color:#f2f2f2;color:#141823;font-weight:bold;font-size:14px;text-decoration:none;min-height:40px;line-height:40px;padding:0px 40px;text-transform:uppercase;border:1px solid #ccc;display:inline-block">{{$verification_code}}</span>
        </p>
        <p>
            (OR) Copy and paste the below link in your browser.<br/>
            <?php echo $email_verify_link;?>
        </p>
        <p>
            If you did not request a Change Email , please contact Customer Support.
        </p>
        <p>

        </p>
    </td>
</tr>
@stop