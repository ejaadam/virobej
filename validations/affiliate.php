<?php
return [	
	'signup'=> [
		'save'=>[			
			'RULES'=>[
				'firstname'=>'required|firstname|min:3|max:30',
				'lastname'=>'required|lastname|min:3|max:30',
				'gardian'=>'required|lastname|min:3|max:50',
				'marital_status'=>'required',
				'gender'=>'required',
				'dob'=>'required',
				'email'=>'required|email|unique:account_mst,email',
				'username'=>'required|username|unique:account_mst,uname',
				'password'=>'required|min:6|max:10',
				'confirm_password'=>'required|min:6|max:10',
				'postcode'=>'required|zipcode',
				'country'=>'required|regex:/^[A-Z]{2}$/'
				'state'=>'required|zipcode'
				'district'=>'required|zipcode',				
			],
			'MESSAGES' => [
				'firstname.required' => "Please enter first Name",
				'firstname.firstname' => "First Name should contain only alphapets",
				'firstname.min' => "First name must contain atleast 3 char",
				'firstname.max' => "First name must not exist 30 char",
				'lastname.required' => "Please enter Last Name",
				'lastname.lastname' => "Last Name should contain only alphapets",
				'lastname.min' => "Last name must contain atleast 1 char",
				'lastname.max' => "Last name must not exist 30 char",
				'email.required' => "Please enter email address",
				'email.email' => "Please enter valide email address",
				'username.required' => "Please enter desire username",
				'username.regex' => "Please enter A-Z,0-9",
				'username.min' => "Username must contain atleast 6 char",
				'username.max' => "Username must not exist 30 char",
				'password.required' => "Please enter your password",
				'password.min' => "Password must contain atleast 6 char",
				'password.max' => "Password must not exist 30 char",
				'postcode.required' => "Please enter your Zipcode/Postal code",
				'postcode.zipcode' => "Invalide Zipcode/Postal code",
				'confirm_password.required' => "Please enter your password",
				'confirm_password.min' => "Password must contain atleast 6 char",
				'country.required' => "Please select country",
			]			
		]
	],
	'forgotpwd'=>[
		'RULES'=>[
			'uname'=>'required|email',
		],
		'MESSAGES' => [
			'email.required' => "Please enter email address",
			'email.email' => "Please enter valide email address",
		]		
	]
];