<?php
namespace App\Helpers;

use DB;
use Illuminate\Support\Facades\Config;
use Mail;
use Log;
use Lang;
use TWMailer;

class CommonNotifSettings
{    

    /**
     * @param string $which which mail to be send
     * @param int|array $id notification to whom single id or array of ids
     * @param int $account_type_id user type id <i>(default 2-customer)</i>
     * @param array $data datas required to frame a content <i>(default empty)</i>
     * @param bool $email send email <i>(default true)</i>
     * @param bool $sms send sms <i>(default true)</i>
     * @param bool $notification send notification <i>(default true)</i>
     * @param bool $must_send it must be sent without any checking <i>(default false)</i>
     *
     * @return array|false sent user list or false
     */
    public static function notify ($which, $id, $account_type_id = 2, $data = [], $email = true, $sms = true, $notification = true, $must_send = false, $preview = false)
    {		
        $id = is_array($id) ? array_filter($id) : [$id];
		$account_ids = [];
        if (!empty($id))
        {
            switch ($account_type_id)
            {
                case Config::get('constants.ACCOUNT_TYPE.SELLER'):
                    $account_ids = DB::table(Config::get('tables.SUPPLIER_MST'))
                            ->whereIn('supplier_id', $id)
                            ->orwhereIn('account_id', $id)
                            ->lists('account_id');
                    break;
                case Config::get('constants.ACCOUNT_TYPE.ADMIN'):
                    $account_ids = DB::table(Config::get('tables.ADMIN_MST'))
                            ->where('status', Config::get('constants.ACTIVE'))
                            ->whereIn('admin_id', $id)
                            ->lists('account_id');
                    break;                
                default:
                    $account_ids = $id;
            }
            $account_ids = array_filter($account_ids);
		}
		if (!empty($account_ids))
		{
			$query = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
					->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
					->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
					->where('am.is_deleted', Config::get('constants.OFF'))
					->where('am.status', Config::get('constants.ACTIVE'))
					->whereIn('am.account_id', $account_ids)
					//->whereNotNull('am.email')
					//->whereNotNull('am.mobile')
					->selectRaw('am.account_id, concat(ad.firstname," ",ad.lastname) as name, am.uname, am.email, am.mobile, ap.send_email, ap.send_sms, ap.send_notification as push_notification, ap.is_mobile_verified, ap.is_email_verified');
			$user = $query->first();	
		} else {
			$user = (object) [
					'account_id'=>null,
					'name'=>isset($data['name']) && !empty($data['name']) ? $data['name'] : null,
					'uname'=>isset($data['uname']) && !empty($data['uname']) ? $data['uname'] : null,
					'mobile'=>isset($data['mobile']) && !empty($data['mobile']) ? $data['mobile'] : null,
					'email'=>isset($data['email']) && !empty($data['email']) ? $data['email'] : null,
					'full_name'=>isset($data['full_name']) && !empty($data['full_name']) ? $data['full_name'] : null,
					'is_email_verified'=>isset($data['is_email_verified']) && !empty($data['is_email_verified']) ? $data['is_email_verified'] : false,
					'is_mobile_verified'=>isset($data['is_mobile_verified']) && !empty($data['is_mobile_verified']) ? $data['is_mobile_verified'] : false,
					'push_notification'=>isset($data['push_notification']) && !empty($data['push_notification']) ? $data['push_notification'] : false,
					'lang'=>isset($data['lang']) && !empty($data['lang']) ? $data['lang'] : 'en'
				];
		}
		
		$settings = (object) Config::get('notify_settings.'.strtoupper($which));
		if (!empty($settings))
		{					
			$user->mobile = isset($data['mobile']) && !empty($data['mobile']) ? $data['mobile'] : (isset($new['mobile']) && !empty($new['mobile']) ? $new['mobile'] : $user->mobile);
			$user->email = isset($data['email']) && !empty($data['email']) ? $data['email'] : (isset($new['email']) && !empty($new['email']) ? $new['email'] : $user->email);
			$user->store_code = isset($data['store_code']) && !empty($data['store_code']) ? $data['store_code'] : (isset($new['store_code']) && !empty($new['store_code']) ? $new['store_code'] : '');
			$user->name = isset($data['name']) && !empty($data['name']) ? $data['name'] : (isset($new['name']) && !empty($new['name']) ? $new['name'] : '');
			$user->store = isset($data['store']) && !empty($data['store']) ? $data['store'] : (isset($new['store']) && !empty($new['store']) ? $new['store'] : '');
			$user->merchant = isset($data['merchant']) && !empty($data['merchant']) ? $data['merchant'] : (isset($new['merchant']) && !empty($new['merchant']) ? $new['merchant'] : '');
			$user->customer_id = isset($data['user_mobile']) && !empty($data['user_mobile']) ? $data['user_mobile'] : (isset($new['user_mobile']) && !empty($new['user_mobile']) ? $new['user_mobile'] : '');
			$user->bill_amount = isset($data['bill_amount']) && !empty($data['bill_amount']) ? $data['bill_amount'] : (isset($new['bill_amount']) && !empty($new['bill_amount']) ? $new['bill_amount'] : '');
			$user->msg = isset($data['message']) && !empty($data['message']) ? $data['message'] : (isset($new['message']) && !empty($new['message']) ? $new['message'] : '');
			
			//$user->email = 'ejdevteam@gmail.com';
			//$user->email = 'jayaprakash.ejugiter@gmail.com';
			//$user->mobile = '9865797657';
		
			$data = array_merge($data, (array) $user);				
			
			//$data = array_merge($data, (array) config('settings'));
			//print_r($data);exit;					
			//$data['name'] = $user->name;
			//$data['uname'] = $user->uname;
			//$user->email = 'jayaprakash.ejugiter@gmail.com';	
			
			if ($settings->email['status'] && ($email && ($must_send || ($user->send_email && $user->is_email_verified))))
			{					
				self::email($user->email, $settings->email['view'], Lang::get($settings->email['subject']), $data, $preview);
			}					
			if ($settings->sms['status'] && ($sms && ($must_send || ($user->send_sms && $user->is_mobile_verified))))
			{ 							
				self::sms($user->mobile, Lang::get($settings->sms['message'], $data));
			}
			if ($settings->notification['status'] && ($notification && ($must_send || $user->push_notification)))
			{												
				$data['route'] = '';
				$data['icon'] = '';
				self::notification($user->account_id, $which, $data, $data['route'], $data['icon']);
			}					
			return true;
			//return $users_list;
		}
        return false;
    }
	
	public static function notification ($account_id, $which, array $data = array(), $route = '', $icon = '')
    {	
        $siteConfig = config('settings');
        $account_id = is_array($account_id) ? $account_id : [$account_id];
        $icon = !empty($icon) ? $icon : $siteConfig->favicon;
        $route = !empty($route) ? $route : '';
        $fdata = array_filter($data, function($s)
        {
            return is_array($s) || is_object($s) ? false : true;
        });
        $data['lang_id'] = isset($data['lang_id']) ? $data['lang_id'] : config('app.locale_id');
		$title = Config::get('notify_settings.'.strtoupper($which).'.notification.title');		
		$msg = trans(Config::get('notify_settings.'.strtoupper($which).'.notification.body'), $data);
		$type = Config::get('notify_settings.'.strtoupper($which).'.notification.type');        
        $fcm_registration_ids = DB::table(Config::get('tables.ACCOUNT_LOG').' as dl')
                ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', function($ap)
                {
                    $ap->on('ap.account_id', '=', 'dl.account_id')
                    ->where('ap.send_notification', '=', Config::get('constants.ACTIVE'));
                })
                ->whereIn('dl.account_id', $account_id)
                ->whereNotNull('dl.fcm_registration_id')
                ->lists('dl.fcm_registration_id');
        $registatoin_ids = array_values(array_unique(array_filter($fcm_registration_ids)));
        if (!empty($registatoin_ids))
        {
            $message_data = [];
            $message_data['data'] = [
                'notification'=>[
                    'title'=>$title,
                    'body'=>$msg,
                    'click_action'=>$route,
                    'icon'=>$icon,
                    'color'=>'#111111',
                    'sound'=>true,
                    'vibrate'=>true,
                ]
            ];
            $notifications = [];
            $notifications['account_ids'] = implode(',', array_filter($account_id));
            $notifications['created_on'] = getGTZ();
            $notifications['notification_type'] = $type;
            $notifications['data'] = json_encode($message_data['data']);
            $message_data['data']['notification']['id'] = DB::table(config('tables.ACCOUNT_NOTIFICATIONS'))
                    ->insertGetId($notifications);
            $message_data['registration_ids'] = $registatoin_ids;
            $Settings = config('services.google');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $Settings['fcm_url']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: key='.$Settings['api_key'], 'Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message_data));
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                die('Notification Sending failed: '.curl_error($ch));
            }
            curl_close($ch);
            return $result;
        }
        return false;
    }
	
	public static function email ($emailid, $view, $subject, array $data = array(), $preview = true)
    {		
        if (substr($emailid, 0, 4) == 'jaya')
        {
            $emailid = 'jayaprakash.ejugiter@gmail.com';
        }
        elseif (substr($emailid, 0, 4) == 'gopi')
        {
            $emailid = 'gopi.ejugiter@gmail.com';
        }
        elseif (substr($emailid, 0, 4) == 'ramy')
        {
            $emailid = 'ramya.ejugiter@gmail.com';
        }
		elseif (substr($emailid, 0, 4) == '1992')
        {
            $emailid = 'ramya281992@gmail.com';
        }
		//print_R($emailid);exit;
		//$preview = true;
		//$emailid = 'ejdevteam@gmail.com';
		//$emailid = 'jayaprakash.ejugiter@gmail.com';
        $siteConfig 	 = config('site_settings');		        
        $data['subject'] = $subject;		        
        $settings 		 = json_decode(stripslashes($siteConfig['outbound_email_configure']));		
        if ($preview)
        {			
            echo view($view, $data)->render();
            exit;
        }
		//print_r($emailid);exit;
        if ($settings->service == 1)
        {
            if ($settings->driver == 2)
            {
				//return $siteConfig;
                Config::set('mail.driver', $settings->sendgrid->driver);
                Config::set('mail.host', $settings->sendgrid->host);
                Config::set('mail.port', $settings->sendgrid->port);
                Config::set('mail.from', array('address'=>$siteConfig['noreplay_emailid'], 'name'=>$siteConfig['site_name']));
                Config::set('mail.encryption', $settings->sendgrid->encryption);
                Config::set('mail.username', $settings->sendgrid->username);
                Config::set('mail.password', $settings->sendgrid->password);
            }		
			
		$data=	TWMailer::send(array(
				 'to'=>$emailid, 
				 'subject'=>$subject,
				 'view'=>$view,
				 'data'=>$data,
				 'from'=>$siteConfig['noreplay_emailid'],
				 'fromname'=>$siteConfig['site_name']), (object) $siteConfig);		


        }
        return false;
    }
	
	public static function sms ($mobile, $message, array $arr = array(), $lang = 'en')
    {
	    $gopi = 9952106187;
        $jayaprakash = 9865797657;
        $parthiban = 9626128834;
        $sriram = 9750379244;
        $ramya = 8248010856;
        $ramya2 = 9940650775;
        $suresh = 9952300725;
        
        if (substr($mobile, 0, 2) == 11)
        {
            $mobile = $jayaprakash;
        }
        elseif (substr($mobile, 0, 2) == 22)
        {
            $mobile = $ramya;
        }
		elseif (substr($mobile, 0, 2) == 33)
        {
            $mobile = $ramya2;
        } 
        
        $Settings = config('services.sms');
        $data = [
            'user'=>$Settings['user'],
            'key'=>$Settings['key'],
            'senderid'=>$Settings['senderid'],            
            'mobile'=>$mobile,            
            'message'=>str_replace(['$ ', '₹ ', '₱ ', '৳ ', '¥ ', '€ ', '£ ', '฿ '], '', $message),
            'accusage'=>1
        ];
		
        if (!empty(trim($data['message'])))
        {
            $ch = curl_init($Settings['url']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
			//print_r($result);exit;
            if ($result === FALSE)
            {
                die('SMS Sending failed: '.curl_error($ch));
            }
            curl_close($ch);
            return $result;
        }  
        return false;
    }	
	
    public static function getHTMLValidation ($key, array $dependency_values = array())
    {	 
		$translated = false;
        $dependency_rules = ['required_with', 'required_if_set', 'required_if', 'editable', 'required_if_verified'];
        $editable = (isset($dependency_values['is_editable'])) ? $dependency_values['is_editable'] : 0;
        $visible = (isset($dependency_values['is_visible'])) ? $dependency_values['is_visible'] : 0;
		$userInfo 	 = session()->has(config('app.role'))?(object) session()->get(config('app.role')):[];
		//$userInfo->currency_code = 'USD';
		//print_r($userInfo);exit;
		//$validations = array_get(array_merge(( request()->is('api/v1/seller/*') || request()->is('seller/*') ? ['seller'=>include('validations/seller.php')] : ( request()->is('admin/*') ? include('validations/admin.php') : ''))), $key);			
		$validations = array_get(array_merge(( request()->is('api/v1/seller/*') || request()->is('seller/*') ? ['seller'=>include('validations/seller.php')] : ( request()->is('admin/*') ? include('validations/admin.php') : ( request()->is('api/v1/user/*') ? ['user'=>include('validations/user_api.php')] : '')))), $key);					
		//print_r($validations);exit;
		$validations = is_array($validations) ? array_filter($validations) : [];
		//print_r($validations);exit;
		if (!empty($validations))
        {
			$labels = array_key_exists('LABELS', $validations) ? $validations['LABELS'] : [];
			if (!is_array($labels))
            {
                $labels = trans($labels);
                $translated = true;
            }				
			$attributes = array_key_exists('ATTRIBUTES', $validations) ? $validations['ATTRIBUTES'] : [];		
			$custommsgs = array_key_exists('MESSAGES', $validations) ? $validations['MESSAGES'] : [];		
			//print_r($custommsgs);exit;	
			if (!is_array($custommsgs))
				{
					$custommsgs = trans($custommsgs);
					$translated = true;
				}
			$rules = array_key_exists('RULES', $validations) ? $validations['RULES'] : [];		
			//print_r($rules);exit;	
			$msgs = trans('validation');		
			array_walk($msgs, function(&$value, $key)use($custommsgs)
			{
				if (array_key_exists($key, $custommsgs))
				{
					$value = $custommsgs[$key];
				}
			});	
			$msgs = array_merge($msgs, array_except($custommsgs, array_keys($msgs)));		
			array_walk($msgs, function(&$value, $key)use($custommsgs)
			{
				if (array_key_exists($key, $custommsgs))
				{
					$value = $custommsgs[$key];
				}
			});
			$msgs = array_merge($msgs, array_except($custommsgs, array_keys($msgs)));
			$arr = [];			
			if(!empty($labels))
			{
				$new_label = [];
					array_walk($labels, function($label, $id) use(&$new_label, &$arr, $attributes, $rules, $msgs, $dependency_rules, $dependency_values, $translated, $editable, $visible)
					{
						if (array_key_exists($id, $rules))
						{
							$rule = explode('|', $rules[$id]);
							$dep = in_array(explode(':', $rule[0])[0], $dependency_rules);
							$add = true;
							if ($dep)
							{
								$params = explode(':', $rule[0]);
								$pairs = explode(',', $params[1]);

								switch ($params[0])
								{
									case 'required_with':
										if (!isset($dependency_values[$pairs[0]]))
										{
											$add = false;
										}
										break;
									case 'required_if':
										if (!isset($dependency_values[$pairs[0]]) || (isset($dependency_values[$pairs[0]]) && !in_array($dependency_values[$pairs[0]], array_slice($pairs, 1))))
										{
											$add = false;
										}
										break;
									case 'required_if_set':
										array_walk($pairs, function(&$pair) use($params, $dependency_values, &$add)
										{
											$field = explode('~', $pair);
											if (!isset($dependency_values[$field[0]]) || (isset($dependency_values[$field[0]]) && $dependency_values[$field[0]] != $field[1]))
											{
												$add = false;
											}
										});
										break;
									case 'required_if_verified':
										if (!isset($dependency_values[$pairs[0]]) || (isset($dependency_values[$pairs[0]]) && !in_array($dependency_values[$pairs[0]], array_slice($pairs, 1))))
										{
											$add = false;
										}
										break;
								}
							}

							if (!$dep || ($dep && $add))
							{
								$new_label[$id] = $translated ? $label : trans($label);
							}
						}
					});
					//print_r($new_label);exit;
					array_walk($new_label, function($label, $id) use(&$arr, $attributes, $rules, $msgs, $dependency_rules, $dependency_values, $editable, $visible)
					{
						$arr[$id] = [];
						$arr[$id]['attr'] = is_array($attributes) && array_key_exists($id, $attributes) ? $attributes[$id] : [];
						$arr[$id]['label'] = $label;
						$arr[$id]['attr']['name'] = $n = self::dotToArrayName($id);
						//print_r($arr);exit;
						if (isset($arr[$id]['attr']['list']))
						{
							$arr[$id]['list'] = $arr[$id]['attr']['list'];
							unset($arr[$id]['attr']['list']);
							switch ($arr[$id]['attr']['type'])
							{
								case 'checkbox':
								case 'radio':
									break;
								case 'select':
									break;
							}
						}
						else
						{
							$arr[$id]['attr']['placeholder'] = $arr[$id]['attr']['title'] = $arr[$id]['label'];
						}
						if (array_key_exists($id, $rules))
						{
							$rule = explode('|', $rules[$id]);
							array_walk($rule, function($r) use(&$arr, $id, $msgs, $dependency_rules, $dependency_values, $editable, $visible)
							{
								$cv = null;
								$field_name = null;
								$c = explode(':', $r);
								$r = $c[0];
								if (isset($c[1]) && !empty($c[1]))
								{
							        $field_name = $cv = $c[1];									
									switch ($r)
									{
										case 'required_with':
											if (strstr($cv, ',') >= 0)
											{
												$fields = explode(',', $cv);
												$cv = $fields;
											}
											array_walk($cv, function(&$v) use($arr)
											{
												$v = $arr[$v]['label'];
											});
											$cv = implode(', ', $cv);
											break;
										case 'required_if':
											if (strstr($cv, ',') >= 0)
											{
												$fields = explode(',', $cv);
												$cv = $fields;
											}
											break;
										case 'required_if_set':

											if (strstr($cv, ',') >= 0)
											{
												$fields = explode(',', $cv);
												array_walk($fields, function(&$field)
												{
													if (strstr($field, '~') >= 0)
													{
														$field = explode('~', $field);
													}
												});
												$cv = $fields;
											}

											break;
										case 'same':
											$cv = $arr[$cv]['label'];
											break;	
									}
								}
								$msg = isset($msgs[$id.'.'.$r]) ? $msgs[$id.'.'.$r] : (isset($msgs[$r]) ? $msgs[$r] : '');
								$msg = str_replace(array(':attribute'), ['attribute'=>$arr[$id]['label']], $msg);
								switch ($r)
								{
									case 'required':
										$arr[$id]['attr']['required'] = 1;
										$arr[$id]['attr']['data-valueMissing'] = $msg;
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'accepted':
										$arr[$id]['attr']['type'] = 'checkbox';
										$arr[$id]['attr']['required'] = 1;
										$arr[$id]['attr']['data-valueMissing'] = $msg;
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'required_with':
										$arr[$id]['attr']['required'] = 1;
										$arr[$id]['attr']['data-valueMissing'] = str_replace(array(':values'), ['value'=>$cv], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'required_if':
										$arr[$id]['attr']['required'] = 1;
										$arr[$id]['attr']['data-valueMissing'] = str_replace(array(':other', ':value'), ['other'=>$arr[$cv[0]]['label'], 'value'=>$dependency_values[$cv[0]]], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'required_if_set':
										$arr[$id]['attr']['required'] = 1;
										$sets = [];
										array_walk($cv, function($v) use(&$sets, $dependency_values, $msg, $arr)
										{
											$sets[] = str_replace([':other', ':value'], ['other'=>$arr[$v[0]]['label'], 'value'=>$dependency_values[$v[0]]], $msg['sets']);
										});
										$sets = implode($msg['concat'], $sets);
										$arr[$id]['attr']['data-valueMissing'] = str_replace([':sets'], ['set'=>$sets], $msg['msg']);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;

										break;
									case 'numeric':
										$arr[$id]['attr']['type'] = 'number';
										$arr[$id]['attr']['data-typeMismatch'] = $msg;
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;

										break;
									case 'digits':
										$arr[$id]['attr']['type'] = 'number';
										$arr[$id]['attr']['data-typeMismatch'] = $msg;
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										if (!empty($cv))
										{
											$arr[$id]['attr']['min'] = (int) str_pad('', $cv, 1);
											$arr[$id]['attr']['data-tooShort'] = str_replace(array(':digits'), ['digits'=>str_pad('', $cv, 1)], $msg);
											$arr[$id]['attr']['max'] = (int) str_pad('', $cv, 9);
											$arr[$id]['attr']['data-tooLong'] = str_replace(array(':digits'), ['digits'=>str_pad('', $cv, 9)], $msg);
										}
										break;
									case 'email':
										$arr[$id]['attr']['type'] = 'email';
										$arr[$id]['attr']['data-typeMismatch'] = $msg;
										break;
									case 'url':
										$arr[$id]['attr']['type'] = 'url';
										$arr[$id]['attr']['data-typeMismatch'] = $msg;
										break;
									case 'alpha':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = '/^[a-zA-Z]*$/';
										$arr[$id]['attr']['data-patternMismatch'] = $msg;
										break;
									case 'alpha_num':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = '/^[a-zA-Z0-9]*$/';
										$arr[$id]['attr']['data-patternMismatch'] = $msg;
										break;
									case 'regex':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = str_replace(['/^', '$/'], ['', ''], $cv);
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'min':
										if (isset($arr[$id]['attr']['type']) && $arr[$id]['attr']['type'] == 'number')
										{
											$arr[$id]['attr']['min'] = $cv;
											$arr[$id]['attr']['data-tooShort'] = str_replace(array(':min'), ['min'=>"$cv"], isset($msg['numeric'])?$msg['numeric']:$msg);
										}
										else
										{
											$arr[$id]['attr']['minLength'] = $cv;
											$arr[$id]['attr']['data-tooShort'] = str_replace(array(':min'), ['min'=>"$cv"],  isset($msg['string'])?$msg['string']:$msg);
										}
										break;
									case 'max':
										if (isset($arr[$id]['attr']['type']) && $arr[$id]['attr']['type'] == 'number')
										{
											$arr[$id]['attr']['max'] = $cv;
											$arr[$id]['attr']['data-tooLong'] = str_replace(array(':max'), ['max'=>"$cv"], isset($msg['numeric'])?$msg['numeric']:$msg);
										}
										else
										{
											$arr[$id]['attr']['maxLength'] = $cv;
											$arr[$id]['attr']['data-tooLong'] = str_replace(array(':max'), ['max'=>"$cv"], isset($msg['string'])?$msg['string']:$msg);
										}
										break;
									
									case 'sometimes':
										// $arr[$id]['attr']['type'] 				= 'text';
										$arr[$id]['attr']['data-typeMismatch'] = $msg;

										if (isset($dependency_values['is_verified']) && ($dependency_values['is_verified'] == 1))
										{
											$arr[$id]['attr']['is_visible'] = 1;
											$arr[$id]['attr']['is_editable'] = 1;
										}
										else
										{
											$arr[$id]['attr']['is_visible'] = 0;
											$arr[$id]['attr']['is_editable'] = 0;
										}
										break;
									case 'bank_editable':
										//$arr[$id]['attr']['type'] 				= 'text';
										$arr[$id]['attr']['data-typeMismatch'] = $msg;
										$arr[$id]['attr']['is_visible'] = $visible;
										$arr[$id]['attr']['is_editable'] = $editable;
										break;
									case 'required_if_bank_verified':
										//$arr[$id]['attr']['type'] 				= 'select';
										$arr[$id]['attr']['data-typeMismatch'] = $msg;

										if (isset($dependency_values['is_verified']) && ($dependency_values['is_verified'] == 1))
										{
											$arr[$id]['attr']['is_visible'] = 1;
											$arr[$id]['attr']['is_editable'] = 1;
										}
										else
										{
											$arr[$id]['attr']['is_visible'] = 0;
											$arr[$id]['attr']['is_editable'] = 0;
										}
										break;
									case 'first_name':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = '[a-zA-Z\s]{3,50}';
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'last_name':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = '[a-zA-Z\s]{1,50}';
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'username':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = '(([A-Za-z0-9_-]{3,20})|([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}))';
                                        /* $arr[$id]['attr']['data-patternMismatch'] = $msg; */
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'password':
										$arr[$id]['attr']['type'] = 'password';
										$arr[$id]['attr']['pattern'] = '\S*'; //(?=.*[a-zA-Z])[0-9a-zA-Z!#$%&?]
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'security_pin':
										$arr[$id]['attr']['type'] = 'password';
										$arr[$id]['attr']['pattern'] = '[0-9]*'; //'[0-9]{4}';
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'business_name':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = '[A-Za-z0-9\s]{3,40}';
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										
									case 'business_name':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = '[A-Za-z0-9\s]{3,40}';
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
										
                                    case 'same':
										$arr[$id]['attr']['data-confirm'] = $field_name;
										$arr[$id]['attr']['data-confirm-err'] = str_replace(array(':other'), ['other'=>"$cv"], $msg);
								}
							});
						}
					});
				}
				
			return $arr;
		}  	
        return false;
    }

    public static function dotToArrayName ($array)
    {
        $formatted = [];
        if (is_array($array))
        {
            array_walk($array, function($str, $k) use(&$formatted)
            {
                $k = str_replace('.', '][', implode('[', explode('.', $k, 2))).(count(explode('.', $k, 2)) > 1 ? ']' : '');
                $formatted_array[$k] = $str;
            });
        }
        else
        {
            $formatted = str_replace('.', '][', implode('[', explode('.', $array, 2))).(count(explode('.', $array, 2)) > 1 ? ']' : '');
        }
        return $formatted;
    }

}
