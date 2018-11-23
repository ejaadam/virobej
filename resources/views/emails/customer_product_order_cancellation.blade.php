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
<!--start content-->
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;"> We would like to inform you that we have cancelled the following item in the Order #{{$order_particulars[0]->order_code}}.</p>
   
</multiline>
</td>
</tr>
<!--end content-->
<tr>
    <td height="24"></td>
</tr>
<!--start 2 columns-->
  <tr>
            <td>
<table align="left" class="bordered" width="560">                    
<thead>
                        <tr>
                            <th bgcolor="#E4E7EA"></th>
                            <th bgcolor="#E4E7EA"></th>
                            <th align="center" bgcolor="#E4E7EA">Item Price</th>
                            <th align="center" bgcolor="#E4E7EA">Qty</th>
                            <th align="right" bgcolor="#E4E7EA">Sub total</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($order_particulars as $detail)

                        <tr>
                        
                            <td><img width="100px" src="<?php echo URL::asset($detail->file_path . $detail->img_path); ?>" /></td>
                            <td>{{$detail->product_name}}</td>
                            <td align="center"> {{number_format( $detail->price,0,'.',',')}} </td>
                            <td align="center">{{$detail->qty}}</td>
                            <td align="right">{{number_format($detail->net_pay,0,'.',',')}} </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td bgcolor="#E4E7EA" colspan="4" align="right"> Total</td>
                            <td bgcolor="#E4E7EA" align="right">{{number_format($shippinginfo->net_pay, 0,'.',',')}} </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>


</tbody>
</table>
@stop










