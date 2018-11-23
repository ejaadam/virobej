<?php

return[
    'email'=>[
        'account_created'=>'Account Created',
        'account_activated'=>'You Acoount has been activated',
        'verify_email'=>'Email ID verification Code',
        'enquiry'=>'',
        'forgot_password'=>'Forgot Password',
		'forgot_security_pin'=>'Forgot Security Pin',
        'change_password'=>'Change Password',
        'change_email'=>'Change E-Mail',
		'change_security_pin'=>'Change Security PIN',
		'payment_successfull'=>'Your transaction with :merchant on :date is successful',		
    ],	
    'sms'=>[        
        'account_created'=>'Hi :name(:uname), Your Supplier account has been created successfully',
        'account_activated'=>'Hi :name(:uname), Your Supplier account has been activated successfully',
        'reset_pwd_verification'=>'Hi :name (:uname), Your OTP to reset the password is :code',
        'verify_email'=>'Hi :name(:uname), Your OTP code to verify your email is :code',
        'enquiry'=>'',
        'forgot_password'=>'Reset password has been sent.',
        'change_password'=>'Password has been changed successfully.',
        'change_email'=>'Email has been changed successfully.',
		'reset_pwd_verification'=>'Hi :name (:uname), Your OTP to reset the password is :code',
    ],
    'notification'=>[
        'account_created'=>['title'=>'Your Account created', 'body'=>'Your account has been created successfully'],
        'verify_email'=>['title'=>'Verification Code has been sent', 'body'=>'Your Email ID verification code has been sent to your Email ID'],
        'account_activated'=>['title'=>'Your Account activated', 'body'=>'Your account has been created successfully'],
        'enquiry'=>['title'=>'', 'body'=>''],
    ],

    'reset_password_validation'=>[
        'username.required'=>'Please enter your Mobile No / Email ID',
        'verification_code.required'=>'Please enter your verification code',
        'verification_code.digits'=>'Please enter valid verification code',
        'password.required'=>'Please enter your Password',
        'password.min'=>'Password should be minimum of :min characters',
        'password.max'=>'Password should be maximum of :max characters',
    ],
];
