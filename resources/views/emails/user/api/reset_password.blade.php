@extends('emails.user.maillayout')
@section('content')
<div class="box box-primary">
    <div class="box-body">
        Dear <?php echo $uname;?>,<br/>
        <p>Your  {{$siteConfig->site_name}} Account Password has been changed.</p>
		<p><b>Login Id : </b><?php echo $email;?></p>        
        
    </div>
</div>
@stop