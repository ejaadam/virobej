<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;
class SendSMS extends Facade{
    protected static function getFacadeAccessor() { return 'sendsms'; }
}