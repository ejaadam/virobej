<?php
namespace App\Models\Api\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;
use App\Helpers\ShoppingPortal;
use App\Models\Memberauth;
class APISupplier extends Model
{

    public function __construct (&$commonObj)
    {
        $this->commonObj = $commonObj;
    }
	
	public function getAllocatedStoresList (array $arr = array())
    {		
        extract($arr);
        if ($system_role_id == Config('constants.ACCOUNT_TYPE.SELLER'))
        {
            $qry = DB::table(Config('tables.STORES').' as msm')
                    ->leftJoin(Config('tables.ADDRESS_MST').' as am', 'am.address_id', '=', 'msm.address_id')                    
                    ->where('msm.supplier_id', '=', $supplier_id)
                    ->where('msm.is_approved', '=', Config('constants.ON'))
                    ->where('msm.is_deleted', '=', Config('constants.OFF'))
                    ->where('msm.status', '=', Config('constants.ON'))                   
                    ->selectRaw('msm.store_code, msm.store_name as store, IF(am.address IS NOT NULL,am.address,\'\') as address');			
            if (isset($for))
            {
                $qry->join(Config('tables.CASHBACK_SETTINGS').' as mcs', function($mcs)
                        {
                            $mcs->on('mcs.supplier_id', '=', 'msm.supplier_id')
                            ->on(function($c)
                            {
                                $c->whereNull('mcs.store_id')
                                ->orWhere('mcs.store_id', '=', 'msm.store_id');
                            });
                        })
                        ->where('mcs.'.$for, Config('constants.ON'));
            }

            return $qry->get();
        }
        else
        {
            /* $qry = DB::table(Config('tables.ADMIN_ASSOCIATE').' as aa')
                    ->join(Config('tables.MERCHANT_STORE_MST').' as msm', function($aa) use($mrid)
                    {
                        $aa->on('msm.store_id', '=', 'aa.relation_id')
                        ->where('msm.mrid', '=', $mrid)
                        ->where('msm.is_deleted', '=', Config('constants.OFF'))
                        ->where('msm.status', '=', Config('constants.ON'));
                    })
                    ->leftJoin(Config('tables.ADDRESS_MST').' as am', 'am.address_id', '=', 'msm.address_id')
                    ->leftjoin(Config('tables.LOCATION_COUNTRIES').' as lc', 'lc.country_id', '=', 'am.country_id')
                    ->leftjoin(Config('tables.LOCATION_STATES').' as ls', 'ls.state', '=', 'am.state_id')
                    ->leftjoin(Config('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'am.district_id')
                    ->leftjoin(Config('tables.LOCATION_LOCALITIES').' as ll', 'll.locality_id', '=', 'am.city_id')
                    ->where('aa.post_type', '=', Config('constants.POST_TYPE.STORE'))
                    ->where('aa.account_id', $account_id)
                    ->where('aa.is_deleted', Config('constants.OFF'))
                    ->selectRaw('msm.store_code,msm.store_name as store,CONCAT_WS(\', \',am.flatno_street,am.address,ll.locality,ld.district,ls.state,concat(lc.country,\'-\',am.postcode),am.landmark) as address');

            if (isset($for))
            {
                $qry->join(Config('tables.MERCHANT_CASHBACK_SETTINGS').' as mcs', function($mcs)
                        {
                            $mcs->on('mcs.mrid', '=', 'msm.mrid')
                            ->on(function($c)
                            {
                                $c->whereNull('mcs.store_id')
                                ->orWhere('mcs.store_id', '=', 'msm.store_id');
                            });
                        })
                        ->where('mcs.'.$for, Config('constants.ON'));
            }
            return $qry->get(); */
        }
    }

    public function saveBussinessInfo ($data = array())
    {	
        extract($data);
        $res = false;
        DB::beginTransaction();
        if (isset($account_id) && !empty($account_id))
        {
            if (isset($account_supplier))
            {
                if (isset($supplier_id) && !empty($supplier_id))
                {
					unset($account_supplier['email']);
					unset($account_supplier['mobile']);
                    $res1 = DB::table(Config::get('tables.SUPPLIER_MST'))
                            ->where('supplier_id', $supplier_id)
                            ->update($account_supplier);
                }
                else
                {
                    $account_supplier['account_id'] = $account_id;
                    $account_supplier['created_on'] = date('Y-m-d H:i:s');
                    $account_supplier['supplier_code'] = $supplier_code;
                    $account_supplier['file_path'] = Config::get('path.SUPPLIER_PRODUCT_IMAGE_PATH').$user_name.'/';
                    $supplier_id = $res1 = DB::table(Config::get('tables.SUPPLIER_MST'))
                            ->insertGetId($account_supplier);
                }
            }
            if (isset($address))
            {
                if (isset($address_id) && !empty($address_id))
                {
					$address['flatno_street'] = $address['street1'];
                    $address['address'] = $address['street2'];
					unset($address['street1']);
					unset($address['street2']);
                    $res2 = DB::table(Config::get('tables.ADDRESS_MST'))
                            ->where('address_id', $address_id)
                            ->update($address);
                }
                else
                {
                    //$address['account_id'] = $account_id;
                    $address['created_on'] = date('Y-m-d H:i:s');
                    $address['flatno_street'] = $address['street1'];
                    $address['address'] = $address['street2'];                    
                    $address['relative_post_id'] = $account_id;
                    $address['post_type'] = Config::get('constants.ACCOUNT_TYPE.SELLER');
					unset($address['street1']);
					unset($address['street2']);
                    $address['address_type_id'] = Config::get('constants.ADDRESS.PRIMARY');
                    $address_id = $res2 = DB::table(Config::get('tables.ADDRESS_MST'))
                            ->insertGetId($address);
                }

                $currency_id = DB::table(Config::get('tables.LOCATION_COUNTRY'))->where('country_id', $address['country_id'])->value('currency_id');
                DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                        ->where('account_id', $account_id)
                        ->update(array('country_id'=>$address['country_id'], 'currency_id'=>$currency_id)); 
            }
        }
        if (!empty($res1) || !empty($res2))
        {
            DB::commit();           
        }
        else
        {
            DB::rollback();
        }
        return ($res1 || $res2) ? $supplier_id : false;
    }
	
	public function saveAccountInfo ($data = array())
    {	//return $data;
        extract($data);
        $res = false;
        DB::beginTransaction();
        if (isset($account_id) && !empty($account_id))
        {
            if (isset($account_details))
            {
                if (isset($account_id) && !empty($account_id))
                {					
                    $res1 = DB::table(Config::get('tables.ACCOUNT_DETAILS'))
                            ->where('account_id', $account_id)
                            ->update($account_details);
                }                
            }           
        }
        if (!empty($res1))
        {
            DB::commit();           
        }
        else
        {
            DB::rollback();
        }
        return ($res1) ? $supplier_id : false;
    }

    public function saveStoreBanking ($arr = array())
    {
        extract($arr);
        $ps = [];
        $payment_setings['ifsc_code'] = strtoupper($payment_setings['ifsc_code']);
        $payment_setings['pan'] = strtoupper($payment_setings['pan']);
        $ps['payment_settings'] = json_encode($payment_setings);
        if (DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                        ->where('supplier_id', $supplier_id)
                        ->exists())
        {
            $ps['updated_by'] = $account_id;
            return DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                            ->where('supplier_id', $supplier_id)
                            ->update($ps);
        }
        else
        {
            $ps['supplier_id'] = $supplier_id;
            $ps['updated_by'] = $account_id;
            return DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                            ->insertGetID($ps);
        }
        return false;
    }
	
	public function save_bank_info ($arr = array())
    {
        extract($arr);
        $ps = [];
        $payment_setings['ifsc_code'] = strtoupper($payment_setings['ifsc_code']);
        $payment_setings['pan'] = strtoupper($payment_setings['pan']);
        $ps['payment_settings'] = json_encode($payment_setings);        
            $ps['supplier_id'] = $supplier_id;
            $ps['updated_by'] = $account_id;
            return DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                            ->insertGetID($ps);        
        return false;
    }
	
	public function update_bank_info ($arr = array())
    {	
        extract($arr);
        $ps = [];
        $payment_setings['ifsc_code'] = strtoupper($payment_setings['ifsc_code']);
        $payment_setings['pan'] = strtoupper($payment_setings['pan']);
			$ps['payment_settings'] = json_encode($payment_setings);                    
            $ps['updated_by'] = $account_id;
            $ps['updated_by'] = $account_id;
            return DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                            ->where('sps_id', $sps_id)
                            ->update($ps);               
    }
	
	public function delete_bank_info ($arr = array())
    {
        extract($arr);        
		return DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
						->where('supplier_id', $supplier_id)
						->where('sps_id', $id)
						->update(['is_deleted'=>1]);        
    }
	
	public function get_bank_info ($arr = array())
    {
        extract($arr);        		
		$settings = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                ->where('supplier_id', $supplier_id)
				->where('sps_id', $id)
                ->where('is_deleted', Config::get('constants.OFF'))
                ->selectRaw('payment_settings, sps_id')->first();		
		 $settings->payment_settings = json_decode($settings->payment_settings);
		 $settings->payment_settings->id = $settings->sps_id;
		 unset($settings->sps_id);	
        return $settings;
    }

    public function updateAccountDetails ($arr = array())
    {	
        $res5 = false;
        extract($arr);       
        if (isset($store_extras) && !empty($store_extras))
        {
            if (isset($store_extras['working_days']) && !empty($store_extras['working_days']))
            {
                $store_extras['working_days'] = implode(',', $store_extras['working_days']);
            }            
			$create['store_name'] = $store_extras['store_name'];
			unset($store_extras['store_name']);		
            if (isset($store_id) && !empty($store_id))
            {			
				
                $res5 = DB::table(Config::get('tables.STORES_EXTRAS'))
                        ->where('store_id', $store_id)
                        ->update($store_extras);
				
				$address['updated_on'] = date('Y-m-d H:i:s');		
				$res5 = DB::table(Config::get('tables.ADDRESS_MST'))
                        ->where('post_type', Config::get('constants.ADDRESS_POST_TYPE.STORE'))
                        ->where('relative_post_id', $store_id)
                        ->update($address);
            }            
        }
        return ($res5) ? $supplier_id : false;
    }

    public function saveKycUpdate ($arr = array())
    {	
        extract($arr);
        $res = false;
        $kyc_verifiacation['dob'] = date('Y-m-d', strtotime($kyc_verifiacation['dob']));
        if (DB::table(Config::get('tables.KYC_DOCUMENTS'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where('relative_post_id', $supplier_id)
                        ->where('post_type', $kyc_verifiacation['post_type'])
                        ->exists())
        {			
            $res = DB::table(Config::get('tables.KYC_DOCUMENTS'))
                    ->where('relative_post_id', $supplier_id)
                    ->where('post_type', $kyc_verifiacation['post_type'])
                    ->update($kyc_verifiacation);
        }
        else
        {
			
             $res = DB::table(Config::get('tables.KYC_DOCUMENTS'))
                    ->insertGetID($kyc_verifiacation);
        }

        $verification_doc['document_type_id'] = 19;
        $verification_doc['path'] = $kyc_verifiacation['pan_card_image'];
        $verification_doc['created_on'] = date('Y-m-d H:i:s');
        if (DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                        ->where('account_id', $verification_doc['account_id'])
                        ->where('document_type_id', $verification_doc['document_type_id'])
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->exists())
        {
            DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                    ->where('account_id', $verification_doc['account_id'])
                    ->where('document_type_id', $verification_doc['document_type_id'])
                    ->update($verification_doc);
        }
        else
        {
            DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                    ->insertGetID($verification_doc);
        }
        $verification_doc['document_type_id'] = $kyc_verifiacation['id_proof_document_type_id'];
        $verification_doc['path'] = $kyc_verifiacation['auth_person_id_proof'];
        $verification_doc['created_on'] = date('Y-m-d H:i:s');
        if (DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                        ->where('account_id', $verification_doc['account_id'])
                        ->where('document_type_id', $verification_doc['document_type_id'])
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->exists())
        {
            DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                    ->where('account_id', $verification_doc['account_id'])
                    ->where('document_type_id', $verification_doc['document_type_id'])
                    ->update($verification_doc);
        }
        else
        {
            DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                    ->insertGetID($verification_doc);
        }
        return $res;
    }    
	
	public function saveSupplierRegister_bk ($arr)
    {	
        extract($arr);
        $res = false;
        if (isset($account_mst))
        {
			$amData = [
                'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),                
                'email'=>$account_mst['email'],
                'mobile'=>$account_mst['mobile'],
                'pass_key'=>md5($account_mst['pass_key']),
                'signedup_on'=>getGTZ(),
                'status'=>Config::get('constants.ON'),
                'activated_on'=>getGTZ()];
				
            DB::beginTransaction();               
			$pwd = $account_mst['pass_key'];			
            $account_id = $res = DB::table(Config::get('tables.ACCOUNT_MST'))->insertGetID($amData); 
					
            if (isset($account_id) && !empty($account_id))
            {       
				$amst['user_code'] = $supplier_code = 'S'.(rand(2,9).rand(2,9).rand(2,9)) . str_pad($country, 3, "0", STR_PAD_LEFT ) . str_pad($account_id, 4, "0", STR_PAD_LEFT);
				$amst['uname'] = 'SEL'.$country.date('ymdHi');
				$amst['activation_key'] = md5($amst['user_code']);				
				DB::table(Config::get('tables.ACCOUNT_MST'))->where('account_id', '=', $account_id)->update($amst);
				
                $account_details['account_id'] = $account_id;				
				//$account_details['created_on'] = getGTZ();
				//$account_details['status_id'] = Config::get('constants.ACTIVE');                                                
                $user_id = $res = DB::table(Config::get('tables.ACCOUNT_DETAILS'))->insertGetId($account_details);
				
			 	$accountADD = [];
                $accountADD['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.ACCOUNT');
                $accountADD['relative_post_id'] = $account_id;                
                $accountADD['address_type_id'] = 1;   /* Permananent */                
                $accountADD['country_id'] = $country;
                $store_id = DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($accountADD); 	 
						
                $account_PRE = [];
                $account_PRE['account_id'] = $account_id;
                $account_PRE['country_id'] = $country;
                $account_PRE['language_id'] = 1;
                $account_PRE['currency_id'] = $currency_id = DB::table(Config::get('tables.LOCATION_COUNTRY'))->where('country_id', $country)->value('currency_id');
                $user_preference_id = DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))->insertGetId($account_PRE);
				
				$account_supplier['category_id'] = $search_form['category'];
                $account_supplier['account_id'] = $account_id;
                $account_supplier['created_on'] = date('Y-m-d H:i:s');
                $account_supplier['supplier_code'] = $supplier_code;
                $account_supplier['company_name'] = $buss_name;
				$account_supplier['status'] = Config::get('constants.ON');
                $account_supplier['file_path'] = Config::get('path.SUPPLIER_PRODUCT_IMAGE_PATH').$supplier_code.'/';
                $supplier_id = $res = DB::table(Config::get('tables.SUPPLIER_MST'))->insertGetId($account_supplier); 
				
				$supp_cashback_setting['supplier_id'] = $supplier_id;
				$supp_cashback_setting['is_redeem_otp_required'] = Config::get('constants.ON');
				$supp_cashback_setting['pay'] = Config::get('constants.ON');
				$supp_cashback_setting['member_redeem_wallets'] = Config::get('constants.WALLET_IDS');
				DB::table(Config::get('tables.CASHBACK_SETTINGS'))->insertGetId($supp_cashback_setting);
				
				$sellerADD = [];
                $sellerADD['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
                $sellerADD['relative_post_id'] = $supplier_id;                
                $sellerADD['address_type_id'] = 1;       /* Permananent */         
                $sellerADD['country_id'] = $country;
                DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($sellerADD); 	               
				
				$store['store_name'] = $buss_name;											
				$store['supplier_id'] = $supplier_id;
				$store['category_id'] = $search_form['category'];
				$store['primary_store'] = 1;								
				$store['created_on'] = date('Y-m-d H:i:s');				
				$store['updated_by'] = $account_id;				
				$store['currency_id'] = $currency_id;				
				$store['status'] = Config::get('constants.ON');
				$store['is_approved'] = Config::get('constants.ON');
				$store_id = DB::table(Config::get('tables.STORES'))->insertGetId($store); 		

				$storeADD = [];
                $storeADD['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.STORE');
                $storeADD['relative_post_id'] = $store_id;                
                $storeADD['address_type_id'] = 1;       /* Permananent */         
                $storeADD['country_id'] = $country;                
                $store_address_id = DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($storeADD); 	
				
				$storecode = (rand(2,9).rand(2,9).rand(2,9)).str_pad($country, 3, "0", STR_PAD_LEFT ) .str_pad($store_id, 4, "0", STR_PAD_LEFT );								
				DB::table(Config::get('tables.STORES'))->where('store_id', '=', $store_id)->update(['store_code'=>$storecode, 'store_slug'=>$storecode, 'address_id'=>$store_address_id]);
				
				$store_extras['store_id'] = $store_id;
				$store_extras['country_id'] = $country;
				DB::table(Config::get('tables.STORES_EXTRAS'))->insert($store_extras); 	
            }			
			
            if ($res)
            {
                ($res) ? DB::commit() : DB::rollback();								
                ShoppingPortal::notify('SUPPLIER_SIGNUP', $supplier_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['acc_email'=>$account_mst['email'], 'user_name'=>$account_details['firstname'] . ' '.$account_details['lastname'], 'password'=>$pwd], true, true, false, true, false);				
                $memberObj = new Memberauth();
                return $result = $memberObj->validateUser(['username'=>$account_mst['email'], 'password'=>$pwd], Config::get('constants.ACCOUNT_TYPE.SELLER'));
            }
        }
        return $res ? $supplier_id : false;
    }

    public function get_stores_list ($arr = array(), $count = false)
    {
        extract($arr);
        $res = DB::table(Config::get('tables.STORES').' as st')
                ->join(Config::get('tables.STORES_EXTRAS').' as se', 'st.store_id', '=', 'se.store_id')
                ->join(Config::get('tables.SUPPLIER_MST').' as as', 'st.supplier_id', '=', 'as.supplier_id')
				->leftjoin(Config::get('tables.ADDRESS_MST').' as am', function($am) 
                {
                    $am->on('am.address_id', '=', 'st.address_id')
                    ->where('am.post_type', '=', Config::get('constants.ADDRESS_POST_TYPE.STORE'));
                })
                ->where('st.supplier_id', $supplier_id);
        if (isset($filterTerms) && !empty($filterTerms) && isset($search_text) && !empty($search_text))
        {
            $search_text = '%'.$search_text.'%';
            $filterTerms = !is_array($filterTerms) ? array($filterTerms) : $filterTerms;
            if (in_array('uname', $filterTerms))
            {
                $res->where('st.store_name', 'like', $search_text);
            }
            if (in_array('phone', $filterTerms))
            {
                $res->where('se.mobile_no', 'like', $search_text);
            }
            if (in_array('code', $filterTerms))
            {
                $res->where('st.store_code', 'like', $search_text);
            }
        }
        if (isset($from) && !empty($from))
        {
            $res->whereDate('st.updated_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (isset($to) && !empty($to))
        {
            $res->whereDate('st.updated_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($start) && isset($length))
        {
            $res->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $res->orderby($orderby, $orderby);
        }
        else
        {
            $res->orderby('st.store_name', 'DESC');
        }
        if (!empty($store_code) && !empty($store_code))
        {
            return $res->where('st.store_code', $store_code);
        }
        if ($count)
        {
            return $res->count();
        }
        else
        {
            $stores = $res->leftJoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'am.city_id')
							->leftJoin(Config::get('tables.LOCATION_PINCODES').' as lp', 'lp.pincode_id', '=', 'lci.pincode_id')
							->leftJoin(Config::get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'lp.district_id')
							->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'ld.state_id')
							->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
							->selectRaw('st.store_id, as.company_name, st.store_name, se.mobile_no, CONCAT(am.flatno_street,\' \',am.address) as address, am.flatno_street as address1, am.address as address2, se.email, se.website, ls.country_id, lci.city, st.status, st.store_code, st.updated_on, am.city_id, am.state_id, am.postal_code, st.supplier_id, st.store_logo, se.landline_no, se.firstname, se.lastname, se.working_days, se.working_hours_from, se.working_hours_to')
                    ->get();
            array_walk($stores, function(&$store)
            {
                $store->updated_on = date('d-M-Y H:i:s', strtotime($store->updated_on));
                $store->actions = [];				
                if ($store->status == Config::get('constants.ACTIVE'))
                {
					$store->status = '<span class="label label-success">Active</span>';
                    $store->actions[] = [
                        'label'=>'Inactive',
                        'url'=>URL::route('seller.stores.change-status'),
                        'data'=>[
                            'id'=>$store->store_id,
                            'status'=>Config::get('constants.INACTIVE')
                        ]
                    ];
                }
                else if ($store->status == Config::get('constants.INACTIVE'))
                {
					$store->status = '<span class="label label-danger">Inactive</span>';
                    $store->actions[] = [
                        'label'=>'Active',
                        'url'=>URL::route('seller.stores.change-status'),
                        'data'=>[
                            'id'=>$store->store_id,
                            'status'=>Config::get('constants.ACTIVE')
                        ]
                    ];
                }
            });

            return $stores;
        }
    }

    public function update_stores ($arr = array())
    {
        $supplier_id = '';
        $store_code = '';
        $update_values = '';
        extract($arr);
        if (isset($store_code) && !empty($store_code))
        {
            if (isset($store_extras['working_days']) && !empty($store_extras['working_days']))
            {
                $store_extras['working_days'] = implode(',', $store_extras['working_days']);
            }
            $res = DB::table(Config::get('tables.STORES').' as st')
                    ->join(Config::get('tables.STORES_EXTRAS').' as se', 'st.store_id', '=', 'se.store_id')
                    ->where('st.store_code', $store_code)
                    ->update(array('st.store_name'=>$create['store_name'],
                'st.status'=>$create['status'],
                'se.firstname'=>$store_extras['firstname'],
                'se.lastname'=>$store_extras['lastname'],
                'se.mobile_no'=>$store_extras['mobile_no'],
                'se.landline_no'=>$store_extras['landline_no'],
                'se.email'=>$store_extras['email'],
                'se.address1'=>$store_extras['address1'],
                'se.address2'=>$store_extras['address2'],
                'se.city_id'=>$store_extras['city_id'],
                'se.postal_code'=>$store_extras['postal_code'],
                'se.working_days'=>$store_extras['working_days'],
                'se.working_hours_from'=>$store_extras['working_hours_from'],
                'se.working_hours_to'=>$store_extras['working_hours_to'],
                'se.website'=>$store_extras['website']));
            return 2;
        }
        else
        {

            $create['supplier_id'] = $supplier_id;
            $create['created_on'] = date('Y-m-d H:i:s');
            $create['updated_by'] = $account_id;
            $create['primary_store'] = Config::get('constants.OFF');
            $store_id = DB::table(Config::get('tables.STORES'))->insertGetId($create);
            $store_code = 'SUP'.rand().$store_id;
            $create['store_code'] = $store_code;
            if ($create['store_code'])
            {
                DB::table(Config::get('tables.STORES'))
                        ->where('store_id', $store_id)
                        ->update($create);
            }
            if (isset($store_extras['working_days']) && !empty($store_extras['working_days']))
            {
                $store_extras['working_days'] = implode(',', $store_extras['working_days']);
            }
            $store_extras['store_id'] = $store_id;
            DB::table(Config::get('tables.STORES_EXTRAS'))
                    ->insert($store_extras);
            return 1;
        }
    }

    public function changeStoreStatus ($arr)
    {
        extract($arr);
        $update = [];
        $update['status'] = $status;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $update['updated_by'] = $account_id;
        $query = Db::table(Config::get('tables.STORES'))
                ->where('store_id', $id);
        if ($status == Config::get('constants.INACTIVE'))
        {
            $query->where('status', Config::get('constants.ACTIVE'));
        }
        elseif ($status == Config::get('constants.ACTIVE'))
        {
            $query->where('status', Config::get('constants.INACTIVE'));
        }
        return $query->update($update);
    }

    public function verificationList ($arr = array(), $count = false)
    {
        extract($arr);
        $res = DB::table(Config::get('tables.ACCOUNT_VERIFICATION').' as av')
                ->join(Config::get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'av.document_type_id')
                ->where('av.is_deleted', Config::get('constants.OFF'))
                ->where('av.account_id', $account_id);
        if (isset($type_filer) && !empty($type_filer))
        {
            $res->where('av.document_type_id', $type_filer);
        }
        if (isset($from) && !empty($from))
        {
            $res->whereDate('av.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (isset($to) && !empty($to))
        {
            $res->whereDate('av.created_on', '<=', date('Y-m-d', strtotime($to)));
        }

        if ($count)
        {
            return $res->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $res->skip($start)->take($length);
            }
            if (isset($orderby))
            {
                $res->orderby($orderby, $orderby);
            }
            else
            {
                $res->orderby('av.created_on', 'DESC');
            }
            $verifications = $res->select('av.*', 'dt.type', 'dt.document_type_id', 'dt.other_fields as doc_other_fields')->get();
            array_walk($verifications, function(&$v)
            {
                $v->other_fields = !empty($v->other_fields) ? json_decode($v->other_fields) : [];
                $v->doc_other_fields = !empty($v->doc_other_fields) ? json_decode($v->doc_other_fields, true) : [];
                array_walk($v->other_fields, function(&$field, $k) use($v)
                {
                    $field = ['id'=>$k, 'label'=>$v->doc_other_fields[$k]['label'], 'value'=>$field];
                });
                unset($v->doc_other_fields);
                $v->actions = [];
                $v->actions[] = [
                    'title'=>'Delete',
                    'url'=>URL::route('api.v1.supplier.verification.delete'),
                    'data'=>[
                        'id'=>$v->uv_id
                    ],
                ];
            });
            return $verifications;
        }
    }

    public function uploadVerificationDocument ($data)
    {
        return DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                        ->insert($data);
    }

    public function getCustomerReviews ($arr = array(), $count = false)
    {
        extract($arr);
        $res = DB::table(Config::get('tables.ACCOUNT_RATINGS').' as rat')
                ->join(Config::get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'rat.account_id')
                ->where('rat.is_deleted', Config::get('constants.OFF'))
                ->where('rat.post_type_id', Config::get('constants.POST_TYPE.SUPPLIER'))
                ->where('rat.relative_post_id', $supplier_id);
        if (isset($start) && isset($length))
        {
            $res->skip($start)->take($length);
        }
        if (isset($search_term) && !empty($search_term))
        {
            $res->where(function($res1) use ($search_term)
            {
                $res1->where(DB::raw('concat(um.firstname,\' \',um.lastname)'), 'LIKE', '%'.$search_term.'%');
                $res1->orWhere('rat.title', 'LIKE', '%'.$search_term.'%');
                $res1->orwhere('rat.description', 'LIKE', '%'.$search_term.'%');
            });
        }
        if (isset($from) && !empty($from))
        {
            $res->whereDate('rat.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (isset($to) && !empty($to))
        {
            $res->whereDate('rat.created_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($orderby))
        {
            $res->orderby($orderby, $orderby);
        }
        else
        {
            $res->orderby('rat.created_on', 'asc');
        }
        if ($count)
        {
            return $res->count();
        }
        else
        {
            $reviews = $res->leftJoin(Config::get('tables.RATING_STATUS_LOOKUPS').' as rs', 'rs.status_id', '=', 'rat.status_id')
                            ->leftJoin(Config::get('tables.VERIFICATION_STATUS_LOOKUPS').' as vs', 'vs.is_verified', '=', 'rat.is_verified')
                            ->selectRaw('rat.title,rat.description,rat.created_on,rat.rating,concat(um.firstname,\' \',um.lastname) as full_name,rat.id,rat.likes_count,rat.unlikes_count,rs.status,vs.verification,rat.status_id,rat.is_verified')->get();
            array_walk($reviews, function($review)
            {
                $review->created_on = date('d-M-Y H:i:s', strtotime($review->created_on));
                $review->likes_count = number_format($review->likes_count, 0, '.', ',');
                $review->unlikes_count = number_format($review->unlikes_count, 0, '.', ',');
                $review->rating = number_format($review->rating, 0, '.', ',');
                $review->actions = [];
                if ($review->status_id == Config::get('constants.PUBLISHED'))
                {
                    $review->actions[] = [
                        'title'=>'Unpublished',
                        'url'=>URL::route('api.v1.supplier.reviews.change-status'),
                        'data'=>[
                            'id'=>$review->id,
                            'status'=>Config::get('constants.UNPUBLISHED')
                        ]
                    ];
                }
                else if ($review->status_id == Config::get('constants.UNPUBLISHED'))
                {
                    $review->actions[] = [
                        'title'=>'Published',
                        'url'=>URL::route('api.v1.supplier.reviews.change-status'),
                        'data'=>[
                            'id'=>$review->id,
                            'status'=>Config::get('constants.PUBLISHED')
                        ]
                    ];
                }
                if ($review->is_verified == Config::get('constants.VERIFIED'))
                {
                    $review->actions[] = [
                        'title'=>'Unverified',
                        'url'=>URL::route('api.v1.supplier.reviews.change-verification-status'),
                        'data'=>[
                            'id'=>$review->id,
                            'status'=>Config::get('constants.UNVERIFIED')
                        ]
                    ];
                }
                else if ($review->is_verified == Config::get('constants.UNVERIFIED'))
                {
                    $review->actions[] = [
                        'title'=>'Verified',
                        'url'=>URL::route('api.v1.supplier.reviews.change-verification-status'),
                        'data'=>[
                            'id'=>$review->id,
                            'status'=>Config::get('constants.VERIFIED')
                        ]
                    ];
                }
                unset($review->status_id);
                unset($review->is_verified);
            });
            return $reviews;
        }
    }

    public function changeCustomerReviewsStatus ($arr = array())
    {
        extract($arr);
        $update = [];
        $update['status_id'] = $status;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.ACCOUNT_RATINGS'))
                ->where('id', $id);
        if ($status == Config::get('constants.UNPUBLISHED'))
        {
            $query->where('status_id', Config::get('constants.PUBLISHED'));
        }
        elseif ($status == Config::get('constants.PUBLISHED'))
        {
            $query->where('status_id', Config::get('constants.UNPUBLISHED'));
        }
        return $query->update($update);
    }

    public function changeCustomerReviewsVerificationStatus ($arr = array())
    {
        extract($arr);
        $update = [];
        $update['is_verified'] = $status;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.ACCOUNT_RATINGS'))
                ->where('id', $id);
        if ($status == Config::get('constants.VERIFIED'))
        {
            $query->where('is_verified', Config::get('constants.UNVERIFIED'));
        }
        elseif ($status == Config::get('constants.UNVERIFIED'))
        {
            $query->where('is_verified', Config::get('constants.VERIFIED'));
        }
        return $query->update($update);
    }
	
	
	
		

}
