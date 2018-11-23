<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,		
		\Illuminate\Session\Middleware\StartSession::class,		
        \App\Http\Middleware\SetAccountRole::class,        		
		\App\Http\Middleware\CompressHTML::class,
	];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
			\App\Http\Middleware\Authenticate_Web::class,           
			\App\Http\Middleware\AuthRequest::class,
			\App\Http\Middleware\ValidateRequest::class,			
        ],

        'api' => [
            'throttle:60,1',						
			\App\Http\Middleware\UserApiAuthRequest::class,
			//\App\Http\Middleware\PaymentGateWayDomainVerification::class,
			\App\Http\Middleware\ValidateRequest::class,
        ],
    ];
	
	
	protected $middlewarePriority = [        
		\Illuminate\Routing\Middleware\ThrottleRequests::class, 	
        \App\Http\Middleware\Authenticate::class,
		\App\Http\Middleware\UserApiAuthRequest::class,
		\App\Http\Middleware\Authenticate_Web::class,
		\App\Http\Middleware\AuthRequest::class,		
		\App\Http\Middleware\PaymentGateWayDomainVerification::class,
        \App\Http\Middleware\ValidateRequest::class,
		\App\Http\Middleware\SetLocation::class,
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,		
		'affauth' => \App\Http\Middleware\AffAuthenticate::class,
		'adauth' => \App\Http\Middleware\AdAuthenticate::class,
		'authweb'=>\App\Http\Middleware\Authenticate_Web::class,
		'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
		'checkPaymentGateWayDomain'=>\App\Http\Middleware\PaymentGateWayDomainVerification::class,
		'validate'=>\App\Http\Middleware\ValidateRequest::class,		
		'setLocation'=>\App\Http\Middleware\SetLocation::class,		
    ];
}
