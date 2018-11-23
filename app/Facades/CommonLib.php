<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CommonLib extends Facade
{

    protected static function getFacadeAccessor ()
    {
        return 'commonlib';
    }

}
