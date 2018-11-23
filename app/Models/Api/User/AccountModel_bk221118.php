<?php

namespace App\Models\Api\User;
use DB;
use Illuminate\Support\Facades\Cookie;
use App\Models\BaseModel;
use CommonLib;
use File;
use Config;

class AccountModel extends BaseModel
{

    public function __construct ()
    {        
		parent::__construct();    
		
		$this->config = config();
        $this->session = session();
        $this->siteConfig = (object) Config::get('site_settings'); 
		$this->request = request();        
    }    
	
	public function checkAccount_login ($request, array $postdata = array(), $account_type = 0)
    {   //print_r('Good');exit;       
		$role = !empty($role) ? $role : $this->config->get('app.role');
        $session_key = !empty($session_key) ? $session_key : $this->config->get('app.session_key');
        $session_cookie = !empty($session_cookie) ? $session_cookie : $this->config->get('session.cookie');
        $email = '';
        $mobile = '';
        $status = '';
        $unique_id = '';		
        
        $op = array();
        extract($postdata);
        if (isset($remember_me) && !empty($remember_me))
        {
            $this->config->set('session.expire_on_close', false);
        }
        if (!empty($postdata) && !empty($username) && !isset($account_id))
        {
            if (strpos($username, '@'))
            {
                $email = $username;
            }
			elseif (preg_match('/^[0-9]{10}+$/', $username) > 0)
            {				
                $mobile = $username;
            }
            elseif (preg_match('/^[\w\-]+$/', $username))
            {
                $unique_id = $username;
            }			
            else
            {
                return $op;
            }
        }		
        if (!empty($email) || !empty($mobile) || !empty($unique_id) || !empty($account_id))
        {
			$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_MST'))                
				->where('is_deleted', '=', $this->config->get('constants.OFF'))                
                ->whereIn('account_type_id',[$this->config->get('constants.ACCOUNT_TYPE.USER'), $this->config->get('constants.ACCOUNT_TYPE.SELLER'), $this->config->get('constants.ACCOUNT_TYPE.SELLER_EMPLOYEE')])
				->select(DB::Raw('account_id, pass_key, email, uname, mobile, login_block,block, is_affiliate, is_closed, account_type_id, status, user_code, security_pin'));		  
			
			$qry = DB::table(DB::raw('('.$fisrtQry->toSql().') as um'))					
					->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
					->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as st', 'st.account_id', '=', 'um.account_id')
					->join($this->config->get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'um.account_type_id')
					->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'st.currency_id')	
					->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'st.country_id')
					->selectRaw('um.account_id, um.is_affiliate, um.account_type_id, at.account_type_name, um.is_closed, um.pass_key, um.uname, concat_ws(\' \',ud.firstname,ud.lastname) as full_name, ud.firstname, ud.lastname, ud.gender, ud.dob, IF(um.mobile IS NOT NULL,um.mobile,\'\') as mobile, lc.phonecode, ud.profile_img, um.email, st.currency_id, cur.currency as currency_code, um.block, um.login_block, st.is_mobile_verified, st.is_email_verified, st.is_verified, um.status, um.user_code, st.country_id, um.security_pin, lc.time_zone, lc.country, lc.iso2 as country_code, cur.currency_symbol, lc.distance_unit') 
					->addBinding($fisrtQry->getBindings());       
			
            if (isset($account_id) && !empty($account_id))
            {
                $qry->where('um.account_id', '=', $account_id);
            }
            else if (!empty($unique_id))
            {
                $qry->where('um.uname', '=', $unique_id);
            }
            else if (!empty($email))
            {
                $qry->where('um.email', '=', $email);
            } 
			else if (!empty($mobile)) {
				
				$qry->where('um.mobile', '=', $mobile);
			}           
            $userData = $qry->first();
			
            if (!empty($userData))
            { 
				if ($userData->account_type_id == $this->config->get('constants.ACCOUNT_TYPE.SELLER')) {
					$store = DB::table($this->config->get('tables.SUPPLIER_MST').' as sm')
									 ->Join($this->config->get('tables.STORES').' as s', 's.supplier_id', '=', 'sm.supplier_id')									
									 ->where('sm.account_id', '=', $userData->account_id)
									 ->where('s.is_online', '=', $this->config->get('constants.OFF'))
									 ->selectRAW('s.store_code, s.store_name, s.primary_store, s.store_id, sm.supplier_id');
					$store_count = $store->count();							
					$stores = $store->get();
				}
					//return $stores;
		        if($userData->account_type_id == $this->config->get('constants.ACCOUNT_TYPE.SELLER') || $userData->account_type_id == $this->config->get('constants.ACCOUNT_TYPE.SELLER_EMPLOYEE')) {
					if (($userData->account_type_id == $this->config->get('constants.ACCOUNT_TYPE.SELLER'))) {
						$adqry = DB::table($this->config->get('tables.ADDRESS_MST').' as addm')                
									->where('addm.relative_post_id', '=', $stores[0]->store_id)
									->where('addm.post_type', '=', $this->config->get('constants.ADDRESS_POST_TYPE.STORE'))
									->where('addm.address_type_id', '=', $this->config->get('constants.ADDRESS.PRIMARY'))
									->selectRAW($stores[0]->store_code . ' as store_code, '.$stores[0]->supplier_id.' as supplier_id');		
					}				
					
					if (($userData->account_type_id == $this->config->get('constants.ACCOUNT_TYPE.SELLER_EMPLOYEE'))) {		
						 $adqry = DB::table($this->config->get('tables.STORE_EMPLOYEES').' as semp')
								 ->Join($this->config->get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 'semp.supplier_id')
								 ->Join($this->config->get('tables.STORES').' as s', 's.store_id', '=', 'semp.store_id')
								 ->join($this->config->get('tables.ADDRESS_MST') . ' as addm', function($join) use($userData)
								{									
									if ($userData->account_type_id == 5) {
										$join->on('addm.relative_post_id', '=', 'semp.store_id');
										$join->where('addm.post_type', '=', $this->config->get('constants.ADDRESS_POST_TYPE.STORE'));										 
									}	
								}) 
								->selectRAW('s.store_code, s.supplier_id')  
								->where('semp.account_id', '=', $userData->account_id);								
					}			
				} else {
					$adqry = DB::table($this->config->get('tables.ADDRESS_MST').' as addm')                
							->where('addm.relative_post_id', '=', $userData->account_id)
							->where('addm.post_type', '=', $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT'))
							->where('addm.address_type_id', '=', $this->config->get('constants.ADDRESS.PRIMARY'));	
				}
					
				$adqry->leftJoin($this->config->get('tables.LOCATION_PINCODES').' as lp', 'lp.pincode', '=', 'addm.postal_code')
					  ->leftJoin($this->config->get('tables.LOCATION_CITY').' as lcty', 'lcty.city_id', '=', 'addm.city_id')
					  ->leftJoin($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'addm.district_id')
					  ->leftJoin($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'addm.state_id')
					  ->addselect('addm.state_id', 'addm.district_id', 'addm.city_id as locality_id', 'addm.geolat', 'addm.geolng', 'addm.flatno_street', 'addm.address', 'addm.landmark', 'addm.postal_code', 'lp.pincode_id', 'ls.region_id', 'ls.state', 'ld.district', 'lcty.city');
				$userAddr =  $adqry->first();	
				
                if (($userData->status == $this->config->get('constants.INACTIVE')))
                {
                    $op['status'] = 14;
                    return $op;
                }                
				if ($userData->login_block == $this->config->get('constants.OFF'))
				{
					if ($userData->block == $this->config->get('constants.OFF'))
					{						
						if (isset($postdata['account_id'])  || (isset($postdata['password']) && $userData->pass_key == md5($postdata['password'])))
						{								 
							$last_active = $currentdate = getGTZ();
							$agent = $request->header('User-Agent');
							$postdata['ip'] = isset($postdata['ip']) ? $postdata['ip'] : '';
							
							if (!DB::table($this->config->get('tables.DEVICES'))
											->where('device', $agent)
											->exists())
							{
								$device_id = DB::table($this->config->get('tables.DEVICES'))
										->insertGetID(['device'=>$agent]);
							}
							else
							{
								$device_id = DB::table($this->config->get('tables.DEVICES'))
										->where('device', $agent)
										->value('device_id');
							}
							$userData->account_log_id = $account_log_id = DB::table($this->config->get('tables.ACCOUNT_LOG'))
									->insertGetId(array('account_id'=>$userData->account_id, 'account_login_ip'=>$postdata['ip'], 'device_id'=>$device_id, 'country_id'=>(!empty($postdata['country_id']) ? $postdata['country_id'] : $userData->country_id), 'account_log_time'=>$currentdate));
							$update['token'] = md5($account_log_id);
							
							$data = array(
								'account_id' => $userData->account_id,
								'uname' => $userData->uname,                                
								'full_name' => $userData->full_name,
								'firstname' => $userData->firstname,
								'lastname' => $userData->lastname,
								'gender' => $userData->gender,
								'email' => $userData->email,
								'account_log_id' => $userData->account_log_id,
								'dob' => $userData->dob,
								'is_affiliate' => $userData->is_affiliate,
								'account_type_name' => $userData->account_type_name,
								'account_type' => $userData->account_type_id,								
								'currency_id' => $userData->currency_id,
								'currency_code' => $userData->currency_code,
								'is_mobile_verified' => $userData->is_mobile_verified,
								'is_email_verified' => $userData->is_email_verified,
								'currency_symbol' => $userData->currency_symbol,                                
								'is_verified' => $userData->is_verified,
								'mobile' => $userData->mobile,
								'country_id'=>$userData->country_id,
								'country'=> $userData->country,
								'country_code'=>$userData->country_code,
								'country_flag'=> asset($this->config->get('path.FLAGS_PATH').strtolower($userData->country_code).'.png'),
								'phonecode' => $userData->phonecode,
								'user_code' => $userData->user_code,								
								'pass_key' => $userData->pass_key,								
								'security_pin'=>$userData->security_pin,
								'token' => $update['token'],			
								'timezone'=>$userData->time_zone,		
								'profile_image' => $userData->profile_img,																		
								'address'=>(object) [
									'lat'=>isset($userAddr->geolat)? $userAddr->geolat : 0,
									'lng'=>isset($userAddr->geolng)? $userAddr->geolng : 0,
									'distance_unit'=>isset($userAddr->distance_unit)? $userData->distance_unit : 1,
									'country_id'=>isset($userAddr->country_id)? $userData->country_id : $userData->country_id,
									'country'=> isset($userAddr->country)? $userData->country : $userData->country,
									'country_code'=>isset($userAddr->country_code)? $userData->country_code : $userData->country_code,
									'region_id'=>isset($userAddr->region_id)? $userAddr->region_id : 0,
									'state_id'=>isset($userAddr->state_id)? $userAddr->state_id : 0,
									'district_id'=>isset($userAddr->district_id)? $userAddr->district_id : 0,
									'pincode_id'=>isset($userAddr->pincode_id)? $userAddr->pincode_id : 0,
									'locality_id'=>isset($userAddr->locality_id)? $userAddr->locality_id : 0,
									/* 'formatted_address'=>$userData->formated_address, */
									'flat_no'=>isset($userAddr->flatno_street)? $userAddr->flatno_street : '',
									'address'=>isset($userAddr->address)? $userAddr->address : '',
									'landmark'=>isset($userAddr->landmark)? $userAddr->landmark : '',
									'postcode'=>isset($userAddr->postcode)? $userAddr->postcode : '',
									'locality'=>isset($userAddr->city)? $userAddr->city : '',
									'district'=>isset($userAddr->district)? $userAddr->district : '',
									'state'=>isset($userAddr->state)? $userAddr->state : '',																		
							]);	
							
							if (($userData->account_type_id == 3) || ($userData->account_type_id == 5))
							{
								if (isset($store_count) && ($store_count > 1)) { 	$is_multi_store = 1;    } else {	$is_multi_store = 0;	}
								$data = array_merge($data, [
									'store_code'=>isset($userAddr->store_code) ? $userAddr->store_code : '',	
									'supplier_id'=>isset($userAddr->supplier_id) ? $userAddr->supplier_id : '',	
									'store_count'=>isset($store_count) ? $store_count : 0,	
									'is_multi_store'=>$is_multi_store,	
									'stores'=>isset($stores) ? $stores : '',									
								]);
							}
							if ($request->session()->has($role))
							{
								//$request->session()->regenerate();
							}
							
							//$geo = $request->session()->get('geo');							
							//$geo->current = $data['address'];							
							//$request->session()->set('geo', $geo);
							
							$op['token'] = $data['full_token'] = $update['token'] = $request->session()->getId().'-'.$update['token'];
							$request->session()->set($role, $data);
							$token_update = DB::table($this->config->get('tables.ACCOUNT_LOG'))
									->where('account_log_id', $account_log_id)
									->update($update);
							if ($token_update)
							{
								 $op['acinfo'] = $data;
							}				
							$data['is_guest'] = 0;
							$data['toggle_app_lock'] = 0;
							$data['has_pin'] = 1;
							return ['status'=>1, 'msg'=>trans('user/account.join_success'), 'accinfo'=>(object)$data, 'token'=>$update['token']];							
						}
						else
						{
							$op['status'] = 3;
						}
					}
					else
					{
						$op['status'] = 5;
					}
				}
				else
				{
					$op['status'] = 6;
				}                
            }
            else if (empty($userData) && !empty($email))
            {
                $op['status'] = 2;
            }
            else if (empty($userData) && !empty($mobile))
            {
                $op['status'] = 3;
            }
            else if (empty($userData) && !empty($unique_id))
            {
                $op['status'] = 9;
            }
            else
            {
                $op['status'] = 4;
            }
        }
        else
        {
            $op['status'] = 8;
        }
        return $op;
    }	
	
	public function store_info_not_used (array $data)
    {
        extract($data);
        if (isset($account_id) && !empty($account_id))
        {	
            $query = DB::table(Config::get('tables.ADMIN_ASSOCIATE').' as aso')
                    ->join(Config::get('tables.STORES').' as mst', function($chk_store)
                    {
                        $chk_store->on('mst.store_id', '=', 'aso.relation_id')							
							      ->where('aso.post_type', '=', 3);
                    })
                    ->join(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'mst.store_id')
                    ->join(Config::get('tables.SUPPLIER_MST').' as ms', 'ms.supplier_id', '=', 'mst.supplier_id')
                    ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'aso.account_id')
                    ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                    ->where('am.account_type_id', Config::get('constants.ACCOUNT_TYPE.MERCHANT_SUB_ADMIN'))
                    ->select('am.account_id', 'mst.supplier_id as mrid', 'mst.store_id', 'am.email', 'am.uname', 'se.firstname', 'ad.lastname', 'mst.store_name', 'am.signedup_on', 'am.mobile', DB::raw('CONCAT_WS(\' \', ad.firstname, ad.lastname) as full_name'))
                    ->where('am.is_deleted', Config::get('constants.NOT_DELETED'))
                    ->where('ms.account_id', '=', $account_id);
            return $result = $query->get();
            array_walk($result, function(&$data)
            {
                $data->signedup_on = showUTZ($data->signedup_on, 'd-M-Y H:i:s');
            });
            return $result;
        }
    }
	
	public function getLocationsList (array $arr = array())
    {		
        $locality_id = null;
        extract($arr);
        $res = DB::table($this->config->get('tables.LOCATION_POPULAR_LOCALITIES').' as lpl')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'lpl.country_id')
                ->selectRaw('lpl.locality_id,lpl.locality,lpl.country_id,lc.country,lc.iso2 as flag,latitude as lat,longitude as lng')
                ->where('lpl.status', $this->config->get('constants.ON'))
                ->orderby('lc.country', 'ASC')
                ->orderby('lpl.locality', 'ASC')
                ->get();
        $list = [];
        array_walk($res, function($v) use(&$list, $locality_id)
        {
            if (!isset($list[$v->country_id]))
            {
                $list[$v->country_id] = ['country'=>$v->country, 'flag'=>asset($this->config->get('path.FLAGS_PATH').strtolower($v->flag).'.png'), 'localities'=>[]];
            }            
            $list[$v->country_id]['localities'][] = ['id'=>$v->locality_id, 'label'=>$v->locality, 'selected'=>!empty($locality_id) && $locality_id == $v->locality_id ? true : false];
        });
        $list = array_values($list);
        return $list;
    }
	
	public function getLocationInfo (array $arr = array(), $with_values = false)
    {		
        $pincode = null;
        $locality = null;
        $country = null;
        $state = null;
        extract($arr);
        if (isset($lat) && isset($lng))
        {
            $ch = curl_init();
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.$this->config->get('services.google.map_api_key').'&result_type=country|administrative_area_level_1|postal_code|locality';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                die('Curl failed: '.curl_error($ch));
            }
            curl_close($ch);

            $result = json_decode($result);
            if (!empty($result) && !empty($result->results))
            {
                foreach ($result->results as $r)
                {
                    foreach ($r->address_components as $c)
                    {
                        if (in_array('country', $c->types))
                        {
                            $country = $c->long_name;
                        }
                        if (in_array('administrative_area_level_1', $c->types))
                        {
                            $state = $c->long_name;
                        }
                        if (in_array('locality', $c->types))
                        {
                            $locality = $c->long_name;
                        }
                        if (in_array('postal_code', $c->types))
                        {
                            $pincode = $c->long_name;
                        }
                    }
                }
            }
        }

        if ((isset($pincode) && !empty($pincode)) || (isset($locality) && !empty($locality)) || (isset($locality_id) && !empty($locality_id)))
        {			
            $query = DB::table($this->config->get('tables.LOCATION_COUNTRY').' as lc')
                     ->join($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.country_id', '=', 'lc.country_id')
                    ->join($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.state_id', '=', 'ls.state_id')
                    ->join($this->config->get('tables.LOCATION_PINCODES').' as lp', 'lp.district_id', '=', 'ld.district_id')
                    ->join($this->config->get('tables.LOCATION_CITY').' as ll', 'll.pincode_id', '=', 'lp.pincode_id')
                    ->join($this->config->get('tables.LOCATION_REGIONS').' as lr', 'lr.country_id', '=', 'lc.country_id')                    
                    ->selectRaw('lc.country, lc.country_id, lc.currency_id, ls.state_id, lr.region_id, ld.district_id, lp.pincode_id, group_concat(ll.city_id) as locality_id, lc.distance_unit, lc.iso2 as country_code'); 					
					
            if (!empty($country))
            {
                $query->where('lc.country', $country);
            }
            if (!empty($country_id))
            {
                $query->where('lc.country_id', $country_id);
            }
            if (!empty($state))
            {
                $query->where('ls.state', $state);
            }
            if (!empty($pincode))
            {
                $query->where('lp.pincode', $pincode)
                        ->groupby('lp.pincode_id');
                if (!empty($locality))
                {
                    $query->orWhere('ll.city', 'like', '%'.$locality.'%');
                }
            }
            elseif (!empty($locality))
            {
                $query->where('ll.city', 'like', '%'.$locality.'%');
            }
            elseif (!empty($locality_id))
            {
                $query->leftjoin($this->config->get('tables.LOCATION_POPULAR_LOCALITIES').' as lpl', 'lpl.locality_id', '=', 'll.city_id')
                        ->where('ll.city_id', $locality_id)
                        ->addSelect('lpl.latitude as lat', 'lpl.longitude as lng', 'll.city as locality');
            }
            $locations = $query->first();
            if (!empty($locations))
            {
                $locations->locality_id = !$with_values ? explode(',', $locations->locality_id)[0] : explode(',', $locations->locality_id);
                $locations->lat = isset($lat) ? $lat : (isset($locations->lat) ? $locations->lat : null);
                $locations->lng = isset($lng) ? $lng : (isset($locations->lng) ? $locations->lng : null);
            }
            return $locations;
        }
        return false;
    }
	
	public function myOrders (array $arr = array(), $count = false)
    {  
        extract($arr);
        $query = DB::table($this->config->get('tables.ORDERS').' as mo')
                ->join($this->config->get('tables.SUPPLIER_MST').' as m', 'm.supplier_id', '=', 'mo.supplier_id')
                ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
				//->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'mo.account_id')
                ->where('mo.is_deleted', $this->config->get('constants.OFF'));
		if ($account_type == 2)	{ 	
            $query->where('mo.account_id', $account_id);		
        } elseif ($account_type == 3 || $account_type == 5)	{ 	 
			$store_id = DB::table($this->config->get('tables.STORES'))->where('store_code', $store_code)->value('store_id');
			$query->where('mo.store_id', $store_id);		
		}
		if (isset($from) && !empty($from))
		{
			$query->whereDate('mo.order_date', '<=', getGTZ());
		}
		if (isset($to) && !empty($to))
		{
			$query->whereDate('mo.order_date', '>=', getGTZ());
		}
		/* if (isset($pay_through) && $pay_through !='')
		{
			$query->where('mo.pay_through', $pay_through);
		} */
		if (isset($search_term) && !empty($search_term))
		{
			$query->where('m.company_name', 'like', '%'.$search_term.'%')
				  ->orwhere('mo.order_code', 'like', '%'.$search_term.'%');
		}
		if (isset($type) && !empty($type))
		{
			switch ($type)
			{				
				case "all":
					
					break;
				case "paid":           	
					$query->where('mo.status', $this->config->get('constants.ON'))
						  ->where('mo.payment_status', $this->config->get('constants.ON'))
						  ->where('mo.order_type', $this->config->get('constants.ORDER.TYPE.IN_STORE'))
						  ->whereIn('mo.pay_through', [$this->config->get('constants.ORDER.PAID_THROUGH.REDEEM'), $this->config->get('constants.ORDER.PAID_THROUGH.PAY')]);	
					 
					break;
				case "cashback":
				    //$query->join($this->config->get('tables.CASHBACKS').' as cb', 'cb.order_id', '=', 'mo.order_id')
				    $query->where('mo.order_type', $this->config->get('constants.ORDER.TYPE.IN_STORE'))
						  ->whereIn('mo.pay_through', [$this->config->get('constants.ORDER.PAID_THROUGH.SHOP_AND_EARN')]);
							
					break; 
				default:
					
					break;
			}
		}
		
		if (isset($orderby) && isset($order))
        {
            $query->orderby($orderby, $order);
        }
        else
        {
            $query->orderby('mo.order_id', 'DESC');
        }
		
        if ($count)
        {         
            return $query->count();			
        }
        else
        {			
			
            if (isset($length))
            {  
                $query->skip($start)->take($length);
            } 
            $orders = $query->leftjoin($this->config->get('tables.ACCOUNT_RATINGS').' as rt', function($rt)
                    {
                        $rt->on('rt.relative_post_id', '=', 'mo.order_id')
                        ->where('rt.post_type_id', '=', $this->config->get('constants.POST_TYPE.ORDER'));
                    }) 
                    ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'mo.currency_id')                   
                    ->selectRaw('mo.statementline_id, m.company_name as merchant, m.logo, ms.store_code, mo.order_code, mo.order_type, mo.pay_through, mo.bill_amount, mo.order_date, mo.status, ms.store_name, ms.store_logo, c.currency as code, mo.currency_id, CONCAT(ud.firstname,\' \',ud.lastname) as customer')
                    ->get();
            array_walk($orders, function(&$order) use ($account_type)
            {
                //$order->logo = asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$order->logo);				
				if ($order->store_logo != null)
				{
					$logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$order->store_logo);
				}
				else
				{
					$logo = asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$order->logo);
				}
				$order->logo = $logo;	
				$order->amt_color = '#D70000';    /* red */
				//$order->order_date = showUTZ($order->order_date, 'h:i A, d M y');
				$order->order_date = date('h:i A, d M y',strtotime('+330 minutes', strtotime($order->order_date))); 
                $order->bill_amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);
				
				//$order->rating = !empty($order->rating) && $order->rating != "0.00" ? round((float) $order->rating) : 0;				
				$order->status_class = $this->config->get('dispclass.order.status.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);
				//$order->status = trans('user/general.order.status.0.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);
				$order->status = ($order->status == 1) ? 'SUCCESS' : 'FAILURE';
				$order->amount = $order->bill_amount;
				unset($order->pay_through);
				unset($order->order_type);
				unset($order->mrlogo);
				unset($order->store_logo);
				if ($account_type == 3 || $account_type == 5)	{ 
					$order->remarks = trans('orders.'.$order->statementline_id.'.seller.list.remarks', (array) $order);
					$order->statementline = trans('orders.'.$order->statementline_id.'.seller.list.details', (array) $order);
				} else {
					$order->remarks = trans('orders.'.$order->statementline_id.'.user.list.remarks', (array) $order);
					$order->statementline = trans('orders.'.$order->statementline_id.'.user.list.details', (array) $order);
				}
				unset($order->code);
                unset($order->statementline_id);
                unset($order->currency_id);              
            });
            return $orders;
        }
    }
	
	public function myOrderDetails (array $arr = array(), $for_receipt = false)
    {	
        $account_id = null;        
        extract($arr);
        $order = DB::table($this->config->get('tables.ORDERS').' as mo')
                //->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                //->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                //->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as s', 's.account_id', '=', 'mo.account_id')
                //->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 's.country_id')
				->Join(Config('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                ->join($this->config->get('tables.SUPPLIER_MST').' as m', 'm.supplier_id', '=', 'mo.supplier_id')
                ->join($this->config->get('tables.STORES').' as msm', 'msm.store_id', '=', 'mo.store_id')
                ->join($this->config->get('tables.ADDRESS_MST').' as add', 'add.address_id', '=', 'msm.address_id')
				->leftjoin($this->config->get('tables.LOCATION_CITY').' as lc', 'lc.city_id', '=', 'add.city_id')
				->leftJoin($this->config->get('tables.CURRENCIES').' as co', 'co.currency_id', '=', 'mo.currency_id')					
                ->where('mo.order_code', $order_code)
                ->where('mo.is_deleted', $this->config->get('constants.OFF'))
				//->whereDate('mo.order_date', '>=', date('Y-m-d'))
				->selectRaw('mo.statementline_id, mo.payment_status, mo.currency_id, mo.order_date, m.logo, m.supplier_code, mo.qty, mo.bill_amount, mo.status, mo.pay_through, order_code, order_code as id, mo.order_id, mo.order_type, co.currency_symbol, co.decimal_places, co.currency as currency_code, msm.store_name, msm.store_code, add.address, msm.store_logo, oc.store_recd_amt as store_received_amt, oc.sys_recd_amt as system_received_amt, lc.city');               
        		
		if ($account_type == 2)	{ 	
			$order->where('mo.account_id', $account_id);
		} elseif ($account_type == 5 || $account_type == 3) {			
			$order->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                    ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                    ->addSelect('um.mobile', 'um.email', 'um.user_code', DB::raw("CONCAT_WS(' ',ad.firstname,ad.lastname) as customer"));
		}
        $order = $order->first();
        if (!empty($order))
        {
			$res = [];
            //$order->payment_status = trans('user/account.order.payment.status.'.$order->payment_status);            
			//$order->order_date = showUTZ($order->order_date, 'h:i A, d M Y');
			$order->order_date = date('h:i A, d M y',strtotime('+330 minutes', strtotime($order->order_date))); 
			if ($order->store_logo != null)
			{
				$logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$order->store_logo);
			}
			else
			{
				$logo = asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$order->logo);
			}
			$order->logo = $logo;            
            $order->amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);
            $order->bill_amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);
            $order->store_received_amt = CommonLib::currency_format($order->store_received_amt, $order->currency_id, true, false);
            $order->system_received_amt = CommonLib::currency_format($order->system_received_amt, $order->currency_id, true, false);            
            $order->status_class = $this->config->get('dispclass.user.order.status.'.$order->status);
            //$order->status = trans('user/general.order.status.0.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);
			$order->status = ($order->status == 1) ? 'SUCCESS' : 'FAILURE';
			
			$payments = DB::table($this->config->get('tables.PAY').' as p')
                    ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'p.from_currency_id')
                    ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', function($wl)
					{
						$wl->on('pt.payment_type_id', '=', 'p.payment_type_id');                                        
					}) 
                    ->leftJoin($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'p.from_wallet_id')
                    ->join($this->config->get('tables.APP_PAYMENT_MODES').' as apm', 'apm.pay_mode_id', '=', 'p.pay_mode_id')
                     ->leftjoin($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as pgr', function($pgr)
                    {
                        $pgr->on('pgr.relative_post_id', '=', 'p.pay_id');
                    })
                    ->where('p.order_id', $order->order_id)
                    ->where('p.is_deleted', $this->config->get('constants.OFF'))
                    ->selectRAW('cur.currency as code, cur.decimal_places, cur.currency_symbol, p.from_amount, p.status as payment_status, pt.payment_type, p.payment_id, IF(p.pay_mode_id=2,w.wallet_code,apm.code) as payment_mode') 
                    ->get();
					
			foreach ($payments as $pay)
            {
                if (in_array($pay->payment_mode, ['netbanking', 'credit-card', 'debit-card']))
				{
                    $order->payment_id = (String) $pay->payment_id;
					$res['mode'] = $pay->payment_mode . ' - PayU';
				} else {
					$order->payment_id = (String) $pay->payment_id;;
					$res['mode'] = 'Vi-Money';
				}	
				$order->payment_type = $res['mode'];
                //$order->payment_type = $pay->payment_type;
                $order->payment_status_class = $this->config->get('dispclass.payment_status.'.$pay->payment_status);
                $order->payment_status = trans('general.order_payment_status.'.$pay->payment_status);
                $lang = trans('orders.'.$order->statementline_id.'.user.payment_details');
				
                if (isset($lang[$pay->payment_mode]))
                    $order->{$pay->payment_mode} = CommonLib::currency_format($pay->from_amount, ['currency_symbol'=>$pay->currency_symbol, 'currency_code'=>$pay->code, 'decimal_places'=>$pay->decimal_places]);
            }		
			//return $order;
			if ($account_type == 2) {
				$res['remark'] = trans('orders.'.$order->statementline_id.'.user.remarks', (array) $order);
				$order->pay_through = trans('general.order.pay_through.'.$order->order_type.'.'.$order->pay_through);
				$format = trans('orders.'.$order->statementline_id.'.user.fields');
				$payment_fld = trans('orders.'.$order->statementline_id.'.user.payment_details', (array) $order);
				$properties = trans('orders.'.$order->statementline_id.'.user.properties');
			} elseif ($account_type == 3 || $account_type == 5) {
				$res['remark'] = trans('orders.'.$order->statementline_id.'.seller.remarks', (array) $order);
				$order->pay_through = trans('seller/general.order.pay_through.'.$order->order_type.'.'.$order->pay_through);
				$format = trans('orders.'.$order->statementline_id.'.seller.fields');
				$payment_fld = trans('orders.'.$order->statementline_id.'.seller.payment_details', (array) $order);
				$properties = trans('orders.'.$order->statementline_id.'.seller.properties');
			}
            $payment_details = [];
            if (is_array($format))
            {
                array_walk($format, function(&$v, $k) use($order, &$res)
                {
                    if (isset($order->{$k}))
                    {
                        $res[$k] = ['label'=>$v, 'value'=>(isset($order->{$k})) ? $order->{$k} : ''];
                    }
                });
            }
            if (is_array($payment_fld))
            {
                array_walk($payment_fld, function($v, $k) use($order, &$payment_details, $properties)
                {
                    if (isset($order->{$k}))
                    {
                        $payment_details[$k] = ['label'=>trans('orders.'.$order->statementline_id.'.seller.payment_details.'.$k, (array) $order), 'value'=>(isset($order->{$k})) ? $order->{$k} : ''];
                    }
                    if (!empty($properties) && is_array($properties) && array_key_exists($k, $properties))
                    {
                        foreach ($properties[$k] as $pro=> $field)
                        {
                            $payment_details[$k][$pro] = $order->{$field};
                        }
                    }
                });
            }
            $res['payment_details'] = (!$this->config->get('app.is_api')) ? $payment_details : array_values($payment_details);
            if ($for_receipt)
            {
                $res['customer'] = $order->customer;
                $res['mobile'] = $order->mobile;
            }
            $res['status'] = $order->status;
            $res['status_class'] = $order->status_class;
			$city = isset($order->city) ? ' - ' . $order->city : '';
            $res['store_name'] = $order->store_name . $city;
            $res['store_code'] = $order->store_code;
            $res['logo'] = $order->logo;
            $res['store_address'] = $order->address;
            $res['supplier_code'] = $order->supplier_code;
            $res['order_date'] = $order->order_date;
			
			/* if ($account_type == 3 || $account_type == 5) {
				$res['customer_name'] = $order->customer;
				$res['member_id'] = $order->user_code;;				
				$res['customer_mobile'] = $order->mobile;
				$res['customer_email'] = $order->email;
			} */
            //$res['qty'] = $order->qty;
            //$res['query_link'] = URL('faqs');
            //$res['download_invoice'] = route('user.payment.receipt', ['order_code'=>$order->order_code]);
            return ['res'=>$res, 'statementline_id'=>$order->statementline_id];		
        }
		return false;
    }
	
	public function myOrderDetails_bk131118 (array $arr = array(), $for_receipt = false)
    {	
        $account_id = null;        
        extract($arr);
        $order = DB::table($this->config->get('tables.ORDERS').' as mo')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as s', 's.account_id', '=', 'mo.account_id')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 's.country_id')
                ->join($this->config->get('tables.SUPPLIER_MST').' as m', 'm.supplier_id', '=', 'mo.supplier_id')
                ->join($this->config->get('tables.STORES').' as msm', 'msm.store_id', '=', 'mo.store_id')
                ->join($this->config->get('tables.ADDRESS_MST').' as add', 'add.address_id', '=', 'msm.address_id')
				->leftJoin($this->config->get('tables.CURRENCIES').' as co', 'co.currency_id', '=', 'mo.currency_id')	
				->leftjoin($this->config->get('tables.ACCOUNT_RATINGS').' as rt', function($rt)
				{
					$rt->on('rt.relative_post_id', '=', 'mo.order_id')
					->where('rt.post_type_id', '=', $this->config->get('constants.POST_TYPE.ORDER'));
				})
                ->where('mo.order_code', $order_code)
                ->where('mo.is_deleted', $this->config->get('constants.OFF'))				
                ->leftjoin($this->config->get('tables.PAY').' as py', function($j)
                {
                    $j->on('py.order_id', '=', 'mo.order_id')
                    ->where('py.payment_type_id', '=', $this->config->get('constants.PAYMENT_TYPES.CASH'));
                })
                ->leftjoin($this->config->get('tables.ACCOUNT_TRANSACTION').' as ac', function($rt) use ($account_id)
                {
                    $rt->on('ac.relation_id', '=', 'mo.order_id');
                    if (!empty($account_id))
                    {
                        $rt->where('ac.account_id', '=', $account_id);
                    }
                })			
                ->selectRaw('mo.statementline_id, ac.transaction_type, mo.payment_status, mo.currency_id, mo.order_date, m.logo, m.supplier_code, mo.bill_amount, mo.status,  py.payment_type_id, mo.pay_through, order_code, order_code as id, mo.order_id, mo.order_type, co.currency_symbol, co.decimal_places, co.currency as currency_code, IF(py.from_amount>0,py.from_amount,0) as collect_amt, ac.transaction_id')
                ->addSelect('msm.store_name', 'msm.store_code', 'msm.store_logo', 'add.address'); 
        if (!$for_receipt)
        {		
            $order->where('mo.account_id', $account_id);
        } else {
			$order->addSelect('um.mobile', DB::raw("CONCAT_WS(' ',ad.firstname,ad.lastname) as customer"));
		}
        $order = $order->first();
        if (!empty($order))
        {
			$res = [];
            $order->payment_status = trans('user/account.order.payment.status.'.$order->payment_status);            
			$order->order_date = showUTZ($order->order_date, 'h:i A, d M Y');
			if ($order->store_logo != null)
			{
				$logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$order->store_logo);
			}
			else
			{
				$logo = asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$order->logo);
			}
			$order->logo = $logo;
            //$order->logo = asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$order->logo);
            //$order->logo = asset('resources/uploads/default.png');	
            $order->amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);
            $order->bill_amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);
            $order->received_amount = CommonLib::currency_format($order->collect_amt, $order->currency_id, true, false);
            $order->collect_amt = CommonLib::currency_format($order->collect_amt, $order->currency_id, true, false);            
            $order->status_class = $this->config->get('dispclass.user.order.status.'.$order->status);
            //$order->status = trans('user/general.order.status.0.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);
			$order->status = ($order->status == 1) ? 'SUCCESS' : 'FAILURE';
			
            if ($order->order_type == $this->config->get('constants.ORDER.TYPE.IN_STORE') && $order->pay_through == $this->config->get('constants.ORDER.PAID_THROUGH.PAY'))
            {   				
                $pay = DB::table($this->config->get('tables.PAY').' as p')                                
								->join($this->config->get('tables.PAYMENT_TYPES').' as pt', function($wl)
								{
									$wl->on('pt.payment_type_id', '=', 'p.payment_type_id');                                        
								}) 
                                ->join($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as pgr', function($pgr)
                                {
                                    $pgr->on('pgr.relative_post_id', '=', 'p.pay_id');
                                }) 
								->leftjoin($this->config->get('tables.WALLET_LANG').' as wl', function($pgr)
                                {
                                    $pgr->on('wl.wallet_id', '=', 'p.from_wallet_id')
                                    ->where('p.payment_type_id', '=', $this->config->get('constants.PAYMENT_TYPES.WALLET'));
                                }) 
                                ->where('p.order_id', $order->order_id)
                                ->where('p.is_deleted', $this->config->get('constants.OFF'))
                                ->selectRAW('p.status, pt.payment_type, pt.payment_type as payment_desc, p.payment_id, pgr.response, wl.wallet as wallet_name')
								->first();

                if (!empty($pay))
                {
                    $order->payment_type = $pay->payment_desc;
                    $order->card = '';                     
					$order->payment_id = '';
                    $cards = ['CC'=>'Credit Card', 'DB'=>'Debit Card', 'NB'=>'Net Banking'];
					if (!empty($pay->response))
                    {
                        $pg_response = json_decode($pay->response);
                        $mode = (isset($pg_response->mode)) ? $pg_response->mode : '';                        
						$order->payment_id = (isset($pg_response->mihpayid)) ? $pg_response->mihpayid : '';
                        if ($mode != '')
                        {
                            $order->card = $cards[$mode];
                        }
                        else
                        {
                            $order->card = '';
                        }
                        $order->wallet_name = $pay->wallet_name;	
						$res['mode'] = 	$order->card . ' - PayU';
                    }                    
                } else {
					$order->payment_id = $order->transaction_id;
					$res['mode'] = 'Vi-Money';
				}				
            } 
            if ($order->order_type == $this->config->get('constants.ORDER.TYPE.IN_STORE') && $order->pay_through == $this->config->get('constants.ORDER.PAID_THROUGH.REDEEM'))
            {
				$wallet_codes = array_flip($this->config->get('constants.WALLETS'));
				$pay = DB::table($this->config->get('tables.PAY').' as p')
                                ->leftjoin($this->config->get('tables.WALLET_LANG').' as wl', function($wl)
                                {
                                    $wl->on('wl.wallet_id', '=', 'p.from_wallet_id');                                    
                                })                                
								->leftjoin($this->config->get('tables.PAYMENT_TYPES').' as ptl', function($wl)
								{
									$wl->on('ptl.payment_type_id', '=', 'p.payment_type_id');                                        
								}) 
                                ->where('p.order_id', $order->order_id)
                                ->where('p.is_deleted', $this->config->get('constants.OFF'))
                                ->selectRaw('p.to_amount as amount, p.payment_type_id, wl.wallet, ptl.payment_type, p.status, wl.wallet_id')
								->get();
			
				array_walk($pay, function(&$redeem, $key) use (&$order, $wallet_codes)
				{
					$redeem->paid_amount = $redeem->redeemed_amount = CommonLib::currency_format($redeem->amount, $order->currency_id);
					if ($redeem->wallet_id != '')
					{
						$order->$wallet_codes[$redeem->wallet_id] = $redeem->paid_amount;  
						$order->received_amt = '0.00';	
						unset($order->transaction_id);
					}
					else
					{
						$order->received_amt = $redeem->paid_amount;
					}
					unset($redeem->amount);
					unset($redeem->wallet);
					unset($redeem->payment_type_id);
				});
				
				$order->amount = $order->bill_amount;
                $payment_details = [];
                $order->amount = $order->bill_amount;						
            }            
            $res['remark'] = trans('transactions.'.$order->statementline_id.'.user.order.remarks', (array) $order);			
			$order->pay_through = trans('user/general.order.pay_through.'.$order->order_type.'.'.$order->pay_through);
			//return $order;
            unset($order->currency_symbol);
            unset($order->payment_type_id);
            unset($order->transaction_type);
            unset($order->currency_id);
            unset($order->decimal_places);
            unset($order->currency_code);
            unset($order->order_type);
            unset($order->order_id);
            unset($order->tip_amount);
			
			$format = trans('transactions.'.$order->statementline_id.'.user.order.fields');
            $payment_fld = trans('transactions.'.$order->statementline_id.'.user.order.payment_details');
            $properties = trans('transactions.'.$order->statementline_id.'.user.order.properties');
			if (is_array($format))
            {                
                array_walk($format, function(&$v, $k) use($order, &$res)
                {
                    if (isset($order->{$k}))
                    {
                        $res[$k] = ['label'=>$v, 'value'=>(isset($order->{$k})) ? $order->{$k} : ''];
                    }
                });
            }
            if (is_array($payment_fld))
            {
                array_walk($payment_fld, function(&$v, $k) use($order, &$payment_details, $properties)
                {
                    if (isset($order->{$k}))
                    {
                        $payment_details[$k] = ['label'=>$v, 'value'=>(isset($order->{$k})) ? $order->{$k} : ''];
                    }
                    if (!empty($properties) && is_array($properties) && array_key_exists($k, $properties))
                    {
                        foreach ($properties[$k] as $pro=> $field)
                        {
                            $payment_details[$k][$pro] = $order->{$field};
                        }
                    }
                });
            }
			$res['payment_details'] = array_values($payment_details);
			if ($for_receipt)
            {
                $res['customer'] = $order->customer;
                $res['mobile'] = $order->mobile;
            }
            $res['status'] = $order->status;
            $res['status_class'] = $order->status_class;
            $res['store_name'] = $order->store_name . ' - ' . $order->address;
            $res['store_code'] = $order->store_code;
            $res['logo'] = $order->logo;
            $res['store_address'] = $order->address;
            $res['supplier_code'] = $order->supplier_code;
            $res['order_date'] = $order->order_date;
            //$res['qty'] = $order->qty;
            //$res['query_link'] = URL('faqs');			
            //$res['download_invoice'] = route('user.payment.receipt', ['order_code'=>$order->order_code]);
			
            return ['res'=>$res, 'statementline_id'=>$order->statementline_id];			
        }
		return false;
    }
	
	public function profile_image_upload (array $arr)
    {	
        extract($arr);
        return $res = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
						->where('account_id', $account_id)
						->update(['profile_img'=>$docpath, 'updated_on'=>getGTZ()]);        
    }
	
	public function getOrderInfo($order_code){
		return DB::table($this->config->get('tables.ORDERS').' as mrd')
						->join($this->config->get('tables.SUPPLIER_MST').' as mm','mm.supplier_id','=','mrd.supplier_id')
                        ->join($this->config->get('tables.STORES').' as sm', 'sm.store_id', '=', 'mrd.store_id')
                        ->join($this->config->get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'mrd.store_id')
                        ->where('mm.is_deleted', $this->config->get('constants.OFF'))
                        ->where('mm.status', $this->config->get('constants.ON'))
                        ->where('sm.is_approved', $this->config->get('constants.ON'))
                        ->where('sm.status', $this->config->get('constants.ON'))
                        ->where('mrd.order_code', $order_code)
                        //->selectRaw('mrd.order_id,sm.store_id,sm.mrid,mm.mrbusiness_name,mm.mrcode,sm.mobile,sm.store_code,sm.store_name')
                        ->selectRaw('mrd.order_id, sm.store_id, sm.supplier_id as mrid, mm.company_name as mrbusiness_name, mm.supplier_code as mrcode, se.mobile_no as mobile, sm.store_code, sm.store_name')
                        ->first();
	}
	
	
	public function saveRatingsFeedbacks (array $arr = array())
    {	//return $arr;
        $post_id = null;
        $order_id = 0;
        extract($arr);
        $pre_rating = DB::table(config('tables.ACCOUNT_RATINGS'))
                ->where('account_id', $account_id)
                ->where('post_type_id', $post_type)
                ->where('relative_post_id', $post_id);
        if (isset($order_id) && !empty($order_id))
        {
            $pre_rating->where('order_id', $order_id);
        } 
        $ratingInfo = $pre_rating->select('rating', 'created_on')->first();
//        if (!isset($feedback) || empty($feedback))
//        {
//            $feedback = trans('general.rating.'.$rating);
//        }
        if (!empty($ratingInfo))
        {
            $pre_rating = $ratingInfo->rating;
            $created_date = date_create($ratingInfo->created_on);
            $now = date_create(date('Y-m-d H:i:s'));
            $interval = date_diff($created_date, $now);
            $interval = $interval->format("%a");
            if ($interval == $this->config->get('constants.RATING_TIMEUP')) //in days
            {
                if (DB::table($this->config->get('tables.ACCOUNT_RATINGS'))
                                ->where('account_id', $account_id)
                                ->where('post_type_id', $post_type)
                                ->where('relative_post_id', $post_id)
                                ->update(['rating'=>$rating, 'feedback'=>$feedback, 'updated_on'=>getGTZ()]))
                {
                    if ($pre_rating != $rating)
                    {
                        $this->updateRatings($post_type, $post_id, $rating, $pre_rating);
                       /*  if (isset($order_id) && !empty($order_id))
                        {
                            return $this->creditCustomerCashback(['order_id'=>$order_id]);
                        }  */
                    }
                    return true;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            if (DB::table($this->config->get('tables.ACCOUNT_RATINGS'))
                            ->insertGetID(['account_id'=>$account_id, 'relative_post_id'=>$post_id, 'order_id'=>$order_id, 'post_type_id'=>$post_type, 'feedback'=>$feedback, 'rating'=>$rating, 'created_on'=>getGTZ(), 'updated_on'=>getGTZ()]))
            {
                $this->updateRatings($post_type, $post_id, $rating);
                /* if (isset($order_id) && !empty($order_id))
                {
                    $this->creditCustomerCashback(['order_id'=>$order_id]);
                } */
                return true;
            }
        }
    }
	
	public function creditCustomerCashback (array $arr = array())
    {
		//return $arr;
        extract($arr);
        DB::beginTransaction();
        $cashback = DB::table($this->config->get('tables.CASHBACKS').' as c')
                ->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'c.order_id')
                ->join($this->config->get('tables.SUPPLIER_MST').' as m', 'm.supplier_id', '=', 'mo.supplier_id')
                ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_RATINGS').' as r', function($join)
                {
                    $join->on('r.relative_post_id', '=', 'mo.order_id')
                    ->on('r.account_id', '=', 'mo.account_id')
                    ->where('r.post_type_id', '=', $this->config->get('constants.POST_TYPE.ORDER'));
                })                
                ->selectRaw('c.cashback_id, c.currency_id, c.cashback_amount, mo.account_id, mo.order_code, mo.bill_amount, COALESCE(ms.store_name,m.company_name) as store_name')
                ->where('c.order_id', $order_id)
                ->where('c.status', $this->config->get('constants.CASHBACK_STATUS.PENDING'))                
                ->first();

        if (!empty($cashback))
        {
            $wallets = DB::table($this->config->get('tables.WALLET').' as w')
                    ->leftJoin($this->config->get('tables.WALLET_LANG').' as wl', function($join)
                    {
                        $join->on('wl.wallet_id', '=', 'w.wallet_id');
                        $join->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                    })
                    ->where('w.cashback_perc', '>', 0)
                    ->select('w.cashback_perc', 'w.wallet_id', 'wl.wallet')
                    ->get();
            if (!empty($wallets))
            {
                $cashbacks = [];
                $is_instant = $this->getSetting($this->config->get('constants.CASHBACK_INSTANT_CREDIT'));
                $is_credited = 0;
                if (($is_instant == $this->config->get('constants.ON')))
                {
                    foreach ($wallets as $wallet)
                    {
                        if ($wallet->cashback_perc > 0)
                        {
                            $credit_amt = ($cashback->cashback_amount * $wallet->cashback_perc) / 100;
                            if ($trans_id = $this->updateAccountTransaction([
                                'to_account_id'=>$cashback->account_id,
                                'to_wallet_id'=>$wallet->wallet_id,
                                'currency_id'=>$cashback->currency_id,
                                'amt'=>$credit_amt,
                                'relation_id'=>$cashback->cashback_id,
                                'credit_remark_data'=>['store_name'=>$cashback->store_name, 'order_code'=>$cashback->order_code, 'bill_amount'=>CommonLib::currency_format($cashback->bill_amount, $cashback->currency_id, true, true)],
                                'transaction_for'=>'CASHBACK']))
                            {
                                $is_credited++;
                                $cashbacks[] = [
                                    'trans_id'=>$trans_id,
                                    'cashback_amount'=>CommonLib::currency_format($credit_amt, $cashback->currency_id, true, true),
                                    'wallet'=>$wallet->wallet
                                ];
                            }
                        }
                    }
                    if ($is_credited == count($wallets))
                    {
                        DB::table($this->config->get('tables.CASHBACKS'))
                                ->where('cashback_id', $cashback->cashback_id)
                                ->update(['status'=>$this->config->get('constants.CASHBACK_STATUS.CONFIRMED'),
                                    'updated_on'=>getGTZ()]);
                        CommonLib::notify($cashback->account_id, 'user_get_cashback', [
                            'store'=>$cashback->store_name,
                            'order_id'=>$cashback->order_code,
                            'bill_amount'=>CommonLib::currency_format($cashback->bill_amount, $cashback->currency_id, true, true),
                            'cashbacks'=>$cashbacks,
                            'date'=>showUTZ('d-M-Y'),
                        ]);
                    }
                    $this->addReferralEarnings($this->config->get('constants.EARNINGS.COMMISSION_TYPE.REFERRAL_CASHBACK'), ['account_id'=>$cashback->account_id, 'amount'=>$cashback->cashback_amount, 'currency_id'=>$cashback->currency_id, 'relative_id'=>$order_id]);
                    DB::commit();
                    return true;
                }
            }
        }
        DB::rollback();
        return false;
    }
	
	
	private function updateRatings ($post_type, $post_id, $rating, $pre_rating = null)
    {		
        if (DB::table($this->config->get('tables.RATING'))
                        ->where('post_type_id', $post_type)
                        ->where('relative_post_id', $post_id)
                        ->count())
        {
            $r = [];
            $r['rating_'.$rating] = DB::raw('rating_'.$rating.'+1');
            $r['updated_on'] = getGTZ();
            if (!is_null($pre_rating))
            {
                $r['rating_'.$pre_rating] = DB::raw('rating_'.$pre_rating.'-1');
                $r['tot_rating'] = DB::raw('tot_rating-'.$pre_rating.'+'.$rating);
            }
            else
            {
                $r['rating_count'] = DB::raw('rating_count+1');
                $r['tot_rating'] = DB::raw('tot_rating+'.$rating);
            }
            if (DB::table($this->config->get('tables.RATING'))
                            ->where('post_type_id', $post_type)
                            ->where('relative_post_id', $post_id)
                            ->update($r))
            {
                return DB::table($this->config->get('tables.RATING'))
                                ->where('post_type_id', $post_type)
                                ->where('relative_post_id', $post_id)
                                ->update(array(
                                    'rating'=>DB::raw('(tot_rating/rating_count)'),
                ));
            }
        }
        else
        {
            return DB::table($this->config->get('tables.RATING'))
                            ->insertGetId(array(
                                'post_type_id'=>$post_type,
                                'relative_post_id'=>$post_id,
                                'rating_'.$rating=>1,
                                'tot_rating'=>$rating,
                                'rating_count'=>1,
                                'rating'=>$rating,
                                'updated_on'=>getGTZ()));
        }
    }
	
	public function getMessages (array $arr = array(), $count = false, $limit = 5)
    {	
        extract($arr);
        $res = DB::table($this->config->get('tables.ACCOUNT_NOTIFICATIONS').' as n')
                ->leftJoin($this->config->get('tables.ACCOUNT_NOTIFICATIONS_READ').' as st', function ($subquery) use($account_id)
                {
                    $subquery->on('st.notification_id', '=', 'n.notification_id')
                    ->where('st.account_id', '=', $account_id);
                })
                ->whereRaw('FIND_IN_SET('.$account_id.',n.account_ids)')
                ->where('n.is_deleted', $this->config->get('constants.OFF'))
                ->selectRaw('n.notification_id, n.data, n.created_on, st.read_on, n.is_starred')
                ->orderby('n.created_on', 'desc');
				
        if (isset($notification_type) && !empty($notification_type))
        {
			if ($notification_type == $this->config->get('constants.MESSAGE.TYPES.all'))
			{
				$res->whereIn('n.notification_type', [$this->config->get('constants.MESSAGE.TYPES.orders'),$this->config->get('constants.MESSAGE.TYPES.notification')]);
			}
			if ($notification_type == $this->config->get('constants.MESSAGE.TYPES.orders'))
			{
				$res->where('n.notification_type', $this->config->get('constants.MESSAGE.TYPES.orders'));
			}
			if ($notification_type == $this->config->get('constants.MESSAGE.TYPES.notification'))
			{
				$res->where('n.notification_type', $this->config->get('constants.MESSAGE.TYPES.notification'));
			}            
        }
        if (isset($notification_id) && !empty($notification_id))
        {
            $res->where('n.notification_id', $notification_id);
        }
        if ($count)
        {
            return $res->count();
        }
        else
        {
            if (isset($length))
            {
                $res->skip($start)->take($length);
            }
            $notifications = $res->get();
            foreach ($notifications as &$notification)
            {
                $notification->data = json_decode($notification->data);				
                $notification->data->notification->id = $notification->notification_id;				
                $notification->data->notification->created_on = showUTZ($notification->created_on, 'd-M-Y H:i:s');
                $notification->data->notification->icon = asset($notification->data->notification->icon);
                //$notification->data->notification->banner = asset('imgs/banner/220/150/image.jpg');
                $notification->data->notification->is_starred = $notification->is_starred;
                $notification->data->notification->is_read = !empty($notification->read_on) ? 1 : 0;
                $notification = $notification->data->notification;				
                unset($notification->sound);
                unset($notification->vibrate);
            }
            return (isset($notification_id) && !empty($notification_id) && isset($notifications[0])) ? $notifications[0] : $notifications;
        }
    }

    public function updateNotificationToken (array $arr = array())
    {	
        extract($arr);
        if (isset($account_id) && !empty($account_id))
        {
            return DB::table($this->config->get('tables.ACCOUNT_LOG'))
                            ->where('account_id', $account_id)
                            ->where('account_log_id', $account_log_id)
                            ->update(['fcm_registration_id'=>$fcm_registration_id]);
        }
        else
        {
            if (DB::table($this->config->get('tables.NOTIFICATION_SUBSCRIBERS'))
                            ->where('fcm_registration_id', $fcm_registration_id)
                            ->exists())
            {
                return DB::table($this->config->get('tables.NOTIFICATION_SUBSCRIBERS'))
                                ->where('fcm_registration_id', $fcm_registration_id)
                                ->where('is_deleted', $this->config->get('constants.ON'))
                                ->update(['is_deleted'=>$this->config->get('constants.OFF')]);
            }
            else
            {
                return DB::table($this->config->get('tables.NOTIFICATION_SUBSCRIBERS'))
                                ->insertGetID(['fcm_registration_id'=>$fcm_registration_id]);
            }
        }
    }
	
	public function toggleAppLock (array $arr = array())
    {
        extract($arr);
        return DB::table($this->config->get('tables.ACCOUNT_LOG'))
                        ->where('account_id', $account_id)
                        ->where('account_log_id', $account_log_id)
                        ->where('toggle_app_lock', '<>', $toggle_app_lock)
                        ->update(['toggle_app_lock'=>$toggle_app_lock]);
    }
	
	public function reviews_and_rating_list (array $arr = array(), $count = false)
    {	
        extract($arr);		
        $qry = DB::table($this->config->get('tables.ACCOUNT_RATINGS').' as ri')
                ->join($this->config->get('tables.ORDERS').' as mo', function($mo)
                {
                    $mo->on('mo.order_id', '=', 'ri.order_id')
						->on('mo.account_id', '=', 'ri.account_id');
                }) 
                ->join($this->config->get('tables.STORES').' as msm', 'msm.store_id', '=', 'mo.store_id')
                ->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'msm.supplier_id')
				->leftjoin($this->config->get('tables.ADDRESS_MST').' as adr', function($lf)
					{
						$lf->on('adr.address_id', '=', 'msm.address_id')->where('adr.address_type_id', '=', 1);
					}) 
				->leftjoin($this->config->get('tables.LOCATION_CITY').' as lc', 'lc.city_id', '=', 'adr.city_id')
                ->leftjoin($this->config->get('tables.RATING').' as rt', function($rt)
                {
                    $rt->on('rt.relative_post_id', '=', 'mo.store_id')
                    ->where('rt.post_type_id', '=', $this->config->get('constants.RATING_POST_TYPE.STORE'));
                })  
                //->where('ri.post_type_id', '=', $this->config->get('constants.RATING_POST_TYPE.STORE'))   
                ->where('ri.account_id', $account_id);
				
				 
        if ($count)
        {
            return $qry->count();
        }
        else
        {			
            if (isset($length))
            {
                $qry->skip($start)->take($length);
            }
            if (isset($orderby) && isset($order))
            {
                $qry->orderby($orderby, $order);
            }
            else
            {
                $qry->orderby('ri.created_on', 'DESC');
            }            
            $ratings = $qry->selectRAW('\'Store\' as post_type, ri.rating, ri.feedback, ri.created_on as updated_on, COALESCE(msm.store_name, mm.company_name) as store_name, msm.store_code, msm.store_logo as logo, mm.logo as mrlogo, rt.rating as overall_rating, lc.city')
                    ->get();
            array_walk($ratings, function(&$rating)
            {
				$city = isset($rating->city) ? ' - ' . $rating->city : '';				
                $rating->store_name = $rating->store_name . $city;
                $rating->overall_rating = !empty($rating->overall_rating) ? floatval($rating->overall_rating) : '';
                $rating->rating = !empty($rating->rating) && $rating->rating != "0.00" ? round((float) $rating->rating, 1) : 0;
                $rating->feedback = !empty($rating->feedback) ? $rating->feedback : '';                
				
				if (!empty($rating->logo))
				{
					$rating->logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$rating->logo);
				} else {
					$rating->logo = asset($this->config->get('path.SELLER.PROFILE_IMG_PATH.WEB').$store->mrlogo);
				}
				
                $rating->updated_on = (!empty($rating->updated_on) && $rating->updated_on != '0000-00-00 00:00:00' ) ? showUTZ($rating->updated_on, 'd-M-Y') : '';
                unset($rating->mrlogo);
                unset($rating->city);
            });
            return $ratings;
        }
    }
}
