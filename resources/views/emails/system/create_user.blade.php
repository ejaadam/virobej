@extends('emails.maillayout')
@section('content')
 <tr>
                        <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear <?php echo $fullname;?>,</td>
                        </tr>
                        <tr>
                        <td align="center" style="font-size:15px;font-weight:bold;color:#000">User Name - <?php echo $username;?>,</td>
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
                            <td align="center" style="font-size:30px;color:#000">Welcome to <?php echo $pagesettings->site_name;?><br> World's Best Crowdfunding Platform</td>
                        </tr>
                        <tr>
                            <td width="40">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:16px;color:#666666">Congratulations on becoming a part of the <?php echo $pagesettings->site_name;?> Community
</td>
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
                            <td align="center" style="font-size:13px;color:#929292">We hope to bring campaign creators and contributors on one platform, so we can create a new breed of caring people worldwide promoting charity drive orientated human beings towards a better future for all mankind alike. Discover some of our awesome campaigns & support the ones that deserve your attention! <br /><p style="font-size:18px;color:#2dcc00">Let's start an amazing journey together!</p> </td>
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
                            <td align="center"><b style="color:#066fc5">Your Login credentials are :</b>
                            <br />
                            <p><b>Username:</b> <?php echo $username;?></p>
							<p><b>Password:</b><?php echo $pwd;?></p>
                             <?php if (isset($tpin))
                                {?>
                             <p><b> Security Pin:</b> <?php echo $tpin;?> </p>
                            <?php }?>
                            </td>
                        </tr>

@stop