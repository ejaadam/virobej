<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Log;
use Session;

class AdAuthenticate
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
	
        if (Session::has('admin') && !empty(Session::has('admin')))
        {	
			Config::set('data.user',Session::get('admin'));
            return $next($request);
        }
        else
        {			
            Log::info('Unautorized Access Token Issue in '.$request->route()->getName().' with token '.$request->header('usrtoken').' from '.$request->header('User-Agent'));			
            return ($request->isMethod('get') ? redirect()->route('admin.login') : response()->json(['msg'=>"We couldn't sign you in. Please try agian", 'status'=>401], 401));

        }
    }

}
