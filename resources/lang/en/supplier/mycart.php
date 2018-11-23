<?php

return [
    'address'=>[
        'validation'=>[
            'address.billing.full_name.required'=>'Please enter your Full Name',
            'address.billing.address1.required'=>'Please enter your address Name',
            'address.billing.address2'=>'Please Enter your Valid Email ID',
            'address.billing.city.required'=>'Please enter your city',
            'address.billing.state_id.required'=>'Please select your state',
            'address.billing.postal_code.required'=>'Please enter your Zip/Postal Code',
            'address.billing.country_id.required'=>'Please select your country',
            'address.billing.mobile_no.required'=>'Please enter your Mobile No.',
            'address.billing.mobile_no.regex'=>'Please enter your valid Mobile No.',
            'address.shipping.full_name.required'=>'Please enter your Full Name',
            'address.shipping.address1.required'=>'Please enter your address Name',
            'address.shipping.address2'=>'',
            'address.shipping.city.required'=>'Please enter your city',
            'address.shipping.state_id.required'=>'Please select your state',
            'address.shipping.postal_code.required'=>'Please enter your Zip/Postal Code',
            'address.shipping.country_id.required'=>'Please select your country',
            'address.shipping.mobile_no.required'=>'Please enter your Mobile No.',
            'address.shipping.mobile_no.regex'=>'Please enter your valid Mobile No.',
        ]
    ],
    'shipping'=>[
        'validation'=>[
        ]
    ],
    'payment'=>[
        'validation'=>[
        ]
    ]
];
