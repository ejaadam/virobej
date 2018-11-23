@extends('emails.user.maillayout')
@section('content')
<tr>
    <td style="text-align:center;">        
        <h2 style="margin:0px;"><b>Cashback</b></h2>
    </td>
</tr>
<tr>
    <td>
        <p>Hurray !!! You have earned cashback from {{$store}} on {{$date}}.</p>
    </td>
</tr>
<tr>
    <td style="text-align:center;">
        <table  style="width:100%;">
            <tr>
                <td style="width:50%;text-align:right;">Order ID :</td>
                <td style="width:50%;text-align:left;" colspan="3"> {{$order_id}}</td>
            </tr>
            <tr>
                <td style="width:50%;text-align:right;">Bill amount :</td>
                <td style="width:50%;text-align:left;" colspan="3"> {{$bill_amount}}</td>
            </tr>
            <tr>
                <td style="width:50%;text-align:right;">Date :</td>
                <td style="width:50%;text-align:left;" colspan="3"> {{$date}}</td>
            </tr>
        </table>
        <table  style="width:100%;">
            <tr>
                <th style="text-align:left;">Wallet</th>
                <th style="text-align:center;">Transaction ID</th>
                <th style="text-align:right;">Cashback</th>
            </tr>
            @foreach($cashbacks as $cashback)
            <tr>
                <td style="text-align:left;">{{$cashback['wallet']}} :</td>
                <td style="text-align:center;">{{$cashback['trans_id']}}</td>
                <td style="text-align:right;">{{$cashback['cashback_amount']}}</td>
            </tr>
            @endforeach
        </table>
    </td>
</tr>
@stop
