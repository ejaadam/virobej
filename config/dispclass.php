<?php

return[
	'payment_status'=>[0=>'info', 1=>'success', 2=>'warning', 3=>'danger', 4=>'danger'],
	'transaction'=>[
        'status'=>[
            1=>'success',
            0=>'info',
            2=>'warning',
            3=>'dangers'
        ]
    ],
	'order'=>[
        'status'=>[
            1=>[
                1=>[
                    0=>'warning',
                    1=>'success',
                    2=>'danger',
                    3=>'danger',
                    4=>'info',
                    5=>'success',
                    6=>'success',
                    7=>'danger',
                ],
                2=>[
                    0=>'warning',
                    1=>'success',
                    2=>'danger',
                    3=>'danger',
                    4=>'info',
                    5=>'success',
                    6=>'success',
                ],
                3=>[
                    0=>'info',
                    1=>'success',
                    2=>'danger',
                    3=>'warning',
                ]
            ],
            2=>[
                0=>[
                    0=>'info',
                    1=>'success',
                    2=>'warning',
                    3=>'success',
                    4=>'danger'
                ],
                1=>[
                    0=>'info',
                    1=>'success',
                    2=>'warning',
                    3=>'success',
                    4=>'danger'
                ],
                2=>[
                    0=>'info',
                    1=>'success',
                    2=>'warning',
                    3=>'success',
                    4=>'danger'
                ],
            ],
            3=>[
                1=>[
                ],
            ],
            4=>[
                1=>[
                    0=>'info',
                    1=>'success',
                    2=>'danger',
                    3=>'warning',
                    4=>'default',
                    5=>'danger',
                ],
            ]
        ],
        'commission'=>[
            'status'=>[
                0=>'warning',
                1=>'success'
            ]
        ],
        'cashback'=>[
            'status'=>[
                0=>'label label-warning',
                1=>'label label-success',
                2=>'label label-danger',
                3=>'label label-danger'
            ]
        ],
        'redeem'=>[
            'status'=>[
                0=>'label label-warning',
                1=>'label label-success',
                2=>'label label-danger',
                3=>'label label-info',
                4=>'label label-info'
            ]
        ]
    ],
	'seller'=>[
        'profit-sharing'=>[
            'status'=>[
                0=>'warning',
                1=>'success',
                2=>'danger',
                3=>'info'
            ]
        ],
		'outlet'=>[
            'is_approved'=>[
                0=>'warning',
                1=>'success',
                2=>'danger'
            ],
            'status'=>[
                0=>'info',
                1=>'success',
                2=>'danger'
            ],
			'images'=>[
			    'status'=>[
					0=>'danger',
					1=>'success',					
				],
				'is_verified'=>[
					0=>'warning',
					1=>'success',
					2=>'danger'
				],
			]
        ],
		'order'=>[
			'status'=>[
				0=>'warning',
				1=>'success',
				2=>'danger',
				3=>'info',
				4=>'info',
			],
			'commission'=>[
				'status'=>[
					0=>'warning',
					1=>'success'
				]
			],
			'cashback'=>[
				'status'=>[
					0=>'label label-warning',
					1=>'label label-success',
					2=>'label label-danger',
					3=>'label label-danger'
				]
			],
			'redeem'=>[
				'status'=>[
					0=>'label label-warning',
					1=>'label label-success',
					2=>'label label-danger',
					3=>'label label-info',
					4=>'label label-info'
				]
			]
		],
	],
	"affiliate"=>[ 
	   'Active'=>'success',
	   'Inactive'=>'danger',
	    0=>'danger',
	    1=>'success',
    ],
	'user'=>[
		'order'=>[
			'status'=>[
				0=>'warning',
				1=>'success',
				2=>'danger',
				3=>'info',
				4=>'info',
			],
			'commission'=>[
				'status'=>[
					0=>'warning',
					1=>'success'
				]
			],
			'cashback'=>[
				'status'=>[
					0=>'label label-warning',
					1=>'label label-success',
					2=>'label label-danger',
					3=>'label label-danger'
				]
			],
			'redeem'=>[
				'status'=>[
					0=>'label label-warning',
					1=>'label label-success',
					2=>'label label-danger',
					3=>'label label-info',
					4=>'label label-info'
				]
			]
		],
	],
	 'affiliates'=>[
        'featured'=>[
            1=>'success',
            0=>'danger'
        ],
        'status'=>[
            1=>'success',
            0=>'danger'
        ],
        'network'=>[
            'status'=>[
                1=>'success',
                0=>'danger'
            ]
        ]
    ],
	'fund_trasnfer'=>[
        'status'=>[
            1=>'success',
            0=>'info',
            2=>'warning',
            3=>'dangers'
        ]
    ],
	'withdrawal_status'=>[0=>'info', 1=>'success', 2=>'warning', 3=>'danger'],
	'category_status'=>[0=>'danger', 1=>'success', 2=>'danger'],
];

