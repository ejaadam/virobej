@extends('emails.maillayout')
@section('title',$siteConfig->site_name.' - Payment Received :: '.$to_transaction_id.', Amount - '.$amount.' '.$currency)
@section('content')

        <tr><td style="font-size:15px;font-weight:bold;color:#000" align="center">Dear {{$to_full_name}},</td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td style="font-size:15px;font-weight:bold;color:#000" align="center">User Name - {{$to_uname}},</td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td height="1" bgcolor="#d7d7d7"></td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td style="font-size:25px;color:#000" align="center">This is to inform a payment has been successfully processed on your {{$siteConfig->site_name}} account.</td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td height="1" bgcolor="#d7d7d7"></td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td style="font-size:13px;color:#929292" align="center">Just thought you'd like to know {{$from_full_name}} ({{$from_uname}}) Sent You ({{$amount.' '.$currency}}).</tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td height="1" bgcolor="#d7d7d7"></td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr>
            <td style="font-size:14px;color:#929292">
                @if(isset($from_transaction_id))
                <p>Transaction ID: {{$to_transaction_id}}</p>
                @if(isset($payment_type))
                <p>Payment Mode: {{$payment_type}}</p>
                @endif
                 @if(isset($from_full_name))
                <p>Sender Name: {{$from_full_name}}</p>
                @endif
                 @if(isset($from_email))
                <p>Sender Email: {{$from_email}}</p>
                @endif
                  @if(isset($from_mobile))
                <p>Sender Phone: {{$from_mobile}}</p>
                @endif
                <br/>
                <br/>
                <p>For any queries, please mail us on {{$siteConfig->support_emailid}}.</p>
                @endif
            </td>
        </tr>
 
@stop
