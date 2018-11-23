<?php

return [
	'order'=>[
		'commissions'=>[
            'title'=>'Order Commissions',
            'order_amount'=>'Order Amount',
            'amount'=>'Bill Amt.',
            'system_commission'=>'System Commission',
            'commission'=>'Fees',
            'tax'=>'Tax',
            'system_settlement'=>'Net pay',            
            'handle_amt'=>'Other Charges',
            'received_amt'=>'Cash Collected',
            'system_received_amt'=>'Fees Earned',
            'status'=>[
                0=>'Pending',
                1=>'Completed'
            ],
        ],
		'order_type'=>[
            1=>[
                1=>2,
                2=>2,
                3=>3,
            ],
            2=>[
                0=>1,
            ],
            3=>[],
        ],
		'pay_through'=>[
			1=>[
				1=>'Pay',
				2=>'Redemption',
				3=>'Cashback'
			],
			2=>[				
				1=>'Pay',
				2=>'Redemption',
				3=>'Shop & Earn'
			],
			3=>[
				1=>'Coupon Purchase'
			],
		],		
		'status'=>[
            0=>[
                1=>[
                    1=>[
                        0=>'Initiated',
                        1=>'Paid',
                        2=>'Failed',
                        3=>'User Cancelled',
                        4=>'Completed',
                        5=>'Refunded',
                        6=>'Under Dispute',
                        7=>'Refund In Progress',
                    ],
                    2=>[
                        0=>'Initiated',
                        1=>'Redeemed',
                        2=>'Failed',
                        3=>'User Cancelled',
                        4=>'Refunded',
                        5=>'Under Dispute',
                        6=>'Refund In Progress',
                    ],
                    3=>[
                        0=>'Initiated',
                        1=>'Confirmed',
                        2=>'Failed',
                        3=>'Rejected',
                    ]
                ],
                2=>[
                    0=>[
                        0=>'Pending',
                        1=>'Bought',
                        2=>'Cancelled',
                        3=>'Redeemed',
                        4=>'Expired'
                    ],
                    1=>[
                        0=>'Pending',
                        1=>'Bought',
                        2=>'Cancelled',
                        3=>'Redeemed',
                        4=>'Expired'
                    ],
                    2=>[
                        0=>'Pending',
                        1=>'Bought',
                        2=>'Cancelled',
                        3=>'Redeemed',
                        4=>'Expired'
                    ],
                ],
                3=>[
                    1=>[
                    ],
                ],
                4=>[
                    1=>[
                        0=>'Tracking',
                        1=>'Confirmed',
                        2=>'Failed',
                        3=>'Cancelled',
                        4=>'Pending',
                        5=>'Rejected',
                    ],
                ]
            ],
            2=>[
                1=>[
                    1=>[
                        0=>'Initiated',
                        1=>'Paid',
                        2=>'Failed',
                        3=>'User Cancelled',
                        4=>'Completed',
                        5=>'Refunded',
                        6=>'Under Dispute',
                        7=>'Refund In Progress',
                    ],
                    2=>[
                        0=>'Initiated',
                        1=>'Redeemed',
                        2=>'Failed',
                        3=>'User Cancelled',
                        4=>'Refunded',
                        5=>'Under Dispute',
                        6=>'Refund In Progress',
                    ],
                    3=>[
                        0=>'Initiated',
                        1=>'Confirmed',
                        2=>'Failed',
                        3=>'Rejected',
                    ]
                ],
                2=>[
                    0=>[
                        0=>'Pending',
                        1=>'Sold',
                        2=>'Cancelled',
                        3=>'Redeemed',
                        4=>'Expired'
                    ],
                    1=>[
                        0=>'Pending',
                        1=>'Sold',
                        2=>'Cancelled',
                        3=>'Redeemed',
                        4=>'Expired'
                    ],
                    2=>[
                        0=>'Pending',
                        1=>'Sold',
                        2=>'Cancelled',
                        3=>'Redeemed',
                        4=>'Expired'
                    ],
                ],
                3=>[
                    1=>[
                    ],
                ],
                4=>[
                    1=>[
                        0=>'Tracking',
                        1=>'Confirmed',
                        2=>'Failed',
                        3=>'Cancelled',
                        4=>'Pending',
                        5=>'Rejected',
                    ],
                ]
            ]
        ],
	],	
	'btn'=>[
        'details'=>'Details',
	],
	'label'=>[
		'order_id'=>'Order ID',
		'date'=>'Date',
		'customer'=>'Customer',
		'merchant_details'=>'Merchant Details',
		'amount'=>'Bill Amt.',
		'remarks'=>'Remarks',
		'status'=>'Status',
		'created_on'=>'Created On',
		'stores'=>'Store & ID',		   
		'outlet'=>'Outlet',		   
		'details'=>'Order Details',		   
		'balance'=>'Balance',		   
		'pending_balance'=>'Pending Balance',		   
		'last_settlement'=>'Last Settlement',		   
    ],
	'invalid_username_or_password'=>'Invalid Username or Password',
	'invalid'=>'Invalid :which',
	'not_accessable'=>'Not accessble right now',
	'profile_pin_should_not_be_same'=>'Your new PIN cannot be same as old PIN',
	'invalid_current_pin'=>'Invalid Current Security PIN',
	'profile_pin_updated_successfully'=>'Your Security PIN has been changed successfully',
	'generate_profile_pin'=>'Create your Security PIN first',
	'code_sent_to_email'=>'Verification code has been sent to your email :email',
	'code'=>'OTP',	
	'not_accessable'=>'Not accessible right now',
];
