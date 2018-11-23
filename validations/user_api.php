<?php
return [
    'offer-cashback'=>[
		'customer'=>[
			'search'=>[
				'RULES'=>[
					'user_code'=>'required|exists:'.config('tables.ACCOUNT_MST').',user_code,account_type_id,2,is_deleted,0,is_closed,0'
				],
				'MESSAGES'=>[
					'user_code.required'=>'Please enter your Account Code',												
					'user_code.exists'=>'Account Code not exists',	
				],
			],
		],
		'get-bill-amount'=>[
            'RULES'=>[
                'amount'=>'required|numeric|greater:49|lesser:4999',
            ],
            'MESSAGES'=>[
				'amount.required'=>'Please enter your Bill Amount',												
				'amount.numeric'=>'Invalid Amount',	
				'amount.greater'=>'Amount must be greater than &#8377; 50.00',	
				'amount.lesser'=>'Amount must be lesser than &#8377; 5000.00',	
			],
        ],
	],
    'merchant'=>[
		'store'=>[
			'select'=>[
				'RULES'=>[
					'store_code'=>'required'
				],
				'MESSAGES'=>[
					'store_code'=>'Store Code is required.'
				],
			],
		],
	],
    'dashboard'=>[
		'search'=>[
			'RULES'=>[
				'search_term'=>'required'
			],
			'MESSAGES'=>[
				'search_term'=>'Search text is required.'
			],
		],
	],
	'product'=>[
		'details'=>[
			'RULES'=>[
				'product_code'=>'required'
			],
			'MESSAGES'=>[
				'product_code'=>'Product Code is required.'
			],
		],
	],
	'toggle-app-lock'=>[
        'RULES'=>[
            'toggle_app_lock'=>'required|in:0,1'
        ],
        'MESSAGES'=>[
            'toggle_app_lock'=>'Toggle App Lock is required.'
        ],
    ],
	'update-notification-token'=>[
        'RULES'=>[
            'fcm_registration_id'=>'required'
        ],
        'MESSAGES'=>[
            'fcm_registration_id'=>'Firebase Registraction ID is required.'
        ],
    ],
	'wallet'=>[
		'transactions'=>[
			'RULES'=>[            
				'transaction_type'=>'sometimes|in:0,1', 
				'filter'=>'sometimes|in:'.implode(',', array_values(config('constants.TRANSACTIONS'))), 
			],
			'MESSAGES'=>[
				'transaction_type.in'=>'Transaction Type is Invalid',
				'filter.in'=>'Transaction Type is Invalid'
			]
		],
	],
	'set-location'=>[
		'RULES'=>[            
			'country_id'=>'required|numeric|exists:'.config('tables.LOCATION_COUNTRY').',country_id,status,1',  
        ],
        'LABELS'=>[
            'country_id'=>'Country'
        ]
	],
	'order'=>[
		'save-rating'=>[
            'RULES'=>[                
                'order_code'=>'required|regex:/^[A-Za-z0-9-]{3,20}$/|exists:'.config('tables.ORDERS').',order_code,is_deleted,0',
                'rating'=>'required|rating',
                //'feedback'=>'sometimes|min:3|max:1000'
            ],            
            'MESSAGES'=>[
                'store_code.required'=>'Merchant ID is required.',
                'store_code.regex'=>'Merchant ID is invalid.',
                'store_code.exists'=>'Merchant ID not exists.',
                'order_code.required'=>'Order Code is required.',
                'order_code.regex'=>'Order Code is invalid.',
                'order_code.exists'=>'Order Code not exists.',
                'rating.required'=>'Submit rating and review.',
                'rating.rating'=>'Invalid rating',
            ]
        ]
	],
	'payment'=>[
        'info'=>[
            'RULES'=>[
                'payment_type'=>'required|in:'.implode(',', array_keys(config('constants.PAYMENT_TYPES')))
            ],
            'LABELS'=>[
                'payment_type'=>'user/general.payment_type'
            ],
			'MESSAGES'=>[
                'payment_type.required'=>'Payment Type is required.'
            ]
        ],
	],
	'withdraw-fund'=>[
		'save-bank-details'=>array_merge(['LABELS'=>'user/withdrawal.labels'], 
							[	
							'ATTRIBUTES'=>[
								'account_details.b_bank_name'=>[
									'type'=>'select',
								],
								'account_details.b_acc_type'=>[
									'type'=>'select',
								],
							],
							'RULES'=>(isset($userInfo->currency_code) ? ($userInfo->currency_code == 'USD' ? [
										'account_details.amount'=>'required|numeric|greater:10',
										'account_details.us_bank_name'=>'required',
										'account_details.us_acc_no'=>'required|min:4',
										'account_details.us_swift_code'=>'required|regex:/^[A-Za-z0-9]{8,11}+$/',
											] : ($userInfo->currency_code == 'INR' ? [
												'account_details.b_bank_name'=>'required',
												'account_details.acc_holder_name'=>'required',
												'account_details.b_accno'=>'required|regex:/^[0-9]*$/|min:4|max:17',
												'account_details.conf_b_accno'=>'required|max:17|same:account_details.b_accno',												
												'account_details.ifsc'=>'required|regex:/^[A-Za-z]{4}[0][A-Za-z0-9]{6}$/',
												//'account_details.b_amount'=>'required|numeric|greater:999|lesser:10000',
												'account_details.b_acc_type'=>'required|in:1,2',
												'account_details.b_acc_branch'=>'required',
													] : ($userInfo->currency_code == 'SGD' ? [
														'account_details.amount'=>'required|numeric|greater:10',
														'account_details.sgd_bank_name'=>'required',
														'account_details.sgd_acc_number'=>'required|min:4',
														'account_details.sgd_swift_code'=>'required|regex:/^[A-Za-z0-9]{8,11}+$/',
														'account_details.sgd_bank_code'=>'required',
														'account_details.sgd_branch_code'=>'required',
															] : []))) : []),
								'MESSAGES'=>[
									'account_details.acc_holder_name.required'=>'Account Holder Name is required.',	
									'account_details.b_accno.required'=>'Enter a valid account number.',		
									'account_details.b_accno.regex'=>'This field can only contain numeric values.',		
									'account_details.b_accno.min'=>'Account No. format is invalid.',		
									'account_details.b_accno.max'=>'Account No. format is invalid.',																		
									'account_details.conf_b_accno.required'=>'Confirm Account No. is required.',		
									'account_details.conf_b_accno.same'=>'Enter Valid Account No.',		
									'account_details.conf_b_accno.max'=>'Enter Valid Account No.',		
									'account_details.ifsc.required'=>'IFSC Code is required.',		
									'account_details.ifsc.regex'=>'Invalid IFSC Code.',		
									'account_details.b_amount.required'=>'Please enter an amount.',
									'account_details.b_amount.greater'=>'Amount must be greater than &#8377; 1000.00',	
									'account_details.b_amount.lesser'=>'Amount must be lesser than &#8377; 10000.00',	
									'account_details.b_acc_type.required'=>'Account type is required',	
									'account_details.b_acc_type.numeric'=>'Account type is invalid',	
									'account_details.b_acc_type.in'=>'Account type is invalid',	
									'account_details.b_acc_branch.required'=>'Account Branch is required',	
								]
                            ]),
		'get-amount'=>[
            'RULES'=>[
                'id'=>'required',
				'amount'=>'required|numeric|greater:999|lesser:10000',
            ],            
			'MESSAGES'=>[
                'id.required'=>'Bank Account ID is Required.',
				'amount.required'=>'Please enter an amount.',
				'amount.greater'=>'Amount must be greater than &#8377; 1000.00',	
				'amount.lesser'=>'Amount must be lesser than &#8377; 10000.00',
            ]
        ],
		'delete-bank-details'=>[
            'RULES'=>[
                'id'=>'required'
            ],            
			'MESSAGES'=>[
                'id.required'=>'Bank Account ID is Required.'
            ]
        ],
		'confirm'=>[
			/* 'RULES'=>[
					'security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
			  ],
			  'LABELS'=>[
				  'security_pin'=>'security Pin',
			 ],
			'MESSAGES'=>[
					'security_pin.required'=>'Enter Security PIN',                   
					'security_pin.regex'=>'Invalid Security PIN,Please try again',			
					'security_pin.min'=>'Security PIN must be 4 digit number',			
					'security_pin.max'=>'Your Security PIN can\'t be longer than 4 digits',
			] */
			'RULES'=>[                
                'auth_type'=>'required|in:1,2',
                'security_pin'=>'required_if:auth_type,1|security_pin',
                'auth_status'=>'required_if:auth_type,2',
            ],
            'LABELS'=>[
                'auth_type'=>'general.label.auth_type',
                'profile_pin'=>'general.profile_pin',
                'auth_status'=>'required_if:auth_type,2',
            ],
			'MESSAGES'=>[
                'auth_type.required'=>'Select Authentication Type.',
                'auth_type.in'=>'Invalid Authentication Type.',
                'security_pin.required_if'=>'Security Pin Required.',
                'security_pin.security_pin'=>'Invalid Security Pin.',
                'auth_status.required_if'=>'Authentication Required.',
            ],
        ],
	],
	'redeem'=>[
        'store'=>[
            'search'=>[
                'RULES'=>[
                    'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,0,status,1,is_approved,1',
				],
                'LABELS'=>[
                    'store_code'=>'general.label.merchant_id',                    
                ],
				'MESSAGES'=>[
                    'store_code.required'=>'Please enter a valid Merchant ID',                    
                    'store_code.exists'=>'Invalid QR Code/Merchant ID',                    
                ],
            ]
        ],
        'set-bill-amount'=>[
            'RULES'=>[
                'amount'=>'required|numeric|greater:99|lesser:50001',
				'wallet'=>'required|in:'.implode(',', array_keys(config('constants.BONUS_WALLET'))),				
				'auth_type'=>'required_with:amount|in:1,2',
                'security_pin'=>'required_if:auth_type,1|security_pin',
                'auth_status'=>'required_if:auth_type,2',
            ],
            'LABELS'=>[
                'amount'=>'user/account.amount',
				'wallet'=>'user/account.wallet',
            ],
			'MESSAGES'=>[
                //'amount.required'=>'Please enter an amount',
                //'amount.numeric'=>'Not Valid Amount',
                //'amount.greater'=>'Bill amount must be greater than or equal to &#8377; 10',
				'amount.required'=>'Please enter an amount',
                'amount.numeric'=>'Not Valid Amount',
                'amount.greater'=>'Enter an amount between 100.00 and 50,000.00 INR',
                'amount.lesser'=>'Enter an amount between 100.00 and 50,000.00 INR',
				'wallet.required'=>'Select wallet.',
                'wallet.in'=>'Selected wallet is invalid.',				
				'auth_type.required_with'=>'Select Authentication Type.',
                'auth_type.in'=>'Invalid Authentication Type.',
                'security_pin.required_if'=>'Security Pin Required.',
                'security_pin.security_pin'=>'Invalid Security Pin.',
                'auth_status.required_if'=>'Authentication Required.',
            ]
        ],  
		'wallet-validate'=>[
            'RULES'=>[
                //'wallet'=>'required|in:'.implode(',', array_keys(config('constants.BONUS_WALLET'))),
                //'amount'=>'required_if:wallet,'.implode(',', array_keys(config('constants.BONUS_WALLET'))).'|numeric|greater:1',    
                'amount'=>'required|numeric|greater:1',    
				'opayment_type'=>'required|in:'.implode(',', array_keys(config('constants.REDEEM_PAYMENT_TYPES'))),
            ],
            'LABELS'=>[
                'wallet'=>'user/account.wallet',
                'amount'=>'user/account.amount',
            ],
            'MESSAGES'=>[
                'wallet.required'=>'Select wallet.',
                'wallet.in'=>'Selected wallet is invalid.',
                'amount.required'=>'Enter amount.',
                'amount.numeric'=>'Amount must be a numeric.',
                'amount.greater'=>'Amount must be greater than 1.',
                'opayment_type.required'=>'The payment type is required.',
                'opayment_type.in'=>'Selected payment type field is Invalid.',
            ],
        ],
		'confirm'=>[
            'RULES'=>[
                //'code'=>'required|regex:/^[1-9]{1}[0-9]{5}$/',
            ],
            'LABELS'=>[
                'code'=>'OTP'
            ],
			'MESSAGES'=>[
                'code.required'=>'Please enter OTP',
                'code.regex'=>'Not Valid OTP',               
            ]
        ], 
    ],
	'pay'=>[
        'store'=>[
            'search'=>[
                'RULES'=>[
                    'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,0,status,1,is_approved,1',
				],
                'LABELS'=>[
                    'store_code'=>'general.label.merchant_id',                    
                ],
				'MESSAGES'=>[
                    'store_code.required'=>'Please enter Merchant ID',                    
                    'store_code.exists'=>'Invalid QR Code/Merchant ID',                    
                ],
            ]
        ],
        'set-bill-amount'=>[
            'RULES'=>[
                'amount'=>'required|numeric|greater:99|lesser:50001'
            ],
            'LABELS'=>[
                'amount'=>'user/account.amount'
            ],
			'MESSAGES'=>[
                'amount.required'=>'Please enter an amount',
                'amount.numeric'=>'Not Valid Amount',
                'amount.greater'=>'Enter an amount between 100.00 and 50,000.00 INR',
                'amount.lesser'=>'Enter an amount between 100.00 and 50,000.00 INR',
            ]
        ],
        'get-payment-types'=>[
            'RULES'=>[
                //'auth_type'=>'required|regex:/^[0-9]$/',
                'auth_type'=>'required|in:1,2',
                'security_pin'=>'required_if:auth_type,1|security_pin',
                'auth_status'=>'required_if:auth_type,2',
            ],
            'LABELS'=>[
                'auth_type'=>'general.label.auth_type',
                'profile_pin'=>'general.profile_pin',
                'auth_status'=>'required_if:auth_type,2',
            ],
			'MESSAGES'=>[
                'auth_type.required'=>'Select Authentication Type.',
                'auth_type.in'=>'Invalid Authentication Type.',
                'security_pin.required_if'=>'Security Pin Required.',
                'security_pin.security_pin'=>'Invalid Security Pin.',
                'auth_status.required_if'=>'Authentication Required.',
            ],
        ],
        'get-payment-info'=>[
            'RULES'=>[
                'payment_mode'=>'required|in:'.implode(',', array_keys(config('constants.PAYMENT_MODES'))),
            ],
            'LABELS'=>[
                'payment_mode'=>'withdrawal.labels.payment_type'
            ],
			'MESSAGES'=>[
                'payment_mode.required'=>'Please select a Payment Mode',
                'payment_mode.in'=>'Invalid Payment Type'
            ]
        ],        
    ],
	'cashback'=>[
	    'store'=>[
	        'search'=>[
                'RULES'=>[
                    'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,0,status,1,is_approved,1',
				],
                'LABELS'=>[
                    'store_code'=>'general.label.merchant_id',                    
                ],
				'MESSAGES'=>[                         
					'store_code.required'=>'Please enter Merchant ID',                    
                    'store_code.exists'=>'Invalid QR Code/Merchant ID',       
                ],
            ],
	    ],
		'set-bill-amount'=>[
			'RULES'=>[
				'bill_amount'=>'required|numeric|greater:9',
			],
			'LABELS'=>[
				'bill_amount'=>'user/account.amount',
			],
			'MESSAGES'=>[
				  'bill_amount.required'=>'Please enter an amount',
                  'bill_amount.numeric'=>'Not Valid Amount',
                  'bill_amount.greater'=>'Enter min. amount of ₹ 10',
			],
	    ],
		'confirm'=>[
            'RULES'=>[
                'code'=>'required|regex:/^[1-9]{1}[0-9]{5}$/',
            ],
            'LABELS'=>[
                'code'=>'OTP'
            ],
			'MESSAGES'=>[
                'code.required'=>'Please enter OTP',
                'code.regex'=>'Invalid OTP, Please try again',               
            ]
        ], 
	],
	'online-stores'=>[
        'details'=>[
            'RULES'=>[
                'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,1,status,1,is_approved,1',
            ],
            'LABELS'=>[
                'store_code'=>'general.outlet_code',
            ]
        ]
    ],
	'favourite'=>[
        'store'=>[
            'add'=>[
                'RULES'=>[
                    'store_code'=>'required|exists:'.config('tables.STORES').',store_code,status,1,is_approved,1,is_deleted,0',
                ],
                'LABELS'=>[
                    'store_id'=>'general.outlet'
                ]
            ],
        ],
	],
	'store'=>[
		'like'=>[
            'RULES'=>[
                'status'=>'required|in:0,1'
            ],
            'MESSAGES'=>[
                'status.required'=>'Parameter missing',
                'status.in'=>'Invalid parameter'
            ],
        ],
		'details'=>[
            'RULES'=>[
                 'store_code'=>'required|exists:'.config('tables.STORES').',store_code,is_deleted,0,is_online,0,status,1,is_approved,1',
            ],
            'MESSAGES'=>[
                'store_code.required'=>'Please enter Store Code',                    
                'store_code.exists'=>'Invalid Store Code',    
            ],
        ],
	],   
	'login'=>[
        'RULES'=>[
            'username'=>'required',
            'password'=>'required|regex:/^[A-Za-z0-9]*$/',            
        ],
        'LABELS'=>[
            'username'=>'Current Password',
            'password'=>'Password',            
        ],
        'MESSAGES'=>[
            'username.required'=>'Please enter email/mobile number',
            'password.required'=>'Please enter password',
        ]
    ],
	'change-pwd'=>[
        'RULES'=>[
            'current_password'=>'required|min:6|max:16',
            'password'=>'required|min:6|max:16|different:current_password|password',
            'conf_password'=>'required|max:16|same:password',
        ],
        'LABELS'=>[
            'current_password'=>'Current Password',
            'password'=>'Password',
            'conf_password'=>'Confirm password',
        ],
        'MESSAGES'=>[
            'current_password.required'=>'Current Password is required',
            'current_password.min'=>'Current Password cannot be less than 6 characters',
            'current_password.max'=>'Your Current password can\'t be longer than 16 characters',
            'password.required'=>'New Password is required',
            'password.min'=>'New Password cannot be less than 6 characters',
            'password.max'=>'Your New Password can\'t be longer than 16 characters',
            'password.password'=>'Invalid Password',
            'password.different'=>'Your New Password cannot be same as old password',
            'conf_password.required'=>'Confirm Password is required',
            'conf_password.max'=>'Your Confirm password can\'t be longer than 16 characters',
            'conf_password.same'=>'Passwords do not match',
        ]
    ],
	'change-pwd_bk'=>[
        'RULES'=>[
            'current_password'=>'required|password',
            'password'=>'required|password|different:current_password',
            'conf_password'=>'required|password|same:password',
        ],
        'LABELS'=>[
            'current_password'=>'Current Password',
            'password'=>'Password',
            'conf_password'=>'Confirm password',
        ],
        'MESSAGES'=>[
            'current_password.required'=>'Current password is required.',
            //'current_password.password'=>'New Password must be different than the Current Password.',
            'password.required'=>'New Password is required.',
            'conf_password.required'=>'Confirm Password is required.',
            'password.different'=>'Your new password cannot be same as old password.',
            'conf_password.same'=>'Passwords do not match. Please try again.',
        ]
    ],	
	'signup'=>[
        'signup'=>[
			'RULES'=>[
				//'full_name'=>'required|full_name|min:3',
				//'full_name'=>'required|signup_first_name|signup_last_name',
				'full_name'=>'required|signup_first_name|signup_last_name',
				'email'=>'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',
				'password'=>'required|min:6|max:16|regex:/^[A-Za-z0-9]*$/',                                 
				'country'=>'required|exists:'.config('tables.LOCATION_COUNTRY').',country_id,status,1',							
				'mob_number'=>'required_with:country|db_regex:mobile_validation,location_countries,country_id,country|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',				
				'referral_code'=>'sometimes|regex:/^[0-9]*$/|min:6|max:6|exists:'.config('tables.ACCOUNT_PREFERENCE').',referral_code',				
			],			
			'MESSAGES'=>[
				'full_name.required'=>'Please enter full name',
				//'full_name.full_name'=>'Please enter valid full name',
				//'full_name.min'=>'Full name must be at least 3 characters',
				'full_name.signup_first_name'=>'Enter your valid first name',
				'full_name.signup_last_name'=>'Enter your valid last name',
				'email.required'=>'Please enter email id',
				'email.email'=>'Please enter a valid email Id',
				'email.max'=>'Your email can\'t be longer than 62 characters',
				'email.unique'=>'You already have an account with that email',
				'password.required'=>'Please enter password', 
				'password.min'=>'Password must be at least 6 characters',
				'password.max'=>'Your password can\'t be longer than 16 characters',	
				'password.regex'=>'The password that you\'ve entered is incorrect',	
				'country.required'=>'Please select country',				
				/* 'referral_code.required'=>'Referral Code is required', */
				'referral_code.regex'=>'Invalid Referral Code, Please try again',
				'referral_code.min'=>'The Referral Code must be 6 characters',
				'referral_code.max'=>'Referral Code can\'t be longer than 6 characters',
				'referral_code.exists'=>'Your Referral Code was entered incorrectly. Please enter It again',
				'mob_number.required_with'=>'Please enter valid mobile number',
				'mob_number.regex'=>'Please enter valid mobile number',				
				//'mob_number.unique'=>'This phone number has already been used',				
				'mob_number.unique'=>'Mobile number has already been taken',				
			], 
			/* 'MESSAGES'=>array_merge([
			'full_name.required'=>'Please enter full name',
							'full_name.full_name'=>'Please enter valid full name',
							'email.required'=>'Please enter email id',
							'email.email'=>'Please enter a valid email Id',
							'password.required'=>'Please enter password',                                 
							'password.password'=>'The password that you\'ve entered is incorrect.', 
							'password.min'=>'Password must be at least 6 characters',
							'password.max'=>'Your password can\'t be longer than 16 characters',	
							'country.required'=>'Please select country',				
							'referral_code.required'=>'Referral Code is required',
							'referral_code.regex'=>'Invalid Referral Code, Please try again',
							'referral_code.min'=>'The Referral Code must be 6 characters',
							'referral_code.max'=>'Referral Code can\'t be longer than 6 characters',
							'referral_code.exists'=>'Your Referral Code was entered incorrectly. Please enter It again',
							'mob_number.required_with'=>'Please enter your mobile number',
			],(!is_null($request->has('country')) ? ($request->get('country') == 135 ? [
							'mob_number.regex'=>'Please enter valid 10 digit mobile number, which starts with 9',              // Philippines 
						] : ($request->get('country') == 77 ? [														
							'mob_number.regex'=>'Please enter valid 10 digit mobile number, which starts with 7 | 8 | 9',		// India 
						] : ($request->get('country') == 104 ? [
							'mob_number.regex'=>'Please enter valid 9 | 10 digit mobile number, which starts with 1',		    // Malaysia 
						] : ($request->get('country') == 152 ? [
							'mob_number.regex'=>'Please enter valid 8 digit mobile number and starts with 8 | 9',		        // Singapore 
						] : ($request->get('country') == 183 ? [
							'mob_number.regex'=>'Please enter valid 10 digit mobile number',									// USA   
						] : []))))) : [])), */
		],
		'verify-mobile'=>[
			'RULES'=>[        
				'country'=>'required|exists:'.config('tables.LOCATION_COUNTRY').',country_id,status,1',            
				'mob_number'=>'required_with:country|db_regex:mobile_validation,location_countries,country_id,country|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',
			],				
			'MESSAGES'=>[					
				'country.required'=>'Please select country',	
				'mob_number.required_with'=>'Please enter valid mobile number',
				'mob_number.regex'=>'Please enter valid mobile number',				
				'mob_number.unique'=>'This phone number has already been used',	
			], 
			/* 'MESSAGES'=>array_merge([								
						'country.required'=>'Please select country',											
						'mob_number.required_with'=>'Please enter your mobile number',
			],(!is_null($request->has('country')) ? ($request->get('country') == 135 ? [
							'mob_number.regex'=>'Please enter valid 10 digit mobile number, which starts with 9',              // Philippines 
						] : ($request->get('country') == 77 ? [														
							'mob_number.regex'=>'Please enter valid 10 digit mobile number, which starts with 7 | 8 | 9',		// India 
						] : ($request->get('country') == 104 ? [
							'mob_number.regex'=>'Please enter valid 9 | 10 digit mobile number, which starts with 1',		    // Malaysia 	
						] : ($request->get('country') == 152 ? [
							'mob_number.regex'=>'Please enter valid 8 digit mobile number and starts with 8 | 9',		        // Singapore 
						] : ($request->get('country') == 183 ? [
							'mob_number.regex'=>'Please enter valid 10 digit mobile number',									// USA   
						] : []))))) : [])), */
		],
		'confirm'=>[
			'RULES'=>[
				'code'=>'required|regex:/^[0-9]{6}$/'          
			],
			'LABELS'=>[
				'code'=>'Code',            
			], 
			'MESSAGES'=>[
				'code.required'=>'Please enter valid OTP',
				'code.regex'=>'OTP is invalid or expired'
			]
		],
    ],	
	'profile-settings'=>[
	    'profile'=>[
			'image-upload'=>[
				'RULES'=>[
					'attachment'=>'required|file|mimes:jpg,jpeg,png|max:1024'
				],
				'LABELS'=>[
					'attachment'=>'user/account.profile_image'
				],
				'MESSAGES'=>[
					'attachment.required'=>'Profile Image is required',
					'attachment.file'=>'Please select a file in the format (jpg,jpeg,png)',
					'attachment.mimes'=>'Please select a file in the format (jpg,jpeg,png)',
					'attachment.max'=>'Profile Image can\'t be longer than 1 MB.',
				]
			],
			'update'=>[
				'RULES'=>[
					'first_name'=>'required|firstname|min:3|max:50',
					'last_name'=>'required|lastname|min:1|max:50',					
					'display_name'=>'required|min:6|regex:/^[a-z0-9]*$/'.(isset($userInfo->uname) ? request()->get('display_name') != $userInfo->uname ? '|unique:'.config('tables.ACCOUNT_MST').',uname,NULL,account_id,is_deleted,0,is_closed,0' : '' : ''),
					],
				'LABELS'=>[
					'first_name'=>'First Name',
					'last_name'=>'Last Name',
					'display_name'=>'Username',
				],			
				'MESSAGES'=>[
					'first_name.required'=>'Enter first name',
					'last_name.required'=>'Enter last name',
					'display_name.required'=>'Enter username',				
					'last_name.min'=>'The lastname must be at least 1 character long',				
					'display_name.min'=>'The username must be at least 6 character long',				
				],
			],
		],
		'security-pin'=>[
	        'save'=>[
                'RULES'=>[
                      'security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
					  'confirm_security_pin'=>'required|max:4|same:security_pin',
                  ],
			      'LABELS'=>[
			          'security_pin'=>'Security Pin',
					  'confirm_security_pin'=>'Confirm Security Pin',
			     ],
				'MESSAGES'=>[
					'security_pin.required'=>'Enter Security PIN',
					'security_pin.regex'=>'Invalid Security PIN,Please try again',
					'security_pin.min'=>'PIN must be 4 digit number',
					'security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
					'confirm_security_pin.required'=>'Enter Confirm Security PIN',
					'confirm_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
					'confirm_security_pin.same'=>'New PIN and Confirm PIN do not match, please try again.'
                ],
	        ],			
			'verify'=>[
				'RULES'=>[
                      'security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
                ],
			    'LABELS'=>[
			          'security_pin'=>'security Pin',
			    ],
				'MESSAGES'=>[
					'security_pin.required'=>'Enter Security PIN',                   
					'security_pin.regex'=>'Invalid Security PIN,Please try again',			
					'security_pin.min'=>'Security PIN must be 4 digit number',			
					'security_pin.max'=>'Your Security PIN can\'t be longer than 4 digits',
                ]
            ],
			'reset'=>[
                'RULES'=>[
                      'security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
					   'confirm_security_pin'=>'required|max:4|same:security_pin',
					   'code'=>'required|',
                ],
			    'LABELS'=>[
			          'security_pin'=>'Security Pin',
					  'confirm_security_pin'=>'Confirm Security Pin',
			    ],
				'MESSAGES'=>[
					'code.required'=>'Enter Verification Code',
					'security_pin.required'=>'Enter Security PIN',
					'security_pin.regex'=>'Invalid Security PIN, please try again',
					'security_pin.min'=>'PIN must be 4 digit number',
					'security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
					'confirm_security_pin.required'=>'Enter Confirm Security PIN',                             
					'confirm_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
					'confirm_security_pin.same'=>'New PIN and Confirm PIN do not match, please try again'
				]
			],	  
			'change'=>[
                'RULES'=>[
                    'current_security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4',
                    'new_security_pin'=>'required|regex:/^[0-9]*$/|min:4|max:4|different:current_security_pin',
                    'confirm_security_pin'=>'required|max:4|same:new_security_pin',
                ],
                'LABELS'=>[
                    'current_security_pin'=>'Current Security PIN',
                    'new_security_pin'=>'New Security PIN',
                    'confirm_security_pin'=>'Confirm Security PIN'
                ],
                'MESSAGES'=>[                  
                    'current_security_pin.required'=>'Enter Current Security PIN',                   
                    'current_security_pin.regex'=>'Invalid Security PIN,Please try again',                   
                    'current_security_pin.min'=>'PIN must be 4 digit number',                   
                    'current_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',                   
                    'new_security_pin.required'=>'Enter New Security PIN ',
                    'new_security_pin.regex'=>'Invalid Security PIN,Please try again',
                    'new_security_pin.min'=>'PIN must be 4 digit number',
                    'new_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
                    'new_security_pin.different'=>'Your new PIN cannot be same as old PIN',
					'confirm_security_pin.required'=>'Enter Confirm Security PIN',
					'confirm_security_pin.profile_pin'=>'PIN must be 4 digit number',
                    'confirm_security_pin.max'=>'Your PIN can\'t be longer than 4 digits',
                    'confirm_security_pin.same'=>'New PIN and Confirm PIN do not match, please try again',
                ],
			],
		],	   
	    'change-email'=>[
            'sendotp'=>[
                'RULES'=>[
                    'code'=>'required|regex:/^[0-9a-z]+$/',
                    'new_email'=>'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',      
                ],
                'LABELS'=>[
                    'code'=>'Profile Pin Session',
                    'new_email'=>'Email'
                ],
				'MESSAGES'=>[
				   'code.required'=>'Please Verification Code',
				   'new_email.required'=>'Please Enter New Email',
			    ]				
            ],
            'verify-otp'=>[
                'RULES'=>[
                    'code'=>'required'
                ],
                'LABELS'=>[
                    'code'=>'verification_code'
                ],
			   'MESSAGES'=>[
                    'code.required'=>'Please Enter Verification Code'
                ]
            ]
        ],
		'change-mobile'=>[
	        'sendotp'=>[
	             'RULES'=>[
					'code'=>'required|regex:/^[0-9a-z]+$/',
					 'mobile'=>'required|max:10|exists:'.config('tables.ACCOUNT_MST').',mobile,account_type_id,2,is_deleted,0,is_closed,0',  
                ],
                'MESSAGES'=>[
					'code.required'=>'Please Enter Verification Code',
					'mobile.required'=>'Please Enter Mobile Number',
                ]	   
	       ],
		   'verify-otp'=>[
                'RULES'=>[
                    'code'=>'required'
                ],
                'LABELS'=>[
                    'code'=>'verification_code'
                ],
				 'MESSAGES'=>[
		            'code.required'=>'Please Enter Verification Code'
		      ],
            ]
	    ]
	],
	'country-update'=>[
        'RULES'=>[
            'country_id'=>'required|exists:'.config('tables.LOCATION_COUNTRY').',country_id,status,1,operate,1',            
        ],
        'LABELS'=>[
            'country_id'=>'Country',           
        ], 
		'MESSAGES'=>[
            'country_id.required'=>'Country is required',           
            'country_id.exists'=>'Invalid Country',           
        ],	
    ],
	'send-login-otp'=>[
        'RULES'=>[
            'mobile'=>'required|max:10|exists:'.config('tables.ACCOUNT_MST').',mobile,account_type_id,2,is_deleted,0,is_closed,0',            
        ],
        'LABELS'=>[
            'mobile'=>'Mobile',           
        ],        
    ],
	'login-with-otp'=>[
        'RULES'=>[
            'mobile'=>'required|max:10|exists:'.config('tables.ACCOUNT_MST').',mobile,account_type_id,2,is_deleted,0,is_closed,0',  
			'otp'=>'required'
        ],
        'LABELS'=>[
            'mobile'=>'Mobile',           
            'otp'=>'OTP',           
        ],        
    ],
	'forgot_pwd'=>[
		'RULES'=>[
            'uname'=>'required|email|exists:'.config('tables.ACCOUNT_MST').',email,account_type_id,2,is_deleted,0,is_closed,0',  
        ],
        'LABELS'=>[
            'uname'=>'Email ID',           
        ], 
		'MESSAGES'=>[
		      'uname.required'=>'Please enter email id',
		      'uname.exists'=>'Please enter a valid e-mail address'
		],
	],
	'reset_pwd'=>[
	    'RULES'=>[
			'code'=>'required|regex:/^[0-9]{6}$/',
			//'newpwd'=>'required:password',
			'newpwd'=>'required|password|min:6|max:16',  
			'conf_newpwd'=>'required|max:16|same:newpwd',
		],
		'LABELS'=>[
			'code'=>'verification_code',
			'newpwd'=>'New Password',
		],
		'MESSAGES'=>[
		    'code.required'=>'Please enter verification code',
			'code.regex'=>'OTP is invalid or expired',
		    //'newpwd.required'=>'Please enter new password',
		    'newpwd.required'=>'Please enter password',                                 
			'newpwd.password'=>'The password that you\'ve entered is incorrect.', 
			'newpwd.min'=>'Password must be at least 6 characters',
			'newpwd.max'=>'Your password can\'t be longer than 16 characters',
			'conf_newpwd.required'=>'Confirm Password is required',
            'conf_newpwd.max'=>'Your Confirm password can\'t be longer than 16 characters',
            'conf_newpwd.same'=>'Passwords do not match',
		],
	],	
];


