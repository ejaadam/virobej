@extends('emails.maillayout')
@section('content')
<div>
<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center" style="padding:20px;">
                        <tbody>
                        <tr>
                            <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear  <?php echo $uname;?>,<br /><br />
                      		</td>
                        </tr>
                         <tr>
                            <td align="center" style="font-size:15px;font-weight:bold;color:#000">
                      		User Name - <?php echo $uname;?></td>
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
                            <td align="center" style="font-size:30px;color:#000">Email has been changed successfully!</td>
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
                           <p> Hey (<?php echo $name;?>)!</p> 
               			   <p>As requested, your email has been changed.</p>
                		   <p>Your new email is:<?php echo $email;?></p>
                 		   <p>You can login using this email.</p>
                 		   <p>Your username is - <?php echo $uname;?>.</p>
                           <p><strong>Important Security Tips:</strong></p>	
                           <ul style="margin: 0px; padding: -0px 0px 0px 16px; font-size: 14px; line-height:25px">
                           <li> Always keep your account details safe.</li>
                           <li>Never disclose your login details to anyone.</li>
                           <li>Change your password regularly.</li>
                           <li>Should you suspect someone is using your account illegally, please notify us immediately.</li>
                            </ul>
                           </td>
                        </tr>
                     </tbody>
                   </table>
  				</div>
            @stop