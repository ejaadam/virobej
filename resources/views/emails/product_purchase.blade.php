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
                Thank you for your order!
            </td>
        </tr>
        <tr>
            <td align="left" class="content gray" mc:edit="content2" style="font-family: Raleway, Arial; font-weight: 400; font-size: 13px; line-height: 19px; color: #585858; -webkit-font-smoothing: antialiased;">
                We will send you another email once the items in your order have been shipped. Meanwhile, you can check the status of your order on {{$pagesettings->site_domain}}
            </td>
        </tr><br>
    <tr>
        <td align="center" class="content gray" mc:edit="content2" style="font-family: Raleway, Arial; font-weight: 400; font-size: 13px; line-height: 19px; color: #585858; -webkit-font-smoothing: antialiased;">
            <a style="width:200px;margin:0px auto;background:linear-gradient(to bottom,#007fb8 1%,#6ebad5 3%,#007fb8 7%,#007fb8 100%);background-color:#027cd5;text-align:center;border:#004b91 solid 1px;padding:8px 0;text-decoration:none;border-radius:2px;display:block;color:#fff;font-size:13px" href="<?php echo URL::asset('user/redemption/orders/list');?>" align="center" target="blank" > <span style="color:#ffffff;font-size:13px;background-color:#007fb8">TRACK ORDER</span> </a>
        </td>
    </tr>
    <br>
    <tr>
        <td style="width:100%;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600">
                <thead>
                    <tr>
                        <th bgcolor="#E4E7EA"></th>
                        <th align="center" bgcolor="#E4E7EA">Item Name</th>
                        <th align="center" bgcolor="#E4E7EA">Item Price</th>
                        <th align="center" bgcolor="#E4E7EA">Qty</th>
                        <th align="right" bgcolor="#E4E7EA">Subtotal</th>
                    </tr>
                </thead><br>
                <tbody>
                    @foreach($cart_info as $product)
                    <tr>
                        <td><img width="100px" height="70px" src="<?php echo URL::asset($product['options']['prod_image_path'].$product['options']['prod_image_name']);?>" /></td>
                        <td>{{$product['name']}}</td>
                        <td align="center"> {{number_format($product['price'],0,'.',',')}} Points</td>
                        <td align="center">{{$product['qty']}}</td>
                        <td align="right">{{number_format($product['subtotal'],0,'.',',')}} Points</td>
                    </tr><br>
                @endforeach
                <br>
                <tr>
                    <td bgcolor="#E4E7EA" colspan="4" align="right"> Total</td>
                    <td bgcolor="#E4E7EA" align="right">{{number_format($total_price,0,'.',',')}} Points</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr><br>
    <tr>
        <td> <strong>DELIVERY ADDRESS</strong></td>
    </tr><br>
    <tr>
        <td>
            <address>
                <strong>{{$full_name}}</strong><br/>
                {{$address1}},<br/>
                {{$address2}},<br/>
                {{$city}},<br/>
                {{$state . '-' . $Postcode}}<br/>

                <addr title="mobile">Mobile No : {{$mobile}}</addr>
            </address>
        </td>
    </tr>

    <!--tr>
        <td>
            <ul>
                <li class="active">Order Placed</li>
                <li>Processing</li>
                <li>Transmitting</li>
                <li>Delivered</li>
            </ul>
        </td>
    </tr-->
</tbody>
</table>
@stop
