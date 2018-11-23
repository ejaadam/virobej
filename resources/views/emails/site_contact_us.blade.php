@extends('emails.maillayout')
@section('content')

<div>
<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center" style="padding:20px;">
                        <tbody>
                        <tr>
                            <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear  ,{{$username}}<br /><br />
                      		</td>
                        </tr>
                         <tr>
                            <td align="center" style="font-size:15px;font-weight:bold;color:#000">
                      		User Name - {{$username}}</td>
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
                            <td align="center" style="font-size:20px;color:#000">Customer Enquiry!</td>
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
							<p>User name:{{$username}}</p>
							<p>User email:{{$useremail}}</p>
                           <p>Message :</p><p> {{$msg}}</p>
                           <ul style="margin: 0px; padding: -0px 0px 0px 16px; font-size: 14px; line-height:25px">
                         
                            </ul>
                           </td>
                        </tr>
                     </tbody>
                   </table>
  				</div>
            @stop