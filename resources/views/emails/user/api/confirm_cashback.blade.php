@extends('emails.user.maillayout')
@section('content')
<tr>
    <td style="padding: 20px 20px 10px 10px;">
        <p>Dear Merchant, </p>        	
        <p>It seems that {{$name}} is requesting for a Cashback. Kindly share the below OTP to accept the Cashback and proceed further.</p>		
    </td>
</tr>
<tr>
	<td style="padding:-20px 10px 10px 10px;">
		<table  style="border-collapse: collapse;width:100%;">			
			<tr>
				<td style="padding:10px;width:2px;"></td>
				<td align="left" style="border-collapse: collapse;padding:10px;align:right;"><b>Customer Name</b></td>
				<td align="right" style="border-collapse: collapse;padding:10px;align:right;"><b>{{$name}}</b></td>			
			</tr>
			<tr>
				<td style="padding:10px;"></td>
				<td align="left" style="border-collapse: collapse;padding:10px;"><b>Customer ID</b></td>
				<td align="right" style="border-collapse: collapse;padding:10px;"><b>{{$customer_id}}</b></td>
			</tr>
			<tr>
				<td style="padding:10px;"></td>
				<td align="left" style="border-collapse: collapse;padding:10px;"><b>Bill Amount</b></td>
				<td align="right" style="border-collapse: collapse;padding:10px;"><b>{{$bill_amount}}</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td style="padding:20px 10px 10px 10px;">
		<table  style="border-collapse: collapse;width:100%;">
			<tr style="padding:10px;">
				<th></th>
				<th align="left" style="background-color:#85b213;color:white;padding:10px;width:600px;"><center><h2>OTP : <b>{{$code}}</b></h2></center></th>
				<th align="right" style="background-color:#85b213;color:white;padding:10px;"><b></b></th>				
			</tr>			
		</table>
	</td>
</tr>
<tr>
    <td style="padding:20px 10px 10px 10px;">        
        <p>Happy serving Customers !!!</p>		
    </td>
</tr>
@stop
