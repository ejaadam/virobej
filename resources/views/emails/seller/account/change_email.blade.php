@extends('emails.maillayout')
@section('content')
<tr>
    <td style="padding: 10px;">
	    <p>Dear {{$full_name}}</p>
		<p>You have initiated a request to change your email address.</p>
		<p>Please click on this link to validate the change in your email address.</p>
		<p><center><a href="{{$email_verify_link}}" style="background-color:#00c484;padding:10px;color:white;text-decoration: none; text-align: center;border-radius:5px" title="Verify Email Address"><b>Click Here to Verify </b></a></center></p>
        <p>If you have not generated this request, please contact us for assistance</p>
        <p>Virob</p>
        <p>This is an automated email - no need to reply</p>
    </td>
</tr>
@stop
