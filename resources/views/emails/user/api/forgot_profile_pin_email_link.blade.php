@extends('emails.user.maillayout')
@section('content')
<tr>
    <td style="padding: 10px;">
        <p> Hi <b>{{$full_name}}</b> </p>
    </td>
</tr>
<tr>
	<td>
		<p>It seems that you forgot your Security PIN. No worries, simply click the button and create new Security PIN. </p>
	</td>
</tr>
<tr>
	<td style="padding:0px 0px 0px 240px;">
		<h3><a href="{{$reset_link}}" style="background-color:#007fcb;padding:12px 37px;color:white;text-decoration: none; border-radius:5px">Reset</a></h3>
	</td>
</tr>
<tr>
	<td>
		<p style="color:#990000;">Alternatively, you can create a new Security PIN by entering OTP (One Time Password)</p>
		<h1 style="text-align:center;color:#90f;">{{$code}}</h1>
		<p>Security PIN is used for authenticating Transactions, so it is advisable to not to share with other than trusted ones. Virob will never contact you to ask for your Security PIN.</p>
		<p>If you didn't make this change, please contact customer support at help@Virob.com.</p>
	</td>	
</tr>
@stop
