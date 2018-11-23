@extends('emails.maillayout')
@section('content')

<tr>
	<td>
		<p>Dear {{$full_name or ''}}!</p>
	</td>
</tr>
<tr>
	<td style="padding:0px 0px 0px 240px;">
		<h3>Your Verification OTP: {{$code}}</h3>
	</td>
</tr>
<tr>
	<td>
		
		<p>If you have not generated this request, please contact us for assistance.</p>
		<p>Virob</p>
		<p>This is an automated email - no need to reply.</p>
	</td>	
</tr>
@stop
