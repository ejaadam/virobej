@extends('emails.maillayout')
@section('content')
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
	
        <tr>
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;">
					<singleline label="title" style="margin-right:400px">Welcome to Virob | Email Verification</singleline>
			</td>
		</tr>
		<tr><td> Dear Sir/Ma'am </td></tr>
<tr>
    <td align="center" class="title-td" mc:edit="subtitle">
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;" width="590">
            <tbody>
                <tr>
                    <td class="small-img line2" height="1" style="font-size: 0px;line-height: 0px;border-collapse: collapse;background-color: #252525;"><img height="1" src="http://digith.com/agency/agency/demo/blue-2/images/spacer.gif" style="border: 0;display: block;-ms-interpolation-mode: bicubic;" width="1" /></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
<!--end title-->
<tr>
    <td class="small-img" height="16" style="font-size: 0px;line-height: 0px;border-collapse: collapse;"><img height="1" src="http://digith.com/agency/agency/demo/blue-2/images/spacer.gif" style="border: 0;display: block;-ms-interpolation-mode: bicubic;" width="1" /></td>
</tr>
<!--start content-->
<tr>
    <td align="left" class="content b" mc:edit="content1" style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; font-style: italic;">
<multiline label="content1">
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">Greetings from Virob!,</p>
  
      <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">Your registration process has been initiated. The next step is to verify your e-mail address.</p><br>
      <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">Your Virob e-mail address verification code is <strong><?php echo $code; ?></strong></p>
	  <br>
	  <span>Login to sellers.virob.com and enter the code.</span>
	  <p>Thank you.<br>Team Virob</p>
	  
  
</multiline>
</td>
</tr>
<!--end content-->
<tr>
    <td height="24"></td>
</tr>
<!--start 2 columns-->
<!--tr>
    <td>
        <table align="left"  border="0" cellpadding="0" cellspacing="0" class="col2" style="border-collapse: collapse;border: none;mso-table-lspace: 0pt;mso-table-rspace: 0pt;" width="0">
            <tbody>
                <tr>
                    <td align="left" class="content gray" mc:edit="content2" style="font-family: Raleway, Arial; font-weight: 400; font-size: 13px; line-height: 19px; color: #585858; -webkit-font-smoothing: antialiased;">
            <multiline label="content2">
                <table  class="wrapper last">
                    <tbody>
                        <tr><td>Your OTP :  </td></tr>
                    </tbody>
                </table>
            </multiline>
    </td>
</tr>
</tbody>
</table>
</td>
</tr-->
<!--end 2 columns-->
</tbody>
</table>
@stop
