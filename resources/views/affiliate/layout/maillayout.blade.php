<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>@yield('title')</title>
        <style type="text/css">
            .ExternalClass {font-size:1px;}
            table {border-collapse:inherit}
            .ReadMsgBody {width: 100%;}
            .ExternalClass {width: 100%;}
            h2,h2 a,h2 a:visited,h3,h3 a,h3 a:visited,h4,h5,h6,.t_cht {color:#006E12 !important}
            div, p, a, li, td { -webkit-text-size-adjust:none; }
            table td { border-collapse: collapse; }
        </style>
    </head>
    <body bgcolor="#E6EBEE" style="table-layout:fixed;">
        <div style="width:100%;background-color:#E6EBEE;">
            <table width="100%" bgcolor="#E6EBEE" cellpadding="0" cellspacing="0" border="0" align="center">
                <tr><td bgcolor="#E6EBEE">
                        <table width="763" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr><td height="20"></td></tr>
                            <tr>
                                <td width="763" valign="top">
                                    <table width="763" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="61"></td>
                                            <td width="641" style="background:transparent url({{URL::asset('assets/img-email/header-top.png')}}) no-repeat left top; height:222px"></td>
                                            <td width="61"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td width="763" valign="top">
                                    <table width="763" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="61"><img src="{{URL::asset('assets/img-email/header-corner-top-left.png')}}" alt="" style="display:block;" /></td>
                                            <td width="641"><div style="position: relative; background:transparent url({{URL::asset('assets/img-email/header-top-middle.png')}}) no-repeat left top; height:70px"><div style="text-align:center; font-family:Tahoma, Geneva, sans-serif; padding-top:15px"><span style=" text-align:center;color:#000;">{{date('M d, Y H:i:sT')}}</span></div></div></td>
                                            <td width="61"><img src="{{URL::asset('assets/img-email/header-corner-top-right.png')}}" alt="" style="display:block;" /></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- TOP End -->
                            <!-- MIDDLE Start -->
                            <tr>
                                <td width="763" valign="top">
                                    <table width="763" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="61"></td>
                                            <td width="641">
                                            </td>
                                            <td width="61"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td width="763" valign="top">
                                    <table width="763" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="61" valign="bottom"><img src="{{URL::asset('assets/img-email/envelope-corner-top-left.png')}}" alt="" border="0" style="display:block;" /></td>
                                            <td width="641"style="font-size:14px;color:#000000" align="center"><table width="641" cellpadding="0" cellspacing="0" border="0" style="font-family:Tahoma, Geneva, sans-serif;">
                                                    <tr>
                                                        <td width="41" bgcolor="#FFFFFF"></td>
                                                        <td width="559" bgcolor="#CAD5DB">
                                                            <table width="559" cellpadding="0" cellspacing="0" border="0">
                                                                <!-- EVENTs Start -->
                                                                <tr>
                                                                    <td width="10"></td>
                                                                    <!-- EVENTs 1 Start -->
                                                                    <td width="250" valign="top">
                                                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                                            <tr>
                                                                                <td width="2%" bgcolor="#FFFFFF"><img src="{{URL::asset('assets/img-email/box-corner-top-left.png')}}" alt="" width="20" height="20" style="" /></td>
                                                                                <td width="210" bgcolor="#FFFFFF"></td>
                                                                                <td width="2%" bgcolor="#FFFFFF" align="right"><img src="{{URL::asset('assets/img-email/box-corner-top-right.png')}}" alt="" width="20" height="20" style="" /></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="20" bgcolor="#FFFFFF"></td>
                                                                                <td width="210" bgcolor="#FFFFFF">
                                                                                	<table style="padding:20px" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center">
    <tbody>
                                                                                    @yield('content')
                                                                                    </tbody>
                                                                                    </table>
                                                                                </td>
                                                                                <td width="20" bgcolor="#FFFFFF"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="20" bgcolor="#FFFFFF"><img src="{{URL::asset('assets/img-email/box-corner-bottom-left.png')}}" alt="" width="20" height="20" style="display:block;" /></td>
                                                                                <td width="210" bgcolor="#FFFFFF"></td>
                                                                                <td width="20" bgcolor="#FFFFFF" align="right"><img src="{{URL::asset('assets/img-email/box-corner-bottom-right.png')}}" alt="" width="20" height="20" style="display:block;" /></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td width="20"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="3%"><img src="{{URL::asset('assets/img-email/box-blue-corner-bottom-left.png')}}" alt="" width="20" height="20" style="display:block;" /></td>
                                                                    <td width="250">&nbsp;</td>
                                                                    <td width="3%" align="right"><img src="{{URL::asset('assets/img-email/box-blue-corner-bottom-right.png')}}" alt="" width="20" height="20" style="display:block;" /></td>
                                                                </tr>
                                                                <!-- EVENTs End -->
                                                            </table>
                                                        </td>
                                                        <td width="41" bgcolor="#FFFFFF"></td>
                                                    </tr>
                                                </table></td>
                                            <td width="61" valign="bottom"><img src="{{URL::asset('assets/img-email/envelope-corner-top-right.png')}}" alt="" border="0" style="display:block;" /></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- MIDDLE End -->
                            <tr>
                                <td width="763" valign="top">
                                    <table width="763" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="61"  style="background:transparent url({{URL::asset('assets/img-email/envelope-corner-top-left-middle.png')}}) no-repeat left top;"></td>
                                            <td width="641" align="center" valign="middle"   style="background:transparent url({{URL::asset('assets/img-email/envelope-1.png')}}) no-repeat left top; height:177px;"><div style="position:relative; text-align:center"><div style="position:absolute; width:100%; top:40px"><a href=""><img src="{{URL::asset('assets/img-email/facebook.png')}}" alt="" width="25" height="25" border="0"/></a></div></td>
                                            <td width="61"  style="background:transparent url({{URL::asset('assets/img-email/envelope-corner-top-right-middle.png')}}) no-repeat left top;"></td>
                                        </tr>
                                        <tr>
                                            <td width="61" style="background:transparent url({{URL::asset('assets/img-email/envelope-corner-top-left-bottom.png')}}) no-repeat left top; height:241px;"></td>
                                            <td width="641"  align="center" valign="middle" style="font-family:Tahoma, Geneva, sans-serif; background:transparent url({{URL::asset('assets/img-email/envelope-2.png')}}) no-repeat left top; height:241px; color:#ffffff" >
                                                <div style="width:60%; margin:0px auto">
                                                        <strong>Regards,</strong><br/> The {{$pagesettings->site_name}} Team
                                                        <?php
                                                        $country = isset($country) ? strtolower($country) : ( isset($userdetails) && isset($userdetails->country) ? strtolower($userdetails->country) : ( ( isset($franchiseedetails) && isset($franchiseedetails->country) ? strtolower($franchiseedetails->country) : 'others')));														
                                                        ?>
                                                        <p style="font-size:12px;color:#fff">{{(isset($pagesettings->address)) ? (isset($pagesettings->address[$country])?$pagesettings->address[$country]:$pagesettings->address['others']) : $pagesettings->address['others']}}</p>

                                                </div>
                                            </td>
                                            <td width="61" style="background:transparent url({{URL::asset('assets/img-email/envelope-corner-top-right-bottom.png')}}) no-repeat left top; height:241px;"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr><td height="20"></td></tr>
                            <!-- FOOTER Start -->
                            <tr>
                                <td width="763" valign="top">
                                    <table width="763" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="61"></td>
                                            <td>
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td align="center" style="text-align:center;line-height:18px;"><span style="font-family:Tahoma, Geneva, sans-serif;font-size:12px;color:#333F44;text-decoration:none;">Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, log in to your account and click "<a href="{{URL::to('contacts')}}">Contact Us</a>" at the bottom of any page.</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size:10px;color:#373737; font-family:Tahoma, Geneva, sans-serif; text-align:center;" ><p style="background:#fff; padding:10px;border-radius: 10px; border:2px solid #106eba; font-size:11px; font-weight:bold"><span style="color:#41b649">Consumer Advisory</span> - Projects can't fundraise for equity, offer financial incentives, or involve prohibited items. We're all in favor of investment, but they're not permitted on {{$pagesettings->site_name}}. Projects can't offer financial incentives like equity or repayment. Since our platform allows Donation or Reward based campaign it does not require the approval from any government authorities as per the current regulations. Users are advised to read the terms and conditions carefully.</p></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="15" align="center" style="font-size:10px;color:#373737; font-family:Tahoma, Geneva, sans-serif;">
                                                            <p> This e-mail has been sent from {{$pagesettings->site_domain}}. This email, together with any attachments, is for the exclusive and confidential use of the addressee(s) and may be privileged. Any distribution, use, alteration or reproduction, of all or any part in any form, without the sender's prior consent is unauthorised and strictly prohibited and may be illegal. Unauthorised recipients are requested to preserve the confidentiality of this e-mail, to advise the sender immediately of any error in transmission and to delete the message from your computer. Any views expressed by an individual within this e-mail, which do not constitute or record legal advice, do not necessarily reflect the views of {{$pagesettings->site_name}}. We will will not be liable for direct, special, indirect or consequential damages arising from alteration of the contents of this message by a third party or as a result of any virus being passed on. For information about {{$pagesettings->site_name}} please visit our web site <a href="{{URL::to('/')}}">{{URL::to('/')}}</a></p>
                                                            <p>{{ $pagesettings->footer_content }}</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="639" style="font-size:10px;color:#373737; font-family:Tahoma, Geneva, sans-serif; text-align:center;" >
                                                            <span style="font-family:Tahoma, Geneva, sans-serif;font-size:11px;color:#333F44;text-decoration:none;"><a href="{{URL::to('project-rules')}}" style="font-family:Tahoma, Arial, sans-serif;font-size:11px;color:#333F44;font-weight:normal;text-decoration:underline"><span style="color:#333F44;">Project Rules</span></a> | <a href="{{URL::to('promotion-rules')}}" style="font-family:Tahoma, Arial, sans-serif;font-size:11px;color:#333F44;font-weight:normal;text-decoration:underline"><span style="color:#333F44;">Promotion Rules</span></a> | <a href="{{URL::to('common-questions')}}" style="font-family:Tahoma, Arial, sans-serif;font-size:11px;color:#333F44;font-weight:normal;text-decoration:underline"><span style="color:#333F44;">Common Questions</span></a> | <a href="{{URL::to('terms-of-use')}}" style="font-family:Tahoma, Arial, sans-serif;font-size:11px;color:#333F44;font-weight:normal;text-decoration:underline"><span style="color:#333F44;">Terms of Use</span></a></span>
                                                        </td>
                                                    </tr>
                                                    <tr><td width="639" height="1"><img src="{{URL::asset('assets/img-email/line-dashed.png')}}" alt="" width="561" height="1" border="0" style="display:block;" /></td></tr>
                                                </table>
                                            </td>
                                            <td width="61"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr><td height="20"></td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
