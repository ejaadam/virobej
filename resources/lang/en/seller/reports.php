<?php

return[
	'transaction'=>[
        'status'=>[
            1=>'Success',
            0=>'Pending',
            2=>'Cancelled',
            3=>'Refund'
        ]
    ],
	 'orders_list'=>[
        'all'=>[
            'title'=>'All',
        ],        
        'received'=>[
            'title'=>'Received'
        ],
        'paid'=>[
            'title'=>'Paid'
        ]
    ],
	'order_remark'=>[
        1=>[
            //1=>':order_type Cash Received #:order_code',            
			1=>'<strong>Payment from :customer</strong><br> Paid for order #:order_code.',
            2=>'<strong>Payment from :customer</strong><br> Paid for order #:order_code.',                        
            3=>'<strong>Payment from :customer</strong><br> Paid for order #:order_code.',
        ],
        2=>[
            0=>':deal_name Purchased'
        ],
        3=>[
        ]
    ] 
    /* 'reports'=>'Reports',
    'redeem'=>[
        'redeem_list'=>'Redeem List',
        'order_id'=>'Order ID',
        'user_details'=>'User Details',
        'bill_amt'=>'Bill Amount',
        'redeem_amt'=>'Redeem Amount',
        'order_date'=>'Order Date',
        'redeem_not_found'=>'Redeem Not Found',
        'trans_id'=>'Transaction ID',
        'remarks'=>'Remarks',
        'for_order_code'=>'For : Ord ID ',
        'from'=>'From : Wallet',
        'approved_by'=>'App. By',
        'user'=>'User',
        'status'=>'Status',
    ],
    'xpay_accepted'=>[
        'redeem_list'=>'Redeem List',
        'order_id'=>'Order ID',
        'user_details'=>'User Details',
        'bill_amt'=>'Bill Amount',
        'redeem_amt'=>'Redeem Amount',
        'order_date'=>'Order Date',
        'redeem_not_found'=>'Redeem Not Found',
        'trans_id'=>'Transaction ID',
        'remarks'=>'Remarks',
        'for_order_code'=>'For : Ord ID ',
        'from'=>'From : Wallet',
        'approved_by'=>'App. By',
        'user'=>'User',
        'status'=>'Status',
        'payment_type'=>'Payment Type',
    ],
    'customer'=>['customer_reviews'=>'Customer Reviews',
    ],
   
    */
];
