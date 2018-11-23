@extends('emails.user.maillayout')
@section('content')
<tr>
	<td>
		<p>You have successfully changed your Security PIN.</p>
		<p>Your security PIN is similar to the PIN on your ATM/debit card. Anyone who knows your PIN has access to your account. For that reason, you should protect your PIN and avoid disclosing it to anyone you do not wish to provide access to your account.</p>
	</td>
</tr>
<tr>
	<td>
		<p>If this was not you, kindly contact our <a href="mailto:help@virob.com" title="help@virob.com">support team</a> immediately. Virob will never contact you to ask for your Security PIN.</p>
	</td>
</tr>
@stop
