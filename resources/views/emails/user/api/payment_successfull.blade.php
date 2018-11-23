@extends('emails.user.maillayout')
@section('content')

<tr>
	<tr>
		<td>				
		<p>Thank you for paying with PayGyft. Your Payment has been successfully processed.</p>		
		</td>
	</tr>
	<tr>
		<td style="padding:10px 0px 10px 10px;">
			<h2 style="text-align:right;color:#48ce0d;padding:0px 25px 0px 25px;"><span style="float:left;">{{$bill_amount}}</span> Success</h2>
			<table  style="border: 1px solid black;border-collapse: collapse;width:100%;">	
				<tr>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;" colspan="2"><b style="padding-left:185px;;color:#0b5394;">Transaction Details</b></td>
				</tr>
				<tr>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;"><b>Merchant Name</b> : {{$merchant}} {{(!empty($landmark) ? '('.$landmark.')' : '')}}</td>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;"><b>Bill Amount</b> : {{$bill_amount}}</td>
				</tr>
				<tr>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;"><b>Payment ID</b> : {{$payment_id}}</td>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;"><b>Merchant Support Phone</b> : {{$mobile}}</td>
				</tr>
				<tr>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;"><b>Merchant ID</b> : {{$merchant_id}}</td>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;"><b>Location</b> : <br>{{$location}}</td>
					
				</tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	<tr>
		<td style="padding:10px 0px 10px 10px;">
			<table  style="border: 1px solid black;border-collapse: collapse;width:100%;">	
				<tr>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;" colspan="2"><b style="padding-left:185px;;color:#0b5394;">Payment Summary</b></td>
				</tr>
				<tr>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;width:50%;">Paid through</td>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;width:50%;">{{$payment_type}}</td>
				</tr>
				<tr>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;width:50%;">Amount Paid</td>
					<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;width:50%;">{{$paid_amount}}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<p>You can view the transaction details anytime from PayGyft dashboard.</p>
			<p>For any queries please reach out to us at help@paygyft.com</p>
			<p>Happy Shopping with PayGyft!</p>
		</td>	
	</tr>
</tr>
@stop
