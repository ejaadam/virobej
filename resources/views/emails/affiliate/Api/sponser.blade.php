@extends('emails.affiliate.maillayout')
@section('content')
<tr>
   <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$username}},</td>
</tr>
<tr>
   <td width="40">&nbsp;</td>
</tr>
<tr>
   <td align="center" style="font-size:15px;font-weight:bold;color:#000">User Name - {{$username}}, </td>
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
   <td align="center" style="font-size:30px;color:#000">Your Registered successfully</td>
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
   <td bgcolor="#d7d7d7" height="1"></td>
</tr>
<tr>
   <td width="40">&nbsp;</td>
</tr>
@stop