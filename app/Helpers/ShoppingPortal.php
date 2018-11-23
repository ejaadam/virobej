<?php
namespace App\Helpers;

use DB;
use Illuminate\Support\Facades\Config;
use Mail;
use Log;
use Lang;
use TWMailer;

class ShoppingPortal
{

    public static function recentProducts ($token, $supplier_product_id = null)
    {
        $device_log = DB::table(Config::get('tables.DEVICE_LOG').' as dl')
                ->where('dl.token', $token)
                ->selectRaw('dl.device_log_id,account_id,dl.cookies')
                ->first();
        $recentPros = [];
        if (!empty($device_log))
        {
            $cookies = json_decode($device_log->cookies);
            if (empty($cookies))
            {
                $cookies = (object) [];
            }
            if (!isset($cookies->recentPros))
            {
                $cookies->recentPros = [];
            }
            if (!empty($supplier_product_id) && !in_array($supplier_product_id, $cookies->recentPros))
            {
                $cookies->recentPros[] = $supplier_product_id;
            }
            $recentPros = $cookies->recentPros;
            if ($supplier_product_id)
            {
                $query = DB::table(Config::get('tables.DEVICE_LOG'))
                        ->where('device_log_id', $device_log->device_log_id);
                if (!empty($device_log->account_id))
                {
                    $query->orWhere('account_id', $device_log->account_id);
                }
                $query->update(['cookies'=>json_encode($cookies)]);
            }
        }
        return $recentPros;
    }

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
            if (!empty($account_ids))
            {
                $query = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                        ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                        ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
                        ->where('am.is_deleted', Config::get('constants.OFF'))
                        ->where('am.status', Config::get('constants.ACTIVE'))
                        ->whereIn('am.account_id', $account_ids)
                        ->whereNotNull('am.email')
                        ->whereNotNull('am.mobile')
                        ->selectRaw('am.account_id, concat(ad.firstname," ",ad.lastname) as name, am.uname, am.email, am.mobile, ap.send_email, ap.send_sms, ap.send_notification, ap.is_mobile_verified, ap.is_email_verified');
                $users_list = $query->get();					
				
                //return $data = array_merge($data, Config::get('site_settings'));
                $settings = (object) Config::get('notify_settings.'.strtoupper($which));				
                if (!empty($settings))
                {					
                    foreach ($users_list as $user)
                    {
                        $data['name'] = $user->name;
                        $data['uname'] = $user->uname;
						//$user->email = 'jayaprakash.ejugiter@gmail.com';
						//$user->mobile = 9865797657;
                        if ($settings->email['status'] && ($email && ($must_send || ($user->send_email && $user->is_email_verified))))
                        {       
							self::email($user->email, $settings->email['view'], Lang::get($settings->email['subject']), $data, $preview);
                        }
                        if ($settings->sms['status'] && ($sms && ($must_send || ($user->send_sms && $user->is_mobile_verified))))
                        {   						
							self::sms($user->mobile, Lang::get($settings->sms['message'], $data));
                        }
                        if ($settings->notification['status'] && ($notification && ($must_send || $user->send_notification)))
                        {
                            PushNotification::send($user->account_id, Lang::get($settings->notification['title'], $data), Lang::get($settings->notification['body'], $data), $settings->notification['click_action']);
                        }
                    }
                    return $users_list;
                }
            }
        }
        return false;
    }
	
	public static function email ($emailid, $view, $subject, array $data = array(), $preview = false)
    {		
        $siteConfig = config('site_settings');		        
        $data['subject'] = $subject;		        
        $settings = json_decode(stripslashes($siteConfig['outbound_email_configure']));
        if ($preview)
        {
            echo view($view, $data)->render();
            exit;
        }
        if ($settings->service == 1)
        {
            if ($settings->driver == 2)
            {
                Config::set('mail.driver', $settings->sendgrid->driver);
                Config::set('mail.host', $settings->sendgrid->host);
                Config::set('mail.port', $settings->sendgrid->port);
                Config::set('mail.from', array('address'=>$siteConfig['noreplay_emailid'], 'name'=>$siteConfig['site_name']));
                Config::set('mail.encryption', $settings->sendgrid->encryption);
                Config::set('mail.username', $settings->sendgrid->username);
                Config::set('mail.password', $settings->sendgrid->password);
            }            
			
			TWMailer::send(array(
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
        $Settings = config('services.sms');
        $data = [
            'user'=>$Settings['user'],
            'key'=>$Settings['key'],
            'senderid'=>$Settings['senderid'],            
            'mobile'=>$mobile,
            //'message'=>str_replace(['$ ', '₹ ', '₱ ', '৳ ', '¥ ', '€ ', '£ ', '฿ '], '', trans('notifications.'.$key.'.sms', $arr)),
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
            if ($result === FALSE)
            {
                die('SMS Sending failed: '.curl_error($ch));
            }
            curl_close($ch);
            return $result;
        }
        return false;
    }		

    public static function getResponse ($uri = '', $method = 'GET', $param = array(), $withStatus = false)
    {
        $uri = str_ireplace(URL::to('/').'/', '', $uri);
        $self_request = Request::create($uri, $method, $param);
        $originalInput = Request::input();
        Request::replace($self_request->input());
        $response = Route::dispatch($self_request);
        if ($withStatus)
        {
            $status = $response->getstatusCode();
        }
        $response = $response->getContent();
        Request::replace($originalInput);
        return $withStatus ? (['status'=>$status, 'responseJSON'=>!empty($response) ? json_decode($response) : []]) : (!empty($response) ? json_decode($response) : []);
    }

    public static function getHTMLValidation ($key, array $dependency_values = array())
    {		
		$translated = false;
        $dependency_rules = ['required_with', 'required_if_set', 'required_if', 'editable', 'required_if_verified'];
        $editable = (isset($dependency_values['is_editable'])) ? $dependency_values['is_editable'] : 0;
        $visible = (isset($dependency_values['is_visible'])) ? $dependency_values['is_visible'] : 0;
        $session = session();
		$validations = array_get(array_merge(( request()->is('api/v1/seller/*') || request()->is('seller/*') ? ['seller'=>include('validations/seller.php')] : ( request()->is('admin/*') ? include('validation/admin.php') : ''))), $key);				
		$validations = is_array($validations) ? array_filter($validations) : [];		
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
			if (!is_array($custommsgs))
				{
					$custommsgs = trans($custommsgs);
					$translated = true;
				}
			$rules = array_key_exists('RULES', $validations) ? $validations['RULES'] : [];		
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
			if (!empty($labels))
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
								$c = explode(':', $r);
								$r = $c[0];
								if (isset($c[1]) && !empty($c[1]))
								{
									$cv = $c[1];
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
									case 'min':
										if (isset($arr[$id]['attr']['type']) && $arr[$id]['attr']['type'] == 'number')
										{
											$arr[$id]['attr']['min'] = $cv;
											$arr[$id]['attr']['data-tooShort'] = str_replace(array(':min'), ['min'=>"$cv"], $msg['numeric']);
										}
										else
										{
											$arr[$id]['attr']['minLength'] = $cv;
											$arr[$id]['attr']['data-tooShort'] = str_replace(array(':min'), ['min'=>"$cv"], $msg['string']);
										}
										break;
									case 'max':
										if (isset($arr[$id]['attr']['type']) && $arr[$id]['attr']['type'] == 'number')
										{
											$arr[$id]['attr']['max'] = $cv;
											$arr[$id]['attr']['data-tooLong'] = str_replace(array(':max'), ['max'=>"$cv"], $msg['numeric']);
										}
										else
										{
											$arr[$id]['attr']['maxLength'] = $cv;
											$arr[$id]['attr']['data-tooLong'] = str_replace(array(':max'), ['max'=>"$cv"], $msg['string']);
										}
										break;
									case 'regex':
										$arr[$id]['attr']['type'] = 'text';
										$arr[$id]['attr']['pattern'] = str_replace(['/^', '$/'], ['', ''], $cv);
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
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
									case 'password':
										$arr[$id]['attr']['type'] = 'password';
										$arr[$id]['attr']['pattern'] = '\S{6,16}';
										$arr[$id]['attr']['data-patternMismatch'] = str_replace(array(':regex'), ['regex'=>"$cv"], $msg);
										$arr[$id]['attr']['is_editable'] = $editable;
										$arr[$id]['attr']['is_visible'] = $visible;
										break;
									case 'security_pin':
										$arr[$id]['attr']['type'] = 'password';
										$arr[$id]['attr']['pattern'] = '[0-9]{4}';
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
