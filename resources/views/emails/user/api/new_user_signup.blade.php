@extends('emails.user.maillayout')
@section('content')
<tr>
   <td align="center" style="font-size:15px;font-weight:bold;color:#000">
	<p style="font-family:Calibri,sans-serif; font-weight: 800; font-size: 18px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic; margin: 0px !important;">Hi {{$fullname}},</p>
   </td>
</tr>
<tr>
   <td width="40">
		 <p>Thanks for signing up! We're excited to have you on board. we know you’ll just love us. Get Started with exploring Virob and Hurray! Virob helps you get the best products and discount on your favourite stores.</p>
   </td>
</tr>
<tr>
	<td style="padding:30px 120px 20px 160px;" align="center">
        <a href="{{$email_verify_link}}" style="background-color:#00c484;padding:10px;color:white;text-decoration: none; text-align: center;border-radius:5px" title="Verify Email Address"><b>Click Here to Verify Email</b></a>
    </td>
</tr>
<!--tr>
        <td>
                <p style="color:#990000;">Alternatively, you can verify email by entering OTP (One Time Password) -</p>
                <h1 style="text-align:center;color:#90f;">{{$code}}</h1>
        </td>
</tr-->
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
   <td>
        <h3>HOW DOES VIROB EARN? </h3>
        <p>Our merchants provide us a commission for generating sales and we pass this amount to our members in the form of CashBack. That means shopping through us will not only get you the best offers but also amazing CashBack on those purchases. Redeem your CashBack Points for next purchases.</p>
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
@stop