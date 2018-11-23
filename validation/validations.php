<?php

$postdata = Input::all();

function getRegex ($key)
{
    $regular_expressions = [
        'firstname'=>'/^[A-Za-z]{3,100}$/',
        'lastname'=>'/^[A-Za-z]{1,50}$/',
        'us_swift_code'=>'/^[A-Za-z0-9]{8,11}+$/',
        'ifsc'=>'/^[A-Za-z]{4}[0][A-Za-z0-9]{6}$/',
        'pan'=>'/^[A-Za-z]{5}[0-9]{4}[a-zA-Z]{1}$/',
        'sgd_swift_code'=>'/^[A-Za-z0-9]{8,11}+$/',
        'gstin'=>'/^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[0-9]{1}Z[0-9]{1}$/',
        'eanbarcode'=>'/^(8[0-9]{11})$|^(8[0-9]{13})$/',
        'upcbarcode'=>'/^[0-9]{10,12}$/',
        'page'=>'/^[A-Za-z-]*$/'
    ];
    if (!array_key_exists($key, $regular_expressions))
    {
        $regular_expressions[$key] = getRegexfromDB($key);
    }
    return $regular_expressions[$key];
}

function getRegexfromDB ($key, $data = array())
{
    extract($data);
    $postdata = Input::all();
    $country_id = isset($postdata['country_id']) ? $postdata['country_id'] : 77;
    switch ($key)
    {
        case 'mobile':
            return DB::table(Config::get('tables.LOCATION_COUNTRY'))
                            ->where('country_id', $country_id)
                            ->select('mobile_validation')
                            ->pluck('mobile_validation');
        case 'telephone':
            return DB::table(Config::get('tables.LOCATION_COUNTRY'))
                            ->where('country_id', $country_id)
                            ->select('telephone_validation', 'telephone_max_length')
                            ->pluck('telephone_validation');
    }
}

function getDiscountRules ()
{
    $v = [];
    $v['RULES'] = [
        'discount.discount'=>'required',
        'discount.discount_type_id'=>'required',
        'discount.description'=>'required',
        'discount.country_id'=>'required',
        'discount.start_date'=>'required|date',
        'discount.end_date'=>'required|date'
    ];
    $v['MESSAGES'] = [
        'discount.discount.required'=>'Please enter Discounts',
        'discount.discount_type_id.required'=>'Please select Discount Type',
        'discount.description.required'=>'Please enter Description',
        'discount.country_id.required'=>'Please select Country',
        'discount.start_date.required'=>'Please select Start date',
        'discount.start_date.date'=>'Please enter valid Start date',
        'discount.end_date.required'=>'Please select End date',
        'discount.end_date.date'=>'lease enter valid End date',
    ];
    $post = Input::all();
    if (!empty($post) && isset($post['discount_post']))
    {
        if (isset($post['is_country_based']))
        {
            $v['RULES']['discount.country_id'] = 'required';
        }
        $count = count($post['discount_post']);
        $count = $count > 0 ? $count : 1;
        for ($i = 0; $i < $count; $i++)
        {
            $v['RULES']['discount_post.'.$i.'.brand_ids'] = 'array';
            $v['MESSAGES']['discount_post.'.$i.'.brand_ids.array'] = 'Please Select Brands';
            $v['RULES']['discount_post.'.$i.'.category_ids'] = 'array';
            $v['MESSAGES']['discount_post.'.$i.'.category_ids.array'] = 'Please Select Categories';
            $v['RULES']['discount_post.'.$i.'.supplier_ids'] = 'array';
            $v['MESSAGES']['discount_post.'.$i.'.supplier_ids.array'] = 'Please Select Suppliers';
            $v['RULES']['discount_post.'.$i.'.product_ids'] = 'array';
            $v['MESSAGES']['discount_post.'.$i.'.product_ids.array'] = 'Please Select Products';
            $v['RULES']['discount_post.'.$i.'.product_cmb_ids'] = 'array';
            $v['MESSAGES']['discount_post.'.$i.'.product_cmb_ids.array'] = 'Please Select Product Combinations';
            $v['RULES']['discount_post.'.$i.'.discount_value_type'] = 'required';
            $v['RULES']['discount_post.'.$i.'.is_qty_based'] = '';
            $v['MESSAGES']['discount_post.'.$i.'.discount_value_type.required'] = 'Please Select Disount value Type';
            $v['MESSAGES']['discount_post.'.$i.'.is_qty_based'] = '';
            $vcount = count($post['discount_post'][$i]['value']);
            $vcount = $vcount > 0 ? $vcount : 1;
            for ($j = $i; $j < $vcount; $j++)
            {
                $v['RULES']['discount_post.'.$i.'.value.'.$j.'.discount_value'] = 'required';
                $v['MESSAGES']['discount_post.'.$i.'.value.'.$j.'.discount_value.required'] = 'Please enter your discount value';
                if (isset($post['discount_post'][$i]['is_qty_based']))
                {
                    $v['RULES']['discount_post.'.$i.'.value.'.$j.'.min_qty'] = 'required|numeric';
                    $v['RULES']['discount_post.'.$i.'.value.'.$j.'.max_qty'] = 'required|numeric';
                    $v['MESSAGES']['discount_post.'.$i.'.value.'.$j.'.min_qty.required'] = 'Please min qty must be a number';
                    $v['MESSAGES']['discount_post.'.$i.'.value.'.$j.'.max_qty.required'] = 'Please max qty must be a number';
                }
                if (isset($post['discount_post'][$i]['discount_value_type']) && $post['discount_post'][$i]['discount_value_type'] == 1)
                {
                    $v['RULES']['discount_post.'.$i.'.value.'.$j.'.currency_id'] = 'required';
                    $v['MESSAGES']['discount_post.'.$i.'.value.'.$j.'.currency_id.required'] = 'Please select discount currency';
                }
            }
        }
    }
    return $v;
}

$withdrawal_filed_rules = [
    'account_details.bitcoin_address'=>'required',
    'account_details.kokard_account_no'=>'required|digits|min:18',
    'account_details.paypal_emailid'=>'required|email',
    'account_details.stp_username'=>'required',
    'account_details.full_name'=>'required',
    'account_details.phonecode'=>'required',
    'account_details.mobile'=>'required|regex:'.getRegex('mobile'),
    'account_details.us_nickname'=>'required',
    'account_details.us_swift_code'=>'required|regex:'.getRegex('us_swift_code'),
    'account_details.us_routing_no'=>'required',
    'account_details.us_state'=>'required',
    'account_details.us_acc_no'=>'required|min:4',
    'account_details.us_bank_country'=>'required',
    'account_details.us_account_type'=>'required|in:"CHECKING","SAVINGS"',
    'account_details.b_nickname'=>'required',
    'account_details.ifsc'=>'required|regex:'.getRegex('ifsc'),
    'account_details.b_branch'=>'required',
    'account_details.b_accno'=>'required|min:4|max:17',
    'account_details.b_acc_type'=>'required|in("SAVINGS")',
    'account_details.b_country'=>'required',
    'account_details.sgd_bank_name'=>'required',
    'account_details.sgd_swift_code'=>'required|regex:'.getRegex('sgd_swift_code'),
    'account_details.sgd_bank_code'=>'required',
    'account_details.sgd_branch_code'=>'required',
    'account_details.sgd_acc_number'=>'required|min:4',
    'account_details.sgd_acc_type'=>'required|in("SAVINGS")',
    'account_details.sgd_country'=>'required',
    'account_details.acc_holder_name'=>'required',
    'account_details.fst_addr'=>'required',
    'account_details.sec_addr'=>'',
    'account_details.acc_country'=>'required',
    'account_details.b_panid'=>'required|regex:'.getRegex('pan')
];
$withdrawal_fileds_by_payout_type = [
    'express-withdrawal'=>[
        'USD'=>[
            'account_details.us_nickname',
            'account_details.us_swift_code',
            'account_details.us_routing_no',
            'account_details.us_state',
            'account_details.us_acc_no',
            'account_details.us_bank_country',
            'account_details.us_account_type',
            'account_details.acc_holder_name',
            'account_details.fst_addr',
            'account_details.sec_addr',
            'account_details.acc_country'],
        'INR'=>[
            'account_details.b_nickname',
            'account_details.ifsc',
            'account_details.b_branch',
            'account_details.b_accno',
            'account_details.b_acc_type',
            'account_details.b_country',
            'account_details.b_panid',
            'account_details.acc_holder_name',
            'account_details.fst_addr',
            'account_details.sec_addr',
            'account_details.acc_country'],
        'SGD'=>[
            'account_details.sgd_bank_name',
            'account_details.sgd_swift_code',
            'account_details.sgd_bank_code',
            'account_details.sgd_acc_number',
            'account_details.sgd_acc_type',
            'account_details.sgd_country',
            'account_details.acc_holder_name',
            'account_details.fst_addr',
            'account_details.sec_addr',
            'account_details.acc_country'],
        'MYR'=>[
            'account_details.sgd_bank_name',
            'account_details.sgd_swift_code',
            'account_details.sgd_bank_code',
            'account_details.sgd_acc_number',
            'account_details.sgd_acc_type',
            'account_details.sgd_country',
            'account_details.acc_holder_name',
            'account_details.fst_addr',
            'account_details.sec_addr',
            'account_details.acc_country']
    ],
    'paypal'=>[
        'account_details.paypal_emailid'
    ],
    'solid-trust-pay'=>[
        'account_details.stp_username'
    ],
    'wire-transfer'=>[
        'USD'=>[
            'account_details.us_nickname',
            'account_details.us_swift_code',
            'account_details.us_routing_no',
            'account_details.us_state',
            'account_details.us_acc_no',
            'account_details.us_bank_country',
            'account_details.us_account_type',
            'account_details.acc_holder_name',
            'account_details.fst_addr',
            'account_details.sec_addr',
            'account_details.acc_country'],
        'INR'=>[
            'account_details.b_nickname',
            'account_details.ifsc',
            'account_details.b_branch',
            'account_details.b_accno',
            'account_details.b_acc_type',
            'account_details.b_country',
            'account_details.b_panid',
            'account_details.acc_holder_name',
            'account_details.fst_addr',
            'account_details.sec_addr',
            'account_details.acc_country'],
        'SGD'=>[
            'account_details.sgd_bank_name',
            'account_details.sgd_swift_code',
            'account_details.sgd_bank_code',
            'account_details.sgd_acc_number',
            'account_details.sgd_acc_type',
            'account_details.sgd_country',
            'account_details.acc_holder_name',
            'account_details.fst_addr',
            'account_details.sec_addr',
            'account_details.acc_country'],
        'MYR'=>[
            'account_details.sgd_bank_name',
            'account_details.sgd_swift_code',
            'account_details.sgd_bank_code',
            'account_details.sgd_acc_number',
            'account_details.sgd_acc_type',
            'account_details.sgd_country',
            'account_details.acc_holder_name',
            'account_details.fst_addr',
            'account_details.sec_addr',
            'account_details.acc_country']
    ],
    'os-debit-card'=>[
        'account_details.os_uname'
    ],
    'ko-kard'=>[
        'account_details.kokard_account_no'
    ],
    'bitcoin'=>[
        'account_details.bitcoin_address'
    ],
    'local-money-transfer'=>[
        'account_details.full_name',
        'account_details.fst_addr',
        'account_details.sec_addr',
        'account_details.acc_country',
        'account_details.phonecode',
        'account_details.mobile'
    ]
];

return [
    'api'=>[
        'v1'=>[
            'get-page-data'=>[
                'RULES'=>[
                    'page'=>'regex:'.getRegex('page')
                ]
            ],
            'supplier'=>[
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
                        'username'=>Lang::get('general.fields.username'),
                        'password'=>Lang::get('general.fields.password')
                    ],
                    'RULES'=>['username'=>'required|min:3|max:100', 'password'=>'required|min:6|max:10']
                ],
                'check-account'=>[
                    'LABELS'=>[
                        'username'=>Lang::get('general.fields.username')
                    ],
                    'RULES'=>[
                        'username'=>'required',
                    ]
                ],
                'check-verification-code'=>[
                    'LABELS'=>[
                        'verification_code'=>Lang::get('general.fields.verification_code')
                    ],
                    'RULES'=>[
                        'verification_code'=>'required|digits:6'
                    ]
                ],
                'forgot-password'=>[
                    'ATTRIBUTES'=>[
                        'password'=>[
                            'type'=>'password'
                        ]
                    ],
                    'LABELS'=>[
                        'username'=>Lang::get('general.fields.username'),
                        'verification_code'=>Lang::get('general.fields.verification_code'),
                        'password'=>Lang::get('general.fields.password'),
                    ],
                    'RULES'=>[
                        'username'=>'required',
                        'verification_code'=>'required|digits:6',
                        'password'=>'required|min:6|max:10'
                    ]
                ],
                'sign-up'=>[
                    'ATTRIBUTES'=>[
                        'login_mst.pass_key'=>['type'=>'password'],
                        'agree'=>['type'=>'checkbox']
                    ],
                    'LABELS'=>[
                        'account_mst.firstname'=>Lang::get('general.fields.firstname'),
                        'account_mst.lastname'=>Lang::get('general.fields.lastname'),
                        'login_mst.email'=>Lang::get('general.fields.email'),
                        'login_mst.mobile'=>Lang::get('general.fields.mobile'),
                        'login_mst.pass_key'=>Lang::get('general.fields.password'),
                        'agree'=>Lang::get('general.fields.supplier_agree')
                    ],
                    'RULES'=>[
                        'account_mst.firstname'=>'required|regex:'.getRegex('firstname'),
                        'account_mst.lastname'=>'required|regex:'.getRegex('lastname'),
                        'login_mst.email'=>'required|email|unique:account_login_mst,email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SUPPLIER'),
                        'login_mst.mobile'=>'required|regex:'.getRegex('mobile').'|unique:account_login_mst,mobile,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SUPPLIER'),
                        'login_mst.pass_key'=>'required|min:6|max:10',
                        'agree'=>'required'
                    ]
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
                                'options'=>Commonsettings::getWorkingDays(),
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
                            'store_extras.website'=>'required|url',
                            'store_extras.mobile_no'=>'required|regex:'.getRegex('mobile'),
                            'store_extras.landline_no'=>'required',
                            'store_extras.address1'=>'required',
                            'store_extras.address2'=>'required',
                            'store_extras.country_id'=>'required',
                            'store_extras.state_id'=>'required',
                            'store_extras.city_id'=>'required',
                            'store_extras.postal_code'=>'required',
                            'store_extras.firstname'=>'required|regex:'.getRegex('firstname'),
                            'store_extras.lastname'=>'required|regex:'.getRegex('lastname'),
                        ]
                    ]
                ],
                'setup'=>[
                    'account-details'=>[
                        'LABELS'=>[
                            'account_supplier.reg_company_name'=>Lang::get('general.fields.reg_company_name'),
                            'account_supplier.company_name'=>Lang::get('general.fields.company_name'),
                            'account_supplier.website'=>Lang::get('general.fields.company_url'),
                            'account_supplier.type_of_bussiness'=>Lang::get('general.fields.type_of_bussiness'),
                            'address.country_id'=>Lang::get('general.fields.country'),
                            'address.state_id'=>Lang::get('general.fields.state'),
                            'address.postal_code'=>Lang::get('general.fields.postal_code'),
                            'address.city_id'=>Lang::get('general.fields.city'),
                            'address.street1'=>Lang::get('general.fields.street1'),
                            'address.street2'=>Lang::get('general.fields.street2'),
                        ],
                        'RULES'=>[
                            'account_supplier.reg_company_name'=>'required|min:3|max:100',
                            'account_supplier.company_name'=>'required|min:3|max:100',
                            'account_supplier.website'=>'required|url',
                            'account_supplier.type_of_bussiness'=>'required',
                            'address.country_id'=>'required',
                            'address.state_id'=>'required',
                            'address.postal_code'=>'required',
                            'address.city_id'=>'required',
                            'address.street1'=>'required',
                            'address.street2'=>'required',
                        ]
                    ],
                    'update-account'=>[
                        'ATTRIBUTES'=>[
                            'store_extras.working_hours_from'=>['type'=>'time'],
                            'store_extras.working_hours_to'=>['type'=>'time'],
                            'store_extras.working_days'=>[
                                'options'=>Commonsettings::getWorkingDays(),
                                'type'=>'checkbox'
                            ]
                        ],
                        'LABELS'=>[
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
                            'store_extras.working_days'=>Lang::get('general.fields.working_days'),
                        ],
                        'RULES'=>[
                            'store_extras.email'=>'required|email|unique:store_extras,email'.(isset($postdata['store_id']) && !empty($postdata['store_id']) ? ','.$postdata['store_id'].',store_id' : ',NULL,store_id'),
                            'store_extras.mobile_no'=>'required|regex:'.getRegex('mobile'),
                            'store_extras.landline_no'=>'required',
                            'store_extras.firstname'=>'required|regex:'.getRegex('firstname'),
                            'store_extras.lastname'=>'required|regex:'.getRegex('lastname'),
                            'store_extras.state_id'=>'required',
                            'store_extras.postal_code'=>'required',
                            'store_extras.city_id'=>'required',
                            'store_extras.address1'=>'required',
                            'store_extras.address2'=>'required',
                            'store_extras.working_hours_from'=>'required',
                            'store_extras.working_hours_to'=>'required',
                            'store_extras.website'=>'required|url'
                        ]
                    ],
                    'update-kyc'=>[
                        'ATTRIBUTES'=>[
                            'kyc_verifiacation.dob'=>[
                                'type'=>'date'
                            ],
                            'auth_person_id_proof'=>[
                                'type'=>'file',
                                'accept'=>'image/*'
                            ],
                            'pan_card_image'=>[
                                'type'=>'file',
                                'accept'=>'image/*'
                            ]
                        ],
                        'LABELS'=>[
                            'kyc_verifiacation.pan_card_no'=>Lang::get('general.fields.pan'),
                            'kyc_verifiacation.gstin'=>Lang::get('general.fields.gstin'),
                            'kyc_verifiacation.pan_card_name'=>Lang::get('general.fields.pan_card_name'),
                            'kyc_verifiacation.dob'=>Lang::get('general.fields.dob_on_pan'),
                            'kyc_verifiacation.pan_card_image'=>Lang::get('general.fields.pan_card_image'),
                            'kyc_verifiacation.vat_no'=>Lang::get('general.fields.vat_no'),
                            'kyc_verifiacation.cst_no'=>Lang::get('general.fields.cst_no'),
                            'kyc_verifiacation.auth_person_name'=>Lang::get('general.fields.auth_person_name'),
                            'kyc_verifiacation.auth_person_id_proof'=>Lang::get('general.fields.auth_person_id_proof'),
                            'auth_person_id_proof'=>Lang::get('general.fields.auth_person_id_proof'),
                            'pan_card_image'=>Lang::get('general.fields.pan_card_image'),
                            'kyc_verifiacation.id_proof_document_type_id'=>Lang::get('general.fields.id_proof_type')
                        ],
                        'RULES'=>[
                            'kyc_verifiacation.pan_card_no'=>'required|regex:'.getRegex('pan').'|unique:supplier_kyc_verification,pan_card_no'.(isset($postdata['supplier_id']) && !empty($postdata['supplier_id']) ? ','.$postdata['supplier_id'].',supplier_id' : ',NULL,supplier_id'),
                            'kyc_verifiacation.gstin'=>'required|regex:'.getRegex('gstin').'|unique:supplier_kyc_verification,gstin'.(isset($postdata['supplier_id']) && !empty($postdata['supplier_id']) ? ','.$postdata['supplier_id'].',supplier_id' : ',NULL,supplier_id'),
                            'kyc_verifiacation.pan_card_name'=>'required|min:3',
                            'kyc_verifiacation.dob'=>'required|date_format:Y-m-d|detween:'.date('Y-m-d', strtotime('-110 years')).','.date('Y-m-d', strtotime('-18 years')),
                            'pan_card_image'=>'required|mimes:jpeg,gif,bmp,png',
                            'kyc_verifiacation.vat_no'=>'required',
                            'kyc_verifiacation.cst_no'=>'required',
                            'kyc_verifiacation.auth_person_name'=>'required',
                            'kyc_verifiacation.id_proof_document_type_id'=>'required',
                            'auth_person_id_proof'=>'required|mimes:jpeg,gif,bmp,png',
                        ]
                    ],
                    'store-banking'=>[
                        'LABELS'=>[
                            'payment_setings.bank_name'=>Lang::get('general.fields.bank_name'),
                            'payment_setings.account_holder_name'=>Lang::get('general.fields.account_holder_name'),
                            'payment_setings.account_no'=>Lang::get('general.fields.account_no'),
                            'payment_setings.account_type'=>Lang::get('general.fields.account_type'),
                            'payment_setings.ifsc_code'=>Lang::get('general.fields.ifsc_code'),
                            'payment_setings.country_id'=>Lang::get('general.fields.country'),
                            'payment_setings.state_id'=>Lang::get('general.fields.state'),
                            'payment_setings.postal_code'=>Lang::get('general.fields.postal_code'),
                            'payment_setings.city_id'=>Lang::get('general.fields.city'),
                            'payment_setings.address1'=>Lang::get('general.fields.street1'),
                            'payment_setings.address2'=>Lang::get('general.fields.street2'),
                            'payment_setings.branch'=>Lang::get('general.fields.branch'),
                            'payment_setings.pan'=>Lang::get('general.fields.pan'),
                        ],
                        'RULES'=>[
                            'payment_setings.bank_name'=>'required|min:3',
                            'payment_setings.account_holder_name'=>'required|min:3',
                            'payment_setings.account_no'=>'required|min:4|max:17',
                            'payment_setings.account_type'=>'required',
                            'payment_setings.ifsc_code'=>'required|regex:'.getRegex('ifsc'),
                            'payment_setings.country_id'=>'required',
                            'payment_setings.state_id'=>'required',
                            'payment_setings.postal_code'=>'required',
                            'payment_setings.city_id'=>'required',
                            'payment_setings.address1'=>'required',
                            'payment_setings.address2'=>'required',
                            'payment_setings.branch'=>'required',
                            'payment_setings.pan'=>'required|regex:'.getRegex('pan'),
                        ]
                    ]
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
                    ]
                ]
            ],
            'partner'=>[
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
                        'username'=>Lang::get('general.fields.username'),
                        'password'=>Lang::get('general.fields.password')
                    ],
                    'RULES'=>['username'=>'required|min:3|max:100', 'password'=>'required|min:6|max:10'],
                    'MESSAGES'=>Lang::get('general.validation.login')
                ],
                'check-account'=>[
                    'LABELS'=>[
                        'username'=>Lang::get('general.fields.username')
                    ],
                    'RULES'=>[
                        'username'=>'required',
                    ]
                ],
                'check-verification-code'=>[
                    'LABELS'=>[
                        'verification_code'=>Lang::get('general.fields.verification_code')
                    ],
                    'RULES'=>[
                        'verification_code'=>'required|digits:6'
                    ]
                ],
                'forgot-password'=>[
                    'ATTRIBUTES'=>[
                        'password'=>[
                            'type'=>'password'
                        ]
                    ],
                    'LABELS'=>[
                        'username'=>Lang::get('general.fields.username'),
                        'verification_code'=>Lang::get('general.fields.verification_code'),
                        'password'=>Lang::get('general.fields.password'),
                    ],
                    'RULES'=>[
                        'username'=>'required',
                        'verification_code'=>'required|digits:6',
                        'password'=>'required|min:6|max:10'
                    ]
                ]
            ],
            'customer'=>[
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
                        'username'=>Lang::get('general.fields.username'),
                        'password'=>Lang::get('general.fields.password')
                    ],
                    'RULES'=>['username'=>'required|min:3|max:100', 'password'=>'required|min:6|max:10'],
                    'MESSAGES'=>Lang::get('general.validation.login')
                ],
                'check-account'=>[
                    'LABELS'=>[
                        'username'=>Lang::get('general.fields.username')
                    ],
                    'RULES'=>[
                        'username'=>'required',
                    ]
                ],
                'check-verification-code'=>[
                    'LABELS'=>[
                        'verification_code'=>Lang::get('general.fields.verification_code')
                    ],
                    'RULES'=>[
                        'verification_code'=>'required|digits:6'
                    ]
                ],
                'forgot-password'=>[
                    'ATTRIBUTES'=>[
                        'password'=>[
                            'type'=>'password'
                        ]
                    ],
                    'LABELS'=>[
                        'username'=>Lang::get('general.fields.username'),
                        'verification_code'=>Lang::get('general.fields.verification_code'),
                        'password'=>Lang::get('general.fields.password'),
                    ],
                    'RULES'=>[
                        'username'=>'required',
                        'verification_code'=>'required|digits:6',
                        'password'=>'required|min:6|max:10'
                    ]
                ],
                'get-sliders'=>[
                    'RULES'=>[
                        'page'=>'required|regex:'.getRegex('page')
                    ]
                ],
                'change-password'=>[
                    'RULES'=>[
                        'current_password'=>'required|min:6|max:10',
                        'new_password'=>'required|min:6|max:10|confirmed',
                        'new_password_confirmation'=>'required'
                    ]
                ],
                'check-verification-mobile'=>[
                    'RULES'=>[
                        'verification_code'=>'required|digits:6'
                    ],
                ],
                'subscribe'=>[
                    'RULES'=>[
                        'subscribe.email_id'=>'required|email|unique:newsletter_subscribers,email_id,is_deleted,0'
                    ],
                ],
                'sign-up'=>[
                    'RULES'=>[
                        'mobile'=>'required|regex:'.getRegex('mobile').'|unique:account_login_mst,mobile,NULL,account_id,account_type_id,2',
                        'verification_code'=>'required|digits:6',
                        'password'=>'required|confirmed',
                        'password_confirmation'=>'required'
                    ],
                ],
                'sign-up-mobile-check'=>[
                    'RULES'=>[
                        'mobile'=>'required|regex:'.getRegex('mobile').'|unique:account_login_mst,mobile,NULL,account_id,account_type_id,2',
                    ],
                ]
            ],
        ]
    ],
    'admin'=>[
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
                'username'=>Lang::get('general.fields.username'),
                'password'=>Lang::get('general.fields.password')
            ],
            'RULES'=>['username'=>'required|min:3|max:100', 'password'=>'required|min:6|max:10']
        ],
        'check-account'=>[
            'LABELS'=>[
                'username'=>Lang::get('general.fields.username')
            ],
            'RULES'=>[
                'username'=>'required',
            ]
        ],
        'check-verification-code'=>[
            'LABELS'=>[
                'verification_code'=>Lang::get('general.fields.verification_code')
            ],
            'RULES'=>[
                'verification_code'=>'required|digits:6'
            ]
        ],
        'forgot-password'=>[
            'ATTRIBUTES'=>[
                'password'=>[
                    'type'=>'password'
                ]
            ],
            'LABELS'=>[
                'username'=>Lang::get('general.fields.username'),
                'verification_code'=>Lang::get('general.fields.verification_code'),
                'password'=>Lang::get('general.fields.password'),
            ],
            'RULES'=>[
                'username'=>'required',
                'verification_code'=>'required|digits:6',
                'password'=>'required|min:6|max:10'
            ]
        ],
        'updated-password'=>[
            'RULES'=>[
                'current_password'=>'required|min:6|max:10',
                'new_password'=>'required|min:6|max:10|confirmed',
                'new_password_confirmation'=>'required'
            ],
        ],
        'supplier'=>[
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
                            'options'=>Commonsettings::getWorkingDays(),
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
                        'store_extras.working_days'=>Lang::get('general.fields.working_days')
                    ],
                    'RULES'=>[
                        'create.store_name'=>'required',
                        'create.store_logo'=>'required',
                        'create.status'=>'required',
                        'store_extras.email'=>'required|email',
                        'store_extras.website'=>'required|url',
                        'store_extras.mobile_no'=>'required|regex:'.getRegex('mobile'),
                        'store_extras.landline_no'=>'required',
                        'store_extras.address1'=>'required',
                        'store_extras.address2'=>'required',
                        'store_extras.country_id'=>'required',
                        'store_extras.state_id'=>'required',
                        'store_extras.city_id'=>'required',
                        'store_extras.postal_code'=>'required',
                        'store_extras.firstname'=>'required|regex:'.getRegex('firstname'),
                        'store_extras.lastname'=>'required|regex:'.getRegex('lastname'),
                    ],
                ]
            ]
        ]
    ],
    'VERIFY_MOBILE'=>[
        'RULES'=>[
            'verification_code'=>'required|digits:6'
        ],
        'MESSAGES'=>Lang::get('customer.verify_mail_id_validation')
    ],
    'VERIFY_EMAIL_ID'=>[
        'RULES'=>[
            'verification_code'=>'required|digits:6',
        ],
        'MESSAGES'=>Lang::get('customer.verify_mail_id_validation')
    ],
    'EMAIL_SETTINGS'=>array(
        'RULES'=>array_merge([
            'sender_name'=>'required',
            'email'=>'required|email',
            'driver_type'=>'required'
                ], isset($postdata['driver_type']) ?
                        (($postdata['driver_type'] == 1) ? [
                            'settings.host'=>'required',
                            'settings.port'=>'required|numeric',
                            'settings.username'=>'required',
                            'settings.password'=>'required',
                            'settings.encryption'=>'required'
                                ] : [
                            'settings.api_user'=>'required',
                            'settings.api_key'=>'required'
                                ]
                        ) : []),
        'MESSAGES'=>Lang::get('email_settings.validation')
    ),
    'ADD_PAYMENT'=>array(
        'RULES'=>[
            'payment_list_id'=>'required',
            'payment_gateway_select'=>'required'
        ],
        'MESSAGES'=>Lang::get('add_payment.validation')
    ),
    'UPDATE_PERSONAL_INFO'=>array(
        'RULES'=>[
            'user.firstname'=>'required|regex:'.getRegex('firstname'),
            'user.lastname'=>'required|regex:'.getRegex('lastname'),
            'user.gender'=>'required',
            'user.dob'=>'required'
        ],
        'MESSAGES'=>Lang::get('update_personal_info.validation')
    ),
    'UPDATE_ADDRESS'=>array(
        'RULES'=>[
            'street1'=>'required',
            'city_id'=>'required',
            'state'=>'required',
            'country'=>'required',
            'pin_code'=>'required',
        ],
        'MESSAGES'=>Lang::get('update_address.validation')
    ),
    'CREATE_CATEGORY'=>array(
        'RULES'=>[
//'create.supplier_id'=>'required',
            'category.category'=>'required',
            'category.parent_category_id'=>'required'
        ],
        'MESSAGES'=>Lang::get('category_list_management.validation')
    ),
    'CREATE_ZONE'=>array(
        'RULES'=>[
//'create.supplier_id'=>'required',
            'geo_zone_id'=>'required',
            'supplier_product_zone.mode_id'=>'required',
            'supplier_product_zone.delivery_days'=>'required',
            'supplier_product_zone.delivery_charge'=>'required'
        ],
        'MESSAGES'=>Lang::get('create_zone_shipment.validation')
    ),
    'CREATE_BRAND'=>array(
        'RULES'=>[
            'brand_name'=>'required|min:2|max:100'
        ],
        'MESSAGES'=>['brand_name.required'=>'Brand Name Required', 'brand_name.min'=>'Brand Name must be atleast :min characters', 'brand_name.max'=>'Brand Name must be lesser that :max characters']
    ),
    'CREATE_PRODUCTS'=>array(
        'FIELDS'=>[
            'product.product_name'=>[
                'label'=>Lang::get('product_browse.product_name')
            ],
            'details.sku'=>[
                'label'=>Lang::get('product_browse.sku')
            ],
            'details.eanbarcode'=>[
                'label'=>Lang::get('product_browse.eanbarcode')
            ],
            'details.upcbarcode'=>[
                'label'=>Lang::get('product_browse.upcbarcode')
            ],
            'details.description'=>[
                'label'=>Lang::get('product_browse.description')
            ],
            'details.is_exclusive'=>[
                'label'=>Lang::get('product_browse.is_exclusive')
            ],
            'details.visiblity_id'=>[
                'label'=>Lang::get('product_browse.visiblity_id')
            ],
            'details.weight'=>[
                'label'=>Lang::get('product_browse.weight'),
                'attr'=>['step'=>0.1],
            ],
            'details.height'=>[
                'label'=>Lang::get('product_browse.height'),
                'attr'=>['step'=>0.1],
            ],
            'details.length'=>[
                'label'=>Lang::get('product_browse.length'),
                'attr'=>['step'=>0.1],
            ],
            'details.width'=>[
                'label'=>Lang::get('product_browse.width'),
                'attr'=>['step'=>0.1],
            ],
            'tags'=>[
                'label'=>Lang::get('product_browse.tags')
            ],
            'meta_info.description'=>[
                'label'=>Lang::get('product_browse.meta_description')
            ],
            'meta_info.meta_keys'=>[
                'label'=>Lang::get('product_browse.meta_keys')
            ],
            'product.category_id'=>[
                'label'=>Lang::get('product_browse.category')
            ],
            'product.brand_id'=>[
                'label'=>Lang::get('product_browse.brand')
            ],
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
        ],
        'MESSAGES'=>[
            'product.product_name.required'=>'Please Enter Product Name',
            'details.eanbarcode.required'=>'Please Enter EAN Barcode',
            'details.upcbarcode.required'=>'Please Enter UPC Barcode',
            'details.sku.required'=>'Please Enter SKU',
            'details.description.required'=>'Please Enter Description',
            'details.is_exclusive.required'=>'Please Select Exclusive or Not',
            'details.visiblity_id.required'=>'Please Select Visibility',
            'details.weight.required'=>'Please Enter Weight',
            'details.weight.numeric'=>'Please Enter valid Weight',
            'details.weight.min'=>'Weight should be minimum of :min',
            'details.weight.max'=>'Weight should not be more then :max',
            'details.height.required'=>'Please Enter Height',
            'details.height.numeric'=>'Please Enter valid Height',
            'details.height.min'=>'Height should be minimum of :min',
            'details.height.max'=>'Height should not be more then :max',
            'details.length.required'=>'Please Enter Length',
            'details.length.numeric'=>'Please Enter valid Length',
            'details.length.min'=>'Length should be minimum of :min',
            'details.length.max'=>'Length should not be more then :max',
            'details.width.required'=>'Please Enter Width',
            'details.width.numeric'=>'Please Enter valid Width',
            'details.width.min'=>'Width should be minimum of :min',
            'details.width.max'=>'Width should not be more then :max',
            'tags.required'=>'Please Select Tags',
            'meta_info.description.required'=>'Please Enter Meta Description',
            'meta_info.meta_keys.required'=>'Please Enter Meta Keys',
            'product.category_id.required'=>'Please Select Category',
            'product.brand_id.required'=>'Please Select Brand',
        ]
    ),
    'SUPPLIER_COUNTRY'=>array(
        'RULES'=>[
            'product.country_id'=>'required',
        ],
        'MESSAGES'=>[
            'product.country_id.required'=>'Please Select Country',
        ]
    ),
    'CREATE_PRODUCT'=>array(
        'RULES'=>[
        //'product.category_id'=>'required',
        //'product.brand_id'=>'required',
        //'product.product_name'=>'required',
        //'product.sku'=>'required',
        //'product.description'=>'required',
        ],
        'MESSAGES'=>Lang::get('product_items.validation')
    ),
    'SUPPLIER_PRODUCT_PRICE'=>[
        'RULES'=>[
            //'spp.currency_id'=>'required',
            'spp.mrp_price'=>'required',
            'spp.price'=>'required',
        ],
        'MESSAGES'=>Lang::get('product_items.validation')
    ],
    'PRODUCT_MRP_PRICE'=>[
        'RULES'=>[
            'spp.mrp_price'=>'required'
        ],
        'MESSAGES'=>Lang::get('product_items.validation')
    ],
    'EXISTING_PRODUCT'=>array(
        'RULES'=>[
            'supplier_product.product_id'=>'required',
            'supplier_product.product_cmb_id'=>'sometimes|required',
            // 'supplier_product.currency_id'=>'required',
            //'supplier_product.mrp_price'=>'required',
            //'supplier_product.price'=>'required',
            'supplier_product.pre_order'=>'required',
            'supplier_product.condition_id'=>'required',
            'supplier_product.is_replaceable'=>'required',
            'spcp.impact_on_price'=>'required'
        ],
        'MESSAGES'=>Lang::get('product_items.validation_ext_product')
    ),
    'MY_FEEDBACK'=>array(
        'RULES'=>[
            'subject'=>'required',
            'description'=>'required',
        ],
        'MESSAGES'=>Lang::get('my_feedback.validation')
    ),
    'PRODUCT_COMBINATION'=>array(
        'RULES'=>[
            'product_cmb.product_cmb'=>'required',
            'product_cmb.sku'=>'required',
            //'property_id'=>'required',
            //'value_id'=>'required',
            'product_cmb_price.impact_on_price'=>'required',
        //'product_cmb_properties'=>'required'
        ],
        'MESSAGES'=>Lang::get('product_combinations.validation')
    ),
    'WITHDRAWAL_INR'=>array(
        'RULES'=>[
            'bank_info.b_nickname'=>'required',
            'bank_info.ifsc'=>'required|regex:'.getRegex('ifsc'),
            'bank_info.b_branch'=>'required',
            'bank_info.b_accno'=>'required',
            'bank_info.b_acc_type'=>'required',
            'bank_info.b_country'=>'required',
            'bank_info.b_panid'=>'required|regex:'.getRegex('pan'),
            'bank_info.fst_addr'=>'required',
            'bank_info.sec_addr'=>'required',
        ],
        'MESSAGES'=>Lang::get('withdrawal_js.validation')
    ),
    'WITHDRAWAL_USD'=>array(
        'RULES'=>[
            'bank_info.us_nickname'=>'required',
            'bank_info.us_swift_code'=>'required',
            'bank_info.us_routing_no'=>'required',
            'bank_info.us_state'=>'required',
            'bank_info.us_acc_no'=>'required',
            'bank_info.us_bank_country'=>'required',
            'bank_info.us_account_type'=>'required',
            'bank_info.fst_addr'=>'required',
            'bank_info.sec_addr'=>'required',
        ],
        'MESSAGES'=>Lang::get('withdrawal_js.validation')
    ),
    'WITHDRAWAL_SNG/MYR'=>array(
        'RULES'=>[
            'bank_info.sgd_bank_name'=>'required',
            'bank_info.sgd_swift_code'=>'required',
            'bank_info.sgd_bank_code'=>'required',
            'bank_info.sgd_branch_code'=>'required',
            'bank_info.sgd_acc_number'=>'required',
            'bank_info.sgd_acc_type'=>'required',
            'bank_info.sgd_country'=>'required',
            'bank_info.acc_holder_name'=>'required',
            'bank_info.fst_addr'=>'required',
            'bank_info.sec_addr'=>'required',
            'bank_info.acc_country'=>'required',
            'bank_info.b_panid'=>'required|regex:'.getRegex('pan'),
        ],
        'MESSAGES'=>Lang::get('withdrawal_js.validation')
    ),
    'WITHDRAWAL'=>array(
        'RULES'=>[
            'max_amount_txt'=>'required',
            'avail_bala_in_wallet'=>'required',
            'enter_ko-kard_account_number'=>'required',
            'numbers_only'=>'required',
            'invalid_ko-kard_account_number'=>'required',
            'enter_paypal_account_email'=>'required',
            'invalid_email'=>'required',
            'enter_bitcoin_address'=>'required',
        ],
        'MESSAGES'=>Lang::get('withdrawal_js.validation')
    ),
    'RATING'=>array(
        'RULES'=>[
            'rating'=>'required',
            'description'=>'required',
        ],
        'MESSAGES'=>Lang::get('rating.validation')
    ),
    'PRODUCT_COMBINATION'=>[
        'RULES'=>[
//'product_cmb.product_cmb'=>'required',
            'product_cmb.sku'=>'required',
            'product_cmb_properties'=>'required',
            'product_cmb_price.impact_on_price'=>'required',
        ],
        'MESSAGES'=>Lang::get('product_combinations.validation')
    ],
    'MYCART'=>[
        'ADDRESS'=>[
            'RULES'=>[
                'address.billing.full_name'=>'required',
                'address.billing.address1'=>'required',
                'address.billing.address2'=>'',
                'address.billing.city_id'=>'required',
                'address.billing.state_id'=>'required',
                'address.billing.postal_code'=>'required',
                'address.billing.country_id'=>'required',
                'address.billing.mobile_no'=>'required|regex:'.getRegex('mobile'),
                'address.shipping.full_name'=>'required',
                'address.shipping.address1'=>'required',
                'address.shipping.address2'=>'',
                'address.shipping.city_id'=>'required',
                'address.shipping.state_id'=>'required',
                'address.shipping.postal_code'=>'required',
                'address.shipping.country_id'=>'required',
                'address.shipping.mobile_no'=>'required|regex:'.getRegex('mobile'),
            ],
            'MESSAGES'=>Lang::get('mycart.address.validation')
        ],
        'SHIPPING'=>[
            'RULES'=>[
            ],
            'MESSAGES'=>Lang::get('mycart.shipping.validation')
        ],
        'PAYMENT'=>[
            'RULES'=>[
            ],
            'MESSAGES'=>Lang::get('mycart.payment.validation')
        ]
    ],
    'SUPPLIER_SIGNUP'=>[
        'FIELDS'=>[
            'account_mst.firstname'=>[
                'label'=>Lang::get('general.fields.firstname')
            ],
            'account_mst.lastname'=>[
                'label'=>Lang::get('general.fields.lastname')
            ],
            'login_mst.email'=>[
                'label'=>Lang::get('general.fields.email')
            ],
            'login_mst.mobile'=>[
                'label'=>Lang::get('general.fields.mobile')
            ],
            'login_mst.pass_key'=>[
                'label'=>Lang::get('general.fields.password'),
                'attr'=>['type'=>'password']
            ],
            'agree'=>[
                'label'=>Lang::get('general.fields.supplier_agree'),
                'attr'=>['type'=>'checkbox']
            ]
        ],
        'RULES'=>[
            'account_mst.firstname'=>'required|regex:'.getRegex('firstname'),
            'account_mst.lastname'=>'required|regex:'.getRegex('lastname'),
            'login_mst.email'=>'required|email|unique:account_login_mst,email,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SUPPLIER'),
            'login_mst.mobile'=>'required|regex:'.getRegex('mobile').'|unique:account_login_mst,mobile,NULL,account_id,account_type_id,'.Config::get('constants.ACCOUNT_TYPE.SUPPLIER'),
            'login_mst.pass_key'=>'required|min:6|max:10',
            'agree'=>'required'
        ],
        'MESSAGES'=>Lang::get('supplier.signup.validation')
    ],
    'SUPPLIER_ACCOUNT_DETAILS'=>[
        'FIELDS'=>[
            'account_supplier.reg_company_name'=>[
                'label'=>Lang::get('general.fields.reg_company_name')
            ],
            'account_supplier.company_name'=>[
                'label'=>Lang::get('general.fields.company_name')
            ],
            'account_supplier.website'=>[
                'label'=>Lang::get('general.fields.company_url')
            ],
            'account_supplier.type_of_bussiness'=>[
                'label'=>Lang::get('general.fields.type_of_bussiness')
            ],
            'address.country_id'=>[
                'label'=>Lang::get('general.fields.country')
            ],
            'address.state_id'=>[
                'label'=>Lang::get('general.fields.state')
            ],
            'address.postal_code'=>[
                'label'=>Lang::get('general.fields.postal_code')
            ],
            'address.city_id'=>[
                'label'=>Lang::get('general.fields.city')
            ],
            'address.street1'=>[
                'label'=>Lang::get('general.fields.street1')
            ],
            'address.street2'=>[
                'label'=>Lang::get('general.fields.street2')
            ],
        ],
        'RULES'=>[
            //'login_mst.email'=>'required|email|unique:account_login_mst,email'.(isset($postdata['account_id']) && !empty($postdata['account_id']) ? ','.$postdata['account_id'].',account_id' : ',NULL,account_id'),
            //'login_mst.mobile'=>'required|regex:'.getRegex('mobile').'|unique:account_login_mst,mobile'.(isset($postdata['account_id']) && !empty($postdata['account_id']) ? ','.$postdata['account_id'].',account_id' : ',NULL,account_id'),
            'account_supplier.reg_company_name'=>'required|min:3|max:100',
            'account_supplier.company_name'=>'required|min:3|max:100',
            'account_supplier.website'=>'required|url',
            'account_supplier.type_of_bussiness'=>'required',
            'address.country_id'=>'required',
            'address.state_id'=>'required',
            'address.postal_code'=>'required',
            'address.city_id'=>'required',
            'address.street1'=>'required',
            'address.street2'=>'required',
        ],
        //'MESSAGES'=>Lang::get('supplier.accountdetails.validation')
        'MESSAGES'=>['login_mst.email.required'=>'Please enter your Email ID',
            'login_mst.email.email'=>'Please enter your valid Email ID',
            'login_mst.email.unique'=>'Email ID Already Exist',
            'login_mst.mobile.required'=>'Please enter your Mobile No.',
            'login_mst.mobile.regex'=>'Please enter your valid Mobile No.',
            'login_mst.mobile.unique'=>'Mobile Number Already Exist',
            'account_supplier.reg_company_name.required'=>'Please enter your Registered Company Name',
            'account_supplier.reg_company_name.min'=>'Registered Company Name must be :min characters length',
            'account_supplier.reg_company_name.max'=>'Registered Company Name must be :max characters length',
            'account_supplier.company_name.required'=>'Please enter your Company name',
            'account_supplier.company_name.min'=>'Company name must be min 3 characters length',
            'account_supplier.company_name.max'=>'Company Name must be max 100 characters length',
            'account_supplier.website.required'=>'Please enter your website',
            'account_supplier.type_of_bussiness.required'=>'Please select your business type',
            'address.country_id.required'=>'Please select your Country',
            'address.state_id.required'=>'Please select your State',
            'address.postal_code.required'=>'Please enter your Postal Code',
            'address.city_id.required'=>'Please enter your City',
            'address.street1.required'=>'Please enter your Address 1',
            'address.street2.required'=>'Please enter your Address 2']
    ],
    'UPDATE_ACCOUNT'=>[
        'FIELDS'=>[
            'store_extras.email'=>[
                'label'=>Lang::get('general.fields.email')
            ],
            'store_extras.mobile_no'=>[
                'label'=>Lang::get('general.fields.mobile')
            ],
            'store_extras.landline_no'=>[
                'label'=>Lang::get('general.fields.landline_no')
            ],
            'store_extras.firstname'=>[
                'label'=>Lang::get('general.fields.firstname')
            ],
            'store_extras.lastname'=>[
                'label'=>Lang::get('general.fields.lastname')
            ],
            'store_extras.state_id'=>[
                'label'=>Lang::get('general.fields.state')
            ],
            'store_extras.country_id'=>[
                'label'=>Lang::get('general.fields.country')
            ],
            'store_extras.postal_code'=>[
                'label'=>Lang::get('general.fields.postal_code')
            ],
            'store_extras.city_id'=>[
                'label'=>Lang::get('general.fields.city')
            ],
            'store_extras.address1'=>[
                'label'=>Lang::get('general.fields.street1')
            ],
            'store_extras.address2'=>[
                'label'=>Lang::get('general.fields.street2')
            ],
            'store_extras.working_hours_from'=>[
                'label'=>Lang::get('general.fields.timing'),
                'attr'=>['type'=>'time']
            ],
            'store_extras.working_hours_to'=>[
                'label'=>Lang::get('general.fields.timing'),
                'attr'=>['type'=>'time']
            ],
            'store_extras.website'=>[
                'label'=>Lang::get('general.fields.store_url')
            ],
            'store_extras.working_days'=>[
                'label'=>Lang::get('general.fields.working_days'),
                'options'=>Commonsettings::getWorkingDays(),
                'attr'=>[
                    'type'=>'checkbox'
                ]
            ]
        ],
        'RULES'=>[
            'store_extras.email'=>'required|email|unique:store_extras,email'.(isset($postdata['store_id']) && !empty($postdata['store_id']) ? ','.$postdata['store_id'].',store_id' : ',NULL,store_id'),
            //'store_extras.mobile_no'=>'required|regex:'.getRegex('mobile').'|unique:store_extras,mobile'.(isset($postdata['store_id']) && !empty($postdata['store_id']) ? ','.$postdata['store_id'].',store_id' : ',NULL,store_id'),
            'store_extras.mobile_no'=>'required|regex:'.getRegex('mobile'),
            //'store_extras.landline_no'=>'numeric|telephone|unique:store_extras,landline_no'.(isset($postdata['store_id']) && !empty($postdata['store_id']) ? ','.$postdata['store_id'].',store_id' : ',NULL,store_id'),
            'store_extras.landline_no'=>'required',
            'store_extras.firstname'=>'required|regex:'.getRegex('firstname'),
            'store_extras.lastname'=>'required|regex:'.getRegex('lastname'),
            'store_extras.state_id'=>'required',
            'store_extras.postal_code'=>'required',
            'store_extras.city_id'=>'required',
            'store_extras.address1'=>'required',
            'store_extras.address2'=>'required',
            'store_extras.working_hours_from'=>'required',
            'store_extras.working_hours_to'=>'required',
            'store_extras.website'=>'required|url'
        ],
        'MESSAGES'=>Lang::get('supplier.updateaccount.validation')
    ],
    'SUPPLIER_KYC_VERIFICATION'=>[
        'FIELDS'=>[
            'kyc_verifiacation.pan_card_no'=>[
                'label'=>Lang::get('general.fields.pan')
            ],
            'kyc_verifiacation.gstin'=>[
                'label'=>Lang::get('general.fields.gstin')
            ],
            'kyc_verifiacation.pan_card_name'=>[
                'label'=>Lang::get('general.fields.pan_card_name')
            ],
            'kyc_verifiacation.dob'=>[
                'label'=>Lang::get('general.fields.dob_on_pan'),
                'attr'=>[
                    'type'=>'date'
                ]
            ],
            'kyc_verifiacation.pan_card_image'=>[
                'label'=>Lang::get('general.fields.pan_card_image')
            ],
            'kyc_verifiacation.vat_no'=>[
                'label'=>Lang::get('general.fields.vat_no')
            ],
            'kyc_verifiacation.cst_no'=>[
                'label'=>Lang::get('general.fields.cst_no')
            ],
            'kyc_verifiacation.auth_person_name'=>[
                'label'=>Lang::get('general.fields.auth_person_name')
            ],
            'kyc_verifiacation.auth_person_id_proof'=>[
                'label'=>Lang::get('general.fields.auth_person_id_proof')
            ],
            'auth_person_id_proof'=>[
                'label'=>Lang::get('general.fields.auth_person_id_proof'),
                'attr'=>[
                    'type'=>'file',
                    'accept'=>'image/*'
                ]
            ],
            'pan_card_image'=>[
                'label'=>Lang::get('general.fields.pan_card_image'),
                'attr'=>[
                    'type'=>'file',
                    'accept'=>'image/*'
                ]
            ],
            'kyc_verifiacation.id_proof_document_type_id'=>[
                'label'=>Lang::get('general.fields.id_proof_type')
            ]
        ],
        'RULES'=>[
            'kyc_verifiacation.pan_card_no'=>'required|regex:'.getRegex('pan').'|unique:supplier_kyc_verification,pan_card_no'.(isset($postdata['supplier_id']) && !empty($postdata['supplier_id']) ? ','.$postdata['supplier_id'].',supplier_id' : ',NULL,supplier_id'),
            'kyc_verifiacation.gstin'=>'required|regex:'.getRegex('gstin').'|unique:supplier_kyc_verification,gstin'.(isset($postdata['supplier_id']) && !empty($postdata['supplier_id']) ? ','.$postdata['supplier_id'].',supplier_id' : ',NULL,supplier_id'),
            'kyc_verifiacation.pan_card_name'=>'required|min:3',
            'kyc_verifiacation.dob'=>'required|date_format:Y-m-d|detween:'.date('Y-m-d', strtotime('-110 years')).','.date('Y-m-d', strtotime('-18 years')),
            'pan_card_image'=>'required|mimes:jpeg,gif,bmp,png',
            'kyc_verifiacation.vat_no'=>'required',
            'kyc_verifiacation.cst_no'=>'required',
            'kyc_verifiacation.auth_person_name'=>'required',
            'kyc_verifiacation.id_proof_document_type_id'=>'required',
            'auth_person_id_proof'=>'required|mimes:jpeg,gif,bmp,png',
        ],
        'MESSAGES'=>Lang::get('supplier.kyc_verification.validation')
    ],
    'STORE_BANKING'=>[
        'FIELDS'=>[
            'payment_setings.bank_name'=>[
                'label'=>Lang::get('general.fields.bank_name')
            ],
            'payment_setings.account_holder_name'=>[
                'label'=>Lang::get('general.fields.account_holder_name')
            ],
            'payment_setings.account_no'=>[
                'label'=>Lang::get('general.fields.account_no')
            ],
            'payment_setings.account_type'=>[
                'label'=>Lang::get('general.fields.account_type')
            ],
            'payment_setings.ifsc_code'=>[
                'label'=>Lang::get('general.fields.ifsc_code')
            ],
            'payment_setings.country_id'=>[
                'label'=>Lang::get('general.fields.country')
            ],
            'payment_setings.state_id'=>[
                'label'=>Lang::get('general.fields.state')
            ],
            'payment_setings.postal_code'=>[
                'label'=>Lang::get('general.fields.postal_code')
            ],
            'payment_setings.city_id'=>[
                'label'=>Lang::get('general.fields.city')
            ],
            'payment_setings.address1'=>[
                'label'=>Lang::get('general.fields.street1')
            ],
            'payment_setings.address2'=>[
                'label'=>Lang::get('general.fields.street2')
            ],
            'payment_setings.branch'=>[
                'label'=>Lang::get('general.fields.branch')
            ],
            'payment_setings.pan'=>[
                'label'=>Lang::get('general.fields.pan')
            ],
        ],
        'RULES'=>[
            'payment_setings.bank_name'=>'required|min:3',
            'payment_setings.account_holder_name'=>'required|min:3',
            'payment_setings.account_no'=>'required|min:4|max:17',
            'payment_setings.account_type'=>'required',
            'payment_setings.ifsc_code'=>'required|regex:'.getRegex('ifsc'),
            'payment_setings.country_id'=>'required',
            'payment_setings.state_id'=>'required',
            'payment_setings.postal_code'=>'required',
            'payment_setings.city_id'=>'required',
            'payment_setings.address1'=>'required',
            'payment_setings.address2'=>'required',
            'payment_setings.branch'=>'required',
            'payment_setings.pan'=>'required|regex:'.getRegex('pan'),
        ],
        'MESSAGES'=>Lang::get('supplier.storebanking.validation')
    ],
    'PARTNER_SIGNUP'=>[
        'RULES'=>[
            'login_mst.email'=>'required|email|unique:account_login_mst,email'.(isset($postdata['account_id']) && !empty($postdata['account_id']) ? ','.$postdata['account_id'].',account_id' : ',NULL,account_id'),
            'login_mst.mobile'=>'required|regex:'.getRegex('mobile').'|unique:account_login_mst,mobile'.(isset($postdata['account_id']) && !empty($postdata['account_id']) ? ','.$postdata['account_id'].',account_id' : ',NULL,account_id')
        ],
        'MESSAGES'=>Lang::get('partner.signup.validation')
    ],
    'PARTNER_ACCOUNT_DETAILS'=>[
        'RULES'=>[
            'login_mst.pass_key'=>'required|confirmed:login_mst.pass_key_confirmation',
            'login_mst.email'=>'required|email|unique:account_login_mst,email'.(isset($postdata['account_id']) && !empty($postdata['account_id']) ? ','.$postdata['account_id'].',account_id' : ',NULL,account_id'),
            'login_mst.mobile'=>'required|regex:'.getRegex('mobile').'|unique:account_login_mst,mobile'.(isset($postdata['account_id']) && !empty($postdata['account_id']) ? ','.$postdata['account_id'].',account_id' : ',NULL,account_id'),
            'account_mst.firstname'=>'required|regex:'.getRegex('firstname'),
            'account_mst.lastname'=>'required|regex:'.getRegex('lastname'),
            'partner.partner_name'=>'required|min:1',
            'address.country_id'=>'required',
            'address.state_id'=>'required',
            'address.postal_code'=>'required',
            'address.city'=>'required',
            'address.street1'=>'required',
            'address.street2'=>'required',
        ],
        'MESSAGES'=>Lang::get('partner.accountdetails.validation')
    ],
    'SUPPLIER_PREFERENCES'=>[
        'RULES'=>[
        ],
        'MESSAGES'=>[
        ]
    ],
    'SAVE_PARTNER_APP_SETTINGS'=>[
        'RULES'=>[
            'settings.partner_url'=>'required|url',
            'settings.partner_domain'=>'required'
        ],
        'MESSAGES'=>Lang::get('partner.appsettings.validation')
    ],
    'ADMIN_DISCOUNTS'=>getDiscountRules(),
//    'WITHDRAWAL'=>[
//        'RULES'=>[
//            'express-withdrawal'=>[
//                'USD'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['express-withdrawal']['USD']),
//                'INR'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['express-withdrawal']['INR']),
//                'SGD'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['express-withdrawal']['SGD']),
//                'MYR'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['express-withdrawal']['MYR'])
//            ],
//            'paypal'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['paypal']),
//            'solid-trust-pay'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['solid-trust-pay']),
//            'wire-transfer'=>[
//                'USD'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['wire-transfer']['USD']),
//                'INR'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['wire-transfer']['INR']),
//                'SGD'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['wire-transfer']['SGD']),
//                'MYR'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['wire-transfer']['MYR'])
//            ],
//            'os-debit-card'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['os-debit-card']),
//            'ko-kard'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['ko-kard']),
//            'bitcoin'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['bitcoin']),
//            'local-money-transfer'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['local-money-transfer']),
//        ],
//        'MESSAGES'=>[
//            'express-withdrawal'=>Lang::get('withdrawal.validation'),
//            'paypal'=>Lang::get('withdrawal.validation'),
//            'solid-trust-pay'=>Lang::get('withdrawal.validation'),
//            'wire-transfer'=>Lang::get('withdrawal.validation'),
//            'os-debit-card'=>Lang::get('withdrawal.validation'),
//            'ko-kard'=>Lang::get('withdrawal.validation'),
//            'bitcoin'=>Lang::get('withdrawal.validation'),
//            'local-money-transfer'=>Lang::get('withdrawal.validation'),
//        ]
//    ],
    'WITHDRAWAL'=>[
        'express-withdrawal'=>[
            'USD'=>[
                'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['express-withdrawal']['USD']),
                'MESSAGES'=>Lang::get('withdrawal.validation')
            ],
            'INR'=>[
                'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['express-withdrawal']['INR']),
                'MESSAGES'=>Lang::get('withdrawal.validation')
            ],
            'SGD'=>[
                'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['express-withdrawal']['SGD']),
                'MESSAGES'=>Lang::get('withdrawal.validation')
            ],
            'MYR'=>[
                'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['express-withdrawal']['MYR']),
                'MESSAGES'=>Lang::get('withdrawal.validation')
            ]
        ],
        'paypal'=>[
            'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['paypal']),
            'MESSAGES'=>Lang::get('withdrawal.validation'),
        ],
        'solid-trust-pay'=>[
            'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['solid-trust-pay']),
            'MESSAGES'=>Lang::get('withdrawal.validation')
        ],
        'wire-transfer'=>[
            'USD'=>[
                'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['wire-transfer']['USD']),
                'MESSAGES'=>Lang::get('withdrawal.validation')
            ],
            'INR'=>[
                'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['wire-transfer']['INR']),
                'MESSAGES'=>Lang::get('withdrawal.validation')
            ],
            'SGD'=>[
                'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['wire-transfer']['SGD']),
                'MESSAGES'=>Lang::get('withdrawal.validation')
            ],
            'MYR'=>[
                'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['wire-transfer']['MYR']),
                'MESSAGES'=>Lang::get('withdrawal.validation')
            ]
        ],
        'os-debit-card'=>[
            'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['os-debit-card']),
            'MESSAGES'=>Lang::get('withdrawal.validation')
        ],
        'ko-kard'=>[
            'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['ko-kard']),
            'MESSAGES'=>Lang::get('withdrawal.validation')
        ],
        'bitcoin'=>[
            'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['bitcoin']),
            'MESSAGES'=>Lang::get('withdrawal.validation')
        ],
        'local-money-transfer'=>[
            'RULES'=>array_only($withdrawal_filed_rules, $withdrawal_fileds_by_payout_type['local-money-transfer']),
            'MESSAGES'=>Lang::get('withdrawal.validation')
        ],
    ],
    'RECORD_FILTERS'=>[
        'RULES'=>[
            'search_term'=>'alpha_num',
            'from'=>'date_format:d-m-Y|'.isset($postdata['to']) && !empty($postdata['to']) ? 'before:'.date('Y-m-d', strtotime($postdata['to'])) : '',
            'to'=>'date_format:d-m-Y|'.isset($postdata['from']) && !empty($postdata['from']) ? 'after:'.date('Y-m-d', strtotime($postdata['from'])) : ''
        ],
        'MESSAGES'=>Lang::get('general.validation.record_filters')
    ]
];
