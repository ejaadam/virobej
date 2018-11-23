<?php
return [
	'check-verification-mobile'=>[
		'ATTRIBUTES'=>[
			'verification_code'=>[
				'type'=>'text'
			]
		],
		'RULES'=>[
			'verification_code'=>'required',
		],
		'LABELS'=>[
			'verification_code'=>'Verification Code',				
		],			
	],
	'login'=>[
		'ATTRIBUTES'=>[
			'username'=>[
				'type'=>'text'
			],
			'password'=>[
				'type'=>'password'
			]
		],
		'LABELS'=>[
			'username'=>Lang::get('general.fields.email_or_mobile'),
			'password'=>Lang::get('general.fields.password')
		],
		'RULES'=>[
			'username'=>'required|username|min:3|max:100', 
			'password'=>'required|password|min:6|max:16' 
		],
		'MESSAGES'=>[
			'username.required'=>'Please enter email/mobile number',
			'username.username'=>'Please enter valid email/mobile number',
			'username.min'=>'Email/mobile number cannot be less than 3 characters',
			'password.required'=>'Please enter password',
			'password.password'=>'The password that you\'ve entered is incorrect.',
			'password.min'=>'Password cannot be less than 6 characters',
		]
	],
	'notification-read'=>[	
	    'RULES'=>[
			'id'=>'required|regex:/^[0-9]*$/', 		
		],
		'MESSAGES'=>[
			'id.required'=>'Notification Id is required',
			'id.regex'=>'Invalid Notification Id, Please try again',
		]
	],		
   'verify-profile-pin'=>[
        'RULES'=>[
            'profile_pin'=>'required|profile_pin|min:4|max:4'
        ],
        'LABELS'=>[
            'profile_pin'=>'Security PIN',
        ],
        'MESSAGES'=>[
            'profile_pin.required'=>'Please enter Vi-Security PIN',
            'profile_pin.profile_pin'=>'It accept only numbers No space allowed',
            'profile_pin.min'=>'Vi-Security PIN must have 4 digit',
            'profile_pin.max'=>'Your Vi-Security PIN can\'t be longer than 4 digit',
        ]
    ],	
	'security-pin'=>[
		'save'=>[
			'RULES'=>[
				'profile_pin'=>'required|security_pin|min:4|max:4',
				'confirm_pin'=>'required|security_pin|same:profile_pin|min:4|max:4',
			],
			'LABELS'=>[
				'profile_pin'=>'Security PIN',
				'confirm_pin'=>'Confirm Security PIN',
			],
			'MESSAGES'=>[
				'profile_pin.required'=>'Please Enter Vi-Security PIN',
				'profile_pin.security_pin'=>'Security PIN is incorrect, please try again!',
				'profile_pin.min'=>'Vi-Security PIN must have 4 digit',
				'profile_pin.max'=>'Your Vi-Security PIN can\'t be longer than 4 digit',
				'confirm_pin.required'=>'Confirm Vi-Security PIN is required',
				'confirm_pin.security_pin'=>'Security PIN is incorrect, please try again!',
				'confirm_pin.max'=>'Your Vi-Security PIN can\'t be longer than 4 digits',
				'confirm_pin.same'=>'New PIN and Confirm PIN do not match, please try again'				
			]
		],
		'reset'=>[
			'RULES'=>[
				'code'=>'required|regex:/^[0-9]*$/|min:6|max:6',
				'profile_pin'=>'required|security_pin|min:4|max:4',
				//'confirm_pin'=>'required|security_pin|same:profile_pin|min:4|max:4',
			],
			'LABELS'=>[
				'code'=>'Verification Code',
				'profile_pin'=>'New Security PIN',
				//'confirm_pin'=>'Confirm Security PIN',
			],
			'MESSAGES'=>[
			    'code.required'=>'Verification Code is required',
				'code.regex'=>'Invalid Verification Code, Please try again',
				'code.min'=>'Verification Code must be 6 digit number',
				'code.max'=>'Verification Code can\'t be longer than 6 digit',				
				'profile_pin.required'=>'New Security PIN is required',
				'profile_pin.security_pin'=>'Security PIN is incorrect, please try again!',
				'profile_pin.min'=>'Please enter 4 digit Security PIN',
				'profile_pin.max'=>'Your Security PIN can\'t be longer than 4 digit',
				/* 'confirm_pin.required'=>'Confirm Vi-Security PIN is required',
				'confirm_pin.security_pin'=>'Security PIN is incorrect, please try again!',
				'confirm_pin.max'=>'Your Vi-Security PIN can\'t be longer than 4 digits',
				'confirm_pin.same'=>'New PIN and Confirm PIN do not match, please try again'	 */
			]
		],
	],
	'profile-settings'=>[
	        'profile-pin'=>[
		    'reset'=>[
                'RULES'=>[
                    'code'=>'required|regex:/^[0-9]*$/|min:6|max:6',
                    'profile_pin'=>'required|profile_pin|min:4|max:4',
                    'confirm_profile_pin'=>'required|profile_pin|max:4|same:profile_pin',
                ],
                'LABELS'=>[
                    'profile_pin'=>'seller/account.new_profile_pin',
                    'code'=>'general.label.verification_code',
                    'confirm_profile_pin'=>'seller/account.confirm_profile_pin'
                ],
                'MESSAGES'=>[
                    'code.required'=>'Verification Code is required',
                    'code.regex'=>'Invalid Verification Code, Please try again',
                    'code.min'=>'Verification Code must be 6 digit number',
                    'code.max'=>'Verification Code can\'t be longer than 6 digits',
                    'profile_pin.required'=>'New Security PIN is required',
                    'profile_pin.profile_pin'=>'Invalid Security PIN, please try again',
                    'profile_pin.min'=>'PIN must be 4 digit number',
                    'profile_pin.max'=>'Your PIN can\'t be longer than 4 digits',
                    'confirm_profile_pin.required'=>'Confirm Security PIN is required',
                    'confirm_profile_pin.profile_pin'=>'Invalid Security PIN, please try again',
                    'confirm_profile_pin.max'=>'Your PIN can\'t be longer than 4 digits',
                    'confirm_profile_pin.same'=>'New PIN and Confirm PIN do not match, please try again'
                ]
            ]
		 ],
	   'change-email'=>[
            'new-email-otp'=>(!empty($userInfo) && isset($userInfo->toggle_app_lock) && $userInfo->toggle_app_lock ?
                    [
                'RULES'=>[
                    'email'=>'required|email|unique:'.config('tables.ACCOUNT_MST').',email,NULL,id,is_deleted,0,is_closed,0'
                ],
                'LABELS'=>[
                    'email'=>'seller/account.new_email'
                ],
                'MESSAGES'=>[
                    'email.required'=>'Email is required',
                    'email.email'=>'Sorry, this doesn\'t look like a valid email',
                    'email.unique'=>'The email id is already in use',
                ]
                    ] :
                    [
                'RULES'=>[
                    //'propin_session'=>'required|regex:/^[0-9a-z]+$/',
                    'email'=>'required|email|unique:'.config('tables.ACCOUNT_MST').',email,NULL,id,is_deleted,0,is_closed,0'
                ],
                'LABELS'=>[
                    //'propin_session'=>'seller/account.propin_session',
                    'email'=>'seller/account.new_email'
                ],
                'MESSAGES'=>[
                    'email.required'=>'Email Id is required',
                    'email.email'=>'Please enter the Correct format email address ',
                    'email.unique'=>'The email id is already in use',
                ]
                    ]),
            'confirm'=>[
                'RULES'=>[
                    'code'=>'required|regex:/^[0-9]*$/|min:6|max:6'
                ],
                'LABELS'=>[
                    'code'=>'general.label.verification_code'
                ],
                'MESSAGES'=>[
                    'code.required'=>'Verification Code is required',
                    'code.regex'=>'Invalid Verification Code, Please try again',
                    'code.min'=>'Verification Code must be 6 digit number',
                    'code.max'=>'Verification Code can\'t be longer than 6 digits',
                ]
            ]
        ],
		'change-mobile'=>[
		   'confirm'=>[
		       'RULES'=>[
			        'code'=>'required|regex:/^[0-9]*$/|min:6|max:6',
					'phone_no'=>'required|regex:/^[0-9]{10}$/|unique:'.Config::get('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0',	
					
                ],
                'LABELS'=>[
                    
                      'phone_no'=>'seller/account.new_phone',
					  'code'=>'general.label.verification_code'
                ],
                'MESSAGES'=>[
                    'phone_no.required'=>'Mobile No required',
                    'phone_no.unique'=>'The Mobile Number is already in use',
                    'phone_no.regex'=>'Please Enter valid Mobile Number',
					'code.required'=>'Verification Code is required',
                    'code.regex'=>'Invalid Verification Code, Please try again',
                    'code.min'=>'Verification Code must be 6 digit number',
                    'code.max'=>'Verification Code can\'t be longer than 6 digits',
                    'profile_pin.required'=>'New Security PIN is required',
                ]

		   ],
		   'new-mob-otp'=>[
		        'RULES'=>[
					'phone_no'=>'required|regex:/^[0-9]{10}$/|unique:'.Config::get('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0',	
                ],
                'LABELS'=>[
                      'phone_no'=>'seller/account.new_phone',
                ],
                'MESSAGES'=>[
                    'phone_no.required'=>'Please Enter Your Mobile Number',
                    'phone_no.unique'=>'The Mobile Number is already in use',
                    'phone_no.regex'=>'Please Enter valid Mobile Number',
                ]
		    ]
	    ]
		
	],
	'check-email-verification'=>[
        'RULES'=>[
            'verification_code'=>'required|regex:/^[0-9]*$/|min:6|max:6',            
        ],
        'LABELS'=>[
            'verification_code'=>'Verification Code',            		
        ],
        'MESSAGES'=>[
            'verification_code.required'=>'Verification code is required',
            'verification_code.regex'=>'Invalid Verification code, Please try again',
            'verification_code.min'=>'Verification code must be 6 digit number',
            'verification_code.max'=>'Verification code can\'t be longer than 6 digits',            
        ]
    ],
	
	'forgot-password'=>[
		'RULES'=>[
            'uname'=>'required|email|exists:'.config('tables.ACCOUNT_MST').',email,account_type_id,3,is_deleted,0,is_closed,0',
        ],
        'LABELS'=>[
            'uname'=>'Email'
        ],
        'MESSAGES'=>[
            'uname.required'=>'Please enter your email',
            'uname.email'=>'The email you entered is not a valid email address',
            'uname.exists'=>'Please enter a valid username',
            //'uname.exists'=>'That account doesn\'t exist. Enter a different account or signup now.',        
        ]
    ],
	/* 'forgot_opt'=>[
        'RULES'=>[
            'opt'=>'required',
        ]
    ], 
	'resetpwd'=>[
        'RULES'=>[
            'code'=>'required|regex:/^[0-9]*$/|min:6|max:6',
            'newpwd'=>'required|password|min:6|max:16',
			'confirm_pwd'=>'required|password|max:16|same:newpwd',
        ],
        'LABELS'=>[
            'code'=>'Verification Code',
            'newpwd'=>'New password',
            'confirm_pwd'=>'Retype password',			
        ],
        'MESSAGES'=>[
            'code.required'=>'Verification code is required',
            'code.regex'=>'Invalid Verification code, Please try again',
            'code.min'=>'Verification code must be 6 digit number',
            'code.max'=>'Verification code can\'t be longer than 6 digits',
            'newpwd.required'=>'New Password is required',
            'newpwd.password'=>'You entered an invalid password. Try again!',
            'newpwd.min'=>'New Password must be at least 6 characters',
            'newpwd.max'=>'Your New password can\'t be longer than 16 characters',
			'confirm_pwd.required'=>'Retype Password is required',
			'confirm_pwd.password'=>'You entered an invalid password. Try again!',
            'confirm_pwd.max'=>'Your Retype password can\'t be longer than 16 characters',
            'confirm_pwd.same'=>'New Password and Retype Password do not match, please try again',
        ]
    ], */
	'pwdreset-link'=>[
        'RULES'=>[
            'token'=>'required|regex:/[a-zA-Z0-9,-]{22,40}[\.][a-f0-9]{32}/',
            'newpwd'=>'required|password|min:6|max:16',
            'confirm_pwd'=>'required|password|max:16|same:newpwd',
        ],
		'LABELS'=>[
            'token'=>'Token',
            'newpwd'=>'New password',
            'confirm_pwd'=>'Retype Password',			
        ],
        'MESSAGES'=>[
            'token.required'=>'Token is required',
            'token.regex'=>'Invalid token',
            'newpwd.required'=>'New Password is required',
			'newpwd.password'=>'New Password is incorrect, please try again!',
            'newpwd.min'=>'Password cannot be less than 6 characters',
            'newpwd.max'=>'New password can\'t be longer than 16 characters',
            'confirm_pwd.required'=>'Retype Password is required',
			'confirm_pwd.password'=>'Retype Password is incorrect, please try again!',
            'confirm_pwd.max'=>'Retype password can\'t be longer than 16 characters',
            'confirm_pwd.same'=>'Passwords do not match',
        ]
    ],	
	'sign-up'=>[
		'ATTRIBUTES'=>[
			'account_mst.pass_key'=>['type'=>'password'],
			'agree'=>['type'=>'checkbox']
		],
		'LABELS'=>[
			'account_details.firstname'=>Lang::get('general.fields.firstname'),
			'account_details.lastname'=>Lang::get('general.fields.lastname'),
			'buss_name'=>'Business Name',
			'account_mst.email'=>Lang::get('general.fields.email'),
			'account_mst.mobile'=>Lang::get('general.fields.mobile'),
			'account_mst.pass_key'=>Lang::get('general.fields.password'),
			'agree'=>Lang::get('general.fields.supplier_agree'),
			'bcategory'=>'Category',
			'country'=>'Country',
		],
		'RULES'=>[
			'account_details.firstname'=>'required|min:3|max:100|regex:/^[A-Za-z]*$/',
			'account_details.lastname'=>'required|min:1|max:50|regex:/^[A-Za-z]*$/',				
			'buss_name'=>'required|min:4|max:60',	
			'service_type'=>'required',				
			'phy_locations'=>'required',	
			'account_mst.email'=>'required|email|max:62|unique:'.Config::get('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0',
			'account_mst.mobile'=>'required|unique:'.Config::get('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0',				
			'account_mst.pass_key'=>'required|min:6|max:15',
			'bcategory'=>'required_if:service_type,1,3',				
			'country'=>'required',				
			'agree'=>'required'
			
		],
		'MESSAGES'=>[
			'account_details.firstname.required'=>'Please enter firstname',
			'account_details.lastname.required'=>'Please enter lastname',
			'buss_name.required'=>'Please enter business name',
			'account_mst.email.required'=>'Please enter business email',
			'account_mst.email.regex'=>'Please enter valid email',
			'account_details.pass_key.required'=>'Please enter password',
			'account_mst.mobile.required'=>'Please enter mobile',
			'account_mst.mobile.required'=>'Please enter valid mobile number',
			'account_mst.pass_key.min'=>'Password cannot be less than 6 characters',
		]
	],	
	'change-reg-mobile'=>[
		'RULES'=>[
			'mobile_no'=>'required|unique:'.Config::get('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0',	
		],
		'MESSAGES'=>[
			'mobile_no.required'=>'Mobile Number is required.',
			'mobile_no.unique'=>'Mobile Number already exists.',
		],
	],
	'change-email'=>[
		'RULES'=>[
			'new_email'=>'required|email|max:62|unique:'.Config::get('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0'
		],
		'MESSAGES'=>[
			'new_email.unique'=>'Email ID already exists.'
		],
	],
	'verify-new-email'=>[
		'RULES'=>['email'=>'required|email|max:62|unique:'.Config::get('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SELLER').',is_deleted,0'
		],
		'LABELS'=>[
			'email'=>Lang::get('general.fields.email'),
		]
	],
	'account-settings'=>[
		'general-details'=>[
			'LABELS'=>[
				'amst.uname'=>'User Name',					
			],
			'RULES'=>[
				'amst.uname'=>'required|min:6|max:20',					
			]
		],
		'update-password'=>[
			'RULES'=>[
				'oldpassword'=>'required|password|min:6|max:16',
				'newpassword'=>'required|password|min:6|max:16|different:oldpassword',
				'confirmpassword'=>'required|password|max:16|same:newpassword',
				//'amst.uname'=>'required|min:6|max:20',					
			],
			'LABELS'=>[
				'oldpassword'=>'Old Password',					
				'newpassword'=>'New Password',					
				'confirmpassword'=>'Retype Password',					
			],
			'MESSAGES'=>[
				'oldpassword.required'=>'Old Password is required',
				'oldpassword.password'=>'Old Password is incorrect, please try again!',
				'oldpassword.min'=>'Password cannot be less than 6 characters',
				'oldpassword.max'=>'Old password can\'t be longer than 16 characters',
				'newpassword.required'=>'New Password is required',
				'newpassword.password'=>'New Password is incorrect, please try again!',
				'newpassword.min'=>'Password cannot be less than 6 characters',
				'newpassword.max'=>'New Password can\'t be longer than 16 characters',
				'newpassword.different'=>'New Password cannot be same as old password',
				'confirmpassword.required'=>'Retype Password is required',				
				'confirmpassword.password'=>'Retype Password is incorrect, please try again!',				
				'confirmpassword.max'=>'Retype password can\'t be longer than 16 characters',
				'confirmpassword.same'=>'Passwords do not match',
			]
		],
		'bank-details'=>[
			'LABELS'=>[
				'payment_setings.beneficiary_name'=>Lang::get('general.fields.beneficiary_name'),
				'payment_setings.account_no'=>Lang::get('general.fields.account_no'),	
				'payment_setings.confirm_account_no'=>Lang::get('general.fields.confirm_account_no'),	
				'payment_setings.ifsc_code'=>Lang::get('general.fields.ifsc_code'),	
				'payment_setings.bank_name'=>Lang::get('general.fields.bank_name'),	
				'payment_setings.branch_name'=>Lang::get('general.fields.branch_name'),	
			],
			'RULES'=>[
				'payment_setings.beneficiary_name'=>'required|regex:/^[A-Za-z ]*$/|min:3',
				'payment_setings.account_no'=>'required|regex:/^[0-9]*$/|min:4|max:17',
				'payment_setings.confirm_account_no'=>'required|same:payment_setings.account_no',
				'payment_setings.ifsc_code'=>'required|regex:/^[A-Za-z]{4}[0][A-Za-z0-9]{6}$/', 
				'payment_setings.bank_name'=>'required',
				'payment_setings.branch_name'=>'required',
			],
		    'MESSAGES'=>[
			   'payment_setings.beneficiary_name.required'=>'Beneficiary Name is required.',
			   'payment_setings.beneficiary_name.regex'=>'You entered an invalid name. Try again!',
			   'payment_setings.beneficiary_name.min'=>'Beneficiary Name must be at least 3 characters',
			   'payment_setings.account_no.required'=>'Current Account Number is required.',
			   'payment_setings.account_no.regex'=>'You entered an invalid account number. Try again!',
			   'payment_setings.account_no.min'=>'Account Number must be at least 4 characters',
			   'payment_setings.account_no.max'=>'Your Account Number can\'t be longer than 17 characters',
			   'payment_setings.confirm_account_no.required'=>'Confirm Account Number is required.',
			   'payment_setings.confirm_account_no.same'=>'Confirm Account No must be same as Current Account No',
			   'payment_setings.ifsc_code.required'=>'IFSC Code is required.',
			   'payment_setings.ifsc_code.regex'=>'You entered an invalid IFSC Code. Try again!',
			   'payment_setings.bank_name.required'=>'Bank Name is required.',
			   'payment_setings.branch_name.regex'=>'Branch is required.',
			],
		],
		'tax-information'=>[
		     'LABELS'=>[
				'pan_name'=>'Name On PAN Card',
				'pan_number'=>'PAN Number',	
			  ],
			'RULES'=>[
				'pan_name'=>'required|regex:/^[a-zA-Z\s]*$/',
				'pan_number'=>isset($userInfo->country_id) ? ($userInfo->country_id == 77) ? 'required|regex:/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/' : '' : '',
				'pan_card_upload'=>'required',
			 ],
		   'MESSAGES'=>[
				'pan_number.required'=>'Please Enter Your PAN Card Number',
				'pan_number.regex'=>'Invalid PAN, Please try again',
				'pan_name.required'=>'Please Enter Your Name on PAN Card',
				'pan_card_upload.required'=>'Please Upload Your PAN details',
				'pan_name.regex'=>'Please Enter Valid Pan Name'
			],
		],
		'gst-information'=>[
		    'LABELS'=>[
				'gstin_no'=>'GSTIN',
				'tan_no'=>'TAN',	
			  ],
            'RULES'=>[
				'gstin_no'=>'regex:/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/',
				'tan_no'=>'regex:/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/'
			 ],
		   'MESSAGES'=>[
				'gstin_no.regex'=>'Please Enter Your Valid GSTIN',
				'tan_no.regex'=>'Please Enter Your Valid TAN',
			],
		],
		
		'upload-store-images'=>[
			'RULES'=>[
				'images'=>'required|dimensions:width:700,height:450',
			],
			'MESSAGES'=>[
				'images.required'=>'Images is required',
				'images.dimensions'=>'Image dimentioned must be 700x450',
			]
		],
		'business-details'=>[
			'RULES'=>[
				'mrmst.business_filing_status'=>'required',
				'address.address'=>'required',
				'address.postal_code'=>'required',
				'address.city_id'=>'required',
				'address.state_id'=>'required',
				'mrmst.website'=>'required',
				'mrmst.service_type'=>'required',
			],
			'MESSAGES'=>[
				'address.address.required'=>'Address is required',
				'address.postal_code.required'=>'Postalcode is required',
				'address.city_id.required'=>'City is required',
				'address.state_id.required'=>'State is required',
				'address.address.required'=>'Address is required',
				'mrmst.service_type.required'=>'Service Type is required',
				'mrmst.website.required'=>'Website is required',
			]
		],		
		'update_pickup_address'=>[
			'RULES'=>[
				'flat_no'=>'required',
				'address.postal_code'=>'required',
				'address.city_id'=>'required',
				'address.state_id'=>'required',
			],
			'MESSAGES'=>[
				'flat_no.required'=>'Flat is required',
				'address.postal_code.required'=>'Postal code is required',
				'address.city_id.required'=>'City is required',
				'address.state_id.required'=>'State is required',
			]
		],
		/* 'change-pin'=>[
			'RULES'=>[
				'current_pin'=>'required',
				'new_pin'=>'required',
				
			],
			'MESSAGES'=>[
				'current_pin.required'=>'Current PIN is required',
				'new_pin.required'=>'New PIN is required',
			]
		], */		
		'change-pin'=>[
			'RULES'=>[
				'current_pin'=>'required|security_pin|min:4|max:4',
				'new_pin'=>'required|security_pin|min:4|max:4',
				//'confirm_pin'=>'required|security_pin|same:profile_pin|min:4|max:4',
			],
			'LABELS'=>[
				'current_pin'=>'Current Security PIN',
				'new_pin'=>'New Security PIN',
				//'confirm_pin'=>'Confirm Security PIN',
			],
			'MESSAGES'=>[
				'current_pin.required'=>'Current Security PIN is required',
				'current_pin.security_pin'=>'Security PIN is incorrect, please try again!', 
				'current_pin.min'=>'Please enter 4 digit Security PIN',
				'current_pin.max'=>'Security PIN can\'t be longer than 4 digit',
				'new_pin.required'=>'New Security PIN is required',
				'new_pin.security_pin'=>'Security PIN is incorrect, please try again!',
				'new_pin.min'=>'Please enter 4 digit Security PIN',
				'new_pin.max'=>'Security PIN can\'t be longer than 4 digits',		
				
				/* 'confirm_pin.required'=>'Confirm PIN is required',
				'confirm_pin.security_pin'=>'Security PIN is incorrect, please try again!',
				'confirm_pin.max'=>'Your PIN can\'t be longer than 4 digit',
				'confirm_pin.same'=>'New PIN and Confirm PIN do not match, please try again' */
			]
		],
	],		
	'products'=>[
		'save'=>[
			'ATTRIBUTES'=>[
				'details.weight'=>['step'=>0.1],
				'details.height'=>['step'=>0.1],
				'details.length'=>['step'=>0.1],
				'details.width'=>['step'=>0.1]
			],
			'LABELS'=>[
				'product.product_name'=>Lang::get('product_browse.product_name'),
				'details.sku'=>Lang::get('product_browse.sku'),
				'details.eanbarcode'=>Lang::get('product_browse.eanbarcode'),
				'details.upcbarcode'=>Lang::get('product_browse.upcbarcode'),
				'details.description'=>Lang::get('product_browse.description'),
				'details.is_exclusive'=>Lang::get('product_browse.is_exclusive'),
				'details.visiblity_id'=>Lang::get('product_browse.visiblity_id'),
				'details.weight'=>Lang::get('product_browse.weight'),
				'details.height'=>Lang::get('product_browse.height'),
				'details.length'=>Lang::get('product_browse.length'),
				'details.width'=>Lang::get('product_browse.width'),
				'tags'=>Lang::get('product_browse.tags'),
				'meta_info.description'=>Lang::get('product_browse.meta_description'),
				'meta_info.meta_keys'=>Lang::get('product_browse.meta_keys'),
				'product.category_id'=>Lang::get('product_browse.category'),
				'product.brand_id'=>Lang::get('product_browse.brand'),
			],
			'RULES'=>[
				'product.product_name'=>'required',
				'details.sku'=>'required',
				//'details.eanbarcode'=>'required|regex:'.getRegex('eanbarcode'),
				//'details.upcbarcode'=>'required|regex:'.getRegex('upcbarcode'),
				'details.eanbarcode'=>'required',
				'details.upcbarcode'=>'required',
				'details.description'=>'required',
				'details.is_exclusive'=>'required',
				'details.visiblity_id'=>'required',
				'details.weight'=>'required|numeric|min:0.1|max:999999999',
				'details.height'=>'required|numeric|min:0.1|max:999999999',
				'details.length'=>'required|numeric|min:0.1|max:999999999',
				'details.width'=>'required|numeric|min:0.1|max:999999999',
				'tags'=>'required',
				'meta_info.description'=>'required',
				'meta_info.meta_keys'=>'required',
				'product.category_id'=>'required',
				'product.brand_id'=>'required',
			]
		],
		'price'=>[
			'save'=>[
				'RULES'=>[							
					'spp.mrp_price'=>'required',
					'spp.price'=>'required',
				],
				//'MESSAGES'=>Lang::get('product_items.validation')
				'MESSAGES'=>[ 
					'supplier_product_new.store_id.required'=>'Please select Store',
					'product.category_id.required'=>'Please select Catergory',
					'product.brand_id.required'=>'Please select Brand',
					'product.product_name.required'=>'Please enter Product Name',
					'product.sku.required'=>'Please enter SKU',
					'product.description.required'=>'Please enter Description',
					'supplier_product_new.currency_id.required'=>'Please select Currency',
					'supplier_product_new.mrp_price.required'=>'Please enter MRP Price',
					'supplier_product_new.price.required'=>'Please enter Price',
				]
			],
		],
	],
	'stores'=>[
		'save'=>[
			'ATTRIBUTES'=>[
				'store_extras.working_hours_from'=>['type'=>'time'],
				'store_extras.working_hours_to'=>['type'=>'time'],
				'create.status'=>[
					'options'=>[
						0=>Lang::get('general.fields.enable'),
						1=>Lang::get('general.fields.disable')
					],
					'type'=>'select'
				],					
				'store_extras.working_days'=>[
					'options'=>[],						
					'type'=>'checkbox'
				]
			],
			'LABELS'=>[
				'create.store_name'=>Lang::get('general.fields.store_name'),
				'store_extras.email'=>Lang::get('general.fields.email'),
				'store_extras.mobile_no'=>Lang::get('general.fields.mobile'),
				'store_extras.landline_no'=>Lang::get('general.fields.landline_no'),
				'store_extras.firstname'=>Lang::get('general.fields.firstname'),
				'store_extras.lastname'=>Lang::get('general.fields.lastname'),
				'store_extras.state_id'=>Lang::get('general.fields.state'),
				'store_extras.country_id'=>Lang::get('general.fields.country'),
				'store_extras.postal_code'=>Lang::get('general.fields.postal_code'),
				'store_extras.city_id'=>Lang::get('general.fields.city'),
				'store_extras.address1'=>Lang::get('general.fields.street1'),
				'store_extras.address2'=>Lang::get('general.fields.street2'),
				'store_extras.working_hours_from'=>Lang::get('general.fields.timing'),
				'store_extras.working_hours_to'=>Lang::get('general.fields.timing'),
				'store_extras.website'=>Lang::get('general.fields.store_url'),
				'create.status'=>Lang::get('general.fields.status'),
				'store_extras.working_days'=>Lang::get('general.fields.working_days'),
			],
			'RULES'=>[
				'create.store_name'=>'required',
				'create.status'=>'required',
				'store_extras.email'=>'required|email',
				'store_extras.working_hours_from'=>'required',
				'store_extras.working_hours_to'=>'required',
				'store_extras.website'=>'required|url',
				'store_extras.mobile_no'=>'required|regex:/^[0-9]{10}$/',
				'store_extras.landline_no'=>'required',
				'store_extras.address1'=>'required',
				'store_extras.address2'=>'required',
				'store_extras.country_id'=>'required',
				'store_extras.state_id'=>'required',
				'store_extras.city_id'=>'required',
				'store_extras.postal_code'=>'required',
				'store_extras.firstname'=>'required|regex:/^[A-Za-z]{3,100}$/',
				'store_extras.lastname'=>'required|regex:/^[A-Za-z]{3,50}$/',
			]
		]
	],
	'outlet'=>[
		'store-save-web'=>[
			'RULES'=>[
				//'search_form.category'=>'required',
				'store_name'=>'required',
				'store_logo'=>'file|mimetypes:image/jpg,image/jpeg,image/gif,image/png|max:1024',
				'title'=>'required',
				'description'=>'required',
				//'email'=>'required|email|unique:'.config('tables.STORES').',email,NULL,store_id,is_deleted,0',
				'email'=>'required|email',
				'mobile'=>'required|regex:/^[0-9]{10}$/',
				'phone'=>'sometimes|regex:/^[0-9]+$/|min:6|max:13',
				'address.address'=>'required',
				'address.postal_code'=>'required',
				'address.city_id'=>'required',
				'address.state_id'=>'required',
				'address.country_id'=>'required',
				/* 'address.geolat'=>'required_with:address.address|lng',
				'address.geolng'=>'required_with:address.address|lng', */
				'specify_working_hrs'=>'required|in:1,2,3',
				'split_working_hrs'=>'boolean',
				'operating_hrs.mon.closed'=>'boolean',
				'operating_hrs.mon.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.mon.closed',
				'operating_hrs.mon.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.mon.closed',
				'operating_hrs.mon.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.mon.closed',
				'operating_hrs.mon.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.mon.closed',
				'operating_hrs.tue.closed'=>'boolean',
				'operating_hrs.tue.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.tue.closed',
				'operating_hrs.tue.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.tue.closed',
				'operating_hrs.tue.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.tue.closed',
				'operating_hrs.tue.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.tue.closed',
				'operating_hrs.wed.closed'=>'boolean',
				'operating_hrs.wed.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.wed.closed',
				'operating_hrs.wed.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.wed.closed',
				'operating_hrs.wed.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.wed.closed',
				'operating_hrs.wed.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.wed.closed',
				'operating_hrs.thu.closed'=>'boolean',
				'operating_hrs.thu.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.thu.closed',
				'operating_hrs.thu.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.thu.closed',
				'operating_hrs.thu.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.thu.closed',
				'operating_hrs.thu.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.thu.closed',
				'operating_hrs.fri.closed'=>'boolean',
				'operating_hrs.fri.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.fri.closed',
				'operating_hrs.fri.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.fri.closed',
				'operating_hrs.fri.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.fri.closed',
				'operating_hrs.fri.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.fri.closed',
				'operating_hrs.sat.closed'=>'boolean',
				'operating_hrs.sat.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.sat.closed',
				'operating_hrs.sat.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.sat.closed',
				'operating_hrs.sat.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.sat.closed',
				'operating_hrs.sat.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.sat.closed',
				'operating_hrs.sun.closed'=>'boolean',
				'operating_hrs.sun.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.sun.closed',
				'operating_hrs.sun.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.sun.closed',
				'operating_hrs.sun.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.sun.closed',
				'operating_hrs.sun.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.sun.closed',
			],
			'MESSAGES'=>[
				'store_name.required'=>'seller/store.store_name_req',
				'logo.file'=>'seller/store.invalid_logo',
				'logo.mimetypes'=>'seller/store.invalid_logo',
				'logo.max'=>'seller/store.max_file_size',
				'title.required'=>'Title Required',
				'description.required'=>'Description Required',
				'search_form.category.required'=>'Select Category',
				'email.required'=>'seller/store.email_req',
				'email.email'=>'seller/store.valid_email',
				'email.unique'=>'seller/store.email_exists',
				'mobile.required_with'=>'seller/store.mobile_req',
				'mobile.regex'=>'seller/store.valid_mobile',
				'mobile.unique'=>'seller/store.mobile_exists',
				//'phone.required'=>'Phone No. required',
				'phone.regex'=>'seller/store.valid_phone',
				'phone.min'=>'seller/store.mobile_minlen',
				'phone.max'=>'seller/store.mobile_maxlen',
				'address.address.required'=>'seller/store.address_req',
				'address.city_id.required'=>'seller/store.locality_req',
				'address.state_id.required'=>'seller/store.state_req',
				'address.country_id.required'=>'seller/store.country_req',
				//'postalcode.required_with'=>'seller/store.provide_correct_addr',
				'address.geolat.required_with'=>'seller/store.provide_correct_addr',
				'address.geolat.lat'=>'seller/store.provide_correct_addr',
				'address.geolng.required_with'=>'seller/store.provide_correct_addr',
				'address.geolng.lng'=>'seller/store.provide_correct_addr',
				'address.postal_code.required'=>'seller/store.postalcode_req',
				'operating_hrs.mon.0.from.required_if_without'=>'required',
				'operating_hrs.mon.0.to.required_if_without'=>'required',
				'operating_hrs.mon.1.from.required_if_with_without'=>'required',
				'operating_hrs.mon.1.to.required_if_with_without'=>'required',
				'operating_hrs.tue.0.from.required_if_without'=>'required',
				'operating_hrs.tue.0.to.required_if_without'=>'required',
				'operating_hrs.tue.1.from.required_if_with_without'=>'required',
				'operating_hrs.tue.1.to.required_if_with_without'=>'required',
				'operating_hrs.wed.0.from.required_if_without'=>'required',
				'operating_hrs.wed.0.to.required_if_without'=>'required',
				'operating_hrs.wed.1.from.required_if_with_without'=>'required',
				'operating_hrs.wed.1.to.required_if_with_without'=>'required',
				'operating_hrs.thu.0.from.required_if_without'=>'required',
				'operating_hrs.thu.0.to.required_if_without'=>'required',
				'operating_hrs.thu.1.from.required_if_with_without'=>'required',
				'operating_hrs.thu.1.to.required_if_with_without'=>'required',
				'operating_hrs.fri.0.from.required_if_without'=>'required',
				'operating_hrs.fri.0.to.required_if_without'=>'required',
				'operating_hrs.fri.1.from.required_if_with_without'=>'required',
				'operating_hrs.fri.1.to.required_if_with_without'=>'required',
				'operating_hrs.sat.0.from.required_if_without'=>'required',
				'operating_hrs.sat.0.to.required_if_without'=>'required',
				'operating_hrs.sat.1.from.required_if_with_without'=>'required',
				'operating_hrs.sat.1.to.required_if_with_without'=>'required',
				'operating_hrs.sun.0.from.required_if_without'=>'required',
				'operating_hrs.sun.0.to.required_if_without'=>'required',
				'operating_hrs.sun.1.from.required_if_with_without'=>'required',
				'operating_hrs.sun.1.to.required_if_with_without'=>'required',
			]
		],
		'store-update-web'=>[
			'RULES'=>[
				//'search_form.category'=>'required',
				'store_name'=>'required',
				'store_logo'=>'file|mimetypes:image/jpg,image/jpeg,image/gif,image/png|max:1024',
				'title'=>'required',
				'description'=>'required',
				//'email'=>'required|email|unique:'.config('tables.STORES').',email,NULL,store_id,is_deleted,0',
				'email'=>'required|email',
				'mobile'=>'required|regex:/^[0-9]{10}$/',
				'phone'=>'sometimes|regex:/^[0-9]+$/|min:6|max:13',
				'address.address'=>'required',
				'address.postal_code'=>'required',
				'address.city_id'=>'required',
				'address.state_id'=>'required',
				'address.country_id'=>'required',
				/* 'address.geolat'=>'required_with:address.address|lng',
				'address.geolng'=>'required_with:address.address|lng', */
				'specify_working_hrs'=>'required|in:1,2,3',
				'split_working_hrs'=>'boolean',
				'operating_hrs.mon.closed'=>'boolean',
				'operating_hrs.mon.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.mon.closed',
				'operating_hrs.mon.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.mon.closed',
				'operating_hrs.mon.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.mon.closed',
				'operating_hrs.mon.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.mon.closed',
				'operating_hrs.tue.closed'=>'boolean',
				'operating_hrs.tue.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.tue.closed',
				'operating_hrs.tue.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.tue.closed',
				'operating_hrs.tue.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.tue.closed',
				'operating_hrs.tue.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.tue.closed',
				'operating_hrs.wed.closed'=>'boolean',
				'operating_hrs.wed.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.wed.closed',
				'operating_hrs.wed.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.wed.closed',
				'operating_hrs.wed.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.wed.closed',
				'operating_hrs.wed.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.wed.closed',
				'operating_hrs.thu.closed'=>'boolean',
				'operating_hrs.thu.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.thu.closed',
				'operating_hrs.thu.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.thu.closed',
				'operating_hrs.thu.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.thu.closed',
				'operating_hrs.thu.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.thu.closed',
				'operating_hrs.fri.closed'=>'boolean',
				'operating_hrs.fri.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.fri.closed',
				'operating_hrs.fri.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.fri.closed',
				'operating_hrs.fri.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.fri.closed',
				'operating_hrs.fri.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.fri.closed',
				'operating_hrs.sat.closed'=>'boolean',
				'operating_hrs.sat.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.sat.closed',
				'operating_hrs.sat.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.sat.closed',
				'operating_hrs.sat.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.sat.closed',
				'operating_hrs.sat.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.sat.closed',
				'operating_hrs.sun.closed'=>'boolean',
				'operating_hrs.sun.0.from'=>'required_if_without:specify_working_hrs,3,operating_hrs.sun.closed',
				'operating_hrs.sun.0.to'=>'required_if_without:specify_working_hrs,3,operating_hrs.sun.closed',
				'operating_hrs.sun.1.from'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.sun.closed',
				'operating_hrs.sun.1.to'=>'required_if_with_without:specify_working_hrs,3,split_working_hrs,operating_hrs.sun.closed',
			],
			'MESSAGES'=>[
				'store_name.required'=>'seller/store.store_name_req',
				'logo.file'=>'seller/store.invalid_logo',
				'logo.mimetypes'=>'seller/store.invalid_logo',
				'logo.max'=>'seller/store.max_file_size',
				'title.required'=>'Title Required',
				'description.required'=>'Description Required',
				'search_form.category.required'=>'Select Category',
				'email.required'=>'seller/store.email_req',
				'email.email'=>'seller/store.valid_email',
				'email.unique'=>'seller/store.email_exists',
				'mobile.required_with'=>'seller/store.mobile_req',
				'mobile.regex'=>'seller/store.valid_mobile',
				'mobile.unique'=>'seller/store.mobile_exists',
				//'phone.required'=>'Phone No. required',
				'phone.regex'=>'seller/store.valid_phone',
				'phone.min'=>'seller/store.mobile_minlen',
				'phone.max'=>'seller/store.mobile_maxlen',
				'address.address.required'=>'seller/store.address_req',
				'address.city_id.required'=>'seller/store.locality_req',
				'address.state_id.required'=>'seller/store.state_req',
				'address.country_id.required'=>'seller/store.country_req',
				//'postalcode.required_with'=>'seller/store.provide_correct_addr',
				'address.geolat.required_with'=>'seller/store.provide_correct_addr',
				'address.geolat.lat'=>'seller/store.provide_correct_addr',
				'address.geolng.required_with'=>'seller/store.provide_correct_addr',
				'address.geolng.lng'=>'seller/store.provide_correct_addr',
				'address.postal_code.required'=>'seller/store.postalcode_req',
				'operating_hrs.mon.0.from.required_if_without'=>'required',
				'operating_hrs.mon.0.to.required_if_without'=>'required',
				'operating_hrs.mon.1.from.required_if_with_without'=>'required',
				'operating_hrs.mon.1.to.required_if_with_without'=>'required',
				'operating_hrs.tue.0.from.required_if_without'=>'required',
				'operating_hrs.tue.0.to.required_if_without'=>'required',
				'operating_hrs.tue.1.from.required_if_with_without'=>'required',
				'operating_hrs.tue.1.to.required_if_with_without'=>'required',
				'operating_hrs.wed.0.from.required_if_without'=>'required',
				'operating_hrs.wed.0.to.required_if_without'=>'required',
				'operating_hrs.wed.1.from.required_if_with_without'=>'required',
				'operating_hrs.wed.1.to.required_if_with_without'=>'required',
				'operating_hrs.thu.0.from.required_if_without'=>'required',
				'operating_hrs.thu.0.to.required_if_without'=>'required',
				'operating_hrs.thu.1.from.required_if_with_without'=>'required',
				'operating_hrs.thu.1.to.required_if_with_without'=>'required',
				'operating_hrs.fri.0.from.required_if_without'=>'required',
				'operating_hrs.fri.0.to.required_if_without'=>'required',
				'operating_hrs.fri.1.from.required_if_with_without'=>'required',
				'operating_hrs.fri.1.to.required_if_with_without'=>'required',
				'operating_hrs.sat.0.from.required_if_without'=>'required',
				'operating_hrs.sat.0.to.required_if_without'=>'required',
				'operating_hrs.sat.1.from.required_if_with_without'=>'required',
				'operating_hrs.sat.1.to.required_if_with_without'=>'required',
				'operating_hrs.sun.0.from.required_if_without'=>'required',
				'operating_hrs.sun.0.to.required_if_without'=>'required',
				'operating_hrs.sun.1.from.required_if_with_without'=>'required',
				'operating_hrs.sun.1.to.required_if_with_without'=>'required',
			]
		]
	],
		
		'manage_users'=>[
			'save_user'=>[
					'RULES'=>[
						'role'=>'required',
						'email'=>'required|email|unique:'.config('tables.ACCOUNT_MST').',email,'.(request()->has('account_id') && !empty(request()->get('account_id')) ? request()->get('account_id').',account_id' : 'NULL,account_id').',is_deleted,0,is_closed,0',
			
						'mobile'=>'required|unique:'.Config::get('tables.ACCOUNT_MST').',mobile,'.(request()->has('account_id') && !empty(request()->get('account_id')) ? request()->get('account_id').',account_id' : 'NULL,account_id').',is_deleted,0,is_closed,0',
						
						'username'=>'required|unique:'.Config::get('tables.ACCOUNT_MST').',uname,'.(request()->has('account_id') && !empty(request()->get('account_id')) ? request()->get('account_id').',account_id' : 'NULL,account_id').',is_deleted,0,is_closed,0',
						'full_name'=>'required|min:4|max:16',
					],
					'MESSAGES'=>[
						'username.unique'=>'Username already taken',
					]
		   	   ],
		   'reset-password'=>[
			  'RULES'=>[
				'new_pwd'=>'required|password|min:6|max:16',
			  ],
				'LABELS'=>[
					'new_pwd'=>'New password',
				  ],
			   'MESSAGES'=>[
				'new_pwd.required'=>'New Password is required',
				'new_pwd.password'=>'You entered an invalid password. Try again!',
				'new_pwd.min'=>'New Password must be at least 6 characters',
				'new_pwd.max'=>'Your New password can\'t be longer than 16 characters',
				]
			]
			
			
		]
		
		
];