<?php
namespace App\Http\Middleware;
use Response;
use Closure;
use Illuminate\Support\Facades\Config;
use Redirect;
use Request;
use Log;
use Session;

class Authenticate_Web
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
		if (Session::has('seller') && !empty(Session::has('seller')))
        {	
			Config::set('data.user', Session::get('seller'));
            return $next($request);
        }
        else
        {			
            Log::info('Unautorized Access Token Issue in '.$request->route()->getName().' with token '.$request->header('usrtoken').' from '.$request->header('User-Agent'));			
            return ($request->isMethod('get') ? redirect()->route('seller.login') : response()->json(['msg'=>"We couldn't sign you in. Please try agian", 'status'=>401], 401));

        }
    }
}
