<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
	'comm_error'=>'Whoops! Please enter a valid username',
	'login_msg'=>[
        'comm_error'=>'Whoops! Please enter a valid username',
        'usr_miss'=>'Username missing',
        'pwd_miss'=>'Password missing',
        'acc_blocked'=>'Your Account has been blocked',
        'acc_invpwd'=>'Invalid Password',
        'acc_pwdnomatch'=>'Invalid username or password',
        'acc_invid'=>'Invalid Username',
        'acc_invmob'=>'Invalid Mobile number',
        'acc_invemail'=>'Invalid Email ID',
        'acc_success'=>'You have successfully logged in',
        'acc_nofound'=>'Account not found',
        //'acc_not_found'=>'Account not found. Enter a different account or get a new one',
        'acc_not_found'=>'Whoops! No account found. Enter a different account or get a new one.',
        'invalid_login'=>'Invalid login, Please try again',
        'invalid_username'=>'Invalid Username',        
        'invalid_uname_pwd'=>'Whoops! You entered an invalid username or password.',
        'status_not_active'=>'Account status not active.',
    ],
	    'forgotpwd'=>[
        'comm_error'=>'Enter valid Email ID',
        'email_inv'=>'Invalid Email ID',
        'mobile_inv'=>'Invalid Mobile',
        'acc_notfound'=>'Account not found',
        //'acc_resetlink'=>'Please check your email. We emailed instructions to :email.',
        //'acc_resetlink'=>'Password reset link has been sent to :email. If you cannot find the message in your inbox, check your junk or spam folders.',
		'acc_resetlink'=>'An email has been sent to your rescue email address :email.Follow the directions in the email to reset your password',
        'acc_blocked'=>'Your Login Is Blocked. Please Contact Our Support Team.!',
        'acc_mobcode_send'=>'Verification code has been send to your mobile..!',
        'headermiss'=>'Unauthorized Access',
        'parammiss'=>'Parameter missing',
        'reset_code_miss'=>'Verification code missing',
        'reset_sess_exp'=>'Reset session expired',
        'reset_code_inv'=>'Invalid verification code',
        'reset_code_nomatch'=>'Invalid OTP',
        'reset_pwd_success'=>'Your password has been updated Successfully',
        'reset_pwd_fail'=>'Password Updation Failed. Please try again.',
        'reset_pwd_unable'=>"We are unable to reset your new password. Please try again later.",
        'new_pwd_is_same_as_old'=>'New and Old passwords are same',
        'newpwd_missing'=>"New password missing",
        'usrsess_miss'=>"Account session expired",
        'invalid_otp'=>"Invalid OTP",
    ],
];
