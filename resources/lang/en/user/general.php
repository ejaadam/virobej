<?php

return [
	'orders_list'=>[
        'all'=>[
            'title'=>'All',
        ],        
        'paid'=>[
            'title'=>'Paid'
        ],
        'cashback'=>[
            'title'=>'Cashback'
        ],
    ],
	'withdrawal_status'=>[
        0=>'Pending',
        1=>'Completed',
        2=>'Processing',
        3=>'Cancelled'
    ],
    'transactions'=>[
        'status'=>[
		    1=>'Success',
            0=>'Pending',
            2=>'Cancelled',
            3=>'Failed'
        ],
        'status_class'=>[
            0=>'warning',
            1=>'success',
            2=>'danger',
            3=>'danger',
        ],
    ],
	'order_payment_status'=>[
		'1'=>'SUCCESS',
		'0'=>'Pending',
		'2'=>'Cancelled',
		'3'=>'Failled',
	],
	'order'=>[
		'cashback'=>[
            'status'=>[
                0=>'Pending',
                1=>'Paid',
                2=>'Cancelled',
                3=>'Failed',
            ]
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
		'payment_status'=>[
            0=>'Pending',
            1=>'Paid',
            2=>'Failed',
            3=>'Cancelled',
            4=>'Partially Paid'
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
                3=>'Cashback'
            ],
            3=>[
                1=>'Coupon Purchase'
            ]
        ],
		'cashback'=>[
            'status'=>[
                0=>'Pending',
                1=>'Paid',
                2=>'Cancelled',
                3=>'Failed',
            ]
        ],
        'commissions'=>[
            'title'=>'Order Commissions',
            'order_amount'=>'Order Amount',
            'system_commission'=>'System Commission',
            'tax'=>'Tax',
            'system_settlement'=>'Settlement',
            'handle_amt'=>'Handle Amount',
            'received_amt'=>'Merchant Received Amount',
            'system_received_amt'=>'Admin Received Amount',
            'status'=>[
                0=>'Pending',
                1=>'Paid'
            ]
        ],
	],
	'payment_type'=>'Payment Type',
	'please_contact_administrator'=>'Please Contact Adminstator',
	'no_order'=>'Orders Not Found',
	'outlet_not_found'=>'No nearby outlet found in this area.',
];
