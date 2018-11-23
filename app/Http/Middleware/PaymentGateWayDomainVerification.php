<?php

namespace App\Http\Middleware;

use DB;
use Closure;

class PaymentGateWayDomainVerification
{

    public function handle ($request, Closure $next)
    {		
        $payment_type = $request->segment(3) != 'user' ? $request->segment(4) : $request->segment(5);				
        $domains = DB::table(config('tables.PAYMENT_TYPES'))
                ->where('payment_key', $payment_type)
                ->value('domains');		
        $domains = explode(',', $domains);
        $domains[] = url('/');
        $request_origin = $request->header('origin');		
        if (config('app.debug') || in_array($request_origin, $domains))
        {			
            return $next($request);
        }
        else
        {
            return ($request->isMethod('get') ? app()->abort(403, 'Not Allowed to Access') : response()->json(['msg'=>'Not Allowed to Access'], 403));
        }
    }

}
