@extends('emails.maillayout')
@section('content')
<div>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center" style="padding:20px;">
        <tbody>
            <tr>
                <td align="center" style="font-size:15px;font-weight:bold;color:#000">
                    Dear <?php echo $account->name;?>,<br/></td>
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
                <td align="center" style="font-size:30px;color:#000">Invited to Gamification!</td>
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
                    <span class="subtitle" style="font-weight:500;font-size:16px;text-transform:uppercase;line-height:25px"> </span>
                    <p></p>
                    <p></p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@stop
