<?php

return [
	'aff'=>[
		'signup-save'=>[			
			'RULES'=>[
				'first_name'=>'required|regex:/^[A-Za-z]{3,30}$/',
				'last_name'=>'required|regex:/^[A-Za-z]{1,30}$/',
				'email'=>'required|email|unique:account_mst,email,NULL,account_id',
				'username'=>'required|unique:account_mst,uname,NULL,account_id',
				'password'=>'required|min:6|max:10',
				'confirm_password'=>'required|min:6|max:10',
				'country'=>'required|regex:/^[A-Z]{2}$/','
			]
		],
	]	
];