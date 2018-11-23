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
@if(!empty($data) && !empty($item))
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="margin-right:400px">   <strong>Hi,  <?php echo $full_name;?></strong>,</singleline></td>
</tr>
<!--start content-->
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;" >We would like to inform you that we are processing your cancellation request for the following items in the Order #{{$data->sub_order_code}}.</p>
   <p>Seller{{$data->company_name}}</p>
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
                            <th align="center" bgcolor="#E4E7EA">Qty</th>
                            <th align="right" bgcolor="#E4E7EA">PRICE/UNIT</th>
                        </tr>
                    </thead>
                    <tbody>
                     

                        <tr>
                        
                            <!--<td><img width="100px" src="<?php /*?><?php echo URL::asset($data->file_path . $data->img_path); ?><?php */?>" /></td>-->
                            <td>{{$data->product_name}}</td>
                       <!--     <td align="center"> {{number_format( $data->amount,0,'.',',')}} </td>-->
                            <td align="center">{{$data->qty}}</td>
                            <td align="right">{{number_format($data->net_pay,0,'.',',')}} </td>
                        </tr>
                      
                       
                    </tbody>
                </table>
                    @if($data->payment_type_id !=19 && !empty($data->payment_type_id ))
                <p> The amount of Rs.{{number_format($data->net_pay, 0,'.',',')}} will be refunded back to your bank account/card.
                We look forward to seeing you again</p>
                @endif
            </td>
        </tr>


</tbody>
</table>
@endif<!-- item based cancel email -->

<!-- order based cancel email -->
@if(isset($order) && !empty($order))

<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="margin-right:400px">   <strong>Hi  <?php echo $full_name;?></strong>,</singleline></td>
</tr>
<!--start content-->
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">We would like to inform you that we are processing your cancellation request for the following items in the Order #{{$details[0]->sub_order_code}}.</p>
   <p>Seller{{$supplier}}</p> 
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
                           
                            <th align="center" bgcolor="#E4E7EA">Qty</th>
                            <th align="right" bgcolor="#E4E7EA">PRICE/UNIT</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($details as $detail)

                        <tr>
                        
                           <!-- <td><img width="100px" src="<?php /*?><?php echo URL::asset($detail->file_path . $detail->img_path); ?><?php */?>" /></td>-->
                            <td>{{$detail->product_name}}</td>
                           <td align="center">{{$detail->qty}}</td>
                            <td align="right">{{number_format($detail->net_pay,0,'.',',')}} </td>
                        </tr>
                        @if($detail->payment_type_id !=19 && !empty($detail->payment_type_id ))
                         <p> The amount of Rs.{{number_format($detail->net_pay, 0,'.',',')}} will be refunded back to your bank account/card.
                We look forward to seeing you again</p>
                @endif
                        @endforeach
                      
                    </tbody>
                </table>
                 
            </td>
        </tr>


</tbody>
</table>

@endif
@stop
