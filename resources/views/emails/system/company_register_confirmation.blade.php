@extends('emails.maillayout')
@section('content')
<div>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center" style="padding:20px;">
        <tbody>
            <tr>
                <td align="center" style="font-size:15px;font-weight:bold;color:#000">
                    Dear <?php echo $company_name;?>,<br/><br />
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size:15px;font-weight:bold;color:#000">
                    User Name - <?php echo $username;?><br/></td>
            </tr>
            <tr>
                <td width="40">&nbsp;</td>
            </tr>
            <tr>
                <td bgcolor="#d7d7d7" height="1"></td>
            </tr>
            <tr>
                <td width="40">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" style="font-size:30px;color:#000">Register Confirmed!</td>
            </tr>
            <tr>
                <td width="40">&nbsp;</td>
            </tr>

            <tr>
                <td bgcolor="#d7d7d7" height="1"></td>
            </tr>
            <tr>
                <td width="40">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-size:14px;color:#929292">
                    <span class="subtitle" style="font-weight:500;font-size:16px;text-transform:uppercase;line-height:25px"> Activate your <!--?php echo $pagesettings->site_name;?--> login</span>
                    <p>Thank you for signing up with <!--?php echo $pagesettings->site_name;?-->. To activate you account, you need to confirm the registration by clicking on the following link.</p>
                    <p><a href=""><b>Activate My Account</b></a></p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@stop
