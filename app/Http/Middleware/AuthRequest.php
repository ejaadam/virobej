<?php
use App\Models\Commonsettings;
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Request;
use Response;
use Illuminate\Support\Facades\Cookie;
use DB;
use Config;

class AuthRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
		
		
        $device_log = false;
		$token = null;		
		Config::set('account_type', Request::is('admin/*') ? 'admin' : (Request::is('seller/*') ? 'seller' : (Request::is('affiliate/*') ? 'affiliate' : (Request::is('api/v1/affiliate/*') ? 'affiliate' : 'customer'))));
		print_r(123);exit;
		if (Request::isMethod('post'))
		{
			$token = Request::header('X-Device-Token');						
		}	
		if ($request->header('usrtoken'))
		{
			$token = $request->header('usrtoken');
		}		 
		if(empty($token) && (($request->is('seller/*') || $request->is('affiliate/*') || $request->is('admin/*'))))
		{
			$token = Cookie::get('X-'.Config::get('account_type').'-Token');			
		}		
		if (!empty($token))
		{
			$device_log = DB::table(Config::get('tables.DEVICE_LOG'))
					->where('token', $token)
					->first();
		}		
		if (!$device_log)
		{
			$device_log_data = [];
			$device_log_data['device_info'] = Request::header('user-agent');
			$device_log_data['ip'] = Request::getClientIp(true);
			$device_log_data['token'] = $token = md5(rand(000, 999).date('YmdHis').$device_log_data['device_info'].$device_log_data['ip']);
			$device_log_data['created_on'] = date('Y-m-d H:i:s');
			$device_log_id = DB::table(Config::get('tables.DEVICE_LOG'))
					->insertGetID($device_log_data);			
			if ($device_log_id)
			{
				$device_log = DB::table(Config::get('tables.DEVICE_LOG'))
						->where('device_log_id', $device_log_id)
						->first();
			}
		}		
		if (empty($device_log) || empty($token))
		{
			return Request::ajax() ? Response::json(array('msg'=>'Page Not Found'), 404) : App::abort(404);
		}		
		Config::set('device_log', $device_log);		
		if (Request::isMethod('get'))
		{
			$token = Config::get('device_log')->token;
			Cookie::queue('X-'.Config::get('account_type').'-Token', $token, 144000); //100 Days
		}				
		if ($device_log->status && !empty($device_log->account_id))
		{			
			$user = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                ->join(Config::get('tables.DEVICE_LOG').' as dl', 'dl.account_id', '=', 'am.account_id')
                ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'am.account_type_id')
                ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
				->leftjoin(Config::get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'ap.currency_id')			
				->leftjoin(Config::get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'ap.country_id')	
                ->selectRaw('am.account_id,am.is_affiliate, at.account_type_name, concat(ad.firstname,\' \',ad.lastname) as full_name, am.email, am.mobile, am.uname, am.account_type_id, dl.token, ap.language_id, ap.currency_id,cur.currency as currency_code, ap.is_mobile_verified, ap.is_email_verified, ap.send_email, ap.send_sms, ap.send_notification, am.pass_key, ap.country_id,am.security_pin')
                ->where('am.account_id', $device_log->account_id)
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
			
			if (!empty($user))
			{
				Config::set('data.user', $user);
			}			
		}
        return $next($request);
    }
}
