<style>
    .panel {
        background: #f2f2f2;
        border: 1px solid #d9d9d9;
        padding: 10px !important;
        width: 700px;
    }
</style>
<div style="">    
    <table align="center" border="0" cellpadding="0" cellspacing="0" class="row" style="border-collapse: collapse;" width="600">
        <thead>
            <tr><td style="padding: 1em 0em;"><a href="{{URL::to('/')}}" style="color: #3291db;text-decoration: none;outline: none;"><center><img  src="<?php echo URL::asset('resources/assets/imgs/logo.png');?>" style="border: 0px; display: block; width: 200px; height: 80px;"></a></center></td></tr>
        </thead>
        <tbody>
            <tr>
                <td align="center" class="module-td1" style="padding: 10px 0 0;">
                    @yield('content')
                </td>
            </tr>
            <tr>
                <td height="30"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td align="center" class="colorbg-dark" style="background-image: none; background-color: #252525;"></td>
            </tr>
            <tr>
                <td class="small-img line2" height="1" style="font-size: 0px;line-height: 0px;border-collapse: collapse;background-color: #333333;"><img height="1" src="<?php echo URL::asset('resources/assets/admin/img/mail_img/spacer.gif');?>" style="border: 0;display: block;-ms-interpolation-mode: bicubic;" width="1" /></td>
            </tr>
            <tr>
                <td>
                    
                </td>
            </tr>
            <tr align="center" class="colorbg-gray" style="background-image: none; background-color: #2c2c2c;">
                <td colspan="2" align="center" style="font-family: Raleway;font-size: 11px;line-height: 11px;color: #f2f2f2; font-weight: 400;padding: 22px 0 20px;">&nbsp;All rights reserved</td>
            </tr>
        </tfoot>
    </table>
</div>
