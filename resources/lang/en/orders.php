<?php

return [
    36=>[
        'admin'=>[
            'remarks'=>'',
            'fields'=>[
                'order_code'=>'',
                'amount'=>'Amount',
            ],
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'cash'=>'Cash Collected By Outlet',
                'xpc'=>'Paid from XPay Credit',
                'cbp'=>'Paid from Cashback Points',
                'bp'=>'Paid from Bonus Points',
                'netbanking'=>'Netbanking - :payment_type',
                'credit-card'=>'Credit Card - :payment_type',
                'debit-card'=>'Debit Card - :payment_type',
            ],
            'properties'=>[
                'payment_status'=>['class'=>'status_class']
            ],
        ],
        'user'=>[
            'list'=>[
                'remarks'=>'Deal Bought from :store_name',
                'details'=>'Paid for order #:order_code'
            ],
            'remarks'=>':deal_name',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
            ],
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'payment_id'=>'PG Transaction No.',
                'cash'=>'Cash Collected By :store_name',
                'xpc'=>'Paid from XPay Credit',
                'cbp'=>'Paid from Cashback Points',
                'bp'=>'Paid from Bonus Points',
                'netbanking'=>'Netbanking - :payment_type',
                'credit-card'=>'Credit Card - :payment_type',
                'debit-card'=>'Debit Card - :payment_type',
                'payment_status'=>'Payment Status',
            ],
            'properties'=>[
                'payment_status'=>['class'=>'status_class']
            ]
        ],
        'seller'=>[
            'list'=>[
                'remarks'=>'For order #:order_code',
                'details'=>'Deal sold to :customer'
            ],
            'remarks'=>':deal_name',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'email'=>'Email',
                'mobile'=>'Mobile',
                'member_id'=>'Member ID'
            ],
            'payment_details'=>[
                'pg_no'=>'Payment Details',
                'payment_id'=>'PG Transaction No.',
                'cash'=>'Cash Collected By :store_name',
                'xpc'=>'Paid from XPay Credit',
                'cbp'=>'Paid from Cashback Points',
                'bp'=>'Paid from Bonus Points',
                'netbanking'=>'Netbanking - :payment_type',
                'credit-card'=>'Credit Card - :payment_type',
                'debit-card'=>'Debit Card - :payment_type',
                'payment_status'=>'Payment Status',
            ],
            'properties'=>[
                'payment_status'=>['class'=>'status_class']
            ]
        ]
    ],
    51=>[
        'admin'=>[
            'list'=>[
                'remarks'=>'Payment from :customer',
                'details'=>'Paid for order #:order_code'
            ],
            'remarks'=>'Payment of :bill_amount received by :store_name!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'cash'=>'Cash Collected By :store_name',
            ],
        ],
        'seller'=>[
            'list'=>[
                'details'=>'Payment from :customer',
                'remarks'=>'Paid for order #:order_code'
            ],
            'remarks'=>'Payment of :bill_amount received!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'user_code'=>'Member ID',
                'mobile'=>'Mobile',
                'email'=>'Email',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'cash'=>'Cash Collected By :store_name',
            ],
        ],
        'user'=>[
            'list'=>[
                'remarks'=>'Payment to :store_name',
                'details'=>'Paid for order #:order_code'
            ],
            'remarks'=>'Payment of :bill_amount received by :store_name!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'cash'=>'Cash Collected By :store_name',
            ],
			'properties'=>[
                'payment_status'=>['class'=>'status_class']
            ]
        ]
    ],
    52=>[
        'admin'=>[
        ],
        'seller'=>[
            'list'=>[
                'details'=>'Payment from :customer',
                'remarks'=>'Paid for order #:order_code'
            ],
            'remarks'=>'Payment of :bill_amount received by PayGyft',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'user_code'=>'Member ID',
                'mobile'=>'Mobile',
                'email'=>'Email',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'vis'=>'Paid from Vi-Shop Point',
				'vib'=>'Paid from Vi-Bonus',
				'vim'=>'Paid from Vi-Money',
				'system_received_amt'=>'Cash collected by PayGyft',
				'store_received_amt'=>'Cash collected by :store_name',	
                'cash'=>'Paid at Outlet',
            ]
        ],
        'user'=>[
            'list'=>[
                'remarks'=>'Payment to :store_name',
                'details'=>'Paid for order #:order_code'
            ],
            'remarks'=>'Payment of :bill_amount received by PayGyft',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'vis'=>'Paid from Vi-Shop Point',
				'vib'=>'Paid from Vi-Bonus',
				'vim'=>'Paid from Vi-Money',
                //'cash'=>'Cash Collected By :store_name',
                'cash'=>'Paid at Outlet',
            ]
        ]
    ],
    53=>[
        'admin'=>[
            'remarks'=>'Payment of :bill_amount received by PayGyft!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',
                'payment_type'=>'',
                'pg_no'=>'',
                'card'=>'',
            ],
        ],
        'seller'=>[
            'list'=>[
                'remarks'=>'Payment from :customer',
                'details'=>'Paid for order #:order_code'
            ],
            'remarks'=>'Payment of :bill_amount received by PayGyft!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'customer'=>'Customer',
                'user_code'=>'Member ID',
                'mobile'=>'Mobile',
                'email'=>'Email',
            ],
            'payment_details'=>[
				'payment_id'=>'PG Transaction No',
                'bill_amount'=>'Bill Amount',
                'VIS'=>'Paid from Vi-Shop Point',
				'VIB'=>'Paid from Vi-Bonus',
				'VIM'=>'Paid from Vi-Money',
				'system_received_amt'=>'Cash collected by PayGyft',
				'store_received_amt'=>'Cash collected by :store_name',				
                //'netbanking'=>'Netbanking - :payment_type',
                //'credit-card'=>'Credit Card - :payment_type',
                //'debit-card'=>'Debit Card - :payment_type',
				
            ]
        ],
        'user'=>[
            'list'=>[
                'remarks'=>'Payment to :store_name',
                'details'=>'Paid for order #:order_code'
            ],
            'remarks'=>'Payment of :bill_amount received by PayGyft!',
            'fields'=>[
                'order_code'=>'Order Number',
                'amount'=>'Amount',
                'payment_type'=>'Payment Type',
            ],
            'payment_details'=>[
                'bill_amount'=>'Bill Amount',                
				'VIS'=>'Paid from Vi-Shop Point',
				'VIB'=>'Paid from Vi-Bonus',
				'VIM'=>'Paid from Vi-Money',
                //'netbanking'=>'Netbanking - :payment_type',
                //'credit-card'=>'Credit Card - :payment_type',
                //'debit-card'=>'Debit Card - :payment_type',
                'payment_id'=>'PG Transaction No',
            ],
        ]
    ]
];
