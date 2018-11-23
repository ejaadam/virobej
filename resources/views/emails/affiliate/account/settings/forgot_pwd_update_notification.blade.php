@extends('emails.user.layouts.blue')
@section('content')
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600"><!--start title-->
    <tbody>
        <tr>
            <td align="" class="h3 b title-td" mc:edit="title" style="font-family: 'Playfair Display'; font-weight: 400; color: #262424; font-size: 24px; line-height: 35px; font-style: italic;"><singleline label="title" style="margin-right:400px">Dear,  <?php echo $full_name; ?></singleline></td>
</tr>
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
    <p style="font-family: 'Playfair Display', Arial; font-weight: 400; font-size: 15px; line-height: 21px; color: #252525; -webkit-font-smoothing: antialiased; margin: 0px !important;">Your {{$siteConfig->site_name}} password was updated by using the email address {{$email}} on {{date('l, F jS, Y at h:i:sA')}}. </p>
<p>Operating system:	{{$browserInfo->platform}}<br />
Browser:	{{$browserInfo->name}}<br />
IP address:	{{$browserInfo->ip}}<br />
Estimated location:	{{$browserInfo->location.', '.$browserInfo->country}}
</p>
</multiline></td>
</tr>
<!--end content-->

</tbody>
</table>
</td>
</tr>
<!--end 2 columns-->
</tbody>
</table>
@stop