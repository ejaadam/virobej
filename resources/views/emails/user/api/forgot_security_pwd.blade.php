@extends('emails.user.maillayout')
@section('content')
<div>
    <p style="font-size: xx-large;
       color: green;">Dear <?php echo $uname;?>,</p>
    <p>Security Password has been updated successfully.	</p>
    <div class="cart_table clearfix">
        <table class="mcart_table_inner" width="100%" cellspacing="0" cellpadding="5" border="1" style="border-collapse: collapse;">
            <tbody class="pay_titlebg">
                <tr><td align="left">New Security Password:</td><td align="left"><?php echo $tpin;?></td></tr>
            </tbody>
        </table>
    </div>
    <div class="cart_table clearfix">
        <table class="table" style="width:100%">
            <tr>
                <td width="10" style="padding:7px 0">&nbsp;</td>
                <td style="padding:7px 0">
                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                    <p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px"><b>Important Security Tips:</b></p>
                    <ol style="margin-bottom:0">
                        <li>Always keep your account details safe.</li>
                        <li>Never disclose your login details to anyone.</li>
                        <li>Change your password regularly.</li>
                        <li>Should you suspect someone is using your account illegally, please notify us immediately.</li>
                    </ol>
                    </font>
                </td>
                <td width="10" style="padding:7px 0">&nbsp;</td>
            </tr>
        </table>
    </div>
</div>
@stop