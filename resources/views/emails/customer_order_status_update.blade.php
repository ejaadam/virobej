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
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="margin-right:400px">   <strong>Hi  <?php echo $details->full_name;?></strong>,</singleline></td>
</tr>
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">
        @if ($current_status_id == 5)
    <p>We would like to inform you that we have cancelled the following item in the Order #{{$order_particulars[0]->order_code}}.</p>
    @endif
    @if ($current_status_id == 4)
    <p>We are pleased to inform you that following items in your Order #{{$order_particulars[0]->order_code}} has been Delivered.</p>
    @endif
    @if ($current_status_id == 3)
    <p>We are pleased to inform you that following items in your Order #{{$order_particulars[0]->order_code}} have been Dispatched and are ready to be Delivered. Once the item(s) are Delivered, you will receive an email.</p>
    @endif
    @if ($current_status_id == 2)
    <p>We are pleased to inform you that following items in your Order #{{$order_particulars[0]->order_code}} have been processed and are ready to be Dispatch. Once the item(s) are Dispatched, you will receive an email.</p>
    @endif
    @if ($current_status_id == 1)
    <p>We are pleased to inform you that following items in your Order #{{$order_particulars[0]->order_code}} have been Completed.</p>
    @endif
    </p>
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
            <tbody>
                @foreach($order_particulars as $detail)
                <tr>
                    <td><img width="100px" src="<?php echo URL::asset($detail->file_path.$detail->img_path);?>" /></td>
                    <td>{{$detail->product_name}}</td>
                    <td align="center"> {{number_format( $detail->price,0,'.',',')}} </td>
                    <td align="center">{{$detail->qty}}</td>
                    <td align="right">{{number_format($detail->net_pay,0,'.',',')}} </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </td>
</tr>
</tbody>
</table>
@stop
