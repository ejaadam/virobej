<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Log;

class Authenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle ($request, Closure $next, $guard = null)
    {			
        if (Config::has('app.accountInfo') && !empty(Config::get('app.accountInfo')))
        {			
            return $next($request);
        }
        else
        {	            
			Log::info('Unautorized Access Token Issue in '.$request->fullUrl().' with token '.$request->header('usrtoken').' from '.$request->header('User-Agent'));
			
            return ($request->isMethod('get') ? redirect()->route(config('app.role') != 'user' ? config('app.role').'.login' : config('app.role').'.home') : response()->json(['msg'=>"We couldn't sign you in. Please try agian", 'status'=>401], 401));

        }
    }

}
