<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;
use Lang;
use Request;
use App\Models\Commonsettings;

class Memberauth extends Model
{

    public function __construct ()
    {
        //$this->commonObj = $commonObj;
		$this->commonstObj = new Commonsettings();
    } 

    public function validateUser ($arr = array(), $account_type_id = NULL)
    {		
	    $account_type_id = !empty($account_type_id) ? $account_type_id : Config::get('constants.ACCOUNT_TYPE.USER');
		$role = !empty($role) ? $role : Config::get('app.role');
        extract($arr);
        $op = [
            'response'=>[
                'msgs'=>'Parameter Missing',
                'status'=>Config::get('httperr.UN_PROCESSABLE')
            ]];
		
        if (isset($username) && !empty($username) && isset($password) && !empty($password))
        {			
			$userData = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                    ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                    ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
                    ->join(Config('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
                    ->selectRaw('am.account_id, am.account_type_id, concat(ad.firstname,\' \',ad.lastname) as full_name, am.email, am.mobile, am.uname, am.is_deleted, am.pass_key, am.login_block, ap.language_id, ap.currency_id, ap.is_mobile_verified, ap.is_email_verified, ap.send_email, ap.send_sms, ap.send_notification, ap.country_id,am.security_pin,lc.phonecode,lc.mobile_validation')
                    ->where('am.account_type_id', $account_type_id)
                    ->where(function($subquery) use($username)
                    {                      						
						$subquery->where('am.email', $username)
                                 ->orWhere('am.mobile', $username);                      
                    })
                    ->first();
			
            if (!empty($userData))
            {				
                if ($userData->is_deleted == Config::get('constants.OFF'))
                {			
                    if ($userData->pass_key == md5($password))
                    {
                        if ($userData->login_block == Config::get('constants.UNBLOCKED'))
                        {							
                            unset($userData->is_deleted);
                            //unset($userData->pass_key);
                            unset($userData->login_block);
                            switch ($userData->account_type_id)
                            {
                                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
                                    $op['response']['url'] = URL::to('admin/dashboard');
                                    $userData->admin_id = DB::table(Config::get('tables.ADMIN_MST'))
                                            ->where('account_id', $userData->account_id)
                                            ->where('is_deleted', Config::get('constants.OFF'))
                                            ->pluck('admin_id');
                                    break;
                                case Config::get('constants.ACCOUNT_TYPE.SELLER'):
										
                                    $s = DB::table(Config::get('tables.SUPPLIER_MST').' as sm')
											->join(config('tables.STORES').' as st',function($st){
												$st->on('st.supplier_id','=','sm.supplier_id')
												   ->where('st.primary_store','=',1);
											})
                                            ->leftJoin(Config::get('tables.ACCOUNT_CREATION_STEPS').' as acs', 'acs.step_id', '=', 'sm.next_step')
                                            ->where('sm.account_id', $userData->account_id)
                                            ->where('sm.is_deleted', Config::get('constants.OFF'))
                                            ->select('sm.supplier_id', 'sm.category_id', 'sm.service_type', 'sm.completed_steps', 'sm.verified_steps','acs.route as next_step','st.store_id as primary_store_id')
                                            ->first();
								    if (!empty($s))
                                    {
                                        $userData->supplier_id 		= $s->supplier_id;
                                        $userData->category_id 		= $s->category_id;
                                        $userData->service_type 	= $s->service_type;
                                        $userData->store_id 		= 0;
                                        $userData->primary_store_id = $s->primary_store_id;
                                        //$userData->next_step = $s->next_step;
                                        $userData->completed_steps    = $s->completed_steps;
                                        $userData->verified_steps 	  = $s->verified_steps;
                                        $userData->is_verified = $this->commonstObj->getSupplierVerificationStatus($userData->supplier_id);
                                        $op['response']['url'] 		= URL::to('seller/dashboard');
										$userData->stores = $stores = $this->getAllocatedStoresList(['system_role_id'=>$userData->account_type_id, 
																											 'account_id'=>$userData->account_id, 
																											 'supplier_id'=>$userData->supplier_id]);
										$userData->select_store 	= (count($stores) == 1 ? false : true);
										$userData->is_merchant 		= $userData->account_type_id == Config('constants.ACCOUNT_TYPE.CASHIER') ? false : true;
										if (count($stores) === 1)
										{
											$store = $this->storeDetails_login(['store_code'=>$stores[0]->store_code, 'account_id'=>$userData->account_id]);
											if ($store) {
												$userData->store_code = $store->code;
												$userData->store_id = $store->store_id;
												$userData->country = $store->country;
											
											}
										}
                                        if (empty($userData->is_email_verified))
                                        {
                                            //$op['response']['url'] = URL::to('seller/verify-email');
                                            $op['response']['url'] = URL::to('seller/dashboard');
                                           
                                        }
                                        $address = $this->commonstObj->getUserAddress($userData->account_id, $userData->account_type_id);
										if (!empty($address))
                                        {
                                            $userData->country_name = $address[0]->country;
                                            $userData->country_id = $address[0]->country_id;
                                        }
                                    }
                                    break;                                
                                case Config::get('constants.ACCOUNT_TYPE.USER'):
                                    $op['response']['url'] = URL::to('/');
                                    break;
                                default:
                                    $op['response']['url'] = URL::to('api/v1/customer');
                            }
                            
							$userData->token = Config::get('device_log')->token;
                            //print_r($userData->token);exit;
							DB::table(Config::get('tables.DEVICE_LOG'))
                                    ->where('device_log_id', Config::get('device_log')->device_log_id)
                                    ->update(array('account_id'=>$userData->account_id, 'status'=>Config::get('constants.ACTIVE'))); 
                            
							/* DB::table(Config::get('tables.ACCOUNT_LOGIN_LOG'))
                                    ->insertGetID(array('device_log_id'=>Config::get('device_log')->device_log_id, 'account_id'=>$userData->account_id, 'login_on'=>date('Y-m-d H:i:s')));  */
									
							/* Start */
							
							$agent = request()->header('User-Agent');
							if (!DB::table(Config::get('tables.DEVICES'))
											->where('device', $agent)
											->exists())
							{
								$device_id = DB::table(Config::get('tables.DEVICES'))
										     ->insertGetID(['device'=>$agent]);
							}
							else
							{
								$device_id = DB::table(Config::get('tables.DEVICES'))
											->where('device', $agent)
											->value('device_id');
							}	 
							
							$account_log_id = DB::table(Config::get('tables.ACCOUNT_LOG'))
                                           ->insertGetId(array('account_id'=>$userData->account_id, 'account_login_ip'=>request()->ip(), 'device_id'=>$device_id, 'country_id'=>(!empty($postdata['country_id']) ? $postdata['country_id'] : $userData->country_id), 'account_log_time'=>getGTZ()));
										   
							$userData->account_log_id = $account_log_id;
						
							$userData->mobile_validation  = (!empty($userData->mobile_validation))?str_replace('$/','',(str_replace('/^','',$userData->mobile_validation))):'';	
                            $update['token'] = md5($account_log_id);					
							/* Start */
							DB::table(Config::get('tables.ACCOUNT_MST'))
                                    ->where('account_id', $userData->account_id)
                                    ->update(array('last_active'=>date('Y-m-d H:i:s')));
									
                            /* Start */
																					
							/* if (request()->session()->has($role) && request()->session()->get($role)['account_id'] != $userData->account_id)
							{
								request()->session()->regenerate();
							} */																					
						    $update['token'] = request()->session()->getId().'-'.$update['token'];		
							
							request()->session()->set($role, $userData);
							$token_update = DB::table(Config::get('tables.ACCOUNT_LOG'))
									->where('account_log_id', $account_log_id)
									->update($update);  
						   
  						   	/* End */
							
							$op['response']['msgs'] = Lang::get('general.you_are_successfully_logged_in');
                            $op['response']['UserDetails'] = $userData;
                            $op['response']['status'] = Config::get('httperr.SUCCESS'); // 308;
                            //$op['status'] = 'OK';
                        }
                        else
                        {
                            $op['response']['msgs'] = Lang::get('general.invalid_username_or_password');
                            $op['response']['status'] = Config::get('httperr.UN_PROCESSABLE'); //403;
                            unset($op['response']['UserDetails']);
                        }
                    }
                    else
                    {
                        $op['response']['msgs'] = Lang::get('general.invalid_username_or_password');
                        $op['response']['status'] = Config::get('httperr.UN_PROCESSABLE');
                        unset($op['response']['UserDetails']);
                    }
                }
                else
                {
                    $op['response']['msgs'] = Lang::get('general.invalid_username_or_password');
                    $op['response']['status'] = Config::get('httperr.UN_PROCESSABLE'); //403;
                    unset($op['response']['UserDetails']);
                }
            }
            else
            {
                $op['response']['msgs'] = Lang::get('general.invalid_username_or_password');
                $op['response']['status'] = Config::get('httperr.UN_PROCESSABLE');
                unset($op['response']['UserDetails']);
            }
        }
        return (object) $op;
    }
	
	public function storeDetails_login (array $arr = array())
    {
        extract($arr);
        $details = DB::table(Config('tables.STORES').' as msm')
                ->join(Config('tables.SUPPLIER_MST').' as mm', function($mm)
                {
                    $mm->on('mm.supplier_id', '=', 'msm.supplier_id')
                    ->where('mm.block', '=', Config('constants.OFF'))
                    ->where('mm.is_deleted', '=', Config('constants.OFF'))
                    ->where('mm.status', '=', Config('constants.ON'));
                })
            /*    ->join(Config('tables.MERCHANT_SETTINGS').' as ms', function($ms)
                {
                    $ms->on('ms.mrid', '=', 'msm.mrid');
                })   */
                ->leftjoin(Config('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'msm.category_id')
                ->leftjoin(Config('tables.ADDRESS_MST').' as am', function($join)
                {
                    $join->on('am.address_id', '=', 'msm.address_id')
						->where('am.post_type', '=', Config('constants.ADDRESS_POST_TYPE.STORE'));
                })
        /*        ->leftjoin(Config('tables.RATINGS').' as r', function($join)
                {
                    $join->on('r.post_id', '=', 'msm.store_id')
                    ->where('r.post_type', '=', Config('constants.POST_TYPE.STORE'));
                })  */
                ->join(Config('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'msm.store_id')
                ->leftjoin(Config('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id')
                ->leftjoin(Config('tables.LOCATION_CITY').' as ll', 'll.city_id', '=', 'am.city_id')
                ->leftjoin(Config('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'am.state_id') 
                ->leftjoin(Config('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($join)
                {
                    $join->on('bcl.bcategory_id', '=', 'msm.category_id')->where('bcl.lang_id', '=', Config('app.locale_id'));
                })
                //->whereDate('ms.expired_on', '>=', getGTZ()) 
                ->where('msm.store_code', $store_code)
                ->where('msm.status', Config('constants.ON'))
                ->where('msm.block', Config('constants.OFF'))
                ->where('msm.is_approved', Config('constants.ON'))
                ->select('mm.supplier_id', 'msm.category_id', 'msm.store_name', 'msm.store_code as code', 'bcl.bcategory_name', 'msm.store_logo as store_logo', 'msm.qr_code', 'mm.company_name as mrbusiness_name', 'mm.logo as mrlogo', 'se.description', 'se.mobile_no as mobile', 'se.landline_no as phone', 'am.address', 'll.city as locality', 'msm.store_id', 'am.flatno_street', 'am.postal_code as acc_postcode', 'am.city_id', 'am.geolat as geolatitute', 'am.geolng as geolongitute', 'am.landmark', 'am.country_id', 'lc.country', 'am.state_id', 'mm.account_id', 'msm.status', 'mm.is_online', 'msm.currency_id', 'lc.distance_unit')
                ->first();
		
        if (!empty($details))
        {
            //$details->qr_code = asset(Config('constants.STORE_QR_CODE.WEB').!empty($details->qr_code) ? $details->qr_code : $this->commonObj->saveStoreQRCode($details->store_id));
            DB::table(Config('tables.STORES'))
                    ->where('store_id', $details->store_id)
                    ->update(['last_login_account'=>$account_id, 'updated_on'=>getGTZ()]);
        }
        return $details;
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
	
	public function get_userbyId ($acc_id)
    {
		$user = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                ->join(Config::get('tables.DEVICE_LOG').' as dl', 'dl.account_id', '=', 'am.account_id')
                ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'am.account_type_id')
                ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
				->leftjoin(Config::get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'ap.currency_id')			
				->leftjoin(Config::get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'ap.country_id')	
                ->selectRaw('am.account_id,am.is_affiliate, at.account_type_name, concat(ad.firstname,\' \',ad.lastname) as full_name, am.email, am.mobile, am.uname, am.account_type_id, dl.token, ap.language_id, ap.currency_id,cur.currency as currency_code, ap.is_mobile_verified, ap.is_email_verified, ap.send_email, ap.send_sms, ap.send_notification, am.pass_key')
                ->where('am.account_id', $acc_id)
                ->where('am.is_deleted', Config::get('constants.OFF'))
                ->first();					
        
        if (!empty($user))
        {
            switch ($user->account_type_id)
            {
                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
						$user->admin_id = DB::table(Config::get('tables.ADMIN_MST'))
								->where('account_id', $user->account_id)
								->where('is_deleted', Config::get('constants.OFF'))
								->value('admin_id');
						break;
				case Config::get('constants.ACCOUNT_TYPE.SELLER'):
					$s = DB::table(Config::get('tables.SUPPLIER_MST').' as s')
							->leftJoin(Config::get('tables.ACCOUNT_CREATION_STEPS').' as acs', 'acs.step_id', '=', 's.next_step')
							->where('s.account_id', $user->account_id)
							->where('s.is_deleted', Config::get('constants.OFF'))
							->select('s.supplier_id', 'acs.route as next_step')
							->first();
						if (!empty($s))
						{
							$user->supplier_id = $s->supplier_id;
							$user->next_step = $s->next_step;							
							/* $user->is_verified = DB::table(Config::get('tables.SUPPLIER_MST'))
													->where('supplier_id', $user->supplier_id)
													->value('is_verified'); */
							$data = DB::table(Config::get('tables.SUPPLIER_MST'))
													->where('supplier_id', $user->supplier_id)
													->selectRaw('is_verified, completed_steps, verified_steps')->first();
							$user->is_verified = $data->is_verified;
							$user->completed_steps = $data->completed_steps;
							$user->verified_steps = $data->verified_steps;
						}
					break;	             
            }
        }
        return $user;
    }

    public function get_userbyId_bk ($acc_id)
    {
        $user = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as lm', 'am.account_id', '=', 'lm.account_id')
                ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'lm.account_type_id')
                ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'lm.account_id')
                ->selectRaw('am.account_id, at.account_type_name, concat(am.firstname,\' \',am.lastname) as full_name,lm.email,lm.mobile, lm.uname, lm.account_type_id,lm.token,ap.language_id,ap.locale_id,ap.time_zone_id,ap.currency_id,ap.is_mobile_verified,ap.is_email_verified,ap.send_email,ap.send_sms,ap.send_notification')
                ->where('am.account_id', $acc_id)
                ->where('am.is_deleted', Config::get('constants.OFF'))
                ->first();
        if (!empty($user))
        {
            switch ($user->account_type_id)
            {
                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
                    $user->admin_id = DB::table(Config::get('tables.ADMIN_MST'))
                            ->where('account_id', $user->account_id)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->pluck('admin_id');
                    break;
                case Config::get('constants.ACCOUNT_TYPE.SUPPLIER'):
                    $s = DB::table(Config::get('tables.ACCOUNT_SUPPLIERS').' as s')
                            ->leftJoin(Config::get('tables.ACCOUNT_CREATION_STEPS').' as acs', 'acs.step_id', '=', 's.next_step')
                            ->where('s.account_id', $user->account_id)
                            ->where('s.is_deleted', Config::get('constants.OFF'))
                            ->select('s.supplier_id', 'acs.route as next_step')
                            ->first();
                    if (!empty($s))
                    {
                        $user->supplier_id = $s->supplier_id;
                        $user->next_step = $s->next_step;
                        $user->is_verified = $this->commonObj->getSupplierVerificationStatus($user->supplier_id);
                    }
                    break;
                case Config::get('constants.ACCOUNT_TYPE.PARTNER'):
                    $user->partner_id = DB::table(Config::get('tables.PARTNER'))
                            ->where('account_id', $user->account_id)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->pluck('partner_id');
                    break;
            }
        }
        return $user;
    }

    /**
     * @param int $account_id account_id which is login
     * @param int $device_log_id from which device have to logout
     * @return boolean true if logout else false
     */
    public function logoutUser ($account_id, $device_log_id)
    {
        if (DB::table(Config::get('tables.DEVICE_LOG'))
                        ->where('account_id', $account_id)
                        ->where('device_log_id', $device_log_id)
                        ->update(array('status'=>Config::get('constants.INACTIVE'))))
        {
            Config::has('data.user', null);
            return true;
        }
        return false;
    }

    /**
     * @param int $account_id account_id which is login
     * @param int $device_log_id from which device have to logout
     * @return boolean true if not logout else false
     */
    public function checkAutoLogout ($account_id, $device_log_id)
    {
        $cur = date('Y-m-d H:i:s');
        if (Request::isMethod('get'))
        {
            if (DB::table(Config::get('tables.ACCOUNT_MST'))
                            ->where('account_id', $account_id)
                            ->whereRaw('TIMESTAMPDIFF(MINUTE,last_active,\''.$cur.'\')<60')
                            ->exists())
            {
                DB::table(Config::get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(array('last_active'=>$cur));
                return true;
            }
            else
            {
                if ($this->logoutUser($account_id, $device_log_id))
                {
                    return false;
                }
            }
        }
        else
        {
            DB::table(Config::get('tables.ACCOUNT_MST'))
                    ->where('account_id', $account_id)
                    ->update(array('last_active'=>$cur));
            return true;
        }
    }

}
