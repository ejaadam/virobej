@extends('emails.user.maillayout')
@section('content')
<div class="box box-primary">
    <div class="box-body">
        Dear <?php echo $uname;?>,<br/>
        <p>Your  {{$siteConfig->site_name}} Account Password has been changed.</p>
		<p><b>Login Id : </b><?php echo $email;?></p>        
        <p>
            Thank you,<br />
            The Team at<br />
            
        </p>
        <p style="font-size:10px; color:#666666;">
            Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, log in to your account and click "Contact Us" at the bottom of any page.
        </p>
    </div>
</div>
@stop