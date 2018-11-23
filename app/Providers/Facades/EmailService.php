<?php

namespace App\Providers\Facades;

use Illuminate\Support\Facades\Facade;

class EmailService extends Facade
{
     /**
     * The IoC key accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'emailservice';
    }
}