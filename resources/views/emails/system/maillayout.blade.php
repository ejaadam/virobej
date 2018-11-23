<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
$curdate='';
$curdate= date('Y-m-d H:i:s');?>
<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="font-family:Arial;font-size:15px;color:#666666">
  <tbody><tr>
    <td bgcolor="#00589a"><table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
        <tbody><tr valign="top">
          <td width="20">&nbsp;</td>
        </tr>
        <tr>
          <td width="30"></td>		  
          <td><img style="display:block; float:left" src="{{URL::asset('assets/img').'/'.'img-email/logo.png'}}"> <span style="float:right; color:#fff; margin-top:10px;">{{$curdate}}</span></td>
          <td width="20"></td>
        </tr>
        <tr>
          <td width="20">&nbsp;</td>
        </tr>
      </tbody></table></td>
  </tr>
 <tr>
    <td bgcolor="#afcfaf">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tbody><tr>
                <td width="20">&nbsp;</td>
            </tr>
              <tr>
                <td></td>
                <td colspan="3" bgcolor="#00589a" height="10"><img width="10" height="10" style="display:block" src="img-email/unnamed.gif" ></td>
                <td></td>
              </tr>
              <tr>
                <td width="20"><img width="20" height="20" style="display:block" src="img-email/unnamed.gif" ></td>
                <td width="10" bgcolor="#00589a"><img width="10" height="20" style="display:block" src="img-email/unnamed.gif" ></td>
                <td>
                <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center" style="padding:20px;">
                        <tbody>
  <!--add content -->
     @yield('content')
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
                            <td align="center" style="font-size:14px;color:#000000">Regards, <br> The <?php echo $pagesettings->site_name;?> Team
                            <p style="font-size:12px;color:#000000"><?php echo $pagesettings->site_name;?>  • 913 N Market Street, Suite 200, Wilmington, DE 19801 • USA</p>
                            </td>
                        </tr>              
                    </tbody></table>                </td>
                <td width="10" bgcolor="#00589a"><img width="10" height="20" style="display:block" src="{{URL::asset('assets/img').'/'.'img-email/unnamed.gif'}}" ></td>
                <td width="20"><img width="20" height="20" style="display:block" src="{{URL::asset('assets/img').'/'.'img-email/unnamed.gif'}}" ></td>          
            </tr>
        </tbody>
		</table>
    </td>
  </tr>
  <tr>
    <td><img width="600" height="34" style="display:block" src="{{URL::asset('assets/img').'/'.'img-email/unnamed.jpg'}}" ></td>
</tr>
<tr bgcolor="#afcfaf">
    <td>
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
            <tr>
                <td align="center">
                    <a target="_blank" href="https://twitter.com/5DollarGem"><img width="31" height="31" style="display:inline-block" alt="twitter" src="{{URL::asset('assets/img').'/'.'img-email/twitter.png'}}" ></a>
                    <a target="_blank" href="https://www.facebook.com/my5DollarGem"><img width="31" height="31" style="display:inline-block" alt="facebook" src="{{URL::asset('assets/img').'/'.'img-email/facebook.png'}}" ></a>

                </td>
            </tr>
            <tr>
                <td width="40">&nbsp;</td>
            </tr>
            
                        <tr><td align="center"><p style="font-size:9px;color:#373737; padding:0px 50px">Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, log in to your account and click "Contact Us" at the bottom of any page.</p></td ></tr>
            <tr>
                            <td bgcolor="#d7d7d7" height="1"></td>
                        </tr>
            <tr>
                <td align="center" style="font-size:10px;color:#373737">
                
                    <p style="padding:0px 50px"> This e-mail has been sent from 5DollarGem.com. This email, together with any attachments, is for the exclusive and confidential use of the addressee(s) and may be privileged. Any distribution, use, alteration or reproduction, of all or any part in any form, without the sender's prior consent is unauthorised and strictly prohibited and may be illegal. Unauthorised recipients are requested to preserve the confidentiality of this e-mail, to advise the sender immediately of any error in transmission and to delete the message from your computer. Any views expressed by an individual within this e-mail, which do not constitute or record legal advice, do not necessarily reflect the views of 5DollarGem. We will will not be liable for direct, special, indirect or consequential damages arising from alteration of the contents of this message by a third party or as a result of any virus being passed on. For information about 5DollarGem please visit our web site <a href="https://www.5DollarGem.com" target="_blank">https://www.5DollarGem.com</a></p>
                    <br>
                    <p>&copy; 2015 5DollarGem, All rights reserved
</p>
                                    </td>
            </tr>
            <tr>
                <td width="40">&nbsp;</td>
            </tr>
        </tbody></table>
    </td>
</tr>
<tr bgcolor="#afcfaf">
    <td>
        <img width="600" height="145" style="display:block" src="{{URL::asset('assets/img').'/'.'img-email/unnamed.png'}}">
    </td>
</tr>
</tbody></table>
</body>
</html>
  