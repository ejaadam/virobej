<?php

return [	
       'affiliate'=>[
		     'save'=>[
			'RULES'=>[
				'first_name'=>'required|firstname|min:3|max:30',
				'last_name'=>'required|lastname|min:3|max:30',
				'email'=>'required|email|unique:account_mst,email',
				'uname'=>'required|username|unique:account_mst,uname',
				'password'=>'required|min:6|max:10',
				'confirm_password'=>'required|min:6|max:10|password|same:password',
				'zipcode'=>'required|zipcode',
				'country'=>'required|',
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
	'aff'=>[
		'root-account'=>[
			'save'=>[
				'RULES'=>[
						'first_name'=>'required|firstname|min:3|max:30',
						'last_name'=>'required|lastname|min:3|max:30',
						'email'=>'required|email|unique:account_mst,email',
						'uname'=>'required|username|unique:account_mst,uname',
						'password'=>'required|min:6|max:10',
						'confirm_password'=>'required|min:6|max:10|password|same:password',
						'zipcode'=>'required|zipcode',
						'country'=>'required|',
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
		]
	],
	
	 'online'=>[
        'store-save'=>[
            'RULES'=>[
                'affiliate.store_name'=>'required',
                //'affiliate.category_id'=>'required',
                'affiliate.store_logo'=>'mimes:jpg,jpeg,gif,png|max:500',
                'affiliate.website_url'=>'',
                'affiliate.tags'=>'',
                'affiliate.program_id'=>'required',
                'affiliate.cashback'=>'required',
                'affiliate.cashback_type'=>'required',
                'affiliate.old_cashback'=>'required',
                'affiliate.old_cashback_type'=>'required',
                'affiliate.program_id'=>'required',
                //'affiliate.url'=>'required|regex:/\{USER_ID\}/',
                'affiliate.url'=>'required',
                'affiliate.status'=>'required',
                'affiliate.expired_on'=>'required',
                'affiliate.logo_url'=>!empty(request()->segments(4)) ? 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/' : 'required_without:image_url|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
                'affiliate.aff_netwrk'=>'required',
                'affiliate.affiliate_desc'=>'',
                'affiliate.meta_title'=>'',
                'affiliate.meta_desc'=>'',
                'affiliate.meta_keyword'=>'',
               
                /* 'affiliate.cb_traking_period'=>'required',
                'affiliate.cb_waiting_period'=>'required', */
                'desc_type'=>'required',
                'img_type'=>'required',
                'image_url'=>!empty(request()->segments(4)) ? 'file|mimes:jpg,jpeg,gif,png|max:1024' : 'required_without:affiliate.logo_url|file|mimes:jpg,jpeg,gif,png|max:1024',
            ],
            'LABELS'=>[
                'affiliate.store_name'=>'admin/affiliate.affiliate_name',
                /*   'affiliate.category_id'=>'admin/affiliate.affiliate_category', */
                'affiliate.store_logo'=>'admin/affiliate.affiliate_image_url',
                'affiliate.website_url'=>'admin/affiliate.website_url',
                'affiliate.tags'=>'admin/affiliate.tags',
                'affiliate.program_id'=>'admin/affiliate.program_id',
                'affiliate.affiliate_desc'=>'admin/affiliate.affiliate_desc',
                'affiliate.meta_title'=>'admin/affiliate.meta_title',
                'affiliate.meta_desc'=>'admin/affiliate.meta_desc',
                'affiliate.meta_keyword'=>'admin/affiliate.meta_keyword',
                'affiliate.url'=>'admin/affiliate.url_format',
                'affiliate.aff_netwrk'=>'admin/affiliate.affiliate_network',
                'affiliate.old_cashback'=>'admin/affiliate.old_cashback',
                'affiliate.cashback'=>'admin/affiliate.cashback',
                'affiliate.expired_on'=>'admin/affiliate.expired_on',
                
            ]
        ],
    ],
	'category'=>[
		'online_store'=>[
			'update'=>[
				'RULES'=>[
					'bcategory_name'=>'required|regex:/^[a-zA-Z0-9\s\&\-\_\']+$/',
					'bcategory_slug'=>'required|regex:/^[a-z0-9\-]+$/',
				],
				'LABELS'=>[
					'bcategory_name'=>'admin/online_category/category.bcategory_name',
					'bcategory_slug'=>'admin/online_category/category.url',
				]
			]
		],
		'products'=>[
			'update'=>[
				'RULES'=>[
					'bcategory_name'=>'required|regex:/^[a-zA-Z0-9\s\&\-\_\']+$/',
					'bcategory_slug'=>'required|regex:/^[a-z0-9\-]+$/',
				],
				'LABELS'=>[
					'bcategory_name'=>'admin/online_category/category.bcategory_name',
					'bcategory_slug'=>'admin/online_category/category.url',
				]
			]
		],
	]
];