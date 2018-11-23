@extends('emails.layouts.maillayout')
@section('content')
<div class="box box-primary">
    <div class="box-body">       
        Dear <?php echo $first_name;?>,<br/>
         <p>As a part of our security measures, we have enabled the use of OTP (One Time Password) for your current session. Following is the OTP to be entered on OTP authorization page to access your account.</p>
        <p>Your (OTP)One Time Password is <?php echo $otp_pwd;?></p>
        <p>
            Thank you,<br />
            The Team at<br />
            <?php echo Config::get('constants.WEB_SITE');?>
        </p>
        <p style="font-size:10px; color:#666666;">
            Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, log in to your account and click "Contact Us" at the bottom of any page.
        </p>
    </div>
</div>
@stop