@extends('emails.user.maillayout')
@section('content')
<tr>
    <td style="padding: 10px;">
        <p>We received a request to change your account <b>{{$email}}</b> email address of {{$siteConfig->site_name}}. </p>
        <p>To confirm your request verification OTP as follows,</p>
        Your OTP : <b title="{{$code}}">{{$code}}</b>
    </td>
</tr>
@stop
