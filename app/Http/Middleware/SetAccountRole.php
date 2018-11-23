<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class SetAccountRole
{

    public function handle ($request, Closure $next)
    {

        if (stripos($request->header('User-Agent'), 'Android') === true || stripos($request->header('User-Agent'), 'Darwin') === true)
        {
            Config::set('app.is_mobile', true);
        }
        else
        {
            Config::set('app.is_mobile', false);
        }
        if ($request->is('api/v1/*'))
        {
            Config::set('session.expire_on_close', false);
            Config::set('app.is_api', true);
        }
        else
        {
            Config::set('app.is_api', false);
        }
        if ($request->is('admin/*'))
        {
            Config::set('app.role', 'admin');
            // Config::set('session.path', 'admin/');
        }
        else if ($request->is('seller/*') || $request->is('api/v1/seller/*'))
        {
            Config::set('app.role', 'seller');
            //Config::set('session.path', 'retailer/');
        }        
        else
        {
            view()->share('is_first_visit', (isset($_COOKIE[config('session.cookie')]) && !empty($_COOKIE[config('session.cookie')]) ? false : true));
            Config::set('app.role', 'user');
            //Config::set('session.path', '/');
        }
        //echo config('session.path');exit;
        Config::set('app.session_key', 'SID-'.strtoupper(config('app.role')));
        view()->share('role', config('app.role'));		
        return $next($request);
    }

}
