@extends('emails.maillayout')
@section('title','Fund Transfer Notification')
@section('content')

  <tr><td style="font-size:15px;font-weight:bold;color:#000" align="center">Dear {{$from_full_name}},</td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td style="font-size:15px;font-weight:bold;color:#000" align="center">User Name - {{$from_uname}},</td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td height="1" bgcolor="#d7d7d7"></td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td style="font-size:25px;color:#000" align="center">Transferred the fund Successfully</td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr><td height="1" bgcolor="#d7d7d7"></td></tr>
        <tr><td width="40">&nbsp;</td></tr>
        <tr>
            <td style="font-size:14px;color:#929292"><p>Hey, {{$from_full_name}}!</p>
			   <p>You sent a payment of <b>{{$amount.' '.$currency}}</b> to {{$to_full_name.' ('.$to_uname.')'}}.</p><br />
                @if(isset($from_transaction_id))
                <p>Transaction ID:{{$from_transaction_id}}</p>
                @endif
            </td>
			</tr> 
			
			
			
			@stop