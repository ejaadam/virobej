@extends('emails.maillayout')
@section('content')

                         <tr>
                            <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear  <?php echo $user->full_name;?><br /><br />
                      		</td>
                        </tr>
                         <tr>
                            <td align="center" style="font-size:15px;font-weight:bold;color:#000">
                      		User Name - <?php echo $user->uname;?></td>
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
                            <td style="font-size:14px;color:#929292"><p>Hey, <?php echo $user->full_name;?> !</p>
                            <p>As a part of our security measures, we have enabled the use of OTP (One Time Password) for your current session.</p> 
        					<p> Following is the OTP to be entered on OTP authorization page to access your account.</p>                            
        					<p>Your (OTP)One Time Password is : <b><?php echo $tac_code;?></b></p>
                         </td>
                       </tr>
                      <tr><td  style="font-size:14px;color:#929292">"This e-mail is confidential and may also be privileged. If you are not the intended recipient, please notify us immediately; you should not copy or use it for any purpose, nor disclose its contents to any other person"</td></tr>
                    
                 @stop 
            