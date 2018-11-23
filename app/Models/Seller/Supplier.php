<?php
namespace App\Models\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;
use App\Helpers\CommonNotifSettings;
use App\Models\Memberauth;

class Supplier extends Model
{
	/* public function __construct (&$commonObj)
    {
        $this->commonObj = $commonObj;
    } */	
	
	public function saveSupplierRegister ($arr)
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
				//$account_details['status_id'] = Config::get('constants.ACTIVE');                                                
                $res = DB::table(Config::get('tables.ACCOUNT_DETAILS'))->insertGetId($account_details);
				
				$accountADD = [];
                $accountADD['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.ACCOUNT');
                $accountADD['relative_post_id'] = $account_id;                
                $accountADD['address_type_id'] = 1;   /* Permananent */                
                $accountADD['country_id'] = $country;
                $address_id = DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($accountADD); 	 
						
                $account_PRE = [];
                $account_PRE['account_id'] = $account_id;
                $account_PRE['country_id'] = $country;
                $account_PRE['language_id'] = 1;
                $account_PRE['currency_id'] = $currency_id = DB::table(Config::get('tables.LOCATION_COUNTRY'))->where('country_id', $country)->value('currency_id');
                $user_preference_id = DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))->insertGetId($account_PRE);
				
				$account_supplier['category_id'] = $bcategory;
                $account_supplier['account_id'] = $account_id;
                $account_supplier['created_on'] = date('Y-m-d H:i:s');
                $account_supplier['supplier_code'] = $supplier_code;
                $account_supplier['company_name'] = $buss_name;
				$account_supplier['status'] = Config::get('constants.ON');
                $account_supplier['file_path'] = Config::get('path.SUPPLIER_PRODUCT_IMAGE_PATH').$supplier_code.'/';
                $supplier_id = $res = DB::table(Config::get('tables.SUPPLIER_MST'))->insertGetId($account_supplier); 
				
				$sca = [];
				$sca['supplier_id'] = $supplier_id;
				$sca['category_id'] = !empty($bcategory)?$bcategory:NULL;
				$sca['status'] = Config::get('constants.ON');
                DB::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))->insertGetId($sca); 
				
				$supp_cashback_setting['supplier_id'] = $supplier_id;
				$supp_cashback_setting['is_redeem_otp_required'] = Config::get('constants.ON');
				$supp_cashback_setting['pay'] = Config::get('constants.ON');
				$supp_cashback_setting['member_redeem_wallets'] = Config::get('constants.WALLET_IDS');
				DB::table(Config::get('tables.CASHBACK_SETTINGS'))->insertGetId($supp_cashback_setting);
				
				$sellerADD = [];
                $sellerADD['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
                $sellerADD['relative_post_id'] = $supplier_id;                
                $sellerADD['address_type_id'] = Config::get('constants.ADDRESS.PRIMARY');       /* Permananent */         
                $sellerADD['country_id'] = $country;
                DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($sellerADD); 	               
				
				$store['store_name'] = $buss_name;											
				$store['supplier_id'] = $supplier_id;
				$store['category_id'] = $bcategory;
				$store['primary_store'] = 1;								
				$store['created_on'] = date('Y-m-d H:i:s');				
				$store['updated_by'] = $account_id;				
				$store['currency_id'] = $currency_id;				
				$store['status'] = Config::get('constants.OFF');
				$store['is_approved'] = Config::get('constants.OFF');
				$store_id = DB::table(Config::get('tables.STORES'))->insertGetId($store); 		

				$storeADD = [];
                $storeADD['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.STORE');
                $storeADD['relative_post_id'] = $store_id;                
                $storeADD['address_type_id'] = 1;       /* Permananent */         
                $storeADD['country_id'] = $country;                
                $store_address_id = DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($storeADD); 	
				
				$storecode = (rand(2,9).rand(2,9).rand(2,9)).str_pad($country, 3, "0", STR_PAD_LEFT ) .str_pad($store_id, 4, "0", STR_PAD_LEFT );								
				DB::table(Config::get('tables.STORES'))->where('store_id', '=', $store_id)->update(['store_code'=>$storecode, 'store_slug'=>$storecode, 'address_id'=>$store_address_id]);
				
				$ss 						 = [];
				$ss['store_id'] 			 = $store_id;
				$ss['specify_working_hours'] = 1;				
				DB::table(Config::get('tables.STORE_SETTINGS'))->insertGetId($ss); 
				
				$store_extras['store_id']    = $store_id;
				$store_extras['country_id']  = $country;
				$store_extras['email'] 		 = $account_mst['email'];
				$store_extras['mobile_no'] 	 = $account_mst['mobile'];
				DB::table(Config::get('tables.STORES_EXTRAS'))->insert($store_extras); 	
            }
            if ($res)
            {               
				($res) ? DB::commit() : DB::rollback();		
                CommonNotifSettings::notify('SUPPLIER_SIGNUP', $supplier_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['acc_email'=>$account_mst['email'], 'user_name'=>$account_details['firstname'] . ' '.$account_details['lastname'], 'password'=>$pwd], true, true, false, true, false);					
                $memberObj = new Memberauth();
				$memberObj->validateUser(['username'=>$account_mst['email'], 'password'=>$pwd], Config::get('constants.ACCOUNT_TYPE.SELLER'));
                return $account_id;
            }
        }
        //return $res ? $account_id : false;
        return false;
    }

    public function doc_list ()
    {
        return DB::table(Config::get('tables.DOCUMENT_TYPES'))
                        ->select('document_type_id', 'type', 'other_fields')
                        ->get();
    }

    public function delete_doc ($data)
    {
        return DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                        ->where('uv_id', $data['uv_id'])
                        ->update(array('is_deleted'=>Config::get('constants.ON')));
    }

    public function customer_management_list ($arr = array(), $count = false)
    {

        $res = DB::table(Config::get('tables.ACCOUNT_MST').' as a')
                ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as mst', 'mst.account_id', '=', 'a.account_id')
                //->where('mst.login_block', Config::get('constants.OFF'))
                ->where('a.is_deleted', Config::get('constants.OFF'))
                ->where('mst.account_type_id', Config::get('constants.ACCOUNT_TYPE.USER'))
                ->selectRaw('mst.email,a.status_id,a.account_id,mst.user_code,mst.uname,mst.last_login,mst.login_block,concat(a.firstname,a.lastname) as full_name,a.created_on');
        if (isset($arr['search_term']) && !empty($arr['search_term']))
        {
            $res->whereRaw('(mst.email like \'%'.$arr['search_term'].'%\'  OR  concat(a.firstname,a.lastname) like \'%'.$arr['search_term'].'%\' OR mst.code like  \'%'.$arr['search_term'].'%\')');
        }
        if (isset($arr['supplier_id']))//check login blocked or not
        {
            $res->whereIn('a.account_id', function($subquery) use($arr)
            {
                $subquery->from(Config::get('tables.SUB_ORDERS'))
                        ->where('supplier_id', $arr['supplier_id'])
                        ->where('is_deleted', Config::get('constants.OFF'))
//			->where('order_item_status',)
                        ->distinct('account_id')
                        ->lists('account_id');
            });
        }
        if (isset($arr['login_block']))//check login blocked or not
        {
            $res->where('mst.login_block', $arr['login_block']);
        }
        if ((!empty($arr['from'])) && (empty($arr['from'])))
        {
            $res->whereRaw('DATE(a.created_on) >=\''.date('Y-m-d', strtotime($arr['from'])).'\'');
        }
        else if ((empty($arr['to'])) && (!empty($arr['to'])))
        {
            $res->whereRaw('DATE(a.created_on) <=\''.date('Y-m-d', strtotime($arr['to'])).'\'');
        }
        else if ((!empty($arr['from'])) && (!empty($arr['to'])))
        {
            $res->whereRaw('DATE(a.created_on) >=\''.date('Y-m-d', strtotime($arr['from'])).'\'');
            $res->whereRaw('DATE(a.created_on) <=\''.date('Y-m-d', strtotime($arr['to'])).'\'');
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }

        if (isset($arr['orderby']))
        {
            $res->orderby($arr['orderby'], $arr['order']);
        }
        else
        {
            $res->orderby('a.created_on', 'asc');
        }

        if ($count)
        {
            $result = $res->count();
        }
        else
        {
            $result = $res->get();
        }

        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        else
        {
            return $result;
        }
    }   
	
	public function update_new_email ($email, $id)
    {        
		DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                        ->where('account_id', $id)
                        ->update(array('is_email_verified'=>0));
						
        return DB::table(Config::get('tables.ACCOUNT_MST'))
                        ->where('account_id', $id)
                        ->update(array('email'=>$email));
    }

    public function view_profile ($id)
    {
        return DB::table(Config::get('tables.ACCOUNT_SUPPLIERS').' as asu')
                        ->join(Config::get('tables.ACCOUNT_EXTRAS').' as ext', 'ext.account_id', '=', 'asu.account_id')
                        ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as mst', 'mst.account_id', '=', 'ext.account_id')
                        ->join(Config::get('tables.ACCOUNT_ADDRESS').' as addr', 'addr.account_id', '=', 'mst.account_id')
                        ->join(config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'addr.country')
                        ->selectRaw('mst.email,asu.*,mst.uname,lc.name as country_name,concat(ext.firstname,ext.lastname) as       		  full_name,ext.mobile,ext.office_fax,ext.office_phone,addr.*')
                        ->where('asu.account_id', $id)
                        ->first();
    }

    public function customer_list ($data = array())
    {
        $res = DB::table(Config::get('tables.SUPPLIER_ORDER_CUSTOMERS').' as asu')
                ->join(Config::get('tables.ACCOUNT_EXTRAS').' as ext', 'ext.account_id', '=', 'asu.account_id')
                ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as mst', 'mst.account_id', '=', 'ext.account_id')
                ->selectRaw('mst.email,mst.uname,concat(ext.firstname,ext.lastname) as full_name,ext.mobile,(select count(account_id) from '.Config::get('tables.SUB_ORDERS').'  where account_id = asu.account_id and supplier_id = asu.supplier_id) as p_order')
                ->where('asu.supplier_id', $data['id'])
                ->distinct('asu.account_id');
        if (isset($data['search_term']) && !empty($data['search_term']))
        {
            $res->whereRaw('CONCAT(ext.firstname,\' \',ext.lastname) LIKE (\'%'.$data['search_term'].'%\')');
        }
        if (isset($data['orderby']))
        {
            //  $res->orderby($data['orderby'], $data['order']);
        }
        if (isset($data['count']) && !empty($data['count']))
        {
            $result = $res->count();
        }
        else
        {
            $result = $res->get();
        }
        return $result;
    }

    public function notification_lists ($arr = array())
    {
        $res = DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS').' as not')
                ->leftJoin(Config::get('tables.NOTIFICATION_USER_STATUS').' as st', function ($subquery) use($arr)
                {
                    $subquery->on('st.notification_id', '=', 'not.notification_id')
                    ->where('st.account_id', '=', $arr['account_id']);
                })
                ->leftJoin(Config::get('tables.STATEMENT_LINE').' as stl', 'stl.statementline_id', '=', 'not.statementline_id')
                ->where('not.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('not.details,not.created_on,stl.statementline,st.viewed_on')
                ->orderby('not.created_on', 'desc');
        if (isset($header_notification))
        {
            $res->whereNull('st.viewed_on')
                    ->whereNull('st.is_deleted')
                    ->take(5);
        }
        if (isset($arr['search_text']) && !empty($arr['search_text']))
        {
            $res->where('not.details', 'like', '%'.$arr['search_text'].'%');
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']))
        {
            $res->orderby($arr['orderby'], $arr['order']);
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            $result = $res->count();
        }
        else
        {
            $result = $res->get();
        }
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        else
        {
            return $result;
        }
    }

    public function conversation_lists ($arr = array())
    {
        $res = DB::table(Config::get('tables.CONVERSATIONS').' as tc')
                ->join(Config::get('tables.ACCOUNT_EXTRAS').' as tae', 'tae.account_id', '=', 'tc.created_by')
                ->selectRaw('tc.subject, tc.created_on, tae.firstname, tc.conversation_id')
                ->where('tc.is_deleted', Config::get('constants.OFF'))
                ->orderby('tc.conversation_id', 'desc');
        //->get();
        if (!empty($arr['closed']))
        {
            $res->where('tc.status', '1');
        }
        else
        {
            $res->where('tc.status', '0');
        }
        if (!empty($arr['participated_in']))
        {
            $res->where('tc.participants', 'like', '%'.$arr['user_id'].'%');
        }
        if (!empty($arr['search_text']))
        {
            $res->where('tc.subject', 'like', '%'.$arr['search_text'].'%');
        }
        if (!empty($arr['created']))
        {
            $res->where('tc.created_by', $arr['user_id']);
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']))
        {
            $res->orderby($arr['orderby'], $arr['order']);
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            $result = $res->count();
        }
        else
        {
            $result = $res->get();
        }
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        else
        {
            return $result;
        }
    }

    public function email_validate ($postdata)
    {
        return DB::table(Config::get('tables.ACCOUNT_LOGIN_MST'))
                        ->where('email', $postdata['email'])
                        ->get();
    }

    public function generate_supplier_code ($account_id)
    {
        $function_ret = '';
        $profix = $account_id;
        $iLoop = true;
        $disp = $this->rKeyGen(3, 1);
        $disp1 = 'SP'.$disp.$profix;
        return $disp1;
    }

    public function genetare_user_code ()
    {
        $user_codes = DB::table(Config::get('tables.ACCOUNT_LOGIN_MST').' as um')
                ->where('um.uname', 'LIKE', '%'.'SUP'.'%')
                ->lists('um.uname');
        re_create:
        $code = 'SUP'.mt_rand(10000000, 99999999);
        if (in_array($code, $user_codes))
        {
            goto re_create;
        }
        else
        {
            return $code;
        }
    }

    function rKeyGen ($digits, $datatype)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $poss_ALP = array();
        $j = 0;
        if ($datatype == 1)
        {
            for ($i = 49; $i < 58; $i++)
            {
                $poss[$j] = chr($i);
                $poss_ALP[$j] = $poss[$j];
                $j = $j + 1;
            }
            for ($k = 1; $k <= $digits; $k++)
            {
                $key = $key.$poss[rand(1, 8)];
            }
            $key;
        }
        else
        {
            $key = $this->rKeyGen_ALPHA($digits, false);
        }
        return $key;
    }

    function rKeyGen_ALPHA ($digits, $lc)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $j = 0;
        // Place numbers 0 to 10 in the array
        for ($i = 50; $i < 57; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place A to Z in the array
        for ($i = 65; $i < 90; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place a to z in the array
        for ($k = 97; $k < 122; $k++)
        {
            $poss[$j] = chr($k);
            $j = $j + 1;
        }
        $ub = 0;
        if ($lc == true)
            $ub = 61;
        else
            $ub = 35;
        for ($k = 1; $k <= 3; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        for ($k = 4; $k <= $digits; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        return $key;
    }

    public function getKycVerification ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.KYC_DOCUMENTS'))
                        ->where('post_type', $acc_type_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where('relative_post_id', $supplier_id)
                        ->first();
    }

    public function getPaymentSettings ($supplier_id)
    {
        $settings = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                ->where('supplier_id', $supplier_id)
                ->value('payment_settings');
        $settings = json_decode($settings);
        return $settings;
    }
	
	public function get_bank_accounts ($supplier_id)
    {
        $settings = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                ->where('supplier_id', $supplier_id)
                ->where('is_deleted', Config::get('constants.OFF'))
                ->selectRaw('payment_settings, sps_id')->get();
		 array_walk($settings, function(&$setting, $key) 
		 {	
			$setting->sps_id = $setting->sps_id;
			$setting->accounts = json_decode($setting->payment_settings);
			unset($setting->payment_settings);			
		 });        
        return $settings;
    }
	
	

    public function updateNextStep ($arr = array())
    {	
        $ASdata = [];
        extract($arr);
        $next = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                ->where('priority', '>', $current_step)
                ->havingRaw('min(priority)')
                ->selectRaw('step_id, route')
                ->first();
        $ASdata['next_step'] = $next->step_id;		
        if (!empty($next_step))
        {
            $ASdata['completed_steps'] = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                    ->where('priority', '<=', $next_step)
                    ->selectRaw('GROUP_CONCAT(step_id) as completed_steps')
                    ->pluck('completed_steps');
        }
        DB::table(Config::get('tables.SUPPLIER_MST'))
                ->where('supplier_id', $supplier_id)
                ->update($ASdata);
        return (isset($next->route) && !empty($next->route)) ? URL::route($next->route) : URL::to('supplier/dashboard');
    }

    public function getNextStep ($arr = array())
    {
        extract($arr);
        $route = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                ->where('priority', '>', $current_step)
                ->havingRaw('min(priority)')
                ->pluck('route');
        return !empty($route) ? URL::route($route) : URL::to('supplier/dashboard');
    }

    public function getSupplierPreferences ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                        ->where('supplier_id', $supplier_id)
                        ->first();
    }

    public function savePerferences ($arr = array())
    {
        extract($arr);
        $preferences['is_ownshipment'] = (isset($preferences['is_ownshipment']) && !empty($preferences['is_ownshipment'])) ? Config::get('constants.ON') : Config::get('constants.OFF');
        if (DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                        ->where('supplier_id', $supplier_id)
                        ->exists())
        {
            return DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                            ->where('supplier_id', $supplier_id)
                            ->update($preferences);
        }
        else
        {
            $preferences['supplier_id'] = $supplier_id;
            return DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                            ->insert($preferences);
        }
    }
	
	public function change_acc_email($arr){
		DB::table(config('tables.ACCOUNT_PREFERENCE'))
				->where('account_id',$arr['account_id'])
				->update(['is_email_verified'=>0]);
			return DB::table(config('tables.ACCOUNT_MST'))
						->where('account_id',$arr['account_id'])
						->update(['email'=>$arr['email']]);
	}
	
	public function suppiler_acc_details($account_id){
		 $query = DB::table(config('tables.ACCOUNT_MST').' as am')
                ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                ->join(config('tables.ACCOUNT_PREFERENCE').' as apf', 'apf.account_id', '=', 'am.account_id')
                ->selectRaw('apf.is_email_verified,apf.is_mobile_verified,am.account_id, am.account_type_id,  concat(ad.firstname,\' \',ad.lastname) as full_name, ad.firstname, ad.lastname, am.email, am.mobile, am.uname, am.is_deleted, am.pass_key, am.login_block')
                ->where('am.is_deleted', Config::get('constants.OFF'))
                ->where('am.account_id', $account_id)
                ->where('am.account_type_id',config('constants.ACCOUNT_TYPE.SELLER'));
         $result = $query->first();
		 //echo '<pre>';print_R($result);exit;
		 return $result;
	}
	
	/* Reset Password */
	public function reset_pwd ($account_id, $newpwd)
    {
        if (!empty($account_id))
        {
            $upd_pass['pass_key'] = md5($newpwd);
            return DB::table(config('tables.ACCOUNT_MST'))
                            ->where('account_id', $account_id)
                            ->update($upd_pass);
        }
        return false;
    }
	
	public function getAccount_info ($params = array())
    {  // print_r($params);exit;		
        extract($params);
        $qry = DB::table(config('tables.ACCOUNT_MST').' as am')
                ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                ->join(config('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', '=', 'am.account_id')
                ->leftjoin(config('tables.ADDRESS_MST').' as adr', function($join)
                {
                    $join->on('adr.relative_post_id', '=', 'am.account_id');
                    $join->where('adr.post_type', '=', 3);
                })
                ->leftjoin(config('tables.LOCATION_COUNTRY').' as loc', 'loc.country_id', '=', 'adr.country_id')
                ->where('am.is_closed', '=', config('constants.OFF'))
                ->where('am.block', '=', config('constants.OFF'))
                ->where('am.status', '=', config('constants.ON'))
				->where('am.is_deleted', '=', config('constants.OFF'));

        $qry->selectRaw('am.account_id,am.pass_key, am.account_type_id,am.uname, am.email, am.mobile, am.last_active, am.signedup_on, am.activated_on, ast.is_verified, ast.is_email_verified, ast.is_mobile_verified, am.status, am.block, am.login_block, ast.activation_key, ast.email_verification_key, concat_ws(\' \',ad.firstname,ad.lastname) as full_name, adr.postal_code, ad.gender, ad.dob, ad.profile_img, ast.currency_id, ast.referral_code, ast.ip_access, loc.country, loc.phonecode, ad.firstname, ad.lastname, adr.flatno_street, adr.landmark, adr.address, adr.city_id, adr.district_id, adr.state_id, adr.country_id,ast.country_id as countryId');
        if (isset($account_type_id))
        {
            $qry->where('am.account_type_id', '=', $account_type_id);
        }
        if (!empty($account_id))
        {
            $qry->where('am.account_id', '=', $account_id);
        }
        else if (isset($email) && !empty($email))
        {
            $qry->where('am.email', '=', $email);
        }
        if (isset($arr['cache']) && $arr['cache'] == true)
        {
            // $qry->remember(20);
        }
        $result = $qry->first();
		
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }

}
