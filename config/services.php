<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],
	
	'ifsc' => [
        'key' =>'2f850b634ebb6bc6b84412773df8a791',
        'url' => 'https://api.bank.codes/in-ifsc/?',
        'region' => 'india',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],	
	'sendgrid'=>[
        'url'=>'https://api.sendgrid.com/api/mail.send.json',
        'api_user'=>'onlinesensor',
        'api_key'=>'ejugiter@123'
    ],	
	'google'=>[
		'map_api_key'=>'AIzaSyBSv4zkc1apoAfsL71cTC1od4tOzNaMgJA',
        'api_key'=>'AIzaSyBfyHmwNMFnDYDcQvGfYUL22Czi7FGroLQ',
        'fcm_url'=>'https://fcm.googleapis.com/fcm/send'
    ],
	'sms'=>[
        'user'=>'virobon',
        'key'=>'936f41a869XX',
        'senderid'=>'VIRONL',
        'url'=>'sms.virob.com/submitsms.jsp?',
    ],	
	'vpi'=> [
		'url' =>'http://localhost/dropshipping/vpi/v1/',
	],
	'api'=> [
		'url' =>'http://localhost/virob_shopping/api/v1/',
	],
	'localApi'=> [
		'url' =>'http://localhost/virob_shopping/api/v1/',
	],	
];
