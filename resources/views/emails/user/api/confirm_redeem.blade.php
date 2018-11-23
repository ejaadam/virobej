@extends('emails.user.maillayout')
@section('content')

<tr>
    <td>
        <p>Greetings from Virob!!</p>
        <p>We noticed that you’ve redeemed some money from your Virob account.</p>
    </td>
</tr>
<tr>
    <td>
        <p><b>Transaction Summary</b></p>
        @if(isset($bp_trans_id) && !empty($bp_trans_id))
        <table  style="border: 1px solid black;border-collapse: collapse;width:100%;">
            <tr>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:right;width:40%;">Transaction ID</td>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:left;width:60%;">{{$bp_trans_id}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:right;width:40%;">Transaction Date</td>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:left;width:60%;">{{$bp_redeem_time}}</td>
            </tr>
			<tr>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:right;width:40%;">Bill Amount</td>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:left;width:60%;">{{$bill_amount}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:right;width:40%;">{{$bp_wallet}} Redeemed Amount </td>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:left;width:60%;">{{$bp_redeem_amount}}</td>
            </tr>
			<tr>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:right;width:40%;">Payment ID</td>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:left;width:60%;">{{$bp_payment_id}}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:right;width:40%;">Merchant Name</td>
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:left;width:60%;">{{$merchant}}</td>
            </tr>			
			<tr>
			    @if(isset($vim_trans_id) && !empty($vim_trans_id))
                <td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:right;width:40%;">Remaining paid through {{$vim_wallet}}</td>
			    @else
				<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:right;width:40%;">Remaining paid through Paid at Outlet</td>
				@endif
				<td style="border: 1px solid #dddddd;border-collapse: collapse;padding:10px;text-align:left;width:60%;">{{$remaining_amt}}</td>
            </tr>
        </table>
        @endif
    </td>
</tr>
<tr>
    <td>
        <p>Happy Shopping with PayGyft!</p>
    </td>
</tr>
@stop
