<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;
class TWMailer extends Facade{
    protected static function getFacadeAccessor() { return 'twmailer'; }
}