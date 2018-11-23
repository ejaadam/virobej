<?php

return [
'validation'=>array(
'sender_name.required'=>'Please enter your Sender Name',
 'email.required'=>'Please enter your Sender Email',
 'email.email'=>'Please enter valid Email',
 'driver_type.required'=>'Select your Driver Type',
 'settings.host.required'=>'Please enter your Host',
 'settings.port.required'=>'Please enter your Port',
 'settings.port.numeric'=>'Please enter valid Port',
 'settings.username.required'=>'Please enter your Username',
 'settings.password.required'=>'Please enter your Password',
 'settings.encryption.required'=>'Please select your Encryption type',
 'settings.api_user.required'=>'Please enter your API User Name',
 'settings.api_key.required'=>'Please enter your API Password',
 'v_code.required'=>'Please enter the Verification Code',
 'v_code.in'=>'Please enter the valid Verification Code'
),
];
