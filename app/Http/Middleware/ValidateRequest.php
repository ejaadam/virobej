<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Route;
use Validator;
use Closure;
use Log;

class ValidateRequest
{
    public function handle ($request, Closure $next)
    {
		
        if ($request->isMethod('post'))
        {
            $route_name  = Route::currentRouteName();			
	        $session 	 = session();
            $userInfo 	 = (object) session(config('app.role'));
			
			//print_r($userInfo);exit;
			//$userInfo->currency_code = 'USD';
            $validations = array_get(array_merge(($request->is('affiliate/*') ? ['aff'=>include('validations/affiliate.php')] : ($request->is('admin/*') ? ['admin'=>include('validations/admin.php')] : (($request->is('seller/*') || $request->is('api/v1/seller/*')) ? ['seller'=>include('validations/seller.php')] : ($request->is('api/v1/affiliate/*') ? ['api'=>['v1'=>['affiliate'=>include('validations/affiliate_api.php')]]] : ($request->is('api/v1/user/*') ? ['api'=>['v1'=>['user'=>include('validations/user_api.php')]]] : [])))))), $route_name);	
		
            $validations = is_array($validations) ? array_filter($validations) : [];	
		
            if (!empty($validations))
            {
                $rules = array_key_exists('RULES', $validations) ? $validations['RULES'] : [];
                $messages = array_key_exists('MESSAGES', $validations) ? $validations['MESSAGES'] : [];
                $attributes = array_key_exists('LABELS', $validations) ? $validations['LABELS'] : [];				
                $attributes = is_array($attributes) ? $attributes : trans($attributes);	
				//print_r($attributes);exit;	
                array_walk($attributes, function(&$attribute)
                {
                    $a = trans($attribute);
                    $attribute = !empty($a) ? $a : $attribute;
                });
                array_walk($messages, function(&$msgLang)
                {
                    $a = trans($msgLang);
                    $msgLang = !empty($a) ? $a : $msgLang;
                });
                
                $reqData = $request->all();                
                $validator = Validator::make($reqData, $rules, $messages, $attributes);
                if ($validator->fails())
                {
					
                    $response = [];                    
					foreach($validator->messages(true) as $k => $v) {
						$error[$k] = [$v[0]]; 
					}
                    $response['error'] = $error;
                    //$response['error'] = $validator->messages(true);
					
                    return response()->json($response, config('httperr.PARAMS_MISSING'));
                }
            }
            else if (!is_array($validations))
            {
                Log::error('Validation Configuration missing for the route: '.$route_name);
                abort(500, 'Validation Configuration missing for the route: '.$route_name);
            }
        }
        return $next($request);
    }
}
