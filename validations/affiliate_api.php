<?php
return [
	'change-pwd'=>[
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
            'password.different'=>'New Password must be different than the Current Password.',
            'conf_password.same'=>'Password do not match. Please try again.',
        ]
    ],
	'sign-up'=>[
        'RULES'=>[
            'full_name'=>'required|firstname',
            'email'=>'required|email|max:62|unique:'.config('tables.ACCOUNT_MST').',email,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',            
            'mobile'=>'required|max:10|unique:'.config('tables.ACCOUNT_MST').',mobile,NULL,account_id,account_type_id,2,is_deleted,0,is_closed,0',            
            'password'=>'required|password',
            'conf_password'=>'required|password|same:password',
            'agree_to_rec_offers'=>'boolean',
            'agree_to_term_cond'=>'accepted', 
        ],
        'LABELS'=>[
            'full_name'=>'Full Name',
            'email'=>'E-Mail',
            'password'=>'Password',
            'conf_password'=>'Confirm Password',
            'agree_to_rec_offers'=>'I agree to receive exclusive offers and promotions from virob.',
            'agree_to_term_cond'=>'I have read & understood virob terms of usage and privacy policy.',
        ]
    ],
	
	'profile_settings'=>[
	    'update'=>[
        'RULES'=>[
            'first_name'=>'required',
            'last_name'=>'required',
            'gender'=>'required',
			'dob'=>'required',
        ],
        'LABELS'=>[
            'first_name'=>'First Name',
            'last_name'=>'Last Name',
            'gender'=>'Gender',
			'dob'=>'Date of Birth',
        ],
       ],
	   'profile-pin'=>[
	         'save'=>[
                'RULES'=>[
                      'security_pin'=>'required|security_pin'
                  ],
			      'LABELS'=>[
			          'security_pin'=>'Profile Pin',
			     ],
				'MESSAGES'=>[
                    'security_pin'=>'Profile must have 4 digit',
                ]
	      ],
		  'verify'=>[
			'RULES'=>[
                      'security_pin'=>'required|security_pin'
                  ],
			      'LABELS'=>[
			          'security_pin'=>'Profile Pin',
			     ],
				'MESSAGES'=>[
                    'security_pin'=>'Profile must have 4 digit',
                ]
            ],
	     ],
	   
	   'change-email'=>[
            'new-email-sendotp'=>
                 [
                'RULES'=>[
                    'propin_session'=>'required|regex:/^[0-9a-z]+$/',
                    'new_email'=>'required|email',
                ],
                'LABELS'=>[
                    'propin_session'=>'affiliate/account.propin_session',
                    'new_email'=>'Email'
                ],
				
           ],
            'verify-otp'=>[
                'RULES'=>[
                    'code'=>'required'
                ],
                'LABELS'=>[
                    'code'=>'verification_code'
                ]
            ]
        ],
	   'change-mobile'=>[
	          'req_otp'=>[
	            'RULES'=>[
                    'propin_session'=>'required|regex:/^[0-9a-z]+$/',
                    'mobile'=>'required|unique:'.config('tables.ACCOUNT_MST')
                ],
                'MESSAGES'=>[
                    'propin_session.required'=>'affiliate/account.propin_session',
                    'mobile'=>'Mobile No. required.'
                ]
	   
	       ],
		   'verify-otp'=>[
                'RULES'=>[
                    'code'=>'required'
                ],
                'LABELS'=>[
                    'code'=>'verification_code'
                ]
            ]
	    ],
	 ],
];