@extends('emails.maillayout')
@section('content')
<style>
    table{
        border-collapse: collapse;
        border: none;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        border-radius:3px;
        margin-left: 30px;

    }
    table tr{
        padding:2px 5px;
        border-radius:3px;
    }
    table tr th,table tr td{
        padding:5px;
    }
    table tr td img{
        border-radius:3px;
    }
    table.bordered tr{
        border:1px #cccccc solid;padding:5px;
    }


</style>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="margin-right:400px">   <strong>Hi  {{$name}}</strong>,</singleline></td>
</tr>
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <font style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">
    <p>{{$order->status_msg}}</p>
    </font>
</multiline>
</td>
</tr>
<tr>
    <td>
        <table class="bordered" width="570">
            <thead>
                <tr>
                    <th bgcolor="#E4E7EA"></th>
                    <th bgcolor="#E4E7EA"></th>
                    <th align="center" bgcolor="#E4E7EA">Item Price</th>
                    <th align="center" bgcolor="#E4E7EA">Qty</th>
                    <th align="right" bgcolor="#E4E7EA">Subtotal</th>
                </tr>
            </thead>
            @foreach($order->sub_orders as $sub_order)
            <tbody>
                <tr>
                    <th colspan="4">{{$sub_order->sub_order_code}}</th>
                    <td align="right">{{$sub_order->net_pay}} </td>
                </tr>
                @foreach($sub_order->particulars as $detail)
                <tr>
                    <td><img width="100px" src="<?php echo URL::asset($detail->file_path.$detail->img_path);?>" /></td>
                    <td>{{$detail->product_name}}</td>
                    <td align="center"> {{$detail->price)}} </td>
                    <td align="center">{{$detail->qty}}</td>
                    <td align="right">{{$detail->net_pay}} </td>
                </tr>

                @endforeach
            </tbody>
            @endforeach
            <tfoot>
                <tr>
                    <td bgcolor="#E4E7EA" colspan="4" align="right"> Total</td>
                    <td bgcolor="#E4E7EA" align="right">{{$order->sub_total}} </td>
                    <td bgcolor="#E4E7EA" align="right">{{$order->sub_total}} </td>
                </tr>
            </tfoot>
        </table>
    </td>
</tr>
</tbody>
</table>
@stop



