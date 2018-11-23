@extends('emails.maillayout')
@section('content')
<style>
    table{
        border-collapse: collapse;
        border: none;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        width:100%;
        border-radius:3px;
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
    ul{
        list-style: none;
        padding: 0;
        width:100%;
    }
    li{
        width:23%;
        text-align:center;
        float: left;
        padding: 5px;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        line-height: normal;
        font-size: 16px;
    }
    li.active{
        background-color:#14CA4C;
    }
</style>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="left" class="content gray" mc:edit="content2" style="font-family: Raleway, Arial; font-weight: 400; font-size: 13px; line-height: 19px; color: #585858; -webkit-font-smoothing: antialiased;">
                <strong>Hi Customer</strong>,
            </td>
        </tr>
        <tr>
            <td align="left" class="content gray" mc:edit="content2" style="font-family: Raleway, Arial; font-weight: 400; font-size: 13px; line-height: 19px; color: #585858; -webkit-font-smoothing: antialiased;">
                We would like to inform you that we are processing your cancellation request for the following item in the Order #{{$order_id}}.
            </td>
        </tr> <br><br>  
        <tr>
            <td>
                <table align="left" class="bordered" width="560">                    
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
                        @foreach($details as $detail)

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
                            <td bgcolor="#E4E7EA" align="right">{{$details[0]->net_pay}}</td>
                        </tr><br><br>
                    </tbody>                     
                </table>
            </td>
        </tr>

        
       
    </tbody>
</table>
@stop
